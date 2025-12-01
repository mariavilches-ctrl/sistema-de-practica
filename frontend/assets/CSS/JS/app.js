

// app.js - Sistema CRUD Completo
// Ubicación: frontend/assets/JS/app.js

const API_BASE_URL = 'http://localhost:5000';

// ==========================================
// 1. FUNCIONES GENERALES (Helpers)
// ==========================================

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        setTimeout(() => modal.classList.add('show'), 10); // Animación suave
    } else {
        console.error('No se encontró el modal:', modalId);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300); // Esperar animación
    }
}

// Cerrar modal al hacer clic afuera
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('show');
        setTimeout(() => event.target.style.display = 'none', 300);
    }
};

async function makeApiRequest(endpoint, method = 'GET', data = null) {
    try {
        const options = {
            method: method,
            headers: { 'Content-Type': 'application/json' }
        };

        if (data) options.body = JSON.stringify(data);

        const response = await fetch(API_BASE_URL + endpoint, options);
        
        // Si la respuesta no es OK, lanzamos error
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP ${response.status}`);
        }

        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error API:', error);
        throw error;
    }
}

function showNotification(message, type = 'success') {
    // Eliminar notificaciones previas
    const existing = document.querySelector('.fixed-notification');
    if (existing) existing.remove();

    const notification = document.createElement('div');
    notification.className = `alert alert-${type} fixed-notification`;
    notification.textContent = message;
    
    // Estilos para que flote
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        zIndex: '10000',
        minWidth: '300px',
        boxShadow: '0 4px 6px rgba(0,0,0,0.1)'
    });
    
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// ==========================================
// 2. MÓDULO: CENTROS DE PRÁCTICA
// ==========================================

async function cargarCentros() {
    try {
        const centros = await makeApiRequest('/centros');
        const tbody = document.querySelector('#tablaCentros tbody');
        if (!tbody) return; // Si no estamos en la página de centros, salir

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
                    <button class="btn-delete" style="padding: 5px 10px;" onclick="eliminarCentro(${centro.idCentroPractica})">Eliminar</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error(error);
        const tbody = document.querySelector('#tablaCentros tbody');
        if(tbody) tbody.innerHTML = `<tr><td colspan="6" style="color:red; text-align:center">Error: ${error.message}</td></tr>`;
    }
}

function abrirFormularioCentro() {
    const form = document.getElementById('formCentro');
    if (form) form.reset();
    openModal('modalCentro');
}

async function guardarCentro() {
    const datos = {
        rutEmpresa: document.getElementById('rutEmpresa').value,
        nombre: document.getElementById('nombreCentro').value,
        descripcion: document.getElementById('descripcionCentro').value,
        habilidadesEsperadas: document.getElementById('habilidadesCentro').value,
        direccion: document.getElementById('direccionCentro').value
    };

    if (!datos.rutEmpresa || !datos.nombre || !datos.direccion) {
        showNotification('Complete los campos obligatorios', 'error');
        return;
    }

    try {
        const resultado = await makeApiRequest('/centros', 'POST', datos);
        showNotification(resultado.message || 'Centro guardado', 'success');
        closeModal('modalCentro');
        cargarCentros();
    } catch (error) {
        showNotification('Error al guardar: ' + error.message, 'error');
    }
}

async function eliminarCentro(id) {
    if (!confirm('¿Seguro que deseas eliminar este centro?')) return;
    try {
        await makeApiRequest(`/centros/${id}`, 'DELETE');
        showNotification('Centro eliminado', 'success');
        cargarCentros();
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// ==========================================
// 3. MÓDULO: PRÁCTICAS
// ==========================================

async function cargarPracticas() {
    try {
        const practicas = await makeApiRequest('/practicas');
        const tbody = document.querySelector('#tablaPracticas tbody');
        if (!tbody) return;

        tbody.innerHTML = '';
        if (!practicas || practicas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No hay prácticas registradas</td></tr>';
            return;
        }

        practicas.forEach(p => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>#${p.idPractica}</td>
                <td>${p.idEstudiante}</td>
                <td><span class="badge">${escapeHtml(p.tipo)}</span></td>
                <td>${p.idCentroPractica}</td>
                <td>${p.idTutor}</td>
                <td>${p.fechaDeInicio || '?'} / ${p.fechaDeTermino || '?'}</td>
                <td>
                    <button class="btn-delete" style="padding: 5px 10px;" onclick="eliminarPractica(${p.idPractica})">Eliminar</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error(error);
    }
}

async function abrirModalPractica() {
    openModal('modalPractica');
    
    // Llenar Estudiantes
    try {
        const estudiantes = await makeApiRequest('/estudiantes');
        const select = document.getElementById('selectEstudiante');
        if(select) {
            select.innerHTML = '<option value="">Seleccione...</option>';
            estudiantes.forEach(e => {
                select.innerHTML += `<option value="${e.idEstudiante}">${e.nombreCompleto || e.rut}</option>`;
            });
        }
    } catch (e) { console.error("Error cargando estudiantes", e); }

    // Llenar Centros
    try {
        const centros = await makeApiRequest('/centros');
        const select = document.getElementById('selectCentro');
        if(select) {
            select.innerHTML = '<option value="">Seleccione...</option>';
            centros.forEach(c => {
                select.innerHTML += `<option value="${c.idCentroPractica}">${c.nombre}</option>`;
            });
        }
    } catch (e) { console.error("Error cargando centros", e); }
}

async function guardarPractica() {
    const datos = {
        idEstudiante: document.getElementById('selectEstudiante').value,
        idCentroPractica: document.getElementById('selectCentro').value,
        tipo: document.getElementById('selectTipo').value,
        idTutor: document.getElementById('idTutor').value,
        idSupervisor: document.getElementById('idSupervisor').value,
        fechaInicio: document.getElementById('fechaInicio').value,
        fechaTermino: document.getElementById('fechaTermino').value,
        actividades: 'Asignada desde Web',
        evidenciaImg: ''
    };

    if(!datos.idEstudiante || !datos.idCentroPractica || !datos.fechaInicio) {
        showNotification('Faltan datos obligatorios', 'error');
        return;
    }

    try {
        const res = await makeApiRequest('/practicas', 'POST', datos);
        showNotification(res.message, 'success');
        closeModal('modalPractica');
        cargarPracticas();
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

async function eliminarPractica(id) {
    if(!confirm('¿Eliminar práctica?')) return;
    try {
        await makeApiRequest(`/practicas/${id}`, 'DELETE');
        showNotification('Práctica eliminada', 'success');
        cargarPracticas();
    } catch (e) {
        showNotification(e.message, 'error');
    }
}

// ==========================================
// 4. MÓDULO: CALENDARIZACIÓN Y TIPOS
// ==========================================

async function cargarTiposPractica() {
    try {
        const res = await makeApiRequest('/tipos-practica');
        const tbody = document.querySelector('#tablaTipos tbody');
        if(!tbody) return;

        tbody.innerHTML = '';
        const tipos = res.data || [];
        tipos.forEach(t => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${t.tipo}</td><td>${t.horas}</td><td>${t.duracion_meses} meses</td><td>${t.descripcion}</td>
            `;
            tbody.appendChild(tr);
        });
    } catch (e) { console.error(e); }
}

