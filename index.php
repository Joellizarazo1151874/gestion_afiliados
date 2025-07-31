<?php
/**
 * Archivo de entrada principal
 * Página de inicio pública con opciones para solicitar afiliación
 */

// Verificar si el usuario ya está logueado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id'])) {
    header('Location: vista/index.php');
    exit();
}

// Si no está logueado, mostrar página de inicio pública
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ORG_NOMBRE; ?> - Inicio</title>
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
            padding: 4rem 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .logo-container {
            margin-bottom: 2rem;
        }
        
        .logo-container img {
            max-height: 120px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        
        .main-container {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 1rem;
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--primary-red) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
            min-width: 200px;
            transition: all 0.3s ease;
        }
        
        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }
        
        /* Responsive para botones en móviles */
        @media (max-width: 768px) {
            .btn {
                margin: 0.75rem 0;
                min-width: 180px;
                display: block;
                width: 100%;
                max-width: 300px;
            }
            
            .btn-lg {
                padding: 1rem 2rem;
                margin: 1rem 0;
            }
            
            /* Contenedor de botones en header */
            .header .mt-4 {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
            
            /* Contenedor de botones en CTA */
            .d-flex.justify-content-center {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .btn {
                margin: 1rem 0;
                min-width: 160px;
                padding: 1.25rem 2rem;
            }
            
            .btn-lg {
                margin: 1rem 0;
            }
            
            .header .mt-4 {
                gap: 1.25rem;
            }
            
            .d-flex.justify-content-center {
                gap: 1.25rem;
            }
        }
        
        .footer {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--primary-red) 100%);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="logo-container">
                <img src="vista/img/logo.png" alt="Logo <?php echo ORG_NOMBRE; ?>">
            </div>
            <h1 class="display-3 fw-bold"><?php echo ORG_NOMBRE; ?></h1>
            <p class="lead fs-4">Organización dedicada al apoyo y empoderamiento de personas con discapacidad</p>
            <div class="mt-4">
                <a href="solicitud_afiliacion.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Solicitar Afiliación
                </a>
                <a href="verificar_solicitud.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-search me-2"></i>Verificar Estado
                </a>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Servicios -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="display-5 fw-bold">
                    <i class="fas fa-heart me-2"></i>Nuestros Servicios
                </h2>
                <p class="lead">Trabajamos para mejorar la calidad de vida de nuestros asociados</p>
            </div>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Apoyo Comunitario</h4>
                    <p>Conectamos personas con discapacidad para crear una red de apoyo mutuo y solidaridad.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4>Capacitación</h4>
                    <p>Ofrecemos programas de formación y desarrollo de habilidades para el empoderamiento.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h4>Asesoría Legal</h4>
                    <p>Brindamos orientación sobre derechos y recursos disponibles para personas con discapacidad.</p>
                </div>
            </div>
        </div>
        
        <!-- CTA Section -->
        <div class="row">
            <div class="col-12">
                <div class="feature-card">
                    <h3 class="mb-4">
                        <i class="fas fa-star me-2"></i>¿Quieres ser parte de nuestra comunidad?
                    </h3>
                    <p class="lead mb-4">
                        Únete a <?php echo ORG_NOMBRE; ?> y forma parte de una red de apoyo que trabaja por la inclusión 
                        y el empoderamiento de personas con discapacidad.
                    </p>
                    <div class="d-flex justify-content-center flex-wrap">
                        <a href="solicitud_afiliacion.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Solicitar Afiliación
                        </a>
                        <a href="verificar_solicitud.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Verificar mi Solicitud
                        </a>
                        <a href="vista/login.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Acceso Administrativo
                        </a>
                    </div>
                </div>
            </div>
        </div>
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
