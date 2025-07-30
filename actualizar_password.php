<?php
/**
 * Script para actualizar la contraseña del administrador
 * Ejecutar una sola vez para corregir la contraseña
 */

// Incluir configuración
require_once 'config.php';

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Generar hash correcto para "admin123"
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Actualizar la contraseña del administrador
    $sql = "UPDATE administradores SET password = ? WHERE usuario = 'admin'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$password_hash]);
    
    if ($stmt->rowCount() > 0) {
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Contraseña Actualizada</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light'>
            <div class='container mt-5'>
                <div class='row justify-content-center'>
                    <div class='col-md-6'>
                        <div class='card'>
                            <div class='card-header bg-success text-white'>
                                <h4 class='mb-0'>
                                    <i class='fas fa-check-circle me-2'></i>
                                    Contraseña Actualizada
                                </h4>
                            </div>
                            <div class='card-body'>
                                <div class='alert alert-success'>
                                    <i class='fas fa-check-circle me-2'></i>
                                    <strong>¡Éxito!</strong> La contraseña del administrador ha sido actualizada correctamente.
                                </div>
                                
                                <h5>Credenciales actualizadas:</h5>
                                <ul class='list-group list-group-flush mb-3'>
                                    <li class='list-group-item'><strong>Usuario:</strong> admin</li>
                                    <li class='list-group-item'><strong>Contraseña:</strong> admin123</li>
                                </ul>
                                
                                <div class='d-grid gap-2'>
                                    <a href='vista/login.php' class='btn btn-primary'>
                                        <i class='fas fa-sign-in-alt me-2'></i>
                                        Ir al Login
                                    </a>
                                    <a href='test.php' class='btn btn-info'>
                                        <i class='fas fa-cogs me-2'></i>
                                        Verificar Sistema
                                    </a>
                                </div>
                                
                                <div class='alert alert-warning mt-3'>
                                    <i class='fas fa-exclamation-triangle me-2'></i>
                                    <strong>Importante:</strong> Por seguridad, elimina este archivo después de usarlo.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    } else {
        throw new Exception("No se encontró el usuario 'admin' en la base de datos");
    }
    
} catch (Exception $e) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Error</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
    </head>
    <body class='bg-light'>
        <div class='container mt-5'>
            <div class='row justify-content-center'>
                <div class='col-md-6'>
                    <div class='card'>
                        <div class='card-header bg-danger text-white'>
                            <h4 class='mb-0'>
                                <i class='fas fa-exclamation-triangle me-2'></i>
                                Error
                            </h4>
                        </div>
                        <div class='card-body'>
                            <div class='alert alert-danger'>
                                <i class='fas fa-exclamation-triangle me-2'></i>
                                <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "
                            </div>
                            
                            <h5>Posibles soluciones:</h5>
                            <ul class='list-group list-group-flush mb-3'>
                                <li class='list-group-item'>Verifica que la base de datos 'gestion_usuarios' exista</li>
                                <li class='list-group-item'>Asegúrate de haber importado el archivo database.sql</li>
                                <li class='list-group-item'>Verifica que XAMPP esté iniciado (Apache y MySQL)</li>
                            </ul>
                            
                            <div class='d-grid gap-2'>
                                <a href='test.php' class='btn btn-info'>
                                    <i class='fas fa-cogs me-2'></i>
                                    Verificar Sistema
                                </a>
                                <a href='README.md' class='btn btn-secondary'>
                                    <i class='fas fa-book me-2'></i>
                                    Ver Documentación
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>";
}
?> 