import os
import secrets
import webbrowser
from threading import Timer
from flask import Flask, jsonify, request, redirect # Agregado redirect
import pyodbc
from flask_cors import CORS
from dotenv import load_dotenv

# Cargar archivo .env
load_dotenv()

app = Flask(__name__)
CORS(app)

# --- CONEXIÓN A BASE DE DATOS ---
def get_db_connection():
    try:
        server = os.getenv('DB_SERVER')
        database = os.getenv('DB_NAME')
        username = os.getenv('DB_USER')
        password = os.getenv('DB_PASSWORD')
        
        connection_string = (
            f'DRIVER={{ODBC Driver 17 for SQL Server}};'
            f'SERVER={server};'
            f'DATABASE={database};'
            f'UID={username};'
            f'PWD={password};'
            f'Encrypt=yes;'
            f'TrustServerCertificate=yes;'
        )
        
        conn = pyodbc.connect(connection_string)
        # print(f"✅ Conexión establecida a {database}") 
        return conn
    except Exception as e:
        print(f"❌ Error en get_db_connection: {e}")
        return None

# ==========================================
# RUTAS (Sin el prefijo '/api' para coincidir con PHP)
# ==========================================

# 1. Redirección automática al Login de PHP
@app.route("/")
def index():
    # Ajusta esta URL si tu carpeta se llama diferente en htdocs
    return redirect("http://localhost/sistema-de-practica/frontend/partials/login.php")

@app.route("/test-db")
def test_db():
    conn = get_db_connection()
    if not conn:
        return jsonify({"success": False, "message": "Fallo conexión BD"}), 500
    conn.close()
    return jsonify({"success": True, "message": "Conexión BD OK"})

# --- LOGIN ---
@app.route("/login", methods=['POST'])
def login():
    try:
        datos = request.get_json()
        email = datos.get('usuario')
        password = datos.get('password')

        conn = get_db_connection()
        if not conn:
            return jsonify({"success": False, "message": "Error conexión BD"}), 500

        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Usuarios WHERE Email = ? AND Password = ?", (email, password))
        usuario_encontrado = cursor.fetchone()
        
        if usuario_encontrado:
            # Convertimos a diccionario para enviar datos limpios
            columns = [column[0] for column in cursor.description]
            user_dict = dict(zip(columns, usuario_encontrado))
            conn.close()
            
            token = secrets.token_urlsafe(24)
            return jsonify({
                "success": True,
                "message": "Login exitoso",
                "token": token,
                "usuario": user_dict # Importante: PHP espera 'usuario'
            })
        else:
            conn.close()
            return jsonify({"success": False, "message": "Credenciales inválidas"}), 401
    except Exception as e:
        return jsonify({"success": False, "message": f"Error servidor: {str(e)}"}), 500

# --- PRÁCTICAS ---
@app.route("/practicas", methods=['GET'])
def get_practicas():
    conn = get_db_connection()
    if not conn: return jsonify([]), 500
    cursor = conn.cursor()
    cursor.execute("SELECT * FROM Practica")
    columns = [column[0] for column in cursor.description]
    results = [dict(zip(columns, row)) for row in cursor.fetchall()]
    conn.close()
    return jsonify(results)

# Usamos la versión con Store Procedure (sp_InsertPractica)
@app.route('/practicas', methods=['POST'])
def crear_practica():
    datos = request.json
    conn = get_db_connection()
    if not conn: return jsonify({'success': False, 'message': 'Error conexión BD'}), 500

    try:
        cursor = conn.cursor()
        # Asegúrate de enviar los campos correctos desde PHP
        cursor.execute("{CALL sp_InsertPractica (?, ?, ?, ?, ?, ?, ?, ?, ?)}", 
                       (datos.get('idEstudiante'), 
                        datos.get('idCentroPractica'), 
                        datos.get('idTutor'), 
                        datos.get('idSupervisor'), 
                        datos.get('tipo'), 
                        datos.get('fechaInicio'), # PHP debe enviar fechaInicio
                        datos.get('fechaTermino'), # PHP debe enviar fechaTermino
                        datos.get('actividades'), 
                        datos.get('evidenciaImg', '')))
        conn.commit()
        return jsonify({'success': True, 'message': 'Práctica guardada'}), 201
    except Exception as e:
        print(f"Error SQL: {e}")
        return jsonify({'success': False, 'message': str(e)}), 500
    finally:
        if conn: conn.close()