async function generarCalendario() {
    const estrategia = document.getElementById('estrategiaCalendario').value;
    const horas = document.getElementById('horasCalendario').value;
    const fecha = document.getElementById('fechaInicioCalendario').value;

    if(!fecha) { showNotification('Selecciona una fecha', 'error'); return; }

    try {
        const res = await makeApiRequest('/generar-calendario', 'POST', {
            horas_totales: horas,
            fecha_inicio: fecha,
            estrategia: estrategia
        });
        
        const tbody = document.querySelector('#tablaCalendarioGenerado tbody');
        tbody.innerHTML = '';
        res.data.slice(0, 15).forEach((s, i) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${i+1}</td><td>${s.fecha}</td><td>${s.horas} hrs</td><td>${s.estado}</td>`;
            tbody.appendChild(tr);
        });
        showNotification('Calendario generado', 'success');
    } catch (e) { showNotification(e.message, 'error'); }
}

// ==========================================
// 5. MÓDULO: SEGUIMIENTO (OBSERVER)
// ==========================================

async function cargarRegistroSeguimiento() {
    try {
        const res = await makeApiRequest('/registro-seguimiento');
        const tbody = document.querySelector('#tablaRegistro tbody');
        if(!tbody) return;
        
        tbody.innerHTML = '';
        const logs = res.data || [];
        
        if(logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" style="text-align:center">No hay eventos recientes</td></tr>';
            return;
        }

        logs.reverse().forEach(log => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${new Date(log.timestamp).toLocaleString()}</td>
                <td><strong>${log.evento}</strong></td>
                <td><small>${JSON.stringify(log.datos)}</small></td>
            `;
            tbody.appendChild(tr);
        });
    } catch (e) { console.error(e); }
}

