<?php
/**
 * Página para verificar el estado de una solicitud de afiliación
 */

require_once 'config.php';
require_once 'controlador/SolicitudController.php';

$controller = new SolicitudController();
$resultado = null;
$error = null;

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_solicitud = trim($_POST['numero_solicitud'] ?? '');
    
    if (empty($numero_solicitud)) {
        $error = 'Por favor ingresa el número de solicitud';
    } else {
        $resultado = $controller->verificarEstado($numero_solicitud);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Estado de Solicitud - <?php echo ORG_NOMBRE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="vista/img/logo.png">
    
    <style>
        :root {
            --primary-yellow: #FFD700;
            --primary-red: #DC143C;
            --primary-green: #32CD32;
        }
        
        body {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(220, 20, 60, 0.1) 50%, rgba(50, 205, 50, 0.1) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--primary-red) 50%, var(--primary-green) 100%);
            color: white;
            padding: 3rem 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .main-container {
            max-width: 800px;
            margin: 3rem auto;
            padding: 0 1rem;
        }
        
        /* Responsive para contenedor principal */
        @media (max-width: 768px) {
            .main-container {
                margin: 2rem auto;
                padding: 0 0.5rem;
            }
            
            .header {
                padding: 2rem 0;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .verification-card {
                margin-bottom: 3rem;
            }
        }
        
        @media (max-width: 480px) {
            .main-container {
                margin: 1.5rem auto;
                padding: 0 0.25rem;
            }
            
            .header {
                padding: 1.5rem 0;
            }
            
            .verification-card {
                margin-bottom: 2rem;
            }
        }
        
        .verification-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        /* Responsive para tarjetas */
        @media (max-width: 768px) {
            .verification-card {
                padding: 2rem;
                margin-bottom: 1.5rem;
                border-radius: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .verification-card {
                padding: 1.5rem;
                margin-bottom: 1rem;
            }
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        /* Responsive para campos de formulario */
        @media (max-width: 768px) {
            .form-control {
                padding: 1rem;
                font-size: 1rem;
            }
            
            .form-control-lg {
                padding: 1.25rem;
            }
        }
        
        .form-control:focus {
            border-color: var(--primary-yellow);
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--primary-red) 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-yellow);
            color: var(--primary-red);
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--primary-red) 100%);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
        }
        
        /* Mejoras para espaciado de botones */
        .btn {
            margin: 0.5rem;
            min-width: 180px;
            transition: all 0.3s ease;
        }
        
        /* Espaciado específico para botones en grupos */
        .button-group .btn {
            margin: 0;
        }
        
        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }
        
        /* Espaciado específico para grupos de botones */
        .button-group {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin: 2rem 0;
        }
        
        /* Responsive para botones en móviles */
        @media (max-width: 768px) {
            .btn {
                min-width: 160px;
                margin: 1.5rem 0;
                padding: 1.25rem 2rem;
                display: block;
                width: 100%;
                max-width: 320px;
            }
            
            .button-group {
                gap: 2.5rem;
                flex-direction: column;
                align-items: center;
                margin: 2rem 0;
            }
            
            .btn-lg {
                width: 100%;
                max-width: 320px;
            }
        }
        
        /* Espaciado extra para pantallas muy pequeñas */
        @media (max-width: 480px) {
            .btn {
                margin: 2rem 0;
                min-width: 140px;
                padding: 1.5rem 2rem;
            }
            
            .button-group {
                gap: 3rem;
                margin: 3rem 0;
            }
            
            .verification-card {
                padding: 1.5rem;
            }
        }
        
        /* Espaciado extra para pantallas extra pequeñas */
        @media (max-width: 360px) {
            .btn {
                margin: 2.5rem 0;
                padding: 1.75rem 2rem;
            }
            
            .button-group {
                gap: 3.5rem;
                margin: 3.5rem 0;
            }
        }
        
        .status-card {
            border-radius: 15px;
            padding: 2rem;
            margin: 1.5rem 0;
            border-left: 5px solid;
        }
        
        .status-pendiente {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-left-color: var(--primary-yellow);
        }
        
        .status-aprobada {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-left-color: var(--primary-green);
        }
        
        .status-rechazada {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border-left-color: var(--primary-red);
        }
        
        .status-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .status-pendiente .status-icon {
            color: var(--primary-yellow);
        }
        
        .status-aprobada .status-icon {
            color: var(--primary-green);
        }
        
        .status-rechazada .status-icon {
            color: var(--primary-red);
        }
        
        .info-list {
            list-style: none;
            padding: 0;
        }
        
        .info-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .info-list li:last-child {
            border-bottom: none;
        }
        
        .info-list strong {
            color: #2c3e50;
        }
        
        .footer {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--primary-red) 100%);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <h1 class="display-4 fw-bold">
                <i class="fas fa-search me-3"></i>Verificar Estado de Solicitud
            </h1>
            <p class="lead fs-5">Consulta el estado de tu solicitud de afiliación</p>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Formulario de verificación -->
        <div class="verification-card">
            <div class="text-center mb-4">
                <h2 class="mb-3">
                    <i class="fas fa-clipboard-check me-2"></i>Consulta tu Solicitud
                </h2>
                <p class="text-muted">Ingresa el número de solicitud que recibiste al momento de enviar tu afiliación</p>
            </div>
            
            <form method="POST" action="">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <label for="numero_solicitud" class="form-label fw-bold">
                                <i class="fas fa-hashtag me-2"></i>Número de Solicitud
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="numero_solicitud" 
                                   name="numero_solicitud" 
                                   placeholder="Ej: SOL000001"
                                   value="<?php echo htmlspecialchars($_POST['numero_solicitud'] ?? ''); ?>"
                                   required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                El número de solicitud se te envió por email al momento de registrar tu afiliación
                            </div>
                        </div>
                        
                        <div class="button-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search me-2"></i>Verificar Estado
                            </button>
                            <a href="index.php" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-home me-2"></i>Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Resultado de la verificación -->
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h5 class="alert-heading">Error en la consulta</h5>
                        <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($resultado && !$resultado['success']): ?>
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h5 class="alert-heading">Solicitud no encontrada</h5>
                        <p class="mb-0"><?php echo htmlspecialchars($resultado['message']); ?></p>
                        <hr>
                        <p class="mb-0">
                            <strong>Sugerencias:</strong>
                        </p>
                        <ul class="mb-0 mt-2">
                            <li>Verifica que el número de solicitud esté escrito correctamente</li>
                            <li>Revisa tu email para encontrar el número de solicitud</li>
                            <li>Si no encuentras el número, puedes <a href="solicitud_afiliacion.php" class="alert-link">presentar una nueva solicitud</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($resultado && $resultado['success']): ?>
            <?php $solicitud = $resultado['solicitud']; ?>
            <div class="verification-card">
                <div class="text-center mb-4">
                    <h3 class="mb-3">
                        <i class="fas fa-clipboard-list me-2"></i>Estado de tu Solicitud
                    </h3>
                </div>
                
                <!-- Estado de la solicitud -->
                <div class="status-card status-<?php echo strtolower($solicitud['estado']); ?>">
                    <div class="text-center">
                        <?php
                        $icono = '';
                        $titulo = '';
                        $descripcion = '';
                        
                        switch($solicitud['estado']) {
                            case 'Pendiente':
                                $icono = 'fas fa-clock';
                                $titulo = 'Solicitud en Revisión';
                                $descripcion = 'Tu solicitud está siendo revisada por nuestro equipo. Te notificaremos cuando tengamos una respuesta.';
                                break;
                            case 'Aprobada':
                                $icono = 'fas fa-check-circle';
                                $titulo = '¡Solicitud Aprobada!';
                                $descripcion = '¡Felicitaciones! Tu solicitud ha sido aprobada. Ya eres miembro de nuestra organización.';
                                break;
                            case 'Rechazada':
                                $icono = 'fas fa-times-circle';
                                $titulo = 'Solicitud Rechazada';
                                $descripcion = 'Tu solicitud no fue aprobada en esta oportunidad. Revisa los detalles más abajo.';
                                break;
                        }
                        ?>
                        
                        <div class="status-icon">
                            <i class="<?php echo $icono; ?>"></i>
                        </div>
                        <h4 class="mb-3"><?php echo $titulo; ?></h4>
                        <p class="lead mb-0"><?php echo $descripcion; ?></p>
                    </div>
                </div>
                
                <!-- Información detallada -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5 class="mb-3">
                            <i class="fas fa-user me-2"></i>Información Personal
                        </h5>
                        <ul class="info-list">
                            <li>
                                <span>Nombre completo:</span>
                                <strong><?php echo htmlspecialchars($solicitud['nombre'] . ' ' . $solicitud['apellidos']); ?></strong>
                            </li>
                            <li>
                                <span>Email:</span>
                                <strong><?php echo htmlspecialchars($solicitud['email']); ?></strong>
                            </li>
                            <li>
                                <span>Teléfono:</span>
                                <strong><?php echo htmlspecialchars($solicitud['telefono']); ?></strong>
                            </li>
                            <li>
                                <span>Fecha de nacimiento:</span>
                                <strong><?php echo date('d/m/Y', strtotime($solicitud['fecha_nacimiento'])); ?></strong>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="mb-3">
                            <i class="fas fa-clipboard me-2"></i>Detalles de la Solicitud
                        </h5>
                        <ul class="info-list">
                            <li>
                                <span>Número de solicitud:</span>
                                <strong><?php echo htmlspecialchars($solicitud['numero_solicitud']); ?></strong>
                            </li>
                            <li>
                                <span>Fecha de solicitud:</span>
                                <strong><?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])); ?></strong>
                            </li>
                            <li>
                                <span>Tipo de discapacidad:</span>
                                <strong><?php echo htmlspecialchars($solicitud['tipo_discapacidad']); ?></strong>
                            </li>
                            <li>
                                <span>Porcentaje de discapacidad:</span>
                                <strong><?php echo htmlspecialchars($solicitud['porcentaje_discapacidad']); ?>%</strong>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <?php if ($solicitud['estado'] === 'Rechazada' && !empty($solicitud['motivo_rechazo'])): ?>
                    <div class="mt-4">
                        <h5 class="mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>Motivo del Rechazo
                        </h5>
                        <div class="alert alert-danger">
                            <p class="mb-0"><strong><?php echo htmlspecialchars($solicitud['motivo_rechazo']); ?></strong></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Acciones según el estado -->
                <div class="text-center mt-4">
                    <?php if ($solicitud['estado'] === 'Pendiente'): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Tu solicitud está siendo procesada.</strong> Te enviaremos una notificación por email cuando tengamos una respuesta.
                        </div>
                    <?php elseif ($solicitud['estado'] === 'Aprobada'): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>¡Bienvenido/a a nuestra organización!</strong> Ya puedes disfrutar de todos los beneficios de ser miembro.
                        </div>
                    <?php elseif ($solicitud['estado'] === 'Rechazada'): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>No te desanimes.</strong> Puedes corregir la información y presentar una nueva solicitud cuando estés listo/a.
                        </div>
                    <?php endif; ?>
                    
                    <div class="button-group">
                        <a href="solicitud_afiliacion.php" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>Nueva Solicitud
                        </a>
                        <a href="index.php" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Volver al Inicio
                        </a>
                        <a href="mailto:<?php echo ORG_EMAIL; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>Contactar
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo ORG_NOMBRE; ?>. Todos los derechos reservados.</p>
            <p><i class="fas fa-envelope me-2"></i><?php echo ORG_EMAIL; ?></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 