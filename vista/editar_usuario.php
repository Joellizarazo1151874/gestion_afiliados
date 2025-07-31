<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/AuthController.php';
require_once '../controlador/UsuarioController.php';

$auth = new AuthController();
$auth->requireAuth();

$usuarioController = new UsuarioController();
$admin = $auth->getCurrentAdmin();
$tipos_discapacidad = $usuarioController->obtenerTiposDiscapacidad();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit();
}

$usuario = $usuarioController->obtenerUsuario($id);
if (!$usuario) {
    header('Location: index.php?mensaje=error&texto=' . urlencode('Usuario no encontrado'));
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="theme-color" content="#FFD700">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="apple-touch-icon" href="img/logo.png">
    <title>Editar Usuario - Familia unida por la discapacidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/menu-mobile.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .btn-action {
            border-radius: 10px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        /* Estilos para el modal personalizado */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            border-radius: 15px 15px 0 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }
        
        .modal-footer {
            border-radius: 0 0 15px 15px;
            border-top: 1px solid #dee2e6;
        }
        
        .btn-close-white {
        
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
                padding: 1rem;
            }
            
            .container-fluid {
                padding: 0;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .form-control, .form-select {
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }
            filter: brightness(0) invert(1);
        }
        
        /* Animación para las alertas */
        .alert {
            animation: slideInRight 0.3s ease-out;
        }
        
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
        
        /* Estilos para el input group del selector */
        .input-group .btn-outline-secondary {
            border-color: #dee2e6;
            color: #6c757d;
        }
        
        .input-group .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }
        
        /* Estilos específicos para el modal de eliminación */
        .modal-header.bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        }
        
        #infoUsuarioEliminar {
            border: 1px solid #dee2e6;
            border-radius: 10px;
        }
        
        #infoUsuarioEliminar .card-body {
            padding: 1rem;
        }
        
        .fa-user-times {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Estilos para el botón de eliminación */
        .btn-danger {
            background: linear-gradient(135deg, #B22222 0%, #DC143C 100%);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(178, 34, 34, 0.4);
        }
        
        /* Responsive para móviles */
        @media (max-width: 768px) {
            .container {
                padding: 0.5rem;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .form-control, .form-select {
                font-size: 16px; /* Evita zoom en iOS */
            }
            
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            .btn-group .btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
            
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .modal-body {
                padding: 1rem;
            }
            
            .row > div {
                margin-bottom: 0.5rem;
            }
            
            /* Estilos para el logo en móviles */
            .logo-sidebar {
                max-width: 60px !important;
            }
        }
        
        /* Estilos para el logo */
        .logo-sidebar {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .logo-sidebar:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        /* Forzar nuevos colores en modales y botones */
        .modal-header {
            background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%) !important;
            color: white !important;
        }
        
        .modal-header.bg-danger {
            background: linear-gradient(135deg, #B22222 0%, #DC143C 100%) !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%) !important;
            border: none !important;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #FFD700 0%, #FF8C00 100%) !important;
            border: none !important;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #B22222 0%, #DC143C 100%) !important;
            border: none !important;
        }
        
        .btn-outline-warning {
            border-color: #FFD700 !important;
            color: #FF8C00 !important;
        }
        
        .btn-outline-warning:hover {
            background-color: #FFD700 !important;
            border-color: #FFD700 !important;
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Botón de menú móvil -->
            <button class="btn btn-primary d-md-none position-fixed" 
                    id="sidebarToggle" 
                    style="top: 10px; left: 10px; z-index: 1060; border-radius: 50%; width: 45px; height: 45px;">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Overlay para móviles -->
            <div class="sidebar-overlay d-md-none" id="sidebarOverlay"></div>
            
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <img src="img/logo.png" alt="Logo Familia unida por la discapacidad" class="logo-sidebar mb-3" style="max-width: 80px; height: auto;">
                        <h5>Gestión de Usuarios</h5>
                        <small>Familia unida por la discapacidad</small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                        <a class="nav-link active" href="#">
                            <i class="fas fa-edit me-2"></i>
                            Editar Usuario
                        </a>
                        <hr class="my-3">
                        <div class="text-center">
                            <small>Bienvenido, <?php echo htmlspecialchars($admin['nombre']); ?></small>
                        </div>
                        <a class="nav-link text-center" href="index.php?logout=1">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Cerrar Sesión
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>
                            <i class="fas fa-edit me-2"></i>
                            Editar Usuario
                        </h2>
                        <div>
                            <a href="ver_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-info btn-action">
                                <i class="fas fa-eye me-2"></i>Ver Usuario
                            </a>
                            <a href="index.php" class="btn btn-secondary btn-action">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>

                    <!-- Formulario de Edición -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user-edit me-2"></i>
                                Editar Información del Usuario
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="procesar_usuario.php">
                                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Número de Asociado *</label>
                                            <input type="text" class="form-control" name="numero_asociado" 
                                                   value="<?php echo htmlspecialchars($usuario['numero_asociado']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Estado</label>
                                            <select class="form-select" name="estado">
                                                <option value="Activo" <?php echo $usuario['estado'] === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                                                <option value="Inactivo" <?php echo $usuario['estado'] === 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                                <option value="Suspendido" <?php echo $usuario['estado'] === 'Suspendido' ? 'selected' : ''; ?>>Suspendido</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre *</label>
                                            <input type="text" class="form-control" name="nombre" 
                                                   value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Apellidos *</label>
                                            <input type="text" class="form-control" name="apellidos" 
                                                   value="<?php echo htmlspecialchars($usuario['apellidos']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Fecha de Nacimiento *</label>
                                            <input type="date" class="form-control" name="fecha_nacimiento" 
                                                   value="<?php echo $usuario['fecha_nacimiento']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Género *</label>
                                            <select class="form-select" name="genero" required>
                                                <option value="">Seleccionar...</option>
                                                <option value="Masculino" <?php echo $usuario['genero'] === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                                                <option value="Femenino" <?php echo $usuario['genero'] === 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                                                <option value="Otro" <?php echo $usuario['genero'] === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                                                <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tipo de Discapacidad *</label>
                                    <div class="input-group">
                                        <select class="form-select" name="tipo_discapacidad" required>
                                            <option value="">Seleccionar tipo de discapacidad...</option>
                                            <?php foreach ($tipos_discapacidad as $tipo): ?>
                                                <option value="<?php echo htmlspecialchars($tipo); ?>" 
                                                        <?php echo $usuario['tipo_discapacidad'] === $tipo ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($tipo); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="btn btn-outline-warning" onclick="agregarNuevoTipo()" title="Agregar nuevo tipo" style="border-color: #FFD700; color: #FF8C00;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Porcentaje de Discapacidad</label>
                                            <input type="number" class="form-control" name="porcentaje_discapacidad" 
                                                   min="0" max="100" value="<?php echo $usuario['porcentaje_discapacidad']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Teléfono</label>
                                            <input type="tel" class="form-control" name="telefono" 
                                                   value="<?php echo htmlspecialchars($usuario['telefono']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" 
                                                   value="<?php echo htmlspecialchars($usuario['email']); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Dirección</label>
                                    <textarea class="form-control" name="direccion" rows="2"><?php echo htmlspecialchars($usuario['direccion']); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ciudad</label>
                                            <input type="text" class="form-control" name="ciudad" 
                                                   value="<?php echo htmlspecialchars($usuario['ciudad']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Código Postal</label>
                                            <input type="text" class="form-control" name="codigo_postal" 
                                                   value="<?php echo htmlspecialchars($usuario['codigo_postal']); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Fecha de Afiliación *</label>
                                            <input type="date" class="form-control" name="fecha_afiliacion" 
                                                   value="<?php echo $usuario['fecha_afiliacion']; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" name="observaciones" rows="3"><?php echo htmlspecialchars($usuario['observaciones']); ?></textarea>
                                </div>
                                
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="ver_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-secondary btn-action">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-action">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

    <!-- Modal para agregar nuevo tipo de discapacidad -->
    <div class="modal fade" id="modalNuevoTipoDiscapacidad" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%);">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>
                        Agregar Nuevo Tipo de Discapacidad
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nuevoTipoDiscapacidad" class="form-label">
                            <i class="fas fa-tag me-2"></i>
                            Nombre del nuevo tipo:
                        </label>
                        <input type="text" class="form-control" id="nuevoTipoDiscapacidad" 
                               placeholder="Ej: Discapacidad cardíaca" maxlength="100">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Ingresa un nombre descriptivo para el nuevo tipo de discapacidad.
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Sugerencias:</strong> Discapacidad cardíaca, Discapacidad respiratoria, 
                        Discapacidad digestiva, etc.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-warning" onclick="confirmarNuevoTipo()" style="background: linear-gradient(135deg, #FFD700 0%, #FF8C00 100%); border: none;">
                        <i class="fas fa-save me-2"></i>Guardar Tipo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar eliminación de usuario -->
    <div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #B22222 0%, #DC143C 100%);">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-user-times fa-4x text-danger mb-3"></i>
                        <h4 class="text-danger">¿Está seguro de que desea eliminar este usuario?</h4>
                        <p class="text-muted">
                            Esta acción no se puede deshacer. Se eliminarán permanentemente todos los datos del usuario.
                        </p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Esta acción es irreversible.
                    </div>
                    
                    <div id="infoUsuarioEliminar" class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-user me-2"></i>
                                Información del Usuario:
                            </h6>
                            <div id="detallesUsuarioEliminar">
                                <!-- Se llenará dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
                        <i class="fas fa-trash me-2"></i>Eliminar Usuario
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/menu-mobile.js"></script>
    
    <script>
        // Función para abrir modal de nuevo tipo de discapacidad
        function agregarNuevoTipo() {
            const modal = new bootstrap.Modal(document.getElementById('modalNuevoTipoDiscapacidad'));
            modal.show();
            
            // Limpiar el campo y enfocarlo
            document.getElementById('nuevoTipoDiscapacidad').value = '';
            setTimeout(() => {
                document.getElementById('nuevoTipoDiscapacidad').focus();
            }, 500);
        }

        // Función para confirmar y agregar el nuevo tipo
        function confirmarNuevoTipo() {
            const nuevoTipo = document.getElementById('nuevoTipoDiscapacidad').value.trim();
            
            if (!nuevoTipo) {
                mostrarAlerta('Por favor, ingresa un nombre para el nuevo tipo de discapacidad.', 'warning');
                return;
            }
            
            if (nuevoTipo.length < 3) {
                mostrarAlerta('El nombre debe tener al menos 3 caracteres.', 'warning');
                return;
            }
            
            // Verificar si ya existe
            const select = document.querySelector('select[name="tipo_discapacidad"]');
            const opciones = Array.from(select.options).map(opt => opt.value.toLowerCase());
            
            if (opciones.includes(nuevoTipo.toLowerCase())) {
                mostrarAlerta('Este tipo de discapacidad ya existe en la lista.', 'warning');
                return;
            }
            
            // Agregar la nueva opción
            const option = document.createElement('option');
            option.value = nuevoTipo;
            option.textContent = nuevoTipo;
            select.appendChild(option);
            select.value = nuevoTipo;
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoTipoDiscapacidad'));
            modal.hide();
            
            // Mostrar mensaje de éxito
            mostrarAlerta(`Tipo de discapacidad "${nuevoTipo}" agregado exitosamente.`, 'success');
        }

        // Función para mostrar alertas personalizadas
        function mostrarAlerta(mensaje, tipo = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                <i class="fas fa-${tipo === 'success' ? 'check-circle' : tipo === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Variable global para almacenar el ID del usuario a eliminar
        let usuarioIdEliminar = null;

        function eliminarUsuario(id, nombre, apellidos, numeroAsociado) {
            usuarioIdEliminar = id;
            
            // Llenar información del usuario en el modal
            document.getElementById('detallesUsuarioEliminar').innerHTML = `
                <div class="row text-start">
                    <div class="col-md-6">
                        <strong>Número Asociado:</strong><br>
                        <span class="text-primary">${numeroAsociado}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Nombre Completo:</strong><br>
                        <span class="text-primary">${nombre} ${apellidos}</span>
                    </div>
                </div>
            `;
            
            // Mostrar modal de confirmación
            const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminacion'));
            modal.show();
        }

        function confirmarEliminacion() {
            if (usuarioIdEliminar) {
                // Mostrar indicador de carga
                const btnEliminar = document.querySelector('#modalConfirmarEliminacion .btn-danger');
                const originalText = btnEliminar.innerHTML;
                btnEliminar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Eliminando...';
                btnEliminar.disabled = true;
                
                // Redirigir a la eliminación
                window.location.href = 'procesar_usuario.php?accion=eliminar&id=' + usuarioIdEliminar;
            }
        }

        // Permitir agregar con Enter en el modal
        document.addEventListener('DOMContentLoaded', function() {
            const inputNuevoTipo = document.getElementById('nuevoTipoDiscapacidad');
            if (inputNuevoTipo) {
                inputNuevoTipo.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        confirmarNuevoTipo();
                    }
                });
            }
        });
    </script>
</body>
</html> 