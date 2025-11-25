import os
from flask import Flask, jsonify, render_template, request
from flask_sqlalchemy import SQLAlchemy
import pyodbc
from flask_cors import CORS
from dotenv import load_dotenv


# Cargar archivo .env
load_dotenv()

app = Flask(__name__)
CORS(app) # Habilitar CORS para todas las rutas

def get_db_connection():
    try:
        server = os.getenv('DB_SERVER')
        database = os.getenv('DB_NAME')
        username = os.getenv('DB_USER')
        password = os.getenv('DB_PASSWORD')

        connection_string = (
            f"DRIVER={{ODBC Driver 17 for SQL Server}};"
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
#Rutas
@app.route('/')

def index():
    return "Sistema funcionando correctamente"

@app.route('/api/login', methods=['POST'])
def login():
    try:
        datos = request.get_json()
        email = datos.get('usuario')
        password = datos.get('password')

        conn = get_db_connection()
        if not conn:
            return jsonify({"Success": False, "mensaje" : "Error de conexión a la base de datos"}), 500
        cursor = conn.cursor()

        query = "SELECT * FROM Usuarios WHERE Email = ? AND Password = ?"
        cursor.execute(query, (email, password))

        usuario_encontrado = cursor.fetchone()

        conn.close()

        if usuario_encontrado:
            return jsonify({
                "Success": True,
                "mensaje" : "login exitoso",
                "datos": {"id": usuario_encontrado[0], "email": email} 
            })
        else:
            return jsonify({
                "Syccess": False,
                "mensaje" : "Credenciales inválidas"
            }), 401
    except Exception as e:
        return jsonify({
            "Success": False,
            "mensaje" : f"Error en el servidor: {str(e)}"
        }), 500
    
@app.route('/api/practicas', methods=['GET'])
def get_practicas():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500
    cursor = conn.cursor()

    cursor.execute("SELECT * FROM Practica")

    columns = [column [0] for column in cursor.description]
    results = []
    for row in cursor.fetchall():
        results.append(dict(zip(columns, row)))

    conn.close()
    return jsonify(results)

@app.route('/api/bitacora', methods=['GET'])
def get_bitacora():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500
    cursor = conn.cursor()

    cursor.execute("SELECT * FROM Bitacora")

    columns = [column [0] for column in cursor.description]
    results = []
    for row in cursor.fetchall():
        results.append(dict(zip(columns, row)))
    conn.close()
    return jsonify(results)
    
if __name__ == '__main__':
    
    app.run(debug=True, port= 5000)