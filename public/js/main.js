// public/js/main.js
document.addEventListener('DOMContentLoaded', () => {
    // Lógica para el toggle de detalles en eventos
    window.toggleDetails = function(button) {
        const details = button.nextElementSibling;
        const isOpen = details.style.display === 'block';
        details.style.display = isOpen ? 'none' : 'block';
        button.textContent = isOpen ? 'Ver detalles ▼' : 'Ocultar detalles ▲';
    };

    // Validación simple de formulario (ejemplo)
    const form = document.querySelector('form');
    if(form) {
        form.addEventListener('submit', (e) => {
            // e.preventDefault(); // Descomentar si usas AJAX
            console.log("Formulario enviado");
        });
    }
});