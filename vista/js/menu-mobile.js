// Funcionalidad del menú móvil para toda la aplicación
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    // Crear botón de menú si no existe
    if (!sidebarToggle) {
        createMobileMenuButton();
    }
    
    // Crear overlay si no existe
    if (!sidebarOverlay) {
        createSidebarOverlay();
    }
    
    // Obtener referencias actualizadas
    const currentSidebarToggle = document.getElementById('sidebarToggle');
    const currentSidebar = document.querySelector('.sidebar');
    const currentSidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (currentSidebarToggle) {
        currentSidebarToggle.addEventListener('click', function() {
            currentSidebar.classList.toggle('show');
            currentSidebarOverlay.classList.toggle('show');
        });
    }
    
    if (currentSidebarOverlay) {
        currentSidebarOverlay.addEventListener('click', function() {
            currentSidebar.classList.remove('show');
            currentSidebarOverlay.classList.remove('show');
        });
    }
    
    // Cerrar sidebar al hacer clic en un enlace (móviles)
    const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                currentSidebar.classList.remove('show');
                currentSidebarOverlay.classList.remove('show');
            }
        });
    });
    
    // Cerrar sidebar al redimensionar la ventana
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            currentSidebar.classList.remove('show');
            currentSidebarOverlay.classList.remove('show');
        }
    });
});

// Función para crear el botón de menú móvil
function createMobileMenuButton() {
    const button = document.createElement('button');
    button.id = 'sidebarToggle';
    button.className = 'btn btn-primary d-md-none position-fixed';
    button.style.cssText = 'top: 10px; left: 10px; z-index: 1060; border-radius: 50%; width: 45px; height: 45px; background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%); border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.2); transition: all 0.3s ease;';
    button.innerHTML = '<i class="fas fa-bars"></i>';
    
    // Agregar efecto hover
    button.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.1)';
        this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.3)';
    });
    
    button.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
        this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
    });
    
    document.body.appendChild(button);
}

// Función para crear el overlay del sidebar
function createSidebarOverlay() {
    const overlay = document.createElement('div');
    overlay.id = 'sidebarOverlay';
    overlay.className = 'sidebar-overlay d-md-none';
    overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1040; display: none;';
    
    document.body.appendChild(overlay);
}

// Función para agregar estilos CSS del menú móvil si no existen
function addMobileMenuStyles() {
    if (!document.getElementById('mobile-menu-styles')) {
        const style = document.createElement('style');
        style.id = 'mobile-menu-styles';
        style.textContent = `
            /* Responsive para móviles */
            @media (max-width: 768px) {
                .sidebar {
                    position: fixed;
                    top: 0;
                    left: -100%;
                    width: 280px;
                    z-index: 1050;
                    transition: left 0.3s ease;
                }
                
                .sidebar.show {
                    left: 0;
                }
                
                .sidebar-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 1040;
                    display: none;
                }
                
                .sidebar-overlay.show {
                    display: block;
                }
                
                #sidebarToggle {
                    background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%);
                    border: none;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                    transition: all 0.3s ease;
                }
                
                #sidebarToggle:hover {
                    transform: scale(1.1);
                    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                }
                
                .main-content {
                    margin-left: 0 !important;
                    width: 100% !important;
                }
                
                .navbar-brand {
                    font-size: 1.1rem;
                }
                
                .card {
                    margin-bottom: 1rem;
                }
                
                .table-responsive {
                    font-size: 0.85rem;
                }
                
                .btn-sm {
                    padding: 0.25rem 0.5rem;
                    font-size: 0.75rem;
                }
                
                .stats-card {
                    text-align: center;
                    margin-bottom: 1rem;
                }
                
                .stats-card .card-body {
                    padding: 1rem 0.5rem;
                }
                
                .stats-card h3 {
                    font-size: 1.5rem;
                }
                
                .stats-card p {
                    font-size: 0.8rem;
                }
                
                .filters-section {
                    padding: 1rem 0.5rem;
                }
                
                .filters-section .row > div {
                    margin-bottom: 0.5rem;
                }
                
                .modal-dialog {
                    margin: 0.5rem;
                }
                
                .modal-body {
                    padding: 1rem;
                }
                
                .alert {
                    margin-bottom: 0.5rem;
                }
                
                /* Estilos para el logo en móviles */
                .logo-sidebar {
                    max-width: 60px !important;
                }
            }
            
            /* Mejoras para tablets */
            @media (min-width: 769px) and (max-width: 1024px) {
                .sidebar {
                    width: 250px;
                }
                
                .main-content {
                    margin-left: 250px !important;
                    width: calc(100% - 250px) !important;
                }
                
                .table-responsive {
                    font-size: 0.9rem;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// Inicializar estilos cuando se carga el script
addMobileMenuStyles(); 