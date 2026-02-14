// script.js

document.addEventListener('DOMContentLoaded', function() {
    
    // Navegação suave
    const smoothScroll = () => {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    };
    
    // Validação de formulários
    const formValidation = () => {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if(!field.value.trim()) {
                        isValid = false;
                        field.classList.add('error');
                    } else {
                        field.classList.remove('error');
                    }
                });
                
                // Validação específica de email
                const emailFields = this.querySelectorAll('input[type="email"]');
                emailFields.forEach(field => {
                    if(field.value && !isValidEmail(field.value)) {
                        isValid = false;
                        field.classList.add('error');
                    }
                });
                
                // Validação de telefone
                const phoneFields = this.querySelectorAll('input[type="tel"]');
                phoneFields.forEach(field => {
                    if(field.value && !isValidPhone(field.value)) {
                        isValid = false;
                        field.classList.add('error');
                    }
                });
                
                if(!isValid) {
                    e.preventDefault();
                    showNotification('Por favor, preencha todos os campos obrigatórios corretamente.', 'error');
                }
            });
        });
    };
    
    // Funções auxiliares de validação
    const isValidEmail = (email) => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    };
    
    const isValidPhone = (phone) => {
        const re = /^[\d\s\(\)\-\+]+$/;
        return re.test(phone) && phone.replace(/\D/g, '').length >= 10;
    };
    
    // Máscara de telefone
    const phoneMask = () => {
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        
        phoneInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                if(value.length <= 11) {
                    if(value.length <= 2) {
                        value = value.replace(/^(\d{0,2})/, '($1');
                    } else if(value.length <= 6) {
                        value = value.replace(/^(\d{2})(\d{0,4})/, '($1) $2');
                    } else if(value.length <= 10) {
                        value = value.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
                    } else {
                        value = value.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                    }
                    
                    e.target.value = value;
                }
            });
        });
    };
    
    // Sistema de notificações
    const showNotification = (message, type = 'info') => {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        `;
        
        document.body.appendChild(notification);
        
        // Animação de entrada
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Fechar notificação
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        });
        
        // Auto-remover após 5 segundos
        setTimeout(() => {
            if(notification.parentNode) {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    };
    
    // Loading em formulários
    const formLoading = () => {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if(submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="loading-spinner"></span> Processando...';
                }
            });
        });
    };
    
    // Inicializar todas as funções
    const init = () => {
        smoothScroll();
        formValidation();
        phoneMask();
        formLoading();
        
        console.log('ClimaTech - Sistema carregado com sucesso!');
    };
    
    init();
});

// Funções globais para uso em outros arquivos
window.ClimaTech = {
    showNotification: (message, type) => {
        // Reutiliza a função de notificação
        const event = new CustomEvent('showNotification', {
            detail: { message, type }
        });
        document.dispatchEvent(event);
    },
    
    formatCurrency: (value) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    },
    
    validateEmail: (email) => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
};