/**
 * Gestor de jugadores del Secret Santa
 * Maneja la asignación de jugadores a URLs
 */

/**
 * Actualiza el jugador asignado a una URL
 * @param {number} urlId - ID de la URL
 * @param {number|string} playerId - ID del jugador
 */
async function updateUrlPlayer(urlId, playerId) {
    try {
        const response = await fetch(`/api/players/urls/${urlId}/player`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                player_id: playerId || null
            })
        });

        const data = await response.json();

        if (data.success) {
            // Actualizar todos los selects con la información de todas las URLs
            updateAllSelects(data.data.urls);
        } else {
            alert('Error: ' + (data.message || 'No se pudo actualizar el jugador'));
            location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al actualizar el jugador. Por favor, intenta de nuevo.');
        location.reload();
    }
}

/**
 * Actualiza todos los selectores de jugadores
 * @param {Array} urlsData - Datos de las URLs
 */
function updateAllSelects(urlsData) {
    urlsData.forEach(urlData => {
        const select = document.getElementById(`playerSelect_${urlData.id}`);
        if (select) {
            const currentValue = urlData.friends ? urlData.friends.toString() : '';
            updateSelectOptions(select, urlData.available_players, currentValue);
        }
    });
}

/**
 * Actualiza las opciones de un selector
 * @param {HTMLSelectElement} select - Elemento select
 * @param {Array} availablePlayers - Jugadores disponibles
 * @param {string} currentValue - Valor actual seleccionado
 */
function updateSelectOptions(select, availablePlayers, currentValue) {
    const selectedValue = currentValue || select.value;

    // Eliminar todas las opciones excepto la primera
    while (select.options.length > 1) {
        select.remove(1);
    }

    // Agregar nuevas opciones
    availablePlayers.forEach(player => {
        const option = document.createElement('option');
        option.value = player.id;
        option.textContent = player.nombre;
        
        if (player.id.toString() === selectedValue) {
            option.selected = true;
        }
        
        select.appendChild(option);
    });
}

/**
 * Verifica el estado de bloqueo de inputs desde localStorage
 */
function checkInputsLockStatus() {
    const inputsLocked = localStorage.getItem('inputsLocked');
    
    if (inputsLocked === 'true') {
        // Deshabilitar todos los selects
        document.querySelectorAll('.christmas-select').forEach(select => {
            select.disabled = true;
            select.title = 'Los inputs están bloqueados desde la página de configuración';
        });
    }
}

// Exportar funciones globalmente
window.updateUrlPlayer = updateUrlPlayer;
window.updateAllSelects = updateAllSelects;
window.updateSelectOptions = updateSelectOptions;
window.checkInputsLockStatus = checkInputsLockStatus;
