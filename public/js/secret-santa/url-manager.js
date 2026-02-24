/**
 * Gestor de URLs del juego Secret Santa
 * Maneja la visualización y copia de URLs
 */

/**
 * Alterna la visibilidad de una URL
 * @param {number} urlId - ID de la URL
 */
function toggleUrl(urlId) {
    const urlPaper = event.currentTarget;
    
    // Verificar si está bloqueado
    if (urlPaper.classList.contains('blocked')) {
        return;
    }
    
    urlPaper.classList.toggle('expanded');
}

/**
 * Copia una URL al portapapeles
 * @param {string} text - Texto a copiar
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.target;
        const originalText = btn.textContent;
        
        btn.textContent = '✅ ¡Copiado!';
        btn.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
        
        setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = '';
        }, 2000);
    }).catch(err => {
        console.error('Error al copiar:', err);
        alert('Error al copiar la URL');
    });
}

// Exportar funciones globalmente
window.toggleUrl = toggleUrl;
window.copyToClipboard = copyToClipboard;
