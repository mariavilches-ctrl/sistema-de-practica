import os
import secrets
import webbrowser
from threading import Timer
from flask import Flask, jsonify, request, redirect
import pyodbc
from flask_cors import CORS
from dotenv import load_dotenv
from datetime import datetime
from patterns import (
    TipoPracticaFactory, 
    CalendarizacionUniforme, 
    CalendarizacionIntensiva, 
    CalendarizacionProgresiva, 
    CalendarioObservable, 
    RegistroSeguimiento, 
    NotificadorSupervisor
)

load_dotenv()

app = Flask(__name__)
CORS(app)

# --- INSTANCIAS GLOBALES ---
calendario_observable = CalendarioObservable()
registro_seguimiento = RegistroSeguimiento()
notificador = NotificadorSupervisor()

calendario_observable.agregar_observador(registro_seguimiento)
calendario_observable.agregar_observador(notificador)

# --- CONEXIÓN BD ---
def get_db_connection():
    try:
        connection_string = (
            f'DRIVER={{ODBC Driver 17 for SQL Server}};'
            f'SERVER={os.getenv("DB_SERVER")};'
            f'DATABASE={os.getenv("DB_NAME")};'
            f'UID={os.getenv("DB_USER")};'
            f'PWD={os.getenv("DB_PASSWORD")};'
            f'Encrypt=yes;'
            f'TrustServerCertificate=yes;'
        )
        return pyodbc.connect(connection_string)
    except Exception as e:
        print(f"❌ Error BD: {e}")
        return None

@app.route("/")
def index():
    return redirect("http://localhost/sistema-de-practica/frontend/login.php")

# --- LOGIN ---
@app.route("/login", methods=['POST'])
def login():
    try:
        datos = request.get_json()
        conn = get_db_connection()
        if not conn: return jsonify({"message": "Error BD"}), 500
        
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Usuarios WHERE Email = ? AND Password = ?", 
                       (datos.get('usuario'), datos.get('password')))
        user = cursor.fetchone()
        
        if user:
            cols = [c[0] for c in cursor.description]
            user_dict = dict(zip(cols, user))
            conn.close()
            return jsonify({"success": True, "token": secrets.token_urlsafe(24), "usuario": user_dict})
        
        conn.close()
        return jsonify({"success": False, "message": "Credenciales inválidas"}), 401
    except Exception as e:
        return jsonify({"success": False, "message": str(e)}), 500

# --- PRÁCTICAS (MODIFICADO CON JOIN) ---
@app.route("/practicas", methods=['GET'])
def get_practicas():
    conn = get_db_connection()
    if not conn: return jsonify([]), 500
    cursor = conn.cursor()
    
    
    query = """
    SELECT 
        P.idPractica,
        E.nombreCompleto AS NombreEstudiante,
        P.tipo,
        C.nombre AS NombreCentro,
        T.nombre AS NombreTutor,
        P.fechaDeInicio,
        P.fechaDeTermino
    FROM Practica P
    INNER JOIN Estudiante E ON P.idEstudiante = E.idEstudiante
    INNER JOIN CentroPractica C ON P.idCentroPractica = C.idCentroPractica
    INNER JOIN Tutor T ON P.idTutor = T.idTutor
    """
    
    try:
        cursor.execute(query)
        columns = [column[0] for column in cursor.description]
        results = [dict(zip(columns, row)) for row in cursor.fetchall()]
    except Exception as e:
        print(f"Error SQL Practicas: {e}")
        results = []
        
    conn.close()
    return jsonify(results)

@app.route('/practicas', methods=['POST'])
def crear_practica():
    datos = request.json
    conn = get_db_connection()
    if not conn: return jsonify({'success': False, 'message': 'Error BD'}), 500

    try:
        cursor = conn.cursor()
        cursor.execute("{CALL sp_InsertPractica (?, ?, ?, ?, ?, ?, ?, ?, ?)}", 
                       (datos.get('idEstudiante'), 
                        datos.get('idCentroPractica'), 
                        datos.get('idTutor'), 
                        datos.get('idSupervisor'), 
                        datos.get('tipo'), 
                        datos.get('fechaInicio'),
                        datos.get('fechaTermino'),
                        datos.get('actividades', 'Asignada'), 
                        datos.get('evidenciaImg', '')))
        conn.commit()
        calendario_observable.notificar_observadores('practica_creada', datos)
        return jsonify({'success': True, 'message': 'Práctica asignada'}), 201
    except Exception as e:
        print(f"Error SQL: {e}")
        return jsonify({'success': False, 'message': str(e)}), 500
    finally:
        if conn: conn.close()

