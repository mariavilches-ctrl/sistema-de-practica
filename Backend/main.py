import os
import secrets
import webbrowser
from threading import Timer
from flask import Flask, jsonify, request, redirect
import pyodbc
from flask_cors import CORS
from dotenv import load_dotenv

# 1. Cargar configuración
load_dotenv()

app = Flask(__name__)
CORS(app)  # Habilitar CORS

# 2. Conexión a Base de Datos (Configuración J9N4 Blindada)
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
        # print(f"✅ Conectado a {database}") # Descomentar si quieres ver logs
        return conn
    except Exception as e:
        print(f"❌ Error BD: {e}")
        return None

# ==========================================
# RUTAS (Endpoints)
# Nota: He quitado el '/api' para coincidir con tu ApiClient.php
# ==========================================

@app.route("/")
def index():
    return "Sistema de Prácticas - Backend Operativo"

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
        # ApiClient envía 'usuario' y 'password'
        email = datos.get('usuario')
        password = datos.get('password')

        conn = get_db_connection()
        if not conn:
            return jsonify({"success": False, "message": "Error de conexión BD"}), 500

        cursor = conn.cursor()
        # Ajusta si tu tabla se llama diferente
        cursor.execute("SELECT * FROM Usuarios WHERE Email = ? AND Password = ?", (email, password))
        usuario_encontrado = cursor.fetchone()
        
        if usuario_encontrado:
            # Convertimos la fila a diccionario
            columns = [column[0] for column in cursor.description]
            user_dict = dict(zip(columns, usuario_encontrado))
            conn.close()
            
            token = secrets.token_urlsafe(24)
            return jsonify({
                "success": True, 
                "message": "Login exitoso",
                "token": token,
                "usuario": user_dict # Enviamos el objeto completo
            })
        else:
            conn.close()
            return jsonify({"success": False, "message": "Credenciales inválidas"}), 401

    except Exception as e:
        return jsonify({"success": False, "message": f"Error servidor: {str(e)}"}), 500

# --- PRÁCTICAS (GET y POST) ---
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

@app.route('/practicas', methods=['POST'])
def create_practica():
    try:
        datos = request.get_json()
        # Validación básica
        required = ['idEstudiante', 'idCentroPractica', 'idTutor', 'idSupervisor', 'tipo']
        if not all(k in datos for k in required):
            return jsonify({'success': False, 'message': 'Faltan campos obligatorios'}), 400

        fechaInicio = datos.get('fechaDeInicio')
        fechaTermino = datos.get('fechaDeTermino')
        evidencia = datos.get('evidenciaImg', '')
        actividades = datos.get('actividades', '')

        conn = get_db_connection()
        if not conn: return jsonify({'success': False, 'message': 'Error BD'}), 500
        
        cursor = conn.cursor()
        query = """
        INSERT INTO Practica (idEstudiante, idCentroPractica, idTutor, idSupervisor, tipo, fechaDeInicio, fechaDeTermino, actividades, evidenciaImg)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        """
        cursor.execute(query, (
            datos['idEstudiante'], datos['idCentroPractica'], datos['idTutor'], datos['idSupervisor'],
            datos['tipo'], fechaInicio, fechaTermino, actividades, evidencia
        ))
        conn.commit()
        conn.close()

        return jsonify({'success': True, 'message': 'Práctica creada'})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

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

# --- TIPOS DE PRÁCTICA ---
@app.route('/tipos', methods=['GET'])
def get_tipos():
    conn = get_db_connection()
    if not conn: return jsonify([]), 500
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT idTipoPractica, nombre, horasRequeridas FROM TipoPractica")
        columns = [column[0] for column in cursor.description]
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
        # Ajusta columnas según tu tabla real
        cursor.execute("SELECT * FROM Sesion")
        columns = [column[0] for column in cursor.description]
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
        # Asegúrate que los nombres de columnas coincidan con tu SQL Server
        cursor.execute(
            "INSERT INTO Sesion (idPractica, fecha, horaInicio, horaTermino, horas, actividad, estado) VALUES (?, ?, ?, ?, ?, ?, ?)",
            (datos.get('idPractica'), datos.get('fecha'), datos.get('horaInicio'), datos.get('horaTermino'), datos.get('horas'), datos.get('actividad', ''), datos.get('estado', 'Programada'))
        )
        conn.commit()
        conn.close()
        return jsonify({'success': True, 'message': 'Sesión creada'}), 201
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# --- ESTUDIANTES Y CENTROS ---
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



def abrir_navegador():
    # Ajusta esta URL a tu proyecto local
    url_login = "http://localhost/sistema-de-practica/frontend/login.php"
    webbrowser.open_new(url_login)

if __name__ == '__main__':
    print("🚀 Servidor Backend iniciado en puerto 5000")
    # Espera 1.5 seg y abre el login automáticamente
    Timer(1.5, abrir_navegador).start()
    app.run(debug=True, port=5000)