import os
from flask import Flask, jsonify, request
import secrets
import pyodbc
from flask_cors import CORS
from dotenv import load_dotenv

# Cargar archivo .env
load_dotenv()

app = Flask(__name__)
CORS(app)  # Habilitar CORS para todas las rutas

def get_db_connection():
    try:
        # Leer datos de conexión desde .env
        server = os.getenv('DB_SERVER')
        database = os.getenv('DB_NAME')
        username = os.getenv('DB_USER')
        password = os.getenv('DB_PASSWORD')

        # Por si algo viene vacío
        if not all([server, database, username, password]):
            print("Faltan variables de entorno para la BD")
            return None

        connection_string = (
            "DRIVER={ODBC Driver 17 for SQL Server};"
            f"SERVER={server};"
            f"DATABASE={database};"
            f"UID={username};"
            f"PWD={password}"
        )

        conn = pyodbc.connect(connection_string)
        return conn
    except Exception as e:
        print("Error al conectar a la base de datos:", e)
        return None

# ---------------- RUTAS ----------------

@app.route("/")
def index():
    return "Sistema funcionando correctamente"

# Ruta de prueba: solo verifica conexión con la BD
@app.route("/api/test-db")
def test_db():
    conn = get_db_connection()
    if not conn:
        return jsonify({"success": False, "message": "No se pudo conectar a la base de datos"}), 500
    conn.close()
    return jsonify({"success": True, "message": "Conexión a la BD OK"})

@app.route("/api/login", methods=['POST'])
def login():
    try:
        datos = request.get_json()
        email = datos.get('usuario')
        password = datos.get('password')

        conn = get_db_connection()
        if not conn:
            return jsonify({"success": False, "message": "Error de conexión a la base de datos"}), 500

        cursor = conn.cursor()

        # Asegúrate que la tabla y columnas existen: Usuarios(Email, Password)
        query = "SELECT * FROM Usuarios WHERE Email = ? AND Password = ?"
        cursor.execute(query, (email, password))

        usuario_encontrado = cursor.fetchone()
        conn.close()

        if usuario_encontrado:
            token = secrets.token_urlsafe(24)
            return jsonify({
                "success": True,
                "message": "Login exitoso",
                "token": token,
                "datos": {"id": usuario_encontrado[0], "email": email}
            })
        else:
            return jsonify({
                "success": False,
                "message": "Credenciales inválidas"
            }), 401
    except Exception as e:
        return jsonify({
            "success": False,
            "message": f"Error en el servidor: {str(e)}"
        }), 500

@app.route("/api/practicas", methods=['GET'])
def get_practicas():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500

    cursor = conn.cursor()
    cursor.execute("SELECT * FROM Practica")  # asegúrate que se llame así la tabla

    columns = [column[0] for column in cursor.description]
    results = [dict(zip(columns, row)) for row in cursor.fetchall()]

    conn.close()
    return jsonify(results)

@app.route("/api/bitacora", methods=['GET'])
def get_bitacora():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500

    cursor = conn.cursor()
    cursor.execute("SELECT * FROM Bitacora")  # tabla Bitacora en SistemaPracticas

    columns = [column[0] for column in cursor.description]
    results = [dict(zip(columns, row)) for row in cursor.fetchall()]

    conn.close()
    return jsonify(results)

if __name__ == "__main__":
    app.run(debug=True, port=5000)
