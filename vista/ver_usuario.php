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
    <title>Ver Usuario - Familia unida por la discapacidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        
        /* Responsive para móviles */
        @media (max-width: 768px) {
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
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-circle me-2"></i>
                                        Información Personal
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Número de Asociado:</label>
                                                <p class="info-value"><?php echo htmlspecialchars($usuario['numero_asociado']); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Estado:</label>
                                                <p class="info-value">
                                                    <span class="badge bg-<?php 
                                                        echo $usuario['estado'] === 'Activo' ? 'success' : 
                                                            ($usuario['estado'] === 'Inactivo' ? 'warning' : 'danger'); 
                                                    ?>">
                                                        <?php echo htmlspecialchars($usuario['estado']); ?>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Nombre:</label>
                                                <p class="info-value"><?php echo htmlspecialchars($usuario['nombre']); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Apellidos:</label>
                                                <p class="info-value"><?php echo htmlspecialchars($usuario['apellidos']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Fecha de Nacimiento:</label>
                                                <p class="info-value"><?php echo date('d/m/Y', strtotime($usuario['fecha_nacimiento'])); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Género:</label>
                                                <p class="info-value"><?php echo htmlspecialchars($usuario['genero']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Tipo de Discapacidad:</label>
                                                <p class="info-value"><?php echo htmlspecialchars($usuario['tipo_discapacidad']); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Porcentaje de Discapacidad:</label>
                                                <p class="info-value">
                                                    <?php echo $usuario['porcentaje_discapacidad'] ? $usuario['porcentaje_discapacidad'] . '%' : 'No especificado'; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-address-book me-2"></i>
                                        Información de Contacto
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Teléfono:</label>
                                                <p class="info-value">
                                                    <?php echo $usuario['telefono'] ? htmlspecialchars($usuario['telefono']) : 'No especificado'; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Email:</label>
                                                <p class="info-value">
                                                    <?php echo $usuario['email'] ? htmlspecialchars($usuario['email']) : 'No especificado'; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="info-label">Dirección:</label>
                                        <p class="info-value">
                                            <?php echo $usuario['direccion'] ? htmlspecialchars($usuario['direccion']) : 'No especificada'; ?>
                                        </p>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Ciudad:</label>
                                                <p class="info-value">
                                                    <?php echo $usuario['ciudad'] ? htmlspecialchars($usuario['ciudad']) : 'No especificada'; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="info-label">Código Postal:</label>
                                                <p class="info-value">
                                                    <?php echo $usuario['codigo_postal'] ? htmlspecialchars($usuario['codigo_postal']) : 'No especificado'; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Información de Afiliación -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        Información de Afiliación
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="info-label">Fecha de Afiliación:</label>
                                        <p class="info-value"><?php echo date('d/m/Y', strtotime($usuario['fecha_afiliacion'])); ?></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="info-label">Fecha de Registro:</label>
                                        <p class="info-value"><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_registro'])); ?></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="info-label">Última Actualización:</label>
                                        <p class="info-value"><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_actualizacion'])); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <?php if ($usuario['observaciones']): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        Observaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="info-value"><?php echo nl2br(htmlspecialchars($usuario['observaciones'])); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 