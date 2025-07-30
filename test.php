<?php
/**
 * Archivo de prueba para verificar la configuración del sistema
 * Acceder a: http://localhost/gestionusuarios/test.php
 */

// Incluir inicialización
require_once 'init.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Prueba del Sistema - Familia unida por la discapacidad</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
</head>
<body class='bg-light'>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-8'>
                <div class='card'>
                    <div class='card-header bg-primary text-white'>
                        <h3 class='mb-0'>
                            <i class='fas fa-cogs me-2'></i>
                            Prueba de Configuración del Sistema
                        </h3>
                    </div>
                    <div class='card-body'>";

// Verificar configuración
echo "<h5><i class='fas fa-check-circle text-success me-2'></i>Verificando Configuración...</h5>";

$errors = [];
$success = [];

// 1. Verificar PHP
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    $success[] = "PHP " . PHP_VERSION . " ✓";
} else {
    $errors[] = "PHP " . PHP_VERSION . " (Se requiere 7.4 o superior)";
}

// 2. Verificar extensiones PHP
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        $success[] = "Extensión $ext ✓";
    } else {
        $errors[] = "Extensión $ext no está instalada";
    }
}

// 3. Verificar conexión a base de datos
try {
    $conexion = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $success[] = "Conexión a base de datos ✓";
    
    // Verificar tablas
    $tables = ['administradores', 'usuarios'];
    foreach ($tables as $table) {
        $stmt = $conexion->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $success[] = "Tabla $table existe ✓";
        } else {
            $errors[] = "Tabla $table no existe";
        }
    }
    
} catch (PDOException $e) {
    $errors[] = "Error de conexión a base de datos: " . $e->getMessage();
}

// 4. Verificar directorios
$directories = ['logs', 'reports', 'uploads'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $success[] = "Directorio $dir existe ✓";
    } else {
        if (mkdir($dir, 0755, true)) {
            $success[] = "Directorio $dir creado ✓";
        } else {
            $errors[] = "No se pudo crear el directorio $dir";
        }
    }
}

// 5. Verificar permisos de escritura
$writable_dirs = ['logs', 'reports', 'uploads'];
foreach ($writable_dirs as $dir) {
    if (is_writable($dir)) {
        $success[] = "Directorio $dir es escribible ✓";
    } else {
        $errors[] = "Directorio $dir no es escribible";
    }
}

// 6. Verificar sesiones
if (session_status() === PHP_SESSION_ACTIVE) {
    $success[] = "Sesiones funcionando ✓";
} else {
    $errors[] = "Problema con las sesiones";
}

// Mostrar resultados
if (empty($errors)) {
    echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle me-2'></i>
            <strong>¡Todo está configurado correctamente!</strong>
          </div>";
} else {
    echo "<div class='alert alert-danger'>
            <i class='fas fa-exclamation-triangle me-2'></i>
            <strong>Se encontraron algunos problemas:</strong>
          </div>";
}

// Mostrar éxitos
if (!empty($success)) {
    echo "<h6 class='text-success mt-3'>✓ Verificaciones exitosas:</h6>";
    echo "<ul class='list-group list-group-flush mb-3'>";
    foreach ($success as $item) {
        echo "<li class='list-group-item text-success'>$item</li>";
    }
    echo "</ul>";
}

// Mostrar errores
if (!empty($errors)) {
    echo "<h6 class='text-danger mt-3'>✗ Problemas encontrados:</h6>";
    echo "<ul class='list-group list-group-flush mb-3'>";
    foreach ($errors as $item) {
        echo "<li class='list-group-item text-danger'>$item</li>";
    }
    echo "</ul>";
}

// Información del sistema
echo "<h5 class='mt-4'><i class='fas fa-info-circle text-info me-2'></i>Información del Sistema</h5>";
$systemInfo = getSystemInfo();
echo "<div class='row'>";
foreach ($systemInfo as $key => $value) {
    echo "<div class='col-md-6 mb-2'>
            <strong>" . ucfirst(str_replace('_', ' ', $key)) . ":</strong> $value
          </div>";
}
echo "</div>";

// Enlaces útiles
echo "<h5 class='mt-4'><i class='fas fa-link text-primary me-2'></i>Enlaces Útiles</h5>";
echo "<div class='d-grid gap-2 d-md-block'>";
echo "<a href='vista/login.php' class='btn btn-primary me-2'>
        <i class='fas fa-sign-in-alt me-2'></i>Ir al Login
      </a>";
echo "<a href='vista/index.php' class='btn btn-success me-2'>
        <i class='fas fa-tachometer-alt me-2'></i>Dashboard
      </a>";
echo "<a href='README.md' class='btn btn-info me-2'>
        <i class='fas fa-book me-2'></i>Documentación
      </a>";
echo "</div>";

// Recomendaciones
if (!empty($errors)) {
    echo "<div class='alert alert-warning mt-4'>
            <h6><i class='fas fa-lightbulb me-2'></i>Recomendaciones:</h6>
            <ul class='mb-0'>
                <li>Verifica que XAMPP esté iniciado (Apache y MySQL)</li>
                <li>Asegúrate de que la base de datos 'gestion_usuarios' exista</li>
                <li>Importa el archivo database.sql en phpMyAdmin</li>
                <li>Verifica los permisos de escritura en los directorios</li>
            </ul>
          </div>";
}

echo "</div>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?> 