/**
 * Sistema de validación de asignaciones del Secret Santa
 * Valida que ningún jugador se tenga a sí mismo como amigo secreto
 */

/**
 * Valida todas las asignaciones de jugadores
 */
async function validateAssignments() {
    const validateBtn = document.getElementById('validateBtn');
    const validationMessage = document.getElementById('validationMessage');
    const regenerateBtn = document.getElementById('regenerateBtn');

    // Deshabilitar botón mientras se valida
    validateBtn.disabled = true;
    validateBtn.textContent = '⏳ Validando...';
    validationMessage.style.display = 'none';

    // Ocultar botón de regenerar mientras se valida
    if (regenerateBtn) {
        regenerateBtn.style.display = 'none';
    }

    // Remover todas las marcas de invalidación previas
    document.querySelectorAll('.christmas-card').forEach(card => {
        card.classList.remove('invalid');
    });

    try {
        const response = await fetch('/api/players/validate-assignments', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const data = await response.json();

        if (data.success) {
            if (data.data.is_valid) {
                showValidationSuccess(validationMessage);
                regenerateBtn.style.display = 'none';
            } else {
                showValidationError(validationMessage, data.data);
                regenerateBtn.style.display = 'inline-block';
                markInvalidCards(data.data.invalid_urls);
            }
        } else {
            showValidationApiError(validationMessage, data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showValidationConnectionError(validationMessage);
    } finally {
        // Restaurar botón
        validateBtn.disabled = false;
        validateBtn.textContent = '✅ Validar Asignaciones';
    }
}

/**
 * Muestra mensaje de validación exitosa
 * @param {HTMLElement} messageElement - Elemento donde mostrar el mensaje
 */
function showValidationSuccess(messageElement) {
    messageElement.innerHTML = `
        <div style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
                    border: 2px solid #28a745;
                    border-radius: 8px;
                    padding: 1rem;
                    color: #155724;
                    font-weight: 500;">
            ✅ <strong>Todas las asignaciones son válidas</strong><br>
            <small>Ningún jugador tiene a sí mismo como amigo secreto.</small>
        </div>
    `;
    messageElement.style.display = 'block';
}

/**
 * Muestra mensaje de error de validación
 * @param {HTMLElement} messageElement - Elemento donde mostrar el mensaje
 * @param {Object} data - Datos de la validación
 */
function showValidationError(messageElement, data) {
    const invalidCount = data.invalid_count;
    messageElement.innerHTML = `
        <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
                    border: 2px solid #dc3545;
                    border-radius: 8px;
                    padding: 1rem;
                    color: #721c24;
                    font-weight: 500;">
            ❌ <strong>Se encontraron ${invalidCount} asignación(es) inválida(s)</strong><br>
            <small>Las tarjetas marcadas en rojo tienen jugadores que se asignaron a sí mismos.</small>
        </div>
    `;
    messageElement.style.display = 'block';
}

/**
 * Muestra mensaje de error de API
 * @param {HTMLElement} messageElement - Elemento donde mostrar el mensaje
 * @param {string} message - Mensaje de error
 */
function showValidationApiError(messageElement, message) {
    messageElement.innerHTML = `
        <div style="background: #f8d7da;
                    border: 2px solid #dc3545;
                    border-radius: 8px;
                    padding: 1rem;
                    color: #721c24;">
            ❌ Error: ${message || 'No se pudo validar las asignaciones'}
        </div>
    `;
    messageElement.style.display = 'block';
}

/**
 * Muestra mensaje de error de conexión
 * @param {HTMLElement} messageElement - Elemento donde mostrar el mensaje
 */
function showValidationConnectionError(messageElement) {
    messageElement.innerHTML = `
        <div style="background: #f8d7da;
                    border: 2px solid #dc3545;
                    border-radius: 8px;
                    padding: 1rem;
                    color: #721c24;">
            ❌ Error al validar las asignaciones. Por favor, intenta de nuevo.
        </div>
    `;
    messageElement.style.display = 'block';
}

/**
 * Marca las tarjetas inválidas visualmente
 * @param {Array} invalidUrls - URLs con asignaciones inválidas
 */
function markInvalidCards(invalidUrls) {
    invalidUrls.forEach((invalidUrl, index) => {
        const card = document.getElementById(`card-${invalidUrl.id}`);
        if (card) {
            card.classList.add('invalid');
            
            // Scroll suave a la primera tarjeta inválida
            if (index === 0) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
}

// Exportar funciones globalmente
window.validateAssignments = validateAssignments;
