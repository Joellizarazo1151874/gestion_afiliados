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

// Obtener filtros
$filtros = [
    'busqueda' => $_GET['busqueda'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'tipo_discapacidad' => $_GET['tipo_discapacidad'] ?? '',
    'fecha_desde' => $_GET['fecha_desde'] ?? '',
    'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
];

// Obtener datos
$usuarios = $usuarioController->obtenerUsuarios($filtros);
$estadisticas = $usuarioController->obtenerEstadisticas();
$tipos_discapacidad = $usuarioController->obtenerTiposDiscapacidad();

// Procesar logout
if (isset($_GET['logout'])) {
    $auth->logout();
    header('Location: login.php');
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
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="apple-touch-icon" href="img/logo.png">
    <title>Dashboard - Familia unida por la discapacidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.0/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%);
            min-height: 100vh;
            color: white;
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
            
            /* Estilos para tarjetas móviles */
            .user-card-mobile {
                border: 1px solid #e9ecef;
                border-radius: 10px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
            }
            
            .user-card-mobile:hover {
                box-shadow: 0 4px 15px rgba(0,0,0,0.15);
                transform: translateY(-2px);
            }
            
            .user-card-mobile .card-body {
                padding: 1rem;
            }
            
            .user-card-mobile h6 {
                color: #495057;
                font-weight: 600;
            }
            
            .user-card-mobile .badge {
                font-size: 0.7rem;
            }
            
            .user-card-mobile .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
            
            /* Estilos para el logo en móviles */
            .logo-sidebar {
                max-width: 60px !important;
            }
            
            .logo-login {
                max-width: 80px !important;
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
        .stats-card {
            background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%);
            color: white;
        }
        .stats-card.success {
            background: linear-gradient(135deg, #228B22 0%, #32CD32 100%);
        }
        .stats-card.warning {
            background: linear-gradient(135deg, #FF8C00 0%, #FFD700 100%);
        }
        .stats-card.danger {
            background: linear-gradient(135deg, #B22222 0%, #DC143C 100%);
        }
        .btn-action {
            border-radius: 10px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        .table th {
            background: #f8f9fa;
            border-top: none;
            font-weight: 600;
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
            background: linear-gradient(135deg, #B22222 0%, #DC143C 100%) !important;
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
        
        .logo-login {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
        
        .logo-login:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(0,0,0,0.25);
        }
        
        /* Forzar nuevos colores en modales - más específico */
        .modal-header {
            background: linear-gradient(135deg, #FFD700 0%,rgba(220, 20, 60, 0.81) 100%) !important;
            color: white !important;
        }
        
        .modal-header.bg-danger {
            background: linear-gradient(135deg, #B22222 0%, #DC143C 100%) !important;
        }
        
        .modal-header.bg-primary {
            background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%) !important;
        }
        
        /* Forzar colores en botones */
        .btn-primary {
            background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%) !important;
            border: none !important;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #FFD700 0%, #FF8C00 100%) !important;
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
        
        .btn-danger {
            background: linear-gradient(135deg, #B22222 0%, #DC143C 100%) !important;
            border: none !important;
        }
        
        /* Forzar colores en botones outline */
        .btn-outline-warning {
            border-color: #FFD700 !important;
            color: #FF8C00 !important;
        }
        
        .btn-outline-warning:hover {
            background-color: #FFD700 !important;
            border-color: #FFD700 !important;
            color: white !important;
        }
        
        .btn-outline-info {
            border-color: #32CD32 !important;
            color: #32CD32 !important;
        }
        
        .btn-outline-info:hover {
            background-color: #32CD32 !important;
            border-color: #32CD32 !important;
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
                        <a class="nav-link active" href="#">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                            <i class="fas fa-plus me-2"></i>
                            Nuevo Usuario
                        </a>
                        <a class="nav-link" href="#" onclick="exportarExcel()">
                            <i class="fas fa-file-excel me-2"></i>
                            Exportar Excel
                        </a>
                        <a class="nav-link" href="#" onclick="exportarCSV()">
                            <i class="fas fa-file-csv me-2"></i>
                            Exportar CSV
                        </a>
                        <hr class="my-3">
                        <div class="text-center">
                            <small>Bienvenido, <?php echo htmlspecialchars($admin['nombre']); ?></small>
                        </div>
                        <a class="nav-link text-center" href="?logout=1">
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
                        <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                        <button class="btn btn-warning btn-action" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario" style="background: linear-gradient(135deg, #FFD700 0%, #FF8C00 100%); border: none;">
                            <i class="fas fa-plus me-2"></i>Nuevo Usuario
                        </button>
                    </div>

                    <!-- Mensajes de éxito/error -->
                    <?php if (isset($_GET['mensaje']) && isset($_GET['texto'])): ?>
                        <div class="alert alert-<?php echo $_GET['mensaje'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                            <i class="fas fa-<?php echo $_GET['mensaje'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                            <?php echo htmlspecialchars(urldecode($_GET['texto'])); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stats-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <h3><?php echo $estadisticas['total_usuarios']; ?></h3>
                                    <p class="mb-0">Total Usuarios</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card success">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <h3><?php echo $estadisticas['usuarios_activos']; ?></h3>
                                    <p class="mb-0">Usuarios Activos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-pause-circle fa-2x mb-2"></i>
                                    <h3><?php echo $estadisticas['usuarios_inactivos']; ?></h3>
                                    <p class="mb-0">Usuarios Inactivos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stats-card danger">
                                <div class="card-body text-center">
                                    <i class="fas fa-ban fa-2x mb-2"></i>
                                    <h3><?php echo $estadisticas['usuarios_suspendidos']; ?></h3>
                                    <p class="mb-0">Usuarios Suspendidos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5>
                            <form method="GET" action="">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="busqueda" 
                                               placeholder="Buscar por nombre, apellidos o número" 
                                               value="<?php echo htmlspecialchars($filtros['busqueda']); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="estado">
                                            <option value="">Todos los estados</option>
                                            <option value="Activo" <?php echo $filtros['estado'] === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                                            <option value="Inactivo" <?php echo $filtros['estado'] === 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                            <option value="Suspendido" <?php echo $filtros['estado'] === 'Suspendido' ? 'selected' : ''; ?>>Suspendido</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="tipo_discapacidad">
                                            <option value="">Todos los tipos</option>
                                            <?php foreach ($tipos_discapacidad as $tipo): ?>
                                                <option value="<?php echo htmlspecialchars($tipo); ?>" 
                                                        <?php echo $filtros['tipo_discapacidad'] === $tipo ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($tipo); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="fecha_desde" 
                                               placeholder="Desde" value="<?php echo $filtros['fecha_desde']; ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="fecha_hasta" 
                                               placeholder="Hasta" value="<?php echo $filtros['fecha_hasta']; ?>">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de Usuarios -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-list me-2"></i>Lista de Usuarios
                                </h5>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-success btn-action" onclick="exportarExcel()" style="background: linear-gradient(135deg, #228B22 0%, #32CD32 100%); border: none;">
                                        <i class="fas fa-file-excel me-2"></i>Excel
                                    </button>
                                    <button class="btn btn-info btn-action" onclick="exportarCSV()" style="background: linear-gradient(135deg, #32CD32 0%, #228B22 100%); border: none;">
                                        <i class="fas fa-file-csv me-2"></i>CSV
                                    </button>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <!-- Vista móvil de tarjetas -->
                                <div class="d-md-none">
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <div class="card mb-3 user-card-mobile">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <i class="fas fa-user me-2"></i>
                                                            <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']); ?>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-id-card me-1"></i>
                                                            <?php echo htmlspecialchars($usuario['numero_asociado']); ?>
                                                        </small>
                                                    </div>
                                                    <span class="badge bg-<?php echo $usuario['estado'] === 'Activo' ? 'success' : 'secondary'; ?>">
                                                        <?php echo htmlspecialchars($usuario['estado']); ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="row mb-2">
                                                    <div class="col-6">
                                                        <small class="text-muted">
                                                            <i class="fas fa-wheelchair me-1"></i>
                                                            <?php echo htmlspecialchars($usuario['tipo_discapacidad']); ?>
                                                        </small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <?php echo date('d/m/Y', strtotime($usuario['fecha_afiliacion'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-end gap-1">
                                                    <button class="btn btn-sm btn-outline-info" 
                                                            onclick="verUsuario(<?php echo $usuario['id']; ?>)" 
                                                            style="border-color: #32CD32; color: #32CD32;">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-warning" 
                                                            onclick="editarUsuario(<?php echo $usuario['id']; ?>)" 
                                                            style="border-color: #FFD700; color: #FF8C00;">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="eliminarUsuario(<?php echo $usuario['id']; ?>, '<?php echo addslashes($usuario['nombre']); ?>', '<?php echo addslashes($usuario['apellidos']); ?>', '<?php echo addslashes($usuario['numero_asociado']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Vista desktop de tabla -->
                                <div class="d-none d-md-block">
                                    <table class="table table-hover" id="tablaUsuarios">
                                    <thead>
                                        <tr>
                                            <th>Número Asociado</th>
                                            <th>Nombre</th>
                                            <th>Apellidos</th>
                                            <th>Tipo Discapacidad</th>
                                            <th>Estado</th>
                                            <th>Fecha Afiliación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($usuario['numero_asociado']); ?></td>
                                                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($usuario['apellidos']); ?></td>
                                                <td><?php echo htmlspecialchars($usuario['tipo_discapacidad']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $usuario['estado'] === 'Activo' ? 'success' : 
                                                            ($usuario['estado'] === 'Inactivo' ? 'warning' : 'danger'); 
                                                    ?>">
                                                        <?php echo htmlspecialchars($usuario['estado']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($usuario['fecha_afiliacion'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info btn-action" 
                                                            onclick="verUsuario(<?php echo $usuario['id']; ?>)" 
                                                            style="background: linear-gradient(135deg, #32CD32 0%, #228B22 100%); border: none;">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning btn-action" 
                                                            onclick="editarUsuario(<?php echo $usuario['id']; ?>)" 
                                                            style="background: linear-gradient(135deg, #FFD700 0%, #FF8C00 100%); border: none;">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-action" 
                                                            onclick="eliminarUsuario(<?php echo $usuario['id']; ?>, '<?php echo addslashes($usuario['nombre']); ?>', '<?php echo addslashes($usuario['apellidos']); ?>', '<?php echo addslashes($usuario['numero_asociado']); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Usuario -->
    <div class="modal fade" id="modalNuevoUsuario" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #FFD700 0%, #DC143C 100%);">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Nuevo Usuario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formNuevoUsuario" method="POST" action="procesar_usuario.php">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Número de Asociado *</label>
                                    <input type="text" class="form-control" name="numero_asociado" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="estado">
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                        <option value="Suspendido">Suspendido</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" name="nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Apellidos *</label>
                                    <input type="text" class="form-control" name="apellidos" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Nacimiento *</label>
                                    <input type="date" class="form-control" name="fecha_nacimiento" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Género *</label>
                                    <select class="form-select" name="genero" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                        <option value="Otro">Otro</option>
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
                                                <option value="<?php echo htmlspecialchars($tipo); ?>">
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
                                    <input type="number" class="form-control" name="porcentaje_discapacidad" min="0" max="100">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" name="telefono">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Dirección</label>
                            <textarea class="form-control" name="direccion" rows="2"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ciudad</label>
                                    <input type="text" class="form-control" name="ciudad">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Código Postal</label>
                                    <input type="text" class="form-control" name="codigo_postal">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Afiliación *</label>
                                    <input type="date" class="form-control" name="fecha_afiliacion" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning" style="background: linear-gradient(135deg, #FFD700 0%, #FF8C00 100%); border: none;">
                            <i class="fas fa-save me-2"></i>Guardar Usuario
                        </button>
                    </div>
                </form>
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
                <div class="modal-header bg-danger text-white">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.0/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.0/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#tablaUsuarios').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.0/i18n/es-ES.json'
                },
                pageLength: 25,
                order: [[5, 'desc']]
            });
        });

        function exportarExcel() {
            const filtros = new URLSearchParams(window.location.search);
            window.location.href = 'exportar_excel.php?' + filtros.toString();
        }

        function exportarCSV() {
            const filtros = new URLSearchParams(window.location.search);
            window.location.href = 'exportar_csv.php?' + filtros.toString();
        }

        function verUsuario(id) {
            window.location.href = 'ver_usuario.php?id=' + id;
        }

        function editarUsuario(id) {
            window.location.href = 'editar_usuario.php?id=' + id;
        }

        // Variable global para almacenar el ID del usuario a eliminar
        let usuarioIdEliminar = null;
        
        // Forzar actualización de estilos para evitar cache
        function forceStyleUpdate() {
            const modals = document.querySelectorAll('.modal-header');
            const buttons = document.querySelectorAll('.btn-primary, .btn-warning, .btn-success, .btn-info, .btn-danger');
            
            modals.forEach(modal => {
                if (!modal.style.background || modal.style.background.includes('667eea')) {
                    modal.style.background = 'linear-gradient(135deg, #FFD700 0%, #DC143C 100%)';
                    modal.style.color = 'white';
                }
            });
            
            buttons.forEach(btn => {
                if (btn.classList.contains('btn-primary') && (!btn.style.background || btn.style.background.includes('667eea'))) {
                    btn.style.background = 'linear-gradient(135deg, #FFD700 0%, #DC143C 100%)';
                    btn.style.border = 'none';
                }
                if (btn.classList.contains('btn-warning') && (!btn.style.background || btn.style.background.includes('ffc107'))) {
                    btn.style.background = 'linear-gradient(135deg, #FFD700 0%, #FF8C00 100%)';
                    btn.style.border = 'none';
                }
                if (btn.classList.contains('btn-success') && (!btn.style.background || btn.style.background.includes('28a745'))) {
                    btn.style.background = 'linear-gradient(135deg, #228B22 0%, #32CD32 100%)';
                    btn.style.border = 'none';
                }
                if (btn.classList.contains('btn-info') && (!btn.style.background || btn.style.background.includes('17a2b8'))) {
                    btn.style.background = 'linear-gradient(135deg, #32CD32 0%, #228B22 100%)';
                    btn.style.border = 'none';
                }
                if (btn.classList.contains('btn-danger') && (!btn.style.background || btn.style.background.includes('dc3545'))) {
                    btn.style.background = 'linear-gradient(135deg, #B22222 0%, #DC143C 100%)';
                    btn.style.border = 'none';
                }
            });
        }
        
        // Funcionalidad del menú móvil
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }
            
            // Cerrar sidebar al hacer clic en un enlace (móviles)
            const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                    }
                });
            });
            
            // Forzar actualización de estilos al cargar la página
            forceStyleUpdate();
            
            // Forzar actualización cuando se abren modales
            document.addEventListener('shown.bs.modal', function() {
                setTimeout(forceStyleUpdate, 100);
            });
        });

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
