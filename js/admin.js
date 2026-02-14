// js/admin.js

document.addEventListener('DOMContentLoaded', function() {
    
    // Máscaras de input
    const moneyMask = () => {
        const moneyInputs = document.querySelectorAll('.money');
        
        moneyInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = (value / 100).toFixed(2) + '';
                value = value.replace(".", ",");
                value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
                value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
                e.target.value = 'R$ ' + value;
            });
        });
    };
    
    // Confirmação para ações perigosas
    const confirmActions = () => {
        const dangerousLinks = document.querySelectorAll('a[class*="btn-danger"]');
        
        dangerousLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if(!confirm('Tem certeza que deseja realizar esta ação? Esta ação não pode ser desfeita.')) {
                    e.preventDefault();
                }
            });
        });
    };
    
    // Preview de imagem
    const imagePreview = () => {
        const imageInputs = document.querySelectorAll('input[type="file"][accept="image/*"]');
        
        imageInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if(file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Remove preview anterior se existir
                        const oldPreview = input.parentNode.querySelector('.image-preview');
                        if(oldPreview) {
                            oldPreview.remove();
                        }
                        
                        // Cria novo preview
                        const preview = document.createElement('div');
                        preview.className = 'image-preview';
                        preview.innerHTML = `
                            <p>Pré-visualização:</p>
                            <img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 150px; border-radius: 5px; margin-top: 10px;">
                        `;
                        input.parentNode.appendChild(preview);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    };
    
    // Filtros de tabela
    const tableFilters = () => {
        const filterInputs = document.querySelectorAll('.table-filter');
        
        filterInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                const filterValue = e.target.value.toLowerCase();
                const table = e.target.closest('.card').querySelector('table');
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filterValue) ? '' : 'none';
                });
            });
        });
    };
    
    // Ordenação de tabelas
    const tableSorting = () => {
        const sortableHeaders = document.querySelectorAll('th[data-sort]');
        
        sortableHeaders.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                const table = this.closest('table');
                const columnIndex = Array.from(this.parentNode.children).indexOf(this);
                const isNumeric = this.getAttribute('data-sort') === 'numeric';
                const isAsc = !this.classList.contains('sorted-asc');
                
                // Remove classes de ordenação de todos os headers
                sortableHeaders.forEach(h => {
                    h.classList.remove('sorted-asc', 'sorted-desc');
                });
                
                // Adiciona classe ao header atual
                this.classList.add(isAsc ? 'sorted-asc' : 'sorted-desc');
                
                // Ordena as linhas
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                
                rows.sort((a, b) => {
                    const aValue = a.children[columnIndex].textContent.trim();
                    const bValue = b.children[columnIndex].textContent.trim();
                    
                    if(isNumeric) {
                        const aNum = parseFloat(aValue.replace(/[^\d,]/g, '').replace(',', '.'));
                        const bNum = parseFloat(bValue.replace(/[^\d,]/g, '').replace(',', '.'));
                        return isAsc ? aNum - bNum : bNum - aNum;
                    } else {
                        return isAsc ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
                    }
                });
                
                // Reinsere as linhas ordenadas
                rows.forEach(row => tbody.appendChild(row));
            });
        });
    };
    
    // Sistema de notificações
    const showNotification = (message, type = 'info', duration = 5000) => {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        // Estilos da notificação
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-left: 4px solid var(--${type});
            z-index: 1000;
            max-width: 400px;
            animation: slideInRight 0.3s ease-out;
        `;
        
        document.body.appendChild(notification);
        
        // Botão fechar
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            notification.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        });
        
        // Auto-remover
        setTimeout(() => {
            if(notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
    };
    
    // Animações CSS
    const addAnimations = () => {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            
            .notification-success {
                border-left-color: var(--success) !important;
            }
            
            .notification-error {
                border-left-color: var(--danger) !important;
            }
            
            .notification-warning {
                border-left-color: var(--warning) !important;
            }
            
            .notification-info {
                border-left-color: var(--primary) !important;
            }
        `;
        document.head.appendChild(style);
    };
    
    // Carregamento de dados via AJAX
    const loadData = (url, callback) => {
        fetch(url)
            .then(response => response.json())
            .then(data => callback(data))
            .catch(error => console.error('Erro ao carregar dados:', error));
    };
    
    // Inicializar todas as funcionalidades
    const init = () => {
        moneyMask();
        confirmActions();
        imagePreview();
        tableFilters();
        tableSorting();
        addAnimations();
        
        console.log('Painel Admin ClimaTech inicializado');
    };
    
    init();
});

// Funções globais para uso em outros scripts
window.Admin = {
    showNotification: (message, type = 'info') => {
        const event = new CustomEvent('showAdminNotification', {
            detail: { message, type }
        });
        document.dispatchEvent(event);
    },
    
    confirmAction: (message = 'Tem certeza que deseja realizar esta ação?') => {
        return confirm(message);
    },
    
    formatCurrency: (value) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    },
    
    formatDate: (dateString) => {
        const options = { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('pt-BR', options);
    }
};

// Listeners para eventos customizados
document.addEventListener('showAdminNotification', (e) => {
    const { message, type } = e.detail;
    // Reutiliza a função de notificação existente
    const notificationEvent = new CustomEvent('showNotification', {
        detail: { message, type }
    });
    document.dispatchEvent(notificationEvent);
});