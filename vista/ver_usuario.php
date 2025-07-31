<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../controlador/AuthController.php';
require_once '../controlador/UsuarioController.php';
require_once '../modelo/Documento.php';

$auth = new AuthController();
$auth->requireAuth();

$usuarioController = new UsuarioController();
$admin = $auth->getCurrentAdmin();

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

// Obtener documentos del usuario desde la tabla documentos_solicitudes
$documento = new Documento();
$documentos = $documento->obtenerDocumentosSolicitudPorEmail($usuario['email']);

// Debug: Verificar qué se está obteniendo
error_log("Usuario ID: " . $id);
error_log("Email del usuario: " . $usuario['email']);
error_log("Documentos obtenidos: " . ($documentos ? count($documentos) : 0));
if ($documentos) {
    error_log("Documentos: " . print_r($documentos, true));
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
    <title>Ver Usuario - Familia unida por la discapacidad</title>
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
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #212529;
        }
        
        .info-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8f9fa;
            transition: all 0.3s ease;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-item:hover {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding-left: 10px;
            padding-right: 10px;
            margin-left: -10px;
            margin-right: -10px;
        }
        
        .hover-shadow {
            transition: all 0.3s ease;
        }
        
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }
        
        .document-card {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }
        
        .document-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
            border-color: #007bff;
        }
        
        .download-btn {
            transition: all 0.3s ease;
        }
        
        .download-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }
        
        .summary-item {
            padding: 1rem;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .summary-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        }
        
        .text-pink {
            color: #e83e8c !important;
        }
        
        /* Estilos para previsualización de documentos */
        .document-preview {
            position: relative;
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .image-preview-container {
            position: relative;
            width: 100%;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .image-preview-container:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .document-image-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        
        .image-overlay i {
            color: white;
            font-size: 1.5rem;
        }
        
        .image-preview-container:hover .image-overlay {
            opacity: 1;
        }
        
        .file-preview-container {
            position: relative;
            width: 100%;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-preview-container:hover {
            background: #e9ecef;
            transform: scale(1.05);
        }
        
        .file-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        
        .file-overlay i {
            color: white;
            font-size: 1.2rem;
        }
        
        .file-preview-container:hover .file-overlay {
            opacity: 1;
        }
        
        /* Modal para imágenes */
        .modal-image {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
        }
        
        .modal-header-custom {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-bottom: none;
        }
        
        .modal-body-custom {
            padding: 2rem;
            text-align: center;
            background: #f8f9fa;
        }
        
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
            
            .container-fluid {
                padding: 0;
            }
            
            .main-content {
                padding: 1rem !important;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            .btn-group .btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
            
            .row > div {
                margin-bottom: 0.5rem;
            }
            
            .info-item {
                padding: 0.5rem 0;
                border-bottom: 1px solid #e9ecef;
            }
            
            .info-item:last-child {
                border-bottom: none;
            }
            
            .info-item:hover {
                padding-left: 5px;
                padding-right: 5px;
                margin-left: -5px;
                margin-right: -5px;
            }
            
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }
            
            .d-flex.justify-content-between .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            /* Estilos para el logo en móviles */
            .logo-sidebar {
                max-width: 60px !important;
            }
            
            /* Ajustes para documentos en móviles */
            .document-card {
                margin-bottom: 1rem;
            }
            
            .summary-item {
                margin-bottom: 1rem;
                text-align: center;
            }
            
            .summary-item h4 {
                font-size: 1.5rem;
            }
            
            /* Ajustes para headers de tarjetas */
            .card-header {
                padding: 0.75rem 1rem;
            }
            
            .card-header h5 {
                font-size: 1rem;
            }
            
            /* Ajustes para información de contacto */
            .info-label {
                font-size: 0.9rem;
            }
            
            .info-value {
                font-size: 0.95rem;
            }
            
            /* Ajustes para previsualización en móviles */
            .document-preview {
                min-height: 100px;
            }
            
            .image-preview-container,
            .file-preview-container {
                height: 100px;
            }
            
            .document-image-preview {
                object-fit: cover;
            }
            
            .image-overlay i,
            .file-overlay i {
                font-size: 1rem;
            }
            
            /* Modal en móviles */
            .modal-dialog {
                margin: 1rem;
            }
            
            .modal-body-custom {
                padding: 1rem;
            }
            
            .modal-image {
                max-height: 60vh;
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
        
        /* Forzar nuevos colores en botones */
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
        
        .btn-success {
            background: linear-gradient(135deg, #228B22 0%, #32CD32 100%) !important;
            border: none !important;
        }
        
        .btn-info {
            background: linear-gradient(135deg, #32CD32 0%, #228B22 100%) !important;
            border: none !important;
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
                            <i class="fas fa-eye me-2"></i>
                            Ver Usuario
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
                            <i class="fas fa-user me-2"></i>
                            Detalles del Usuario
                        </h2>
                        <div>
                            <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-warning btn-action" style="background: linear-gradient(135deg, #FFD700 0%, #FF8C00 100%); border: none;">
                                <i class="fas fa-edit me-2"></i>Editar
                            </a>
                            <a href="index.php" class="btn btn-secondary btn-action">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>

                    <!-- Información del Usuario -->
                    <div class="row">
                        <!-- Columna Principal -->
                        <div class="col-lg-8">
                            <!-- Información Personal -->
                            <div class="card mb-4 hover-shadow">
                                <div class="card-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #FFD700 0%, #FF8C00 100%); border: none;">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-circle me-2"></i>
                                        Información Personal
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-id-card me-2 text-primary"></i>Número de Asociado:
                                                </label>
                                                <p class="info-value fw-bold text-primary"><?php echo htmlspecialchars($usuario['numero_asociado']); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-circle me-2 text-success"></i>Estado:
                                                </label>
                                                <p class="info-value">
                                                    <span class="badge bg-<?php 
                                                        echo $usuario['estado'] === 'Activo' ? 'success' : 
                                                            ($usuario['estado'] === 'Inactivo' ? 'warning' : 'danger'); 
                                                    ?> fs-6">
                                                        <?php echo htmlspecialchars($usuario['estado']); ?>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-user me-2 text-info"></i>Nombre:
                                                </label>
                                                <p class="info-value"><?php echo htmlspecialchars($usuario['nombre']); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-user me-2 text-info"></i>Apellidos:
                                                </label>
                                                <p class="info-value"><?php echo htmlspecialchars($usuario['apellidos']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-calendar me-2 text-warning"></i>Fecha de Nacimiento:
                                                </label>
                                                <p class="info-value"><?php echo date('d/m/Y', strtotime($usuario['fecha_nacimiento'])); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-venus-mars me-2 text-pink"></i>Género:
                                                </label>
                                                <p class="info-value"><?php echo htmlspecialchars($usuario['genero']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-wheelchair me-2 text-secondary"></i>Tipo de Discapacidad:
                                                </label>
                                                <p class="info-value"><?php echo htmlspecialchars($usuario['tipo_discapacidad']); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-percentage me-2 text-danger"></i>Porcentaje de Discapacidad:
                                                </label>
                                                <p class="info-value">
                                                    <?php if ($usuario['porcentaje_discapacidad']): ?>
                                                        <span class="badge bg-danger fs-6"><?php echo $usuario['porcentaje_discapacidad']; ?>%</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <div class="card mb-4 hover-shadow">
                                <div class="card-header bg-gradient-info text-white" style="background: linear-gradient(135deg, #FFD700 0%, #FF8C00 100%); border: none;">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-address-book me-2"></i>
                                        Información de Contacto
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-phone me-2 text-success"></i>Teléfono:
                                                </label>
                                                <p class="info-value">
                                                    <?php if ($usuario['telefono']): ?>
                                                        <a href="tel:<?php echo htmlspecialchars($usuario['telefono']); ?>" class="text-decoration-none">
                                                            <?php echo htmlspecialchars($usuario['telefono']); ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-envelope me-2 text-primary"></i>Email:
                                                </label>
                                                <p class="info-value">
                                                    <?php if ($usuario['email']): ?>
                                                        <a href="mailto:<?php echo htmlspecialchars($usuario['email']); ?>" class="text-decoration-none">
                                                            <?php echo htmlspecialchars($usuario['email']); ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <label class="info-label">
                                            <i class="fas fa-map-marker-alt me-2 text-danger"></i>Dirección:
                                        </label>
                                        <p class="info-value">
                                            <?php if ($usuario['direccion']): ?>
                                                <?php echo htmlspecialchars($usuario['direccion']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">No especificada</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-city me-2 text-info"></i>Ciudad:
                                                </label>
                                                <p class="info-value">
                                                    <?php if ($usuario['ciudad']): ?>
                                                        <?php echo htmlspecialchars($usuario['ciudad']); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No especificada</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label class="info-label">
                                                    <i class="fas fa-mail-bulk me-2 text-warning"></i>Código Postal:
                                                </label>
                                                <p class="info-value">
                                                    <?php if ($usuario['codigo_postal']): ?>
                                                        <?php echo htmlspecialchars($usuario['codigo_postal']); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No especificado</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna Lateral -->
                        <div class="col-lg-4">
                            <!-- Información de Afiliación -->
                            <div class="card mb-4 hover-shadow">
                                <div class="card-header bg-gradient-success text-white" style="background: linear-gradient(135deg, #FFD700 0%, #FF8C00 100%); border: none;">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        Información de Afiliación
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-item">
                                        <label class="info-label">
                                            <i class="fas fa-calendar-check me-2 text-success"></i>Fecha de Afiliación:
                                        </label>
                                        <p class="info-value fw-bold"><?php echo date('d/m/Y', strtotime($usuario['fecha_afiliacion'])); ?></p>
                                    </div>
                                    
                                    <div class="info-item">
                                        <label class="info-label">
                                            <i class="fas fa-clock me-2 text-info"></i>Fecha de Registro:
                                        </label>
                                        <p class="info-value"><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_registro'])); ?></p>
                                    </div>
                                    
                                    <div class="info-item">
                                        <label class="info-label">
                                            <i class="fas fa-edit me-2 text-warning"></i>Última Actualización:
                                        </label>
                                        <p class="info-value"><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_actualizacion'])); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <?php if ($usuario['observaciones']): ?>
                            <div class="card hover-shadow">
                                <div class="card-header bg-gradient-warning text-dark">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        Observaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-item">
                                        <p class="info-value">
                                            <i class="fas fa-comment me-2 text-warning"></i>
                                            <?php echo nl2br(htmlspecialchars($usuario['observaciones'])); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Documentos del Usuario -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card hover-shadow">
                                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #FFD700 0%, #FF8C00 100%); border: none;">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-file-alt me-2"></i>
                                        Documentos del Usuario
                                    </h5>
                                    <?php if ($documentos && count($documentos) > 0): ?>
                                        <span class="badge bg-light text-dark fs-6"><?php echo count($documentos); ?> documento(s)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if ($documentos && count($documentos) > 0): ?>
                                        <div class="row">
                                            <?php foreach ($documentos as $doc): ?>
                                                <div class="col-md-4 col-lg-3 mb-3">
                                                    <div class="card h-100 border-0 shadow-sm hover-shadow document-card">
                                                        <div class="card-body text-center">
                                                            <div class="mb-3 document-preview">
                                                                <?php
                                                                $extension = strtolower(pathinfo($doc['nombre_archivo'], PATHINFO_EXTENSION));
                                                                $ruta_archivo = dirname(__DIR__) . '/' . $doc['ruta_archivo'];
                                                                
                                                                                                                                 if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                                     // Mostrar previsualización de imagen
                                                                     if (file_exists($ruta_archivo)) {
                                                                         echo '<div class="image-preview-container">';
                                                                         echo '<img src="../' . $doc['ruta_archivo'] . '" alt="' . htmlspecialchars($doc['tipo_documento']) . '" class="document-image-preview" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="../' . $doc['ruta_archivo'] . '" data-image-title="' . htmlspecialchars($doc['tipo_documento']) . '">';
                                                                         echo '<div class="image-overlay">';
                                                                         echo '<i class="fas fa-search-plus"></i>';
                                                                         echo '</div>';
                                                                         echo '</div>';
                                                                     } else {
                                                                         echo '<i class="fas fa-image fa-3x text-muted"></i>';
                                                                         echo '<p class="text-muted small mt-2">Imagen no disponible</p>';
                                                                     }
                                                                } elseif ($extension === 'pdf') {
                                                                    echo '<div class="file-preview-container">';
                                                                    echo '<i class="fas fa-file-pdf fa-3x text-danger"></i>';
                                                                    echo '<div class="file-overlay">';
                                                                    echo '<i class="fas fa-eye"></i>';
                                                                    echo '</div>';
                                                                    echo '</div>';
                                                                } elseif (in_array($extension, ['doc', 'docx'])) {
                                                                    echo '<div class="file-preview-container">';
                                                                    echo '<i class="fas fa-file-word fa-3x text-primary"></i>';
                                                                    echo '<div class="file-overlay">';
                                                                    echo '<i class="fas fa-eye"></i>';
                                                                    echo '</div>';
                                                                    echo '</div>';
                                                                } else {
                                                                    echo '<div class="file-preview-container">';
                                                                    echo '<i class="fas fa-file fa-3x text-secondary"></i>';
                                                                    echo '<div class="file-overlay">';
                                                                    echo '<i class="fas fa-eye"></i>';
                                                                    echo '</div>';
                                                                    echo '</div>';
                                                                }
                                                                ?>
                                                            </div>
                                                            <h6 class="card-title fw-bold text-primary"><?php echo htmlspecialchars($doc['tipo_documento']); ?></h6>
                                                            
                                                            <?php if (isset($doc['obligatorio']) && $doc['obligatorio']): ?>
                                                                <span class="badge bg-warning text-dark mb-2">Obligatorio</span>
                                                            <?php endif; ?>
                                                            
                                                            <p class="card-text small text-muted mb-2">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                <?php echo date('d/m/Y', strtotime($doc['fecha_carga'])); ?>
                                                            </p>
                                                            
                                                            <?php if (isset($doc['estado']) && $doc['estado']): ?>
                                                                <p class="card-text small mb-2">
                                                                    <span class="badge bg-<?php echo $doc['estado'] === 'Aprobado' ? 'success' : ($doc['estado'] === 'Rechazado' ? 'danger' : 'warning'); ?>">
                                                                        <?php echo htmlspecialchars($doc['estado']); ?>
                                                                    </span>
                                                                </p>
                                                            <?php endif; ?>
                                                            
                                                            <div class="mt-3">
                                                                <a href="descargar_documento_solicitud.php?id=<?php echo $doc['id']; ?>" 
                                                                   class="btn btn-sm btn-outline-primary download-btn" 
                                                                   title="Descargar documento">
                                                                    <i class="fas fa-download me-1"></i>
                                                                    Descargar
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <!-- Resumen de documentos -->
                                        <div class="mt-4 p-4 bg-light rounded-3 shadow-sm">
                                            <h6 class="fw-bold mb-3 text-primary">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Resumen de Documentos
                                            </h6>
                                            <div class="row text-center">
                                                <div class="col-md-3">
                                                    <div class="summary-item">
                                                        <h4 class="text-primary mb-1"><?php echo count($documentos); ?></h4>
                                                        <small class="text-muted">Total</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="summary-item">
                                                        <h4 class="text-warning mb-1"><?php echo count(array_filter($documentos, function($doc) { return isset($doc['obligatorio']) && $doc['obligatorio']; })); ?></h4>
                                                        <small class="text-muted">Obligatorios</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="summary-item">
                                                        <h4 class="text-info mb-1"><?php echo count(array_filter($documentos, function($doc) { return !isset($doc['obligatorio']) || !$doc['obligatorio']; })); ?></h4>
                                                        <small class="text-muted">Opcionales</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="summary-item">
                                                        <h4 class="text-success mb-1"><?php echo date('d/m/Y', strtotime(max(array_column($documentos, 'fecha_carga')))); ?></h4>
                                                        <small class="text-muted">Última carga</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay documentos cargados</h5>
                                            <p class="text-muted">Este usuario no tiene documentos asociados en el sistema.</p>
                                            <div class="mt-3">
                                                <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-outline-primary">
                                                    <i class="fas fa-plus me-2"></i>
                                                    Agregar Documentos
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para previsualización de imágenes -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title" id="imageModalLabel">
                        <i class="fas fa-image me-2"></i>
                        Previsualización de Documento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <img id="modalImage" src="" alt="Documento" class="modal-image">
                    <div class="mt-3">
                        <h6 id="modalImageTitle" class="text-primary"></h6>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                    <a id="modalDownloadBtn" href="#" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Descargar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/menu-mobile.js"></script>
    
    <script>
        // Funcionalidad para el modal de imágenes
        document.addEventListener('DOMContentLoaded', function() {
            const imageModal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalImageTitle = document.getElementById('modalImageTitle');
            const modalDownloadBtn = document.getElementById('modalDownloadBtn');
            
            // Event listener para abrir el modal con la imagen
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('document-image-preview') || e.target.closest('.image-preview-container')) {
                    const container = e.target.closest('.image-preview-container');
                    const img = container.querySelector('.document-image-preview');
                    const imageSrc = img.getAttribute('data-image-src');
                    const imageTitle = img.getAttribute('data-image-title');
                    const documentId = container.closest('.document-card').querySelector('.download-btn').getAttribute('href').split('=')[1];
                    
                    // Configurar el modal
                    modalImage.src = imageSrc;
                    modalImageTitle.textContent = imageTitle;
                    modalDownloadBtn.href = 'descargar_documento_solicitud.php?id=' + documentId;
                    
                    // Mostrar el modal
                    const modal = new bootstrap.Modal(imageModal);
                    modal.show();
                }
            });
            
            // Efectos hover para contenedores de archivos
            const fileContainers = document.querySelectorAll('.file-preview-container');
            fileContainers.forEach(container => {
                container.addEventListener('click', function() {
                    const downloadBtn = this.closest('.document-card').querySelector('.download-btn');
                    if (downloadBtn) {
                        downloadBtn.click();
                    }
                });
            });
            
            // Mejorar la experiencia de descarga
            const downloadButtons = document.querySelectorAll('.download-btn');
            downloadButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Descargando...';
                    this.disabled = true;
                    
                    // Restaurar después de un tiempo
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }, 3000);
                });
            });
        });
    </script>
</body>
</html> 