# --- BITÁCORA ---
@app.route('/bitacora', methods=['GET'])
def get_bitacora():
    conn = get_db_connection()
    if not conn: return jsonify([]), 500
    cursor = conn.cursor()
    cursor.execute("SELECT * FROM Bitacora")
    columns = [column[0] for column in cursor.description]
    results = [dict(zip(columns, row)) for row in cursor.fetchall()]
    conn.close()
    return jsonify(results)

# --- TIPOS ---
@app.route('/tipos', methods=['GET'])
def get_tipos():
    conn = get_db_connection()
    if not conn: return jsonify([]), 500
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT idTipoPractica, nombre, horasRequeridas FROM TipoPractica")
        columns = [c[0] for c in cursor.description]
        results = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(results)
    except Exception:
        return jsonify([])

@app.route('/tipos', methods=['POST'])
def create_tipo():
    try:
        datos = request.get_json()
        nombre = datos.get('nombre')
        horas = int(datos.get('horasRequeridas', 0))
        
        conn = get_db_connection()
        if not conn: return jsonify({'success': False}), 500
        
        cursor = conn.cursor()
        cursor.execute("INSERT INTO TipoPractica (nombre, horasRequeridas) VALUES (?, ?)", (nombre, horas))
        conn.commit()
        conn.close()
        return jsonify({'success': True, 'message': 'Tipo creado'}), 201
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# --- SESIONES ---
@app.route('/sesiones', methods=['GET'])
def get_sesiones():
    conn = get_db_connection()
    if not conn: return jsonify([]), 500
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Sesion")
        columns = [c[0] for c in cursor.description]
        results = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(results)
    except Exception:
        return jsonify([])

@app.route('/sesiones', methods=['POST'])
def create_sesion():
    try:
        datos = request.get_json()
        conn = get_db_connection()
        if not conn: return jsonify({'success': False}), 500
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO Sesion (idPractica, fecha, horaInicio, horaTermino, horas, actividad, estado) VALUES (?, ?, ?, ?, ?, ?, ?)",
            (datos.get('idPractica'), datos.get('fecha'), datos.get('horaInicio'), datos.get('horaTermino'), datos.get('horas'), datos.get('actividad', ''), datos.get('estado', 'Programada'))
        )
        conn.commit()
        conn.close()
        return jsonify({'success': True, 'message': 'Sesión creada'}), 201
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# --- AUXILIARES (Estudiantes, Centros) ---
@app.route('/estudiantes', methods=['GET'])
def get_estudiantes():
    conn = get_db_connection()
    if not conn: return jsonify([]), 500
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Estudiante")
        columns = [c[0] for c in cursor.description]
        results = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(results)
    except Exception:
        return jsonify([])

@app.route('/centros', methods=['GET'])
def get_centros():
    conn = get_db_connection()
    if not conn: return jsonify([]), 500
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM CentroPractica")
        columns = [c[0] for c in cursor.description]
        results = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(results)
    except Exception:
        return jsonify([])

# ==========================================
# INICIO DEL SERVIDOR
# ==========================================

def abrir_navegador():
    # URL de tu Login en PHP
    url = "http://localhost/sistema-de-practica/frontend/login.php"
    webbrowser.open_new(url)

if __name__ == "__main__":
    print("🚀 Iniciando Backend...")
    # Timer para abrir el navegador automáticamente después de 1.5 seg
    Timer(1.5, abrir_navegador).start()
    app.run(debug=True, port=5000)