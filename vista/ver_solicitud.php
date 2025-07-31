<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin_id'])) {
    echo '<div class="alert alert-danger">No autorizado</div>';
    exit;
}

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/controlador/SolicitudController.php';

$solicitud_id = intval($_GET['id'] ?? 0);

if (!$solicitud_id) {
    echo '<div class="alert alert-danger">ID de solicitud no válido</div>';
    exit;
}

try {
    $controller = new SolicitudController();
    $solicitud = $controller->obtenerSolicitud($solicitud_id);
    
    if (!$solicitud) {
        echo '<div class="alert alert-danger">Solicitud no encontrada</div>';
        exit;
    }
    
    // Obtener documentos de la solicitud
    $documentos = [];
    if (class_exists('Documento')) {
        $documento = new Documento();
        $documentos = $documento->obtenerDocumentosSolicitud($solicitud_id);
    }
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error al cargar la solicitud: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit;
}
?>

<div class="row">
    <div class="col-md-6">
        <h5><i class="fas fa-user me-2"></i>Información Personal</h5>
        <table class="table table-sm">
            <tr>
                <td><strong>Número de Solicitud:</strong></td>
                <td><?php echo htmlspecialchars($solicitud['numero_solicitud']); ?></td>
            </tr>
            <tr>
                <td><strong>Nombre:</strong></td>
                <td><?php echo htmlspecialchars($solicitud['nombre'] . ' ' . $solicitud['apellidos']); ?></td>
            </tr>
            <tr>
                <td><strong>Fecha de Nacimiento:</strong></td>
                <td><?php echo date('d/m/Y', strtotime($solicitud['fecha_nacimiento'])); ?></td>
            </tr>
            <tr>
                <td><strong>Género:</strong></td>
                <td><?php echo htmlspecialchars($solicitud['genero']); ?></td>
            </tr>
            <tr>
                <td><strong>Tipo de Discapacidad:</strong></td>
                <td><?php echo htmlspecialchars($solicitud['tipo_discapacidad']); ?></td>
            </tr>
            <tr>
                <td><strong>Porcentaje de Discapacidad:</strong></td>
                <td><?php echo $solicitud['porcentaje_discapacidad']; ?>%</td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h5><i class="fas fa-address-book me-2"></i>Información de Contacto</h5>
        <table class="table table-sm">
            <tr>
                <td><strong>Teléfono:</strong></td>
                <td><?php echo htmlspecialchars($solicitud['telefono']); ?></td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td><a href="mailto:<?php echo htmlspecialchars($solicitud['email']); ?>"><?php echo htmlspecialchars($solicitud['email']); ?></a></td>
            </tr>
            <tr>
                <td><strong>Dirección:</strong></td>
                <td><?php echo nl2br(htmlspecialchars($solicitud['direccion'])); ?></td>
            </tr>
            <tr>
                <td><strong>Ciudad:</strong></td>
                <td><?php echo htmlspecialchars($solicitud['ciudad']); ?></td>
            </tr>
            <tr>
                <td><strong>Código Postal:</strong></td>
                <td><?php echo htmlspecialchars($solicitud['codigo_postal']); ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <h5><i class="fas fa-info-circle me-2"></i>Información Adicional</h5>
        <table class="table table-sm">
            <tr>
                <td><strong>Fecha de Solicitud:</strong></td>
                <td><?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])); ?></td>
            </tr>
            <tr>
                <td><strong>Estado:</strong></td>
                <td>
                    <?php
                    $badge_class = '';
                    switch ($solicitud['estado']) {
                        case 'Pendiente':
                            $badge_class = 'badge bg-warning';
                            break;
                        case 'Aprobada':
                            $badge_class = 'badge bg-success';
                            break;
                        case 'Rechazada':
                            $badge_class = 'badge bg-danger';
                            break;
                    }
                    ?>
                    <span class="<?php echo $badge_class; ?>"><?php echo $solicitud['estado']; ?></span>
                </td>
            </tr>
            <?php if ($solicitud['fecha_revision']): ?>
            <tr>
                <td><strong>Fecha de Revisión:</strong></td>
                <td><?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_revision'])); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($solicitud['motivo_rechazo']): ?>
            <tr>
                <td><strong>Motivo de Rechazo:</strong></td>
                <td class="text-danger"><?php echo nl2br(htmlspecialchars($solicitud['motivo_rechazo'])); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($solicitud['observaciones']): ?>
            <tr>
                <td><strong>Observaciones:</strong></td>
                <td><?php echo nl2br(htmlspecialchars($solicitud['observaciones'])); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php if (!empty($documentos)): ?>
<div class="row mt-3">
    <div class="col-12">
        <h5><i class="fas fa-file-upload me-2"></i>Documentos Cargados</h5>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Tipo de Documento</th>
                        <th>Nombre del Archivo</th>
                        <th>Fecha de Carga</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documentos as $documento): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($documento['tipo_documento']); ?></td>
                        <td><?php echo htmlspecialchars($documento['nombre_archivo']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($documento['fecha_carga'])); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    data-documento-id="<?php echo $documento['id']; ?>">
                                <i class="fas fa-download"></i> Descargar
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            No se encontraron documentos cargados para esta solicitud.
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($solicitud['estado'] === 'Pendiente'): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" onclick="aprobarSolicitud(<?php echo $solicitud['id']; ?>)">
                <i class="fas fa-check me-1"></i>Aprobar Solicitud
            </button>
            <button type="button" class="btn btn-danger" onclick="rechazarSolicitud(<?php echo $solicitud['id']; ?>)">
                <i class="fas fa-times me-1"></i>Rechazar Solicitud
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

 