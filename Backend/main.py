import os
from flask import Flask, jsonify, render_template, request
import secrets
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
            # Generate a short random token for minimal compatibility with frontend
            token = secrets.token_urlsafe(24)
            return jsonify({
                "success": True,
                "message": "login exitoso",
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
            "message" : f"Error en el servidor: {str(e)}"
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


@app.route('/api/practicas', methods=['POST'])
def create_practica():
    try:
        datos = request.get_json()
        required = ['idEstudiante', 'idCentroPractica', 'idTutor', 'idSupervisor', 'tipo', 'actividades']
        if not all(k in datos for k in required):
            return jsonify({'success': False, 'message': 'Faltan campos obligatorios'}), 400

        fechaInicio = datos.get('fechaDeInicio')
        fechaTermino = datos.get('fechaDeTermino')
        evidencia = datos.get('evidenciaImg', '')

        conn = get_db_connection()
        if not conn:
            return jsonify({'success': False, 'message': 'Error de conexión a BD'}), 500
        cursor = conn.cursor()

        insert_q = """
        INSERT INTO Practica (idEstudiante, idCentroPractica, idTutor, idSupervisor, tipo, fechaDeInicio, fechaDeTermino, actividades, evidenciaImg)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        """
        cursor.execute(insert_q, (
            datos['idEstudiante'], datos['idCentroPractica'], datos['idTutor'], datos['idSupervisor'],
            datos['tipo'], fechaInicio, fechaTermino, datos['actividades'], evidencia
        ))
        conn.commit()
        new_id = None
        try:
            new_id = cursor.lastrowid
        except Exception:
            new_id = None
        conn.close()

        return jsonify({'success': True, 'message': 'Práctica creada', 'id': new_id})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

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


@app.route('/api/tipos', methods=['GET'])
def get_tipos():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500
    cursor = conn.cursor()

    try:
        cursor.execute("SELECT idTipoPractica, nombre, horasRequeridas FROM TipoPractica")
        columns = [column[0] for column in cursor.description]
        rows = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(rows)
    except Exception:
        # If table doesn't exist or other error, return empty list
        return jsonify([])


@app.route('/api/tipos', methods=['POST'])
def create_tipo():
    try:
        datos = request.get_json()
        nombre = datos.get('nombre')
        horas = int(datos.get('horasRequeridas', 0))
        if not nombre or horas <= 0:
            return jsonify({'success': False, 'message': 'nombre y horasRequeridas son requeridos'}), 400

        conn = get_db_connection()
        if not conn:
            return jsonify({'success': False, 'message': 'Error de conexión a BD'}), 500
        cursor = conn.cursor()
        cursor.execute("INSERT INTO TipoPractica (nombre, horasRequeridas) VALUES (?, ?)", (nombre, horas))
        conn.commit()
        conn.close()
        return jsonify({'success': True, 'message': 'Tipo creado'}), 201
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500


@app.route('/api/sesiones', methods=['GET'])
def get_sesiones():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500
    cursor = conn.cursor()
    try:
        cursor.execute("SELECT idSesion, idPractica, fecha, horaInicio, horaTermino, horas, actividad, estado FROM Sesion")
        columns = [column[0] for column in cursor.description]
        rows = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(rows)
    except Exception:
        return jsonify([])


@app.route('/api/sesiones', methods=['POST'])
def create_sesion():
    try:
        datos = request.get_json()
        required = ['fecha', 'horaInicio', 'horaTermino', 'horas']
        if not all(k in datos for k in required):
            return jsonify({'success': False, 'message': 'Faltan campos obligatorios para sesión'}), 400

        conn = get_db_connection()
        if not conn:
            return jsonify({'success': False, 'message': 'Error de conexión a BD'}), 500
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO Sesion (idPractica, fecha, horaInicio, horaTermino, horas, actividad, estado) VALUES (?, ?, ?, ?, ?, ?, ?)",
            (datos.get('idPractica'), datos['fecha'], datos['horaInicio'], datos['horaTermino'], datos['horas'], datos.get('actividad', ''), datos.get('estado', 'Programada'))
        )
        conn.commit()
        conn.close()
        return jsonify({'success': True, 'message': 'Sesión creada'}), 201
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500


@app.route('/api/estudiantes', methods=['GET'])
def get_estudiantes():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500
    cursor = conn.cursor()
    try:
        cursor.execute("SELECT * FROM Estudiante")
        columns = [c[0] for c in cursor.description]
        rows = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(rows)
    except Exception:
        return jsonify([])


@app.route('/api/centros', methods=['GET'])
def get_centros():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500
    cursor = conn.cursor()
    try:
        cursor.execute("SELECT * FROM CentroPractica")
        columns = [c[0] for c in cursor.description]
        rows = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(rows)
    except Exception:
        return jsonify([])
    
if __name__ == '__main__':
    
    app.run(debug=True, port= 5000)