// ==========================================
// MÓDULO: BITÁCORA
// ==========================================
async function guardarBitacora() {
    const datos = {
        idPractica: document.getElementById('idPracticaBitacora').value,
        idEstudiante: document.getElementById('idEstudianteBitacora').value,
        habilidadesDesarrolladas: document.getElementById('habilidadesBitacora').value,
        desafios: document.getElementById('desafiosBitacora').value,
        logros: document.getElementById('logrosBitacora').value
    };

    if (!datos.idPractica || !datos.idEstudiante || !datos.habilidadesDesarrolladas) {
        showNotification('Completa los campos obligatorios', 'error');
        return;
    }

    try {
        console.log('Enviando bitácora:', datos);
        const res = await makeApiRequest('/bitacora', 'POST', datos);
        showNotification(res.message || 'Bitácora guardada', 'success');
        closeModal('modalBitacora');
        // Recargar para ver el nuevo registro en la tabla
        window.location.reload();
    } catch (error) {
        console.error(error);
        showNotification('Error al guardar la bitácora: ' + error.message, 'error');
    }
}

// Abre el modal para NUEVA entrada (limpia el formulario)
function abrirFormularioBitacora() {
    // Limpiar campos
    document.getElementById('idBitacora').value = '';
    document.getElementById('idPracticaBitacora').value = '';
    document.getElementById('idEstudianteBitacora').value = '';
    document.getElementById('habilidadesBitacora').value = '';
    document.getElementById('desafiosBitacora').value = '';
    document.getElementById('logrosBitacora').value = '';

    // Abrir modal usando el helper que ya tienes
    openModal('modalBitacora');
}

// Abre el modal desde una FILA de la tabla (para ver/editar)
function abrirFormularioBitacoraDesdeFila(fila) {
    // Tomar datos desde los data-* de la fila
    const id           = fila.dataset.id || '';
    const idEstudiante = fila.dataset.idEstudiante || '';
    const idPractica   = fila.dataset.idPractica || '';
    const titulo       = fila.dataset.titulo || '';
    const descripcion  = fila.dataset.descripcion || '';

    // Rellenar campos básicos
    document.getElementById('idBitacora').value = id;
    document.getElementById('idPracticaBitacora').value = idPractica;
    document.getElementById('idEstudianteBitacora').value = idEstudiante;

    // Como en la tabla no tienes habilidades/desafíos/logros,
    // por ahora usamos lo que hay:
    document.getElementById('habilidadesBitacora').value = descripcion || '';
    document.getElementById('desafiosBitacora').value = '';
    document.getElementById('logrosBitacora').value = titulo || '';

    // Opcional: podrías guardar en historial aquí si quieres
    // guardarEnHistorial({ id, idEstudiante, idPractica, titulo });

    openModal('modalBitacora');
}