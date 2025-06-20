/**
 * Framework Tukuchi - JavaScript principal
 * Funcionalidades del lado del cliente
 */

// Namespace principal
window.Tukuchi = window.Tukuchi || {};

// Configuración global
Tukuchi.config = {
    baseUrl: window.location.origin + window.location.pathname.replace('/index.php', ''),
    csrfToken: null,
    debug: true
};

// Utilidades
Tukuchi.utils = {
    /**
     * Realizar petición AJAX
     */
    ajax: function(options) {
        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        };
        
        const config = Object.assign({}, defaults, options);
        
        // Agregar token CSRF si existe
        if (Tukuchi.config.csrfToken && (config.method === 'POST' || config.method === 'PUT')) {
            if (config.body instanceof FormData) {
                config.body.append('_token', Tukuchi.config.csrfToken);
            } else if (typeof config.body === 'object') {
                config.body._token = Tukuchi.config.csrfToken;
                config.body = JSON.stringify(config.body);
            }
        }
        
        return fetch(config.url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                if (Tukuchi.config.debug) {
                    console.error('Tukuchi AJAX Error:', error);
                }
                throw error;
            });
    },

    /**
     * Mostrar notificación
     */
    notify: function(message, type = 'info', duration = 5000) {
        const alertClass = `alert-${type}`;
        const alertId = 'tukuchi-alert-' + Date.now();
        
        const alertHtml = `
            <div id="${alertId}" class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', alertHtml);
        
        // Auto-remover después del tiempo especificado
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.remove();
            }
        }, duration);
    },

    /**
     * Validar formulario
     */
    validateForm: function(form) {
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        });
        
        return isValid;
    },

    /**
     * Formatear fecha
     */
    formatDate: function(date, format = 'DD/MM/YYYY') {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        
        return format
            .replace('DD', day)
            .replace('MM', month)
            .replace('YYYY', year);
    },

    /**
     * Debounce function
     */
    debounce: function(func, wait, immediate) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
};

// Componentes UI
Tukuchi.ui = {
    /**
     * Inicializar tooltips
     */
    initTooltips: function() {
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    },

    /**
     * Inicializar modales
     */
    initModals: function() {
        // Configuración personalizada para modales si es necesario
    },

    /**
     * Smooth scroll
     */
    smoothScroll: function(target, duration = 1000) {
        const targetElement = document.querySelector(target);
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
};

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes UI
    Tukuchi.ui.initTooltips();
    Tukuchi.ui.initModals();
    
    // Obtener token CSRF si existe
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        Tukuchi.config.csrfToken = csrfToken.getAttribute('content');
    }
    
    // Manejar formularios AJAX automáticamente
    document.querySelectorAll('form[data-ajax="true"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!Tukuchi.utils.validateForm(form)) {
                Tukuchi.utils.notify('Por favor, completa todos los campos requeridos.', 'warning');
                return;
            }
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Mostrar loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';
            
            Tukuchi.utils.ajax({
                url: form.action,
                method: form.method,
                body: formData
            })
            .then(response => {
                if (response.status === 'success') {
                    Tukuchi.utils.notify(response.message || 'Operación exitosa', 'success');
                    form.reset();
                } else {
                    throw new Error(response.message || 'Error desconocido');
                }
            })
            .catch(error => {
                Tukuchi.utils.notify(error.message, 'danger');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    });
    
    // Smooth scroll para enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('href');
            if (target !== '#') {
                Tukuchi.ui.smoothScroll(target);
            }
        });
    });
    
    console.log('🐦 Framework Tukuchi inicializado correctamente');
});