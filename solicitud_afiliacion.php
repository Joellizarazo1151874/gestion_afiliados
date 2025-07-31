<?php
require_once 'config.php';
require_once 'modelo/Usuario.php';

// Obtener tipos de discapacidad para el formulario
$usuario = new Usuario();
$tipos_discapacidad = $usuario->obtenerTiposDiscapacidad();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Afiliación - <?php echo ORG_NOMBRE; ?></title>
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
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .logo-container {
            margin-bottom: 1rem;
        }
        
        .logo-container img {
            max-height: 80px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .form-header {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--primary-red) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .form-body {
            padding: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
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
        
        .section-title {
            color: var(--primary-red);
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid var(--primary-yellow);
        }
        
        .file-upload-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .file-upload-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
            z-index: 2;
        }
        
        .file-upload-label {
            display: block;
            width: 100%;
            min-height: 120px;
            border: 3px dashed var(--primary-yellow);
            border-radius: 15px;
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(220, 20, 60, 0.1) 100%);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .file-upload-label:hover {
            border-color: var(--primary-red);
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.2) 0%, rgba(220, 20, 60, 0.2) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
        }
        
        .file-upload-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            text-align: center;
            color: #495057;
        }
        
        .file-upload-text {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .file-upload-hint {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .file-preview {
            margin-top: 0.5rem;
            padding: 0.5rem;
            border-radius: 8px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            display: none;
        }
        
        .file-preview.show {
            display: block;
        }
        
        .document-description {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.1) 0%, rgba(220, 20, 60, 0.05) 100%);
            border-left: 4px solid var(--primary-yellow);
            padding: 0.75rem;
            margin-top: 0.5rem;
            border-radius: 0 8px 8px 0;
            font-size: 0.9rem;
            line-height: 1.4;
            color: #495057;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .document-description strong {
            color: var(--primary-red);
        }
        
        .document-description i {
            color: var(--primary-green);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(50, 205, 50, 0.1) 0%, rgba(34, 139, 34, 0.1) 100%);
            color: #155724;
            border-left: 4px solid var(--primary-green);
        }
        
        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 20, 60, 0.1) 0%, rgba(139, 0, 0, 0.1) 100%);
            color: #721c24;
            border-left: 4px solid var(--primary-red);
        }
        
        .footer {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, var(--primary-red) 100%);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .form-body {
                padding: 1rem;
            }
            
            .file-upload-label {
                min-height: 100px;
            }
            
            .file-upload-content {
                padding: 1.5rem 0.5rem;
            }
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
            <h1 class="display-4 fw-bold">Solicitud de Afiliación</h1>
            <p class="lead">Únete a <?php echo ORG_NOMBRE; ?> y forma parte de nuestra comunidad</p>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>¡Solicitud enviada exitosamente!</strong> Tu número de solicitud es: <strong><?php echo htmlspecialchars($_GET['numero']); ?></strong>
                <br>Te notificaremos por email cuando tu solicitud sea revisada.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulario de Solicitud -->
        <div class="form-card">
            <div class="form-header">
                <h2><i class="fas fa-user-plus me-2"></i>Formulario de Solicitud</h2>
                <p>Completa todos los campos obligatorios y adjunta los documentos requeridos</p>
            </div>
            
            <div class="form-body">
                <form action="procesar_solicitud.php" method="POST" enctype="multipart/form-data" id="formSolicitud">
                    
                    <!-- Información Personal -->
                    <h3 class="section-title">
                        <i class="fas fa-user me-2"></i>Información Personal
                    </h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-user me-1"></i>Nombre
                                </label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-user me-1"></i>Apellidos
                                </label>
                                <input type="text" class="form-control" name="apellidos" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-calendar me-1"></i>Fecha de Nacimiento
                                </label>
                                <input type="date" class="form-control" name="fecha_nacimiento" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-venus-mars me-1"></i>Género
                                </label>
                                <select class="form-select" name="genero" required>
                                    <option value="">Seleccionar género</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-percentage me-1"></i>Porcentaje de Discapacidad
                                </label>
                                <input type="number" class="form-control" name="porcentaje_discapacidad" min="0" max="100" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-wheelchair me-1"></i>Tipo de Discapacidad
                                </label>
                                <select class="form-select" name="tipo_discapacidad" required>
                                    <option value="">Seleccionar tipo de discapacidad</option>
                                    <?php foreach ($tipos_discapacidad as $tipo): ?>
                                        <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" id="nuevoTipoContainer" style="display: none;">
                                <label class="form-label">
                                    <i class="fas fa-plus-circle me-1"></i>Agregar nuevo tipo
                                </label>
                                <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalNuevoTipoDiscapacidad">
                                    <i class="fas fa-plus me-1"></i>Nuevo Tipo de Discapacidad
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información de Contacto -->
                    <h3 class="section-title">
                        <i class="fas fa-address-book me-2"></i>Información de Contacto
                    </h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-phone me-1"></i>Teléfono
                                </label>
                                <input type="tel" class="form-control" name="telefono" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-envelope me-1"></i>Email
                                </label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-map-marker-alt me-1"></i>Dirección
                                </label>
                                <textarea class="form-control" name="direccion" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-city me-1"></i>Ciudad
                                </label>
                                <input type="text" class="form-control" name="ciudad" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-mail-bulk me-1"></i>Código Postal
                                </label>
                                <input type="text" class="form-control" name="codigo_postal" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-comment me-1"></i>Observaciones
                        </label>
                        <textarea class="form-control" name="observaciones" rows="3" placeholder="Información adicional que consideres relevante..."></textarea>
                    </div>
                    
                    <!-- Documentos -->
                    <h3 class="section-title">
                        <i class="fas fa-file-upload me-2"></i>Documentos Requeridos
                    </h3>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Los campos marcados con <span class="badge bg-danger">OBLIGATORIO</span> son obligatorios. 
                        Los campos marcados con <span class="badge bg-secondary">OPCIONAL</span> son opcionales.
                        Todos los archivos deben tener un tamaño máximo de 5MB.
                    </div>
                    
                    <!-- Documentos Obligatorios -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-camera me-1"></i>Foto tipo carnet del Asociado
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[foto_carnet]" accept="image/*,.pdf" required id="foto_carnet">
                                    <label for="foto_carnet" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="foto_carnet-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: JPG, PNG, PDF (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Foto tipo carnet del asociado, debe ser una imagen clara del rostro con fondo neutro, similar a una foto de identificación oficial.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-id-card me-1"></i>Cédula del Asociado
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[cedula_asociado]" accept="image/*,.pdf" required id="cedula_asociado">
                                    <label for="cedula_asociado" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="cedula_asociado-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: JPG, PNG, PDF (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Fotocopia de la cédula de ciudadanía del asociado, ambas caras del documento deben ser visibles y legibles.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-user me-1"></i>Foto cuerpo entero del Asociado
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[foto_cuerpo]" accept="image/*,.pdf" required id="foto_cuerpo">
                                    <label for="foto_cuerpo" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="foto_cuerpo-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: JPG, PNG, PDF (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Foto de cuerpo entero del asociado, debe mostrar claramente la condición física y cualquier dispositivo de apoyo que utilice.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-secondary me-2">OPCIONAL</span>
                                    <i class="fas fa-user-tie me-1"></i>Cédula del representante legal
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[cedula_representante]" accept="image/*,.pdf" id="cedula_representante">
                                    <label for="cedula_representante" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="cedula_representante-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: JPG, PNG, PDF (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Fotocopia de la cédula del representante legal (solo si aplica), ambas caras del documento deben ser visibles y legibles.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-file-alt me-1"></i>Hoja de Vida
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[hoja_vida]" accept=".pdf,.doc,.docx" required id="hoja_vida">
                                    <label for="hoja_vida" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="hoja_vida-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: PDF, DOC, DOCX (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Hoja de vida actualizada del asociado o del representante legal, incluyendo experiencia laboral, educación y referencias personales.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-certificate me-1"></i>Certificado de Discapacidad
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[certificado_discapacidad]" accept="image/*,.pdf" required id="certificado_discapacidad">
                                    <label for="certificado_discapacidad" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="certificado_discapacidad-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: JPG, PNG, PDF (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Certificado médico oficial que acredite la discapacidad, emitido por una institución de salud autorizada con el porcentaje de discapacidad.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-secondary me-2">OPCIONAL</span>
                                    <i class="fas fa-notes-medical me-1"></i>Historia médica
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[historia_medica]" accept="image/*,.pdf" id="historia_medica">
                                    <label for="historia_medica" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="historia_medica-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: JPG, PNG, PDF (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Historia clínica médica del asociado (solo si aplica), incluyendo diagnósticos, tratamientos y evolución de la condición de salud.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-clipboard-list me-1"></i>Caracterización
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[caracterizacion]" accept="image/*,.pdf" required id="caracterizacion">
                                    <label for="caracterizacion" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="caracterizacion-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: JPG, PNG, PDF (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Documento de caracterización socioeconómica emitido por entidades oficiales, que determine el nivel socioeconómico del asociado.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-file-medical me-1"></i>SISBEN
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[sisben]" accept="image/*,.pdf" required id="sisben">
                                    <label for="sisben" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="sisben-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: JPG, PNG, PDF (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Certificado del SISBEN (Sistema de Identificación de Potenciales Beneficiarios de Programas Sociales) con el puntaje actualizado.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-file-medical me-1"></i>FOSYGA
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[fosyga]" accept="image/*,.pdf" required id="fosyga">
                                    <label for="fosyga" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="fosyga-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: JPG, PNG, PDF (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Certificado del FOSYGA (Fondo de Solidaridad y Garantía) que acredite la afiliación al sistema de seguridad social en salud.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-secondary me-2">OPCIONAL</span>
                                    <i class="fas fa-file-signature me-1"></i>Renuncia anterior
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[renuncia_anterior]" accept="image/*,.pdf" id="renuncia_anterior">
                                    <label for="renuncia_anterior" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="renuncia_anterior-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: JPG, PNG, PDF (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Documento de renuncia a organización anterior (solo si aplica), que acredite que no pertenece a otra organización de personas con discapacidad.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <span class="badge bg-danger me-2">OBLIGATORIO</span>
                                    <i class="fas fa-handshake me-1"></i>Declaración de estatutos
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="documentos[declaracion_estatutos]" accept=".pdf,.doc,.docx" required id="declaracion_estatutos">
                                    <label for="declaracion_estatutos" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <span class="file-upload-text">Haz clic para seleccionar archivo</span>
                                            <span class="file-upload-hint">o arrastra y suelta aquí</span>
                                        </div>
                                    </label>
                                    <div class="file-preview" id="declaracion_estatutos-preview"></div>
                                </div>
                                <small class="text-muted">Formatos: PDF, DOC, DOCX (máx. 5MB)</small>
                                <div class="document-description">
                                    <i class="fas fa-info-circle text-info me-1"></i>
                                    <strong>Descripción:</strong> Declaración firmada donde el asociado manifiesta conocer y aceptar los estatutos, reglamentos y políticas de la organización.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón de envío -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud de Afiliación
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Información adicional -->
        <div class="form-card">
            <div class="form-header">
                <h3><i class="fas fa-info-circle me-2"></i>Información Importante</h3>
            </div>
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-clock me-2"></i>Proceso de Revisión</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Tu solicitud será revisada por nuestro equipo</li>
                            <li><i class="fas fa-check text-success me-2"></i>Recibirás una notificación por email</li>
                            <li><i class="fas fa-check text-success me-2"></i>El proceso puede tomar 3-5 días hábiles</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-shield-alt me-2"></i>Protección de Datos</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-lock text-success me-2"></i>Tus datos están protegidos</li>
                            <li><i class="fas fa-lock text-success me-2"></i>Solo se usan para fines de afiliación</li>
                            <li><i class="fas fa-lock text-success me-2"></i>Cumplimos con la normativa vigente</li>
                        </ul>
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

    <!-- Modal para nuevo tipo de discapacidad -->
    <div class="modal fade" id="modalNuevoTipoDiscapacidad" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Tipo de Discapacidad
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nuevo tipo de discapacidad:</label>
                        <input type="text" class="form-control" id="nuevoTipoDiscapacidad" placeholder="Ej: Discapacidad auditiva">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="agregarNuevoTipo()">
                        <i class="fas fa-plus me-1"></i>Agregar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar funcionalidad de carga de archivos
        initializeFileUploads();
        
        function initializeFileUploads() {
            const fileInputs = document.querySelectorAll('.file-upload-input');
            
            fileInputs.forEach(input => {
                const wrapper = input.closest('.file-upload-wrapper');
                const label = wrapper.querySelector('.file-upload-label');
                const preview = wrapper.querySelector('.file-preview');
                
                // Manejar cambio de archivo
                input.addEventListener('change', function(e) {
                    handleFileSelect(this, e.target.files[0]);
                });
                
                // Drag and drop
                label.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('dragover');
                });
                
                label.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.classList.remove('dragover');
                });
                
                label.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('dragover');
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        input.files = files;
                        handleFileSelect(input, files[0]);
                    }
                });
            });
        }
        
        function handleFileSelect(input, file) {
            if (!file) return;
            
            const wrapper = input.closest('.file-upload-wrapper');
            const preview = wrapper.querySelector('.file-preview');
            const label = wrapper.querySelector('.file-upload-label');
            
            // Validar tamaño (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('El archivo es demasiado grande. Máximo 5MB.');
                input.value = '';
                return;
            }
            
            // Validar tipo de archivo
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                alert('Tipo de archivo no permitido. Use JPG, PNG, PDF, DOC o DOCX.');
                input.value = '';
                return;
            }
            
            // Mostrar preview
            showFilePreview(preview, file);
            
            // Actualizar label
            label.classList.add('file-selected');
            label.querySelector('.file-upload-text').textContent = 'Archivo seleccionado';
            label.querySelector('.file-upload-hint').textContent = file.name;
        }
        
        function showFilePreview(preview, file) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const fileIcon = getFileIcon(file.type);
            
            preview.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <div class="text-success">
                        <i class="${fileIcon} fa-2x"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${file.name}</div>
                        <div class="text-muted small">${fileSize} MB</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            preview.classList.add('show');
        }
        
        function getFileIcon(fileType) {
            if (fileType.startsWith('image/')) {
                return 'fas fa-image';
            } else if (fileType === 'application/pdf') {
                return 'fas fa-file-pdf';
            } else if (fileType.includes('word')) {
                return 'fas fa-file-word';
            } else {
                return 'fas fa-file';
            }
        }
        
        function removeFile(button) {
            const preview = button.closest('.file-preview');
            const wrapper = preview.closest('.file-upload-wrapper');
            const input = wrapper.querySelector('.file-upload-input');
            const label = wrapper.querySelector('.file-upload-label');
            
            input.value = '';
            preview.classList.remove('show');
            preview.innerHTML = '';
            
            // Restaurar label original
            label.classList.remove('file-selected');
            label.querySelector('.file-upload-text').textContent = 'Haz clic para seleccionar archivo';
            label.querySelector('.file-upload-hint').textContent = 'o arrastra y suelta aquí';
        }
        
        // Controlar visibilidad del botón de agregar nuevo tipo
        document.querySelector('select[name="tipo_discapacidad"]').addEventListener('change', function() {
            const nuevoTipoContainer = document.getElementById('nuevoTipoContainer');
            if (this.value === 'Otra') {
                nuevoTipoContainer.style.display = 'block';
            } else {
                nuevoTipoContainer.style.display = 'none';
            }
        });
        
        function agregarNuevoTipo() {
            const nuevoTipo = document.getElementById('nuevoTipoDiscapacidad').value.trim();
            if (!nuevoTipo) {
                alert('Por favor ingresa un tipo de discapacidad');
                return;
            }
            
            const select = document.querySelector('select[name="tipo_discapacidad"]');
            const option = document.createElement('option');
            option.value = nuevoTipo;
            option.textContent = nuevoTipo;
            select.appendChild(option);
            select.value = nuevoTipo;
            
            // Ocultar el botón después de agregar el nuevo tipo
            document.getElementById('nuevoTipoContainer').style.display = 'none';
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoTipoDiscapacidad'));
            modal.hide();
            
            // Limpiar campo
            document.getElementById('nuevoTipoDiscapacidad').value = '';
        }
    </script>
</body>
</html> 