<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/controlador/SolicitudController.php';

$controller = new SolicitudController();

// Obtener solicitudes según filtro
$estado_filtro = $_GET['estado'] ?? null;
$solicitudes = $controller->obtenerSolicitudesAdmin($estado_filtro);

// Obtener estadísticas
$estadisticas = $controller->obtenerEstadisticas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Solicitudes - <?php echo ORG_NOMBRE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="css/menu-mobile.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logo.png">
    
    <style>
        :root {
            --primary-yellow: #FFD700;
            --primary-red: #DC143C;
            --primary-green: #32CD32;
        }
        
        /* Estilos para notificaciones personalizadas */
        .custom-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transform: translateX(100%);
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            overflow: hidden;
            border-left: 5px solid var(--primary-green);
        }
        
        .custom-notification.show {
            transform: translateX(0);
        }
        
        .custom-notification.success {
            border-left-color: var(--primary-green);
        }
        
        .custom-notification.error {
            border-left-color: var(--primary-red);
        }
        
        .custom-notification.warning {
            border-left-color: var(--primary-yellow);
        }
        
        .custom-notification.info {
            border-left-color: #17a2b8;
        }
        
        .notification-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, #228B22 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .notification-header.error {
            background: linear-gradient(135deg, var(--primary-red) 0%, #8B0000 100%);
        }
        
        .notification-header.warning {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, #FFA500 100%);
            color: #333;
        }
        
        .notification-header.info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }
        
        .notification-title {
            font-weight: bold;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: inherit;
            font-size: 1.5em;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }
        
        .notification-close:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .notification-body {
            padding: 20px;
            color: #333;
        }
        
        .notification-message {
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .notification-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            font-size: 0.9em;
            color: #666;
            border-left: 3px solid var(--primary-green);
        }
        
        .notification-details.error {
            border-left-color: var(--primary-red);
            background: #fff5f5;
        }
        
        .notification-details.warning {
            border-left-color: var(--primary-yellow);
            background: #fffbf0;
        }
        
        .notification-details.info {
            border-left-color: #17a2b8;
            background: #f0f8ff;
        }
        
        .notification-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .notification-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            flex: 1;
        }
        
        .notification-btn.primary {
            background: linear-gradient(135deg, var(--primary-green) 0%, #228B22 100%);
            color: white;
        }
        
        .notification-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(50, 205, 50, 0.3);
        }
        
        .notification-btn.secondary {
            background: #f8f9fa;
            color: #666;
            border: 1px solid #ddd;
        }
        
        .notification-btn.secondary:hover {
            background: #e9ecef;
        }
        
        .notification-progress {
            height: 3px;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .notification-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-green) 0%, #228B22 100%);
            width: 100%;
            animation: progress 5s linear;
        }
        
        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
        
        .notification-icon {
            font-size: 1.2em;
        }
        
        /* Overlay para notificaciones */
        .notification-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .notification-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* Modal de confirmación personalizado */
        .custom-confirm-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.7);
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            z-index: 10000;
            max-width: 500px;
            width: 90%;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .custom-confirm-modal.show {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, -50%) scale(1);
        }
        
        .confirm-header {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--primary-red) 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
            border-radius: 20px 20px 0 0;
        }
        
        .confirm-title {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .confirm-subtitle {
            font-size: 1em;
            opacity: 0.9;
        }
        
        .confirm-body {
            padding: 30px;
            text-align: center;
        }
        
        .confirm-message {
            font-size: 1.1em;
            color: #333;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .confirm-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .confirm-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
            transition: all 0.3s ease;
            min-width: 120px;
        }
        
        .confirm-btn.success {
            background: linear-gradient(135deg, var(--primary-green) 0%, #228B22 100%);
            color: white;
        }
        
        .confirm-btn.success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(50, 205, 50, 0.3);
        }
        
        .confirm-btn.cancel {
            background: #f8f9fa;
            color: #666;
            border: 2px solid #ddd;
        }
        
        .confirm-btn.cancel:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }
        
        body {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(220, 20, 60, 0.1) 50%, rgba(50, 205, 50, 0.1) 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary-yellow) 0%, var(--primary-red) 50%, var(--primary-green) 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link {
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin: 0.25rem 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.3);
            font-weight: 600;
        }
        
        .main-content {
            padding: 2rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-left: 5px solid var(--primary-yellow);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card.pendientes {
            border-left-color: var(--primary-yellow);
        }
        
        .stats-card.aprobadas {
            border-left-color: var(--primary-green);
        }
        
        .stats-card.rechazadas {
            border-left-color: var(--primary-red);
        }
        
        .table-container {
        
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
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .table-responsive {
                font-size: 0.85rem;
            }
        }
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--primary-red) 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--primary-green) 0%, #228B22 100%);
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--primary-red) 0%, #8B0000 100%);
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .badge {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
        }
        
        .badge-pendiente {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, #FFA500 100%);
            color: #000;
        }
        
        .badge-aprobada {
            background: linear-gradient(135deg, var(--primary-green) 0%, #228B22 100%);
            color: white;
        }
        
        .badge-rechazada {
            background: linear-gradient(135deg, var(--primary-red) 0%, #8B0000 100%);
            color: white;
        }
        
        .filters {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 80%;
                z-index: 1000;
                transition: left 0.3s ease;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
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
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <img src="img/logo.png" alt="Logo" class="img-fluid" style="max-height: 60px; border-radius: 10px;">
                        <h5 class="text-white mt-2"><?php echo ORG_NOMBRE; ?></h5>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="solicitudes.php">
                                <i class="fas fa-clipboard-list me-2"></i>Solicitudes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-users me-2"></i>Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-clipboard-list me-2"></i>Gestión de Solicitudes
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportarSolicitudes()">
                                <i class="fas fa-download me-1"></i>Exportar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card pendientes">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="text-warning mb-0">
                                        <?php 
                                        $pendientes = array_filter($solicitudes, function($s) { return $s['estado'] === 'Pendiente'; });
                                        echo count($pendientes);
                                        ?>
                                    </h3>
                                    <p class="text-muted mb-0">Pendientes</p>
                                </div>
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card aprobadas">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="text-success mb-0">
                                        <?php 
                                        $aprobadas = array_filter($solicitudes, function($s) { return $s['estado'] === 'Aprobada'; });
                                        echo count($aprobadas);
                                        ?>
                                    </h3>
                                    <p class="text-muted mb-0">Aprobadas</p>
                                </div>
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card rechazadas">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="text-danger mb-0">
                                        <?php 
                                        $rechazadas = array_filter($solicitudes, function($s) { return $s['estado'] === 'Rechazada'; });
                                        echo count($rechazadas);
                                        ?>
                                    </h3>
                                    <p class="text-muted mb-0">Rechazadas</p>
                                </div>
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="filters">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Filtrar por estado:</label>
                            <select class="form-select" onchange="filtrarSolicitudes(this.value)">
                                <option value="">Todos los estados</option>
                                <option value="Pendiente" <?php echo $estado_filtro === 'Pendiente' ? 'selected' : ''; ?>>Pendientes</option>
                                <option value="Aprobada" <?php echo $estado_filtro === 'Aprobada' ? 'selected' : ''; ?>>Aprobadas</option>
                                <option value="Rechazada" <?php echo $estado_filtro === 'Rechazada' ? 'selected' : ''; ?>>Rechazadas</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Acciones:</label>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" onclick="verTodas()">
                                    <i class="fas fa-eye me-1"></i>Ver Todas
                                </button>
                                <button class="btn btn-success" onclick="aprobarSeleccionadas()">
                                    <i class="fas fa-check me-1"></i>Aprobar Seleccionadas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Solicitudes -->
                <div class="table-container">
                    <h4 class="mb-3">
                        <i class="fas fa-list me-2"></i>Lista de Solicitudes
                    </h4>
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaSolicitudes">
                            <thead class="table-dark">
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Número</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Tipo Discapacidad</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($solicitudes as $solicitud): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="solicitud-checkbox" value="<?php echo $solicitud['id']; ?>">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($solicitud['numero_solicitud']); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($solicitud['nombre'] . ' ' . $solicitud['apellidos']); ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($solicitud['email']); ?>">
                                            <?php echo htmlspecialchars($solicitud['email']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($solicitud['tipo_discapacidad']); ?>
                                        <br>
                                        <small class="text-muted"><?php echo $solicitud['porcentaje_discapacidad']; ?>%</small>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])); ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = '';
                                        switch ($solicitud['estado']) {
                                            case 'Pendiente':
                                                $badge_class = 'badge-pendiente';
                                                break;
                                            case 'Aprobada':
                                                $badge_class = 'badge-aprobada';
                                                break;
                                            case 'Rechazada':
                                                $badge_class = 'badge-rechazada';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo $solicitud['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="verSolicitud(<?php echo $solicitud['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($solicitud['estado'] === 'Pendiente'): ?>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="aprobarSolicitud(<?php echo $solicitud['id']; ?>)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="rechazarSolicitud(<?php echo $solicitud['id']; ?>)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para ver solicitud -->
    <div class="modal fade" id="modalVerSolicitud" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>Detalles de la Solicitud
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalSolicitudContent">
                    <!-- Contenido cargado dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para rechazar solicitud -->
    <div class="modal fade" id="modalRechazarSolicitud" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-times me-2"></i>Rechazar Solicitud
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formRechazarSolicitud">
                        <input type="hidden" id="solicitudIdRechazar">
                        <div class="mb-3">
                            <label class="form-label">Motivo del rechazo:</label>
                            <textarea class="form-control" id="motivoRechazo" rows="4" required 
                                      placeholder="Explica el motivo del rechazo..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="confirmarRechazar()">
                        <i class="fas fa-times me-1"></i>Rechazar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notificaciones personalizadas -->
    <div id="notificationContainer"></div>
    
    <!-- Modal de confirmación personalizado -->
    <div class="custom-confirm-modal" id="customConfirmModal">
        <div class="confirm-header">
            <div class="confirm-title">
                <i class="fas fa-question-circle me-2"></i>Confirmar Acción
            </div>
            <div class="confirm-subtitle">Revisa los detalles antes de continuar</div>
        </div>
        <div class="confirm-body">
            <div class="confirm-message" id="confirmMessage">
                ¿Estás seguro de que deseas realizar esta acción?
            </div>
            <div class="confirm-actions">
                <button class="confirm-btn cancel" onclick="hideCustomConfirm()">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button class="confirm-btn success" id="confirmSuccessBtn">
                    <i class="fas fa-check me-1"></i>Confirmar
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="js/menu-mobile.js"></script>
    
    <script>
        // Variables globales para confirmación personalizada
        let confirmCallback = null;
        
        $(document).ready(function() {
            // Inicializar DataTable
            $('#tablaSolicitudes').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 25,
                order: [[5, 'desc']] // Ordenar por fecha de solicitud descendente
            });
            
            // Select all checkbox
            $('#selectAll').change(function() {
                $('.solicitud-checkbox').prop('checked', this.checked);
            });
        });
        
        // Función para mostrar notificaciones personalizadas
        function showNotification(type, title, message, details = null, duration = 5000) {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            const notificationId = 'notification-' + Date.now();
            
            notification.className = `custom-notification ${type}`;
            notification.id = notificationId;
            
            let icon = '';
            switch(type) {
                case 'success':
                    icon = 'fas fa-check-circle';
                    break;
                case 'error':
                    icon = 'fas fa-exclamation-circle';
                    break;
                case 'warning':
                    icon = 'fas fa-exclamation-triangle';
                    break;
                default:
                    icon = 'fas fa-info-circle';
            }
            
            notification.innerHTML = `
                <div class="notification-header ${type}">
                    <div class="notification-title">
                        <i class="${icon} notification-icon"></i>
                        ${title}
                    </div>
                    <button class="notification-close" onclick="closeNotification('${notificationId}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="notification-body">
                    <div class="notification-message">${message}</div>
                    ${details ? `<div class="notification-details ${type}">${details}</div>` : ''}
                    <div class="notification-actions">
                        <button class="notification-btn primary" onclick="closeNotification('${notificationId}')">
                            <i class="fas fa-check me-1"></i>Entendido
                        </button>
                    </div>
                </div>
                <div class="notification-progress">
                    <div class="notification-progress-bar"></div>
                </div>
            `;
            
            container.appendChild(notification);
            
            // Mostrar con animación
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            // Auto-cerrar después del tiempo especificado
            if (duration > 0) {
                setTimeout(() => {
                    closeNotification(notificationId);
                }, duration);
            }
        }
        
        // Función para cerrar notificaciones
        function closeNotification(notificationId) {
            const notification = document.getElementById(notificationId);
            if (notification) {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 500);
            }
        }
        
        // Función para mostrar confirmación personalizada
        function showCustomConfirm(message, callback) {
            confirmCallback = callback;
            document.getElementById('confirmMessage').innerHTML = message;
            document.getElementById('customConfirmModal').classList.add('show');
        }
        
        // Función para ocultar confirmación personalizada
        function hideCustomConfirm() {
            document.getElementById('customConfirmModal').classList.remove('show');
            confirmCallback = null;
        }
        
        // Configurar botón de confirmación
        document.getElementById('confirmSuccessBtn').addEventListener('click', function() {
            if (confirmCallback) {
                confirmCallback();
            }
            hideCustomConfirm();
        });
        
        function filtrarSolicitudes(estado) {
            const url = new URL(window.location);
            if (estado) {
                url.searchParams.set('estado', estado);
            } else {
                url.searchParams.delete('estado');
            }
            window.location.href = url.toString();
        }
        
        function verTodas() {
            window.location.href = 'solicitudes.php';
        }
        
        function verSolicitud(id) {
            // Cargar detalles de la solicitud via AJAX
            fetch(`ver_solicitud.php?id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalSolicitudContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('modalVerSolicitud')).show();
                    
                    // Agregar event listeners para los botones de descarga
                    document.querySelectorAll('[data-documento-id]').forEach(button => {
                        button.onclick = function() {
                            const documentoId = this.getAttribute('data-documento-id');
                            descargarDocumento(documentoId);
                        };
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los detalles de la solicitud');
                });
        }
        
        function descargarDocumento(documentoId) {
            // Mostrar indicador de carga
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Descargando...';
            button.disabled = true;
            
            // Crear un enlace temporal y hacer clic en él para descargar
            const link = document.createElement('a');
            link.href = `descargar_documento_solicitud.php?id=${documentoId}`;
            link.style.display = 'none';
            document.body.appendChild(link);
            
            // Intentar descarga
            link.click();
            
            // Limpiar después de un breve delay
            setTimeout(() => {
                document.body.removeChild(link);
                button.innerHTML = originalText;
                button.disabled = false;
            }, 1000);
        }
        
        function aprobarSolicitud(id) {
            const confirmMessage = `
                <div style="text-align: center;">
                    <i class="fas fa-user-check" style="font-size: 3em; color: var(--primary-green); margin-bottom: 15px;"></i>
                    <h4 style="color: #2c3e50; margin-bottom: 15px;">¿Aprobar Solicitud?</h4>
                    <p style="color: #666; line-height: 1.6;">
                        ¿Estás seguro de que deseas aprobar esta solicitud?<br>
                        <strong>El usuario será creado automáticamente</strong> y se enviará una notificación por email.
                    </p>
                    <div style="background: #f8f9fa; border-radius: 10px; padding: 15px; margin-top: 15px;">
                        <small style="color: #666;">
                            <i class="fas fa-info-circle me-1"></i>
                            Esta acción no se puede deshacer
                        </small>
                    </div>
                </div>
            `;
            
            showCustomConfirm(confirmMessage, function() {
                // Mostrar indicador de carga
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                button.disabled = true;
                
                // Mostrar notificación de procesamiento
                showNotification('info', 'Procesando', 'Aprobando solicitud y enviando notificación...', null, 0);
                
                fetch('procesar_solicitud_admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `accion=aprobar&solicitud_id=${id}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mostrar notificación de éxito
                        const emailStatus = data.email_enviado ? 
                            '<i class="fas fa-envelope me-1"></i>Email de notificación enviado exitosamente' : 
                            '<i class="fas fa-exclamation-triangle me-1"></i>Email no enviado (verificar configuración)';
                        
                        showNotification(
                            'success',
                            '<i class="fas fa-check-circle me-1"></i>Solicitud Aprobada',
                            'La solicitud ha sido aprobada exitosamente y el usuario ha sido creado en el sistema.',
                            emailStatus,
                            8000
                        );
                        
                        // Recargar página después de un breve delay
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showNotification(
                            'error',
                            '<i class="fas fa-exclamation-circle me-1"></i>Error',
                            'No se pudo aprobar la solicitud.',
                            data.message,
                            8000
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(
                        'error',
                        '<i class="fas fa-exclamation-circle me-1"></i>Error de Conexión',
                        'Error al procesar la solicitud.',
                        error.message,
                        8000
                    );
                })
                .finally(() => {
                    // Restaurar botón
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            });
        }
        
        function rechazarSolicitud(id) {
            document.getElementById('solicitudIdRechazar').value = id;
            new bootstrap.Modal(document.getElementById('modalRechazarSolicitud')).show();
        }
        
        function confirmarRechazar() {
            const id = document.getElementById('solicitudIdRechazar').value;
            const motivo = document.getElementById('motivoRechazo').value;
            
            if (!motivo.trim()) {
                showNotification(
                    'warning',
                    '<i class="fas fa-exclamation-triangle me-1"></i>Campo Requerido',
                    'Por favor ingresa el motivo del rechazo.',
                    'El motivo es obligatorio para rechazar una solicitud.',
                    5000
                );
                return;
            }
            
            // Mostrar indicador de carga
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            button.disabled = true;
            
            // Cerrar modal de rechazo
            bootstrap.Modal.getInstance(document.getElementById('modalRechazarSolicitud')).hide();
            
            // Mostrar notificación de procesamiento
            showNotification('info', 'Procesando', 'Rechazando solicitud...', null, 0);
            
            fetch('procesar_solicitud_admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `accion=rechazar&solicitud_id=${id}&motivo=${encodeURIComponent(motivo)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar notificación de éxito
                    const emailStatus = data.email_enviado ? 
                        '<i class="fas fa-envelope me-1"></i>Email de notificación enviado exitosamente' : 
                        '<i class="fas fa-exclamation-triangle me-1"></i>Email no enviado (verificar configuración)';
                    
                    showNotification(
                        'success',
                        '<i class="fas fa-check-circle me-1"></i>Solicitud Rechazada',
                        'La solicitud ha sido rechazada exitosamente.',
                        emailStatus,
                        8000
                    );
                    
                    // Recargar página después de un breve delay
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showNotification(
                        'error',
                        '<i class="fas fa-exclamation-circle me-1"></i>Error',
                        'No se pudo rechazar la solicitud.',
                        data.message,
                        8000
                    );
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification(
                    'error',
                    '<i class="fas fa-exclamation-circle me-1"></i>Error de Conexión',
                    'Error al procesar la solicitud.',
                    error.message,
                    8000
                );
            })
            .finally(() => {
                // Restaurar botón
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
        
        function aprobarSeleccionadas() {
            const seleccionadas = Array.from(document.querySelectorAll('.solicitud-checkbox:checked'))
                .map(cb => cb.value);
            
            if (seleccionadas.length === 0) {
                alert('Por favor selecciona al menos una solicitud');
                return;
            }
            
            if (confirm(`¿Estás seguro de que deseas aprobar ${seleccionadas.length} solicitud(es)?`)) {
                // Implementar aprobación múltiple
                alert('Función de aprobación múltiple en desarrollo');
            }
        }
        
        function exportarSolicitudes() {
            const estado = document.querySelector('select').value;
            const url = `exportar_solicitudes.php?estado=${estado}`;
            window.open(url, '_blank');
        }
    </script>
</body>
</html> 