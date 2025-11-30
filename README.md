# sistema-de-practica
Sistema para gestionar prácticas profesionales: organiza tipos, centros, sesiones, estudiantes y seguimiento de horas.

Seed para desarrollo local: `Base De Datos/seed_local.sql` — crea tablas mínimas y un usuario de prueba (test@example.com / password123).

Instrucciones rápidas para pruebas locales:

1) Ejecuta el seed (ajusta tu conexión):

```powershell
sqlcmd -S <DB_SERVER> -d <DB_NAME> -U <DB_USER> -P '<DB_PASSWORD>' -i 'Base De Datos\\seed_local.sql'
```

2) Levanta el backend desde la carpeta `Backend` (instala dependencias si hace falta).

3) Levanta el frontend desde la carpeta `frontend` (php -S localhost:8000) y prueba el login.
