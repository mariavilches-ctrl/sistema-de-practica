// app.js - Sistema CRUD + Patrones de Diseño
// Ubicación: frontend/assets/JS/app.js

const API_BASE_URL = 'http://localhost:5000';

// ==========================================
// FUNCIONES GENERALES
// ==========================================

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        modal.classList.add('show');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
    }
}

document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});

async function makeApiRequest(endpoint, method = 'GET', data = null) {
    try {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };

        if (data) {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(API_BASE_URL + endpoint, options);
        
        if (!response.ok && response.status !== 201) {
            throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error API:', error);
        throw error;
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.style.padding = '15px';
    notification.style.borderRadius = '5px';
    
    document.body.appendChild(notification);
    
    setTimeout(() => notification.remove(), 3000);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ==========================================
// FACTORY PATTERN: TIPOS DE PRÁCTICA
// ==========================================

async function cargarTiposPractica() {
    try {
        const tipos = await makeApiRequest('/tipos-practica');
        renderizarTipos(tipos.data || []);
    } catch (error) {
        showNotification('Error al cargar tipos: ' + error.message, 'error');
    }
}

function renderizarTipos(tipos) {
    const tbody = document.querySelector('#tablaTipos tbody');
    if (!tbody) return;

    tbody.innerHTML = '';
    
    if (!tipos || tipos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">No hay tipos disponibles</td></tr>';
        return;
    }

    tipos.forEach(tipo => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${escapeHtml(tipo.tipo)}</td>
            <td>${tipo.horas}</td>
            <td>${tipo.duracion_meses} meses</td>
            <td>${escapeHtml(tipo.descripcion)}</td>
        `;
        tbody.appendChild(tr);
    });
}

// ==========================================
// STRATEGY PATTERN: CALENDARIZACIÓN
// ==========================================

async function generarCalendario() {
    const estrategia = document.getElementById('estrategiaCalendario')?.value || 'uniforme';
    const horas = document.getElementById('horasCalendario')?.value || 80;
    const fecha = document.getElementById('fechaInicioCalendario')?.value;

    if (!fecha) {
        showNotification('Por favor selecciona una fecha', 'error');
        return;
    }

    try {
        const resultado = await makeApiRequest('/generar-calendario', 'POST', {
            horas_totales: horas,
            fecha_inicio: fecha + 'T00:00:00',
            sesiones_por_semana: 2,
            estrategia: estrategia
        });

        showNotification('Calendario generado: ' + resultado.data.length + ' sesiones', 'success');
        renderizarCalendarioGenerado(resultado.data);
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

function renderizarCalendarioGenerado(sesiones) {
    const tbody = document.querySelector('#tablaCalendarioGenerado tbody');
    if (!tbody) return;

    tbody.innerHTML = '';
    
    sesiones.slice(0, 10).forEach((sesion, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${idx + 1}</td>
            <td>${sesion.fecha}</td>
            <td>${sesion.horas} hrs</td>
            <td><span class="badge badge-warning">${sesion.estado}</span></td>
        `;
        tbody.appendChild(tr);
    });
}

// ==========================================
// COMPOSITE PATTERN: ESTRUCTURA DE PRÁCTICAS
// ==========================================

async function cargarEstructuraPractica(idPractica) {
    try {
        const resultado = await makeApiRequest(`/practica-estructura/${idPractica}`);
        mostrarEstructuraComposite(resultado.data);
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

function mostrarEstructuraComposite(estructura) {
    const div = document.getElementById('contenedorEstructura');
    if (!div) return;

    let html = `
        <div class="estructura-practica">
            <h3>Práctica: ${escapeHtml(estructura.tipo_practica)}</h3>
            <p>Horas Totales: <strong>${estructura.horas_totales} hrs</strong></p>
            <p>Número de Sesiones: <strong>${estructura.num_sesiones}</strong></p>
            <div class="sesiones-container">
    `;

    if (estructura.sesiones && estructura.sesiones.length > 0) {
        estructura.sesiones.forEach(sesion => {
            html += `
                <div class="sesion-item" style="border-left: 4px solid #2563eb; padding-left: 15px; margin: 10px 0;">
                    <h4>Sesión #${sesion.num_sesion} - ${sesion.fecha}</h4>
                    <p>Horas: <strong>${sesion.horas_totales}</strong></p>
            `;

            if (sesion.actividades && sesion.actividades.length > 0) {
                html += '<div style="margin-left: 20px;"><strong>Actividades:</strong><ul>';
                sesion.actividades.forEach(act => {
                    html += `<li>${escapeHtml(act.nombre)} (${act.horas} hrs)</li>`;
                });
                html += '</ul></div>';
            }
            html += '</div>';
        });
    }

    html += '</div></div>';
    div.innerHTML = html;
}

// ==========================================
// CRUD: CENTROS DE PRÁCTICA
// ==========================================

async function cargarCentros() {
    try {
        const centros = await makeApiRequest('/centros');
        renderizarCentros(centros);
    } catch (error) {
        showNotification('Error al cargar centros: ' + error.message, 'error');
    }
}

function renderizarCentros(centros) {
    const tbody = document.querySelector('#tablaCentros tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (!centros || centros.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">No hay centros registrados</td></tr>';
        return;
    }

    centros.forEach(centro => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${escapeHtml(centro.idCentroPractica)}</td>
            <td>${escapeHtml(centro.rutEmpresa)}</td>
            <td>${escapeHtml(centro.nombre)}</td>
            <td>${escapeHtml(centro.descripcion || '-')}</td>
            <td>${escapeHtml(centro.direccion)}</td>
            <td>
                <button class="btn-edit" onclick="editarCentro(${centro.idCentroPractica})">Editar</button>
                <button class="btn-delete" onclick="eliminarCentro(${centro.idCentroPractica})">Eliminar</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function abrirFormularioCentro() {
    document.getElementById('formCentro').reset();
    document.getElementById('idCentro').value = '';
    document.querySelector('#modalCentro h2').textContent = 'Agregar Centro de Práctica';
    openModal('modalCentro');
}

async function guardarCentro() {
    const id = document.getElementById('idCentro').value;
    const datos = {
        rutEmpresa: document.getElementById('rutEmpresa').value,
        nombre: document.getElementById('nombreCentro').value,
        descripcion: document.getElementById('descripcionCentro').value,
        habilidadesEsperadas: document.getElementById('habilidadesCentro').value,
        direccion: document.getElementById('direccionCentro').value
    };

    if (!datos.rutEmpresa || !datos.nombre || !datos.direccion) {
        showNotification('Por favor completa los campos obligatorios', 'error');
        return;
    }

    try {
        const resultado = await makeApiRequest('/centros', 'POST', datos);
        showNotification(resultado.message || 'Centro guardado exitosamente', 'success');
        closeModal('modalCentro');
        cargarCentros();
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

async function editarCentro(id) {
    try {
        const centro = await makeApiRequest(`/centros/${id}`);
        
        document.getElementById('idCentro').value = centro.idCentroPractica;
        document.getElementById('rutEmpresa').value = centro.rutEmpresa;
        document.getElementById('nombreCentro').value = centro.nombre;
        document.getElementById('descripcionCentro').value = centro.descripcion || '';
        document.getElementById('habilidadesCentro').value = centro.habilidadesEsperadas || '';
        document.getElementById('direccionCentro').value = centro.direccion;
        
        document.querySelector('#modalCentro h2').textContent = 'Editar Centro de Práctica';
        openModal('modalCentro');
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

async function eliminarCentro(id) {
    if (!confirm('¿Estás seguro de que quieres eliminar este centro?')) {
        return;
    }

    try {
        const resultado = await makeApiRequest(`/centros/${id}`, 'DELETE');
        showNotification(resultado.message || 'Centro eliminado', 'success');
        cargarCentros();
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// ==========================================
// CRUD: PRÁCTICAS
// ==========================================

async function cargarPracticas() {
    try {
        const practicas = await makeApiRequest('/practicas');
        renderizarPracticas(practicas);
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

function renderizarPracticas(practicas) {
    const tbody = document.querySelector('#tablaPracticas tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (!practicas || practicas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No hay prácticas</td></tr>';
        return;
    }

    practicas.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${p.idPractica}</td>
            <td>${p.idEstudiante}</td>
            <td>${escapeHtml(p.tipo)}</td>
            <td>${p.idCentroPractica}</td>
            <td>${p.idTutor}</td>
            <td>${p.fechaDeInicio || '-'}</td>
            <td>
                <button class="btn-delete" onclick="eliminarPractica(${p.idPractica})">Eliminar</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

async function eliminarPractica(id) {
    if (!confirm('¿Estás seguro?')) return;
    
    try {
        await makeApiRequest(`/practicas/${id}`, 'DELETE');
        showNotification('Práctica eliminada', 'success');
        cargarPracticas();
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// ==========================================
// CRUD: SESIONES
// ==========================================

async function cargarSesiones() {
    try {
        const sesiones = await makeApiRequest('/sesiones');
        renderizarSesiones(sesiones);
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

function renderizarSesiones(sesiones) {
    const tbody = document.querySelector('#tablaSesiones tbody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (!sesiones || sesiones.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No hay sesiones</td></tr>';
        return;
    }

    sesiones.forEach(s => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${s.fecha}</td>
            <td>${s.horaInicio} - ${s.horaTermino}</td>
            <td>${s.idPractica}</td>
            <td>${escapeHtml(s.actividad || '-')}</td>
            <td>${s.horas}</td>
            <td><span class="badge">${s.estado}</span></td>
            <td>
                <button class="btn-delete" onclick="eliminarSesion(${s.idSesion})">Eliminar</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

async function eliminarSesion(id) {
    if (!confirm('¿Estás seguro?')) return;
    
    try {
        await makeApiRequest(`/sesiones/${id}`, 'DELETE');
        showNotification('Sesión eliminada', 'success');
        cargarSesiones();
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// ==========================================
// OBSERVER PATTERN: REGISTRO DE SEGUIMIENTO
// ==========================================

async function cargarRegistroSeguimiento() {
    try {
        const resultado = await makeApiRequest('/registro-seguimiento');
        renderizarRegistro(resultado.data || []);
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

function renderizarRegistro(registro) {
    const tbody = document.querySelector('#tablaRegistro tbody');
    if (!tbody) return;

    tbody.innerHTML = '';
    
    registro.slice(0, 20).forEach(evento => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${evento.timestamp}</td>
            <td><strong>${escapeHtml(evento.evento)}</strong></td>
            <td>${JSON.stringify(evento.datos).substring(0, 50)}...</td>
        `;
        tbody.appendChild(tr);
    });
}