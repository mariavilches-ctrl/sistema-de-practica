# sistema-de-practica

Sistema para gestionar prácticas profesionales: organiza tipos, centros, sesiones, estudiantes y seguimiento de horas. Incluye calendarización automática, registro de bitácoras, CRUD de entidades y un backend en Python conectado a SQL Server.

Para ejecutar este proyecto localmente, necesitas tener instalado el siguiente software:

### Backend (Python)
* **Python 3.10+**: Lenguaje base del servidor.
* **Librerías de Python**:
    * `Flask`: Framework web para la API.
    * `Flask-CORS`: Para permitir la conexión entre PHP y Python.
    * `pyodbc`: Driver para conectar Python con SQL Server.
    * `python-dotenv`: Para leer las credenciales seguras desde el archivo `.env`.
    
    ### Base de Datos
* **Microsoft SQL Server**: Motor de base de datos.
* **ODBC Driver 17 for SQL Server**: Controlador necesario para que Windows permita la conexión (generalmente viene instalado con SQL Server Management Studio).

### Frontend
* **XAMPP (o similar)**: Servidor Apache con soporte para PHP 7.4 o superior.
* **Navegador Web Moderno**: Chrome, Edge o Firefox.
Para instalar dependencias busque el requirements.txt
cd Backend
pip install -r requirements.txt

2. Configura las variables de entorno del backend creando un archivo `.env` dentro de la carpeta `Backend`
    Si no tienes el .env debes crearlo de la siguiente manera:

```
DB_SERVER=<tu_servidor>
DB_NAME=<tu_bd>
DB_USER=<tu_usuario>
DB_PASSWORD=<tu_password>
```

3. Levanta el backend desde la carpeta `Backend` (instala dependencias si hace falta).
   En PowerShell:

```powershell
pip install -r requirements.txt
python main.py
```

Esto abrirá automáticamente la pantalla de login del sistema en el navegador.

4. Levanta el frontend desde la carpeta `frontend`:

```powershell
php -S localhost:8000
```

Luego prueba el login con las credenciales del usuario de prueba.


## Endpoints disponibles del backend (general)

* `/login`
* `/practicas` (GET, POST, DELETE)
* `/centros` (GET, POST, DELETE)
* `/estudiantes` (GET, POST, DELETE)
* `/carreras` (GET)
* `/bitacora` (GET, POST)
* `/generar-calendario`
* `/tipos-practica`
* `/registro-seguimiento`


## Patrones utilizados (implementados en `patterns.py`)

* Factory (tipos de práctica)
* Strategy (algoritmos de calendarización)
* Observer (registro y notificación de cambios)
* Composite (estructura de actividades)