@app.route('/practicas/<int:id>', methods=['DELETE'])
def delete_practica(id):
    try:
        conn = get_db_connection()
        if not conn: return jsonify({'success': False}), 500
        cursor = conn.cursor()
        cursor.execute("{CALL sp_DeletePractica (?)}", (id,))
        conn.commit()
        conn.close()
        calendario_observable.notificar_observadores('practica_eliminada', {'id': id})
        return jsonify({'success': True})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# --- OTROS CRUDs (Centros, Estudiantes, etc.) ---
@app.route('/centros', methods=['GET'])
def get_centros():
    conn = get_db_connection()
    if not conn: return jsonify([]), 500
    cursor = conn.cursor()
    cursor.execute("SELECT * FROM CentroPractica")
    columns = [c[0] for c in cursor.description]
    res = [dict(zip(columns, row)) for row in cursor.fetchall()]
    conn.close()
    return jsonify(res)

@app.route('/centros', methods=['POST'])
def create_centro():
    try:
        d = request.get_json()
        conn = get_db_connection()
        if not conn: return jsonify({'success': False}), 500
        cursor = conn.cursor()
        cursor.execute("{CALL sp_InsertCentroPractica (?, ?, ?, ?, ?)}",
            (d.get('rutEmpresa'), d.get('nombre'), d.get('descripcion'), d.get('habilidadesEsperadas'), d.get('direccion')))
        conn.commit()
        conn.close()
        calendario_observable.notificar_observadores('centro_creado', d)
        return jsonify({'success': True, 'message': 'Centro creado'}), 201
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

@app.route('/centros/<int:id>', methods=['DELETE'])
def delete_centro(id):
    try:
        conn = get_db_connection()
        if not conn: return jsonify({'success': False}), 500
        cursor = conn.cursor()
        cursor.execute("{CALL sp_DeleteCentroPractica (?)}", (id,))
        conn.commit()
        conn.close()
        return jsonify({'success': True})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

@app.route('/estudiantes', methods=['GET'])
def get_estudiantes():
    conn = get_db_connection()
    if not conn: return jsonify([]), 500
    cursor = conn.cursor()
    cursor.execute("SELECT * FROM Estudiante")
    cols = [c[0] for c in cursor.description]
    res = [dict(zip(cols, row)) for row in cursor.fetchall()]
    conn.close()
    return jsonify(res)

# --- CALENDARIZACIÓN Y SEGUIMIENTO ---
@app.route('/generar-calendario', methods=['POST'])
def generar_calendario():
    try:
        d = request.get_json()
        horas = int(d.get('horas_totales', 80))
        inicio = datetime.fromisoformat(d.get('fecha_inicio'))
        estrategia = d.get('estrategia', 'uniforme')
        if estrategia == 'intensiva': cal = CalendarizacionIntensiva()
        elif estrategia == 'progresiva': cal = CalendarizacionProgresiva()
        else: cal = CalendarizacionUniforme()
        sesiones = cal.generar_calendario(horas, inicio, 2)
        return jsonify({'success': True, 'data': sesiones})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

@app.route('/registro-seguimiento', methods=['GET'])
def get_seguimiento():
    return jsonify({'success': True, 'data': registro_seguimiento.obtener_registro()})

@app.route('/tipos-practica', methods=['GET'])
def get_tipos():
    return jsonify({'success': True, 'data': TipoPracticaFactory.obtener_todos_tipos()})

@app.route('/bitacora', methods=['GET'])
def get_bitacora():
    conn = get_db_connection()
    if not conn: return jsonify([]), 500
    cursor = conn.cursor()
    cursor.execute("SELECT * FROM Bitacora")
    cols = [c[0] for c in cursor.description]
    res = [dict(zip(cols, row)) for row in cursor.fetchall()]
    conn.close()
    return jsonify(res)

def abrir_nav():
    webbrowser.open_new("http://localhost/sistema-de-practica/frontend/login.php")

if __name__ == '__main__':
    print("🚀 Backend Iniciado...")
    Timer(1.5, abrir_nav).start()
    app.run(port=5000, debug=True)