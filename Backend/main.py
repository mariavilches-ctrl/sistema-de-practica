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
    NotificadorSupervisor,
    PracticaComposite,
    Sesion,
    Actividad
)

load_dotenv()

app = Flask(__name__)
CORS(app)

# Instancias globales de patrones
calendario_observable = CalendarioObservable()
registro_seguimiento = RegistroSeguimiento()
notificador = NotificadorSupervisor()

calendario_observable.agregar_observador(registro_seguimiento)
calendario_observable.agregar_observador(notificador)

# ==========================================
# CONEXIÓN A BASE DE DATOS
# ==========================================

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
        return conn
    except Exception as e:
        print(f"❌ Error en get_db_connection: {e}")
        return None

# ==========================================
# RUTAS GENERALES
# ==========================================

@app.route("/")
def index():
    return redirect("http://localhost/sistema-de-practica/frontend/login.php")

@app.route("/test-db")
def test_db():
    conn = get_db_connection()
    if not conn:
        return jsonify({"success": False, "message": "Fallo conexión BD"}), 500
    conn.close()
    return jsonify({"success": True, "message": "Conexión BD OK"})

# ==========================================
# LOGIN
# ==========================================

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
            columns = [column[0] for column in cursor.description]
            user_dict = dict(zip(columns, usuario_encontrado))
            conn.close()
            
            token = secrets.token_urlsafe(24)
            return jsonify({
                "success": True,
                "message": "Login exitoso",
                "token": token,
                "usuario": user_dict
            })
        else:
            conn.close()
            return jsonify({"success": False, "message": "Credenciales inválidas"}), 401
    except Exception as e:
        return jsonify({"success": False, "message": f"Error servidor: {str(e)}"}), 500

# ==========================================
# FACTORY PATTERN: TIPOS DE PRÁCTICA
# ==========================================

@app.route('/tipos-practica', methods=['GET'])
def get_tipos_practica():
    """Obtiene todos los tipos de práctica usando Factory"""
    try:
        tipos = TipoPracticaFactory.obtener_todos_tipos()
        return jsonify({
            'success': True,
            'data': tipos
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

@app.route('/tipos-practica/<tipo>', methods=['GET'])
def get_tipo_practica(tipo):
    """Obtiene un tipo específico de práctica"""
    try:
        tipo_obj = TipoPracticaFactory.crear_tipo(tipo)
        return jsonify({
            'success': True,
            'data': tipo_obj.obtener_detalles()
        })
    except ValueError as e:
        return jsonify({'success': False, 'message': str(e)}), 400
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# ==========================================
# STRATEGY PATTERN: CALENDARIZACIÓN
# ==========================================

@app.route('/generar-calendario', methods=['POST'])
def generar_calendario():
    """Genera calendario usando Strategy pattern"""
    try:
        datos = request.get_json()
        horas_totales = int(datos.get('horas_totales', 80))
        fecha_inicio = datetime.fromisoformat(datos.get('fecha_inicio'))
        sesiones_por_semana = int(datos.get('sesiones_por_semana', 2))
        estrategia = datos.get('estrategia', 'uniforme').lower()
        
        # Seleccionar estrategia
        if estrategia == 'intensiva':
            calendario = CalendarizacionIntensiva()
        elif estrategia == 'progresiva':
            calendario = CalendarizacionProgresiva()
        else:  # uniforme por defecto
            calendario = CalendarizacionUniforme()
        
        sesiones = calendario.generar_calendario(horas_totales, fecha_inicio, sesiones_por_semana)
        
        # Notificar observadores
        calendario_observable.notificar_observadores('calendario_generado', {
            'estrategia': estrategia,
            'horas': horas_totales,
            'sesiones': len(sesiones)
        })
        
        return jsonify({
            'success': True,
            'message': 'Calendario generado exitosamente',
            'data': sesiones
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# ==========================================
# OBSERVER PATTERN: REGISTRO DE SEGUIMIENTO
# ==========================================

@app.route('/registro-seguimiento', methods=['GET'])
def get_registro_seguimiento():
    """Obtiene el registro de seguimiento de cambios"""
    try:
        registro = registro_seguimiento.obtener_registro()
        return jsonify({
            'success': True,
            'data': registro
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

@app.route('/notificacion-sesion-completada', methods=['POST'])
def notificar_sesion_completada():
    """Notifica que una sesión fue completada"""
    try:
        datos = request.get_json()
        calendario_observable.notificar_observadores('sesion_completada', datos)
        return jsonify({'success': True, 'message': 'Notificación enviada'})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# ==========================================
# COMPOSITE PATTERN: ESTRUCTURA DE PRÁCTICAS
# ==========================================

@app.route('/practica-estructura/<int:id_practica>', methods=['GET'])
def get_estructura_practica(id_practica):
    """Obtiene la estructura jerárquica de una práctica"""
    try:
        conn = get_db_connection()
        if not conn:
            return jsonify({'success': False}), 500
        
        # Obtener práctica
        cursor = conn.cursor()
        cursor.execute("SELECT idPractica, tipo, fechaDeInicio FROM Practica WHERE idPractica = ?", (id_practica,))
        practica_row = cursor.fetchone()
        
        if not practica_row:
            conn.close()
            return jsonify({'success': False, 'message': 'Práctica no encontrada'}), 404
        
        # Crear estructura composite
        practica = PracticaComposite(practica_row[0], practica_row[1], str(practica_row[2]))
        
        # Obtener sesiones
        cursor.execute("SELECT idSesion, fecha FROM Sesion WHERE idPractica = ?", (id_practica,))
        sesiones_rows = cursor.fetchall()
        
        for idx, sesion_row in enumerate(sesiones_rows, 1):
            sesion = Sesion(sesion_row[0], str(sesion_row[1]), idx)
            
            # Obtener actividades de la sesión
            cursor.execute(
                """SELECT idActividad, nombre, horas, descripcion 
                   FROM (SELECT ROW_NUMBER() OVER (PARTITION BY idSesion ORDER BY idSesion) as idActividad,
                   nombre, horas, descripcion FROM Bitacora WHERE idPractica = ?) 
                   WHERE idActividad IS NOT NULL""",
                (id_practica,)
            )
            actividades_rows = cursor.fetchall()
            
            for act_row in actividades_rows:
                actividad = Actividad(act_row[0], act_row[1], act_row[2], act_row[3])
                sesion.agregar_actividad(actividad)
            
            practica.agregar_sesion(sesion)
        
        conn.close()
        
        return jsonify({
            'success': True,
            'data': practica.obtener_estructura()
        })
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# ==========================================
# CRUD BÁSICO: CENTROS DE PRÁCTICA
# ==========================================

@app.route('/centros', methods=['GET'])
def get_centros():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM CentroPractica")
        columns = [c[0] for c in cursor.description]
        results = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(results)
    except Exception as e:
        print(f"Error: {e}")
        return jsonify([])

@app.route('/centros/<int:id>', methods=['GET'])
def get_centro(id):
    conn = get_db_connection()
    if not conn:
        return jsonify({'error': 'BD error'}), 500
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM CentroPractica WHERE idCentroPractica = ?", (id,))
        row = cursor.fetchone()
        conn.close()
        
        if not row:
            return jsonify({'error': 'Centro no encontrado'}), 404
        
        columns = [c[0] for c in cursor.description]
        return jsonify(dict(zip(columns, row)))
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/centros', methods=['POST'])
def create_centro():
    try:
        datos = request.get_json()
        conn = get_db_connection()
        if not conn:
            return jsonify({'success': False, 'message': 'Error BD'}), 500
        
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO CentroPractica (rutEmpresa, nombre, descripcion, habilidadesEsperadas, direccion) VALUES (?, ?, ?, ?, ?)",
            (datos.get('rutEmpresa'), datos.get('nombre'), datos.get('descripcion'), 
             datos.get('habilidadesEsperadas'), datos.get('direccion'))
        )
        conn.commit()
        conn.close()
        
        calendario_observable.notificar_observadores('centro_creado', datos)
        
        return jsonify({'success': True, 'message': 'Centro creado'}), 201
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

@app.route('/centros/<int:id>', methods=['PUT'])
def update_centro(id):
    try:
        datos = request.get_json()
        conn = get_db_connection()
        if not conn:
            return jsonify({'success': False}), 500
        
        cursor = conn.cursor()
        cursor.execute(
            "UPDATE CentroPractica SET rutEmpresa=?, nombre=?, descripcion=?, habilidadesEsperadas=?, direccion=? WHERE idCentroPractica=?",
            (datos.get('rutEmpresa'), datos.get('nombre'), datos.get('descripcion'),
             datos.get('habilidadesEsperadas'), datos.get('direccion'), id)
        )
        conn.commit()
        conn.close()
        
        calendario_observable.notificar_observadores('centro_actualizado', {'id': id})
        
        return jsonify({'success': True, 'message': 'Centro actualizado'})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

@app.route('/centros/<int:id>', methods=['DELETE'])
def delete_centro(id):
    try:
        conn = get_db_connection()
        if not conn:
            return jsonify({'success': False}), 500
        
        cursor = conn.cursor()
        cursor.execute("DELETE FROM CentroPractica WHERE idCentroPractica = ?", (id,))
        conn.commit()
        conn.close()
        
        calendario_observable.notificar_observadores('centro_eliminado', {'id': id})
        
        return jsonify({'success': True, 'message': 'Centro eliminado'})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# ==========================================
# CRUD: PRÁCTICAS
# ==========================================

@app.route("/practicas", methods=['GET'])
def get_practicas():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Practica")
        columns = [column[0] for column in cursor.description]
        results = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(results)
    except Exception as e:
        print(f"Error: {e}")
        return jsonify([])

@app.route('/practicas', methods=['POST'])
def crear_practica():
    datos = request.json
    conn = get_db_connection()
    if not conn:
        return jsonify({'success': False, 'message': 'Error conexión BD'}), 500

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
                        datos.get('actividades'), 
                        datos.get('evidenciaImg', '')))
        conn.commit()
        
        calendario_observable.notificar_observadores('practica_creada', datos)
        
        return jsonify({'success': True, 'message': 'Práctica guardada'}), 201
    except Exception as e:
        print(f"Error SQL: {e}")
        return jsonify({'success': False, 'message': str(e)}), 500
    finally:
        if conn:
            conn.close()

@app.route('/practicas/<int:id>', methods=['DELETE'])
def delete_practica(id):
    try:
        conn = get_db_connection()
        if not conn:
            return jsonify({'success': False}), 500
        
        cursor = conn.cursor()
        cursor.execute("DELETE FROM Practica WHERE idPractica = ?", (id,))
        conn.commit()
        conn.close()
        
        calendario_observable.notificar_observadores('practica_eliminada', {'id': id})
        
        return jsonify({'success': True, 'message': 'Práctica eliminada'})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# ==========================================
# CRUD: SESIONES
# ==========================================

@app.route('/sesiones', methods=['GET'])
def get_sesiones():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Sesion")
        columns = [c[0] for c in cursor.description]
        results = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(results)
    except Exception as e:
        print(f"Error: {e}")
        return jsonify([])

@app.route('/sesiones', methods=['POST'])
def create_sesion():
    try:
        datos = request.get_json()
        conn = get_db_connection()
        if not conn:
            return jsonify({'success': False}), 500
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO Sesion (idPractica, fecha, horaInicio, horaTermino, horas, actividad, estado) VALUES (?, ?, ?, ?, ?, ?, ?)",
            (datos.get('idPractica'), datos.get('fecha'), datos.get('horaInicio'), datos.get('horaTermino'), 
             datos.get('horas'), datos.get('actividad', ''), datos.get('estado', 'Programada'))
        )
        conn.commit()
        conn.close()
        
        calendario_observable.notificar_observadores('sesion_creada', datos)
        
        return jsonify({'success': True, 'message': 'Sesión creada'}), 201
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

@app.route('/sesiones/<int:id>', methods=['DELETE'])
def delete_sesion(id):
    try:
        conn = get_db_connection()
        if not conn:
            return jsonify({'success': False}), 500
        
        cursor = conn.cursor()
        cursor.execute("DELETE FROM Sesion WHERE idSesion = ?", (id,))
        conn.commit()
        conn.close()
        
        calendario_observable.notificar_observadores('sesion_eliminada', {'id': id})
        
        return jsonify({'success': True, 'message': 'Sesión eliminada'})
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# ==========================================
# OTRAS RUTAS
# ==========================================

@app.route('/bitacora', methods=['GET'])
def get_bitacora():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Bitacora")
        columns = [column[0] for column in cursor.description]
        results = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(results)
    except Exception as e:
        print(f"Error: {e}")
        return jsonify([])

@app.route('/estudiantes', methods=['GET'])
def get_estudiantes():
    conn = get_db_connection()
    if not conn:
        return jsonify([]), 500
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM Estudiante")
        columns = [c[0] for c in cursor.description]
        results = [dict(zip(columns, row)) for row in cursor.fetchall()]
        conn.close()
        return jsonify(results)
    except Exception as e:
        print(f"Error: {e}")
        return jsonify([])

# ==========================================
# INICIO DEL SERVIDOR
# ==========================================

def abrir_navegador():
    url = "http://localhost/sistema-de-practica/frontend/login.php"
    webbrowser.open_new(url)

if __name__ == "__main__":
    print("🚀 Iniciando Backend con Patrones de Diseño...")
    Timer(1.5, abrir_navegador).start()
    app.run(debug=True, port=5000)