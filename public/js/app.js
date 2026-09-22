/**
 * ARCHIVO PRINCIPAL DE JAVASCRIPT
 * ================================
 * Este archivo contiene la lógica del cliente (navegador)
 * que permite:
 * - Cargar dinámicamente los barberos disponibles
 * - Enviar formularios sin recargar la página
 * - Mostrar errores al usuario
 * - Recargar datos después de guardar
 */

// ========== INICIALIZACIÓN ==========
// Cuando la página HTML termina de cargar, ejecutar estas funciones
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM cargado, iniciando carga de barberos...');
    // Cargar la lista de barberos desde el servidor
    cargarBarberos();
    // Cargar la lista de turnos desde el servidor
    cargarTurnos();
});

// ========== FUNCIÓN: CARGAR BARBEROS ==========
/**
 * Cargar barberos dinámicamente en el select (dropdown)
 * 
 * Esta función:
 * 1. Hace una solicitud HTTP al servidor para obtener los barberos
 * 2. Llena el dropdown con los nombres de los barberos
 * 3. Muestra mensajes de error si algo falla
 */
async function cargarBarberos() {
    // Obtener el elemento <select> con ID 'barberoId'
    const selectBarberos = document.getElementById('barberoId');
    console.log('Iniciando carga de barberos...');

    try {
        // Hacer solicitud HTTP GET al servidor
        // Esto obtiene el listado de barberos desde la API
        console.log('Haciendo fetch a /Barber_Manager/barberos');
        const response = await fetch('/Barber_Manager/barberos');

        // Verificar que la solicitud fue exitosa
        console.log('Respuesta recibida:', response.status);

        if (!response.ok) {
            throw new Error(`Error ${response.status}: No se pudo obtener los barberos`);
        }

        // Convertir la respuesta de texto a JSON
        const text = await response.text();
        console.log('Texto crudo recibido:', text);

        const barberos = JSON.parse(text);
        console.log('Barberos parseados:', barberos);

        // Limpiar el dropdown: eliminar todas las opciones anteriores
        selectBarberos.innerHTML = '<option value="">Seleccione un barbero...</option>';

        // Si hay barberos disponibles, agregarlos al dropdown
        if (Array.isArray(barberos) && barberos.length > 0) {
            // Para cada barbero en la lista
            barberos.forEach(barbero => {
                // Crear una nueva opción HTML
                const option = document.createElement('option');
                // El valor será el ID del barbero
                option.value = barbero.id;
                // El texto mostrado: nombre y especialidad
                option.textContent = `${barbero.nombre} (${barbero.especialidad})`;
                // Agregar la opción al dropdown
                selectBarberos.appendChild(option);
            });
            console.log('Barberos cargados exitosamente');
        } else {
            // Si no hay barberos, mostrar mensaje
            selectBarberos.innerHTML = '<option value="">No hay barberos disponibles</option>';
            console.log('No hay barberos activos');
        }
    } catch (error) {
        // Si ocurre error, mostrar en la consola y en el dropdown
        console.error('Error al cargar barberos:', error);
        selectBarberos.innerHTML = '<option value="">Error al cargar barberos</option>';
    }
}

// ========== FUNCIÓN: CARGAR TURNOS ==========
/**
 * Cargar turnos y mostrarlos en la tabla
 * 
 * Esta función:
 * 1. Obtiene todos los turnos del servidor
 * 2. Los muestra en una tabla HTML
 */
async function cargarTurnos() {
    // Obtener el <tbody> (cuerpo de la tabla)
    const cuerpoTabla = document.getElementById('cuerpo-tabla');

    try {
        // Hacer solicitud HTTP GET al servidor
        // Obtiene listado de todos los turnos
        const response = await fetch('/Barber_Manager/turnos');
        // Convertir respuesta a JSON
        const turnos = await response.json();

        // Limpiar la tabla (vaciar todas las filas)
        cuerpoTabla.innerHTML = '';

        // Para cada turno, crear una fila en la tabla
        turnos.forEach(turno => {
            // Crear una fila <tr>
            const fila = document.createElement('tr');
            // Llenar la fila con datos del turno
            fila.innerHTML = `
                <td>${turno.clienteNombre}</td>
                <td>${turno.clienteTelefono}</td>
                <td>${turno.nombreBarbero}</td>
                <td>${turno.fecha}</td>
                <td>${turno.hora}</td>
                <td>${turno.servicio || '-'}</td>
            `;
            // Agregar la fila a la tabla
            cuerpoTabla.appendChild(fila);
        });

    } catch (error) {
        console.error('Error al cargar turnos:', error);
    }
}

// ========== MANEJO DEL FORMULARIO ==========

// Obtener el formulario del HTML
const form = document.querySelector('form');

// Cuando el usuario envía el formulario, ejecutar esta función
form.addEventListener('submit', async (e) => {
    // Prevenir el comportamiento por defecto (recargar página)
    e.preventDefault();

    // Obtener elementos para mostrar errores
    const panelErrores = document.getElementById('panel-errores');
    const listaErrores = document.getElementById('lista-errores');

    // Ocultar errores previos (limpiar panel de errores)
    panelErrores.style.display = 'none';
    listaErrores.innerHTML = '';

    // Capturar los datos del formulario
    const formData = new FormData(form);

    try {
        // Enviar los datos al servidor mediante POST
        const response = await fetch('/Barber_Manager/turnos', {
            method: 'POST',
            body: formData  // Enviar como FormData para que PHP lo lea en $_POST
        });

        // Convertir respuesta a JSON
        const resultado = await response.json();

        // Si todo salió bien
        if (response.ok && resultado.ok) {
            // Mostrar mensaje de éxito
            alert('¡Turno reservado con éxito!');
            // Limpiar los campos del formulario
            form.reset();
            // Recargar la tabla con el nuevo turno
            cargarTurnos();
        } else {
            // Si hay errores, mostrar el panel de errores
            panelErrores.style.display = 'block';
            // Agregar cada error a la lista
            if (resultado.errors) {
                resultado.errors.forEach(err => {
                    const li = document.createElement('li');
                    li.textContent = err;
                    listaErrores.appendChild(li);
                });
            } else {
                // Mostrar error genérico si no hay lista de errores
                alert('Error: ' + (resultado.error || 'Ocurrió un problema'));
            }
        }
    } catch (error) {
        console.error('Error en la comunicación:', error);
        alert('No se pudo conectar con el servidor.');
    }
});
