/**
 * Sistema de regeneración de URLs del Secret Santa
 * Permite regenerar todas las URLs y reiniciar el juego
 */

/**
 * Regenera todas las URLs del juego
 */
async function regenerateUrls() {
    if (!confirmRegeneration()) {
        return;
    }

    const regenerateBtn = document.getElementById('regenerateBtn');
    const validationMessage = document.getElementById('validationMessage');

    regenerateBtn.disabled = true;
    regenerateBtn.textContent = '⏳ Regenerando URLs...';
    validationMessage.style.display = 'none';

    try {
        const urlsGenerated = await generateNewUrls();
        
        if (urlsGenerated) {
            await resetGameState();
            showRegenerationSuccess(validationMessage, urlsGenerated);
            
            // Ocultar botón de regenerar
            regenerateBtn.style.display = 'none';
            
            // Recargar la página para actualizar las URLs
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    } catch (error) {
        console.error('Error:', error);
        showRegenerationError(validationMessage);
    } finally {
        regenerateBtn.disabled = false;
        regenerateBtn.textContent = '🔄 Regenerar URL';
    }
}

/**
 * Confirma la regeneración con el usuario
 * @returns {boolean} - True si el usuario confirma
 */
function confirmRegeneration() {
    return confirm(
        '⚠️ ¿Estás seguro de que deseas regenerar las URLs?\n\n' +
        'Esto:\n' +
        '- Eliminará todas las URLs existentes\n' +
        '- Generará nuevas URLs para todos los jugadores\n' +
        '- Los IDs serán procesados en orden aleatorio\n' +
        '- El estado del juego se pondrá en 0\n\n' +
        '¿Deseas continuar?'
    );
}

/**
 * Genera nuevas URLs para todos los jugadores
 * @returns {Promise<Object|null>} - Datos de las URLs generadas o null si falla
 */
async function generateNewUrls() {
    const response = await fetch('/api/players/sync-urls-and-assign-names', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    });

    const data = await response.json();

    if (data.success) {
        return data.data;
    } else {
        showRegenerationApiError(document.getElementById('validationMessage'), data.message);
        return null;
    }
}

/**
 * Reinicia el estado del juego a 0
 */
async function resetGameState() {
    try {
        const response = await fetch('/api/game-config/', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                startgame: 0
            })
        });

        const data = await response.json();
        
        if (data.success) {
            console.log('Estado del juego actualizado a 0');
        }
    } catch (error) {
        console.error('Error al actualizar estado del juego:', error);
    }
}

/**
 * Muestra mensaje de regeneración exitosa
 * @param {HTMLElement} messageElement - Elemento donde mostrar el mensaje
 * @param {Object} data - Datos de la regeneración
 */
function showRegenerationSuccess(messageElement, data) {
    messageElement.innerHTML = `
        <div style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
                    border: 2px solid #28a745;
                    border-radius: 8px;
                    padding: 1rem;
                    color: #155724;
                    font-weight: 500;">
            ✅ <strong>URLs regeneradas exitosamente</strong><br>
            <small>Se generaron ${data.total_urls} URL(s) para ${data.total_players} jugador(es). 
            Los IDs fueron procesados en orden aleatorio. El estado del juego se ha puesto en 0.</small>
        </div>
    `;
    messageElement.style.display = 'block';
}

/**
 * Muestra mensaje de error de API
 * @param {HTMLElement} messageElement - Elemento donde mostrar el mensaje
 * @param {string} message - Mensaje de error
 */
function showRegenerationApiError(messageElement, message) {
    messageElement.innerHTML = `
        <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
                    border: 2px solid #dc3545;
                    border-radius: 8px;
                    padding: 1rem;
                    color: #721c24;
                    font-weight: 500;">
            ❌ <strong>Error</strong><br>
            <small>${message || 'No se pudieron regenerar las URLs.'}</small>
        </div>
    `;
    messageElement.style.display = 'block';
}

/**
 * Muestra mensaje de error de conexión
 * @param {HTMLElement} messageElement - Elemento donde mostrar el mensaje
 */
function showRegenerationError(messageElement) {
    messageElement.innerHTML = `
        <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
                    border: 2px solid #dc3545;
                    border-radius: 8px;
                    padding: 1rem;
                    color: #721c24;
                    font-weight: 500;">
            ❌ <strong>Error de conexión</strong><br>
            <small>No se pudo conectar con el servidor.</small>
        </div>
    `;
    messageElement.style.display = 'block';
}

// Exportar funciones globalmente
window.regenerateUrls = regenerateUrls;
