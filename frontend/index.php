<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Sistema de Prácticas UNACH</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div class="app">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h2>Prácticas UNACH</h2>
            </div>
            <nav class="menu">
                <a href="#" class="menu-item active">Dashboard</a>
                <a href="#" class="menu-item active">Mis practicas</a>
                <a href="#" class="menu-item active">Calendario</a>
                <a href="#" class="menu-item active">bitacora</a> // colocar dentro las horas
                <a href="#" class="menu-item active"></a>

            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="main">
            <header class="topbar">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span>I</span>
                </div>
            </header>

            <section class="content">
                <!-- Tarjetas resumen -->
                <div class="cards">
                    <div class="card">
                        <h3>Estudiantes en práctica</h3>
                        <p class="card-number">algun numero random</p>
                    </div>
                    <div class="card">
                        <h3>Prácticas posibles</h3>
                        <p class="card-number">12</p>
                    </div>
                    <div class="card">
                        <h3>Centros de práctica</h3>
                        <p class="card-number">8</p>
                    </div>
                </div>

                <!-- Tabla de prácticas asignadas (ejemplo) -->
                <div class="section-header">
                    <h2>ya se nos ocurrira que poner aqui xdxdxdxdxd</h2>
                    <button class="btn-primary">blablablabla</button>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Tipo de Práctica</th>
                                <th>Centro</th>
                                <th>Supervisor Interno</th>
                                <th>Supervisor Externo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                             
                                <td>Diego Aquiles Castro</td>
                                <td>Práctica Profesional</td>
                                <td>Hospital Regional</td>
                                <td>Prof. Soto</td>
                                <td>Dr. Manuel Eduardo jara de La Hoz</td>
                                <td><span class="badge badge-success">en procesoooooooooooooo</span></td>
                            </tr>
                            <tr>
                                <td>Ana Díaz</td>
                                <td>Práctica I</td>
                                <td>Colegio Adventista</td>
                                <td>Prof. García</td>
                                <td>Sr. López</td>
                                <td><span class="badge badge-warning">Pendiente</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </section>
        </main>
    </div>
</body>
</html>
