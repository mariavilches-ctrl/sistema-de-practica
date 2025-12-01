# sistema-de-practica

Sistema para gestionar prácticas profesionales: organiza tipos, centros, sesiones, estudiantes y seguimiento de horas. Incluye calendarización automática, registro de bitácoras, CRUD de entidades y un backend en Python conectado a SQL Server.

Seed para desarrollo local: `Base De Datos/seed_local.sql` — crea tablas mínimas y un usuario de prueba ([test@example.com](mailto:test@example.com) / password123).

Instrucciones rápidas para pruebas locales:

1. Ejecuta el seed (ajusta tu conexión):

```powershell
sqlcmd -S <DB_SERVER> -d <DB_NAME> -U <DB_USER> -P '<DB_PASSWORD>' -i 'Base De Datos\\seed_local.sql'
```

2. Configura las variables de entorno del backend creando un archivo `.env` dentro de la carpeta `Backend`:

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

