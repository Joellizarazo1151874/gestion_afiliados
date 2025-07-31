<?php
require_once dirname(__DIR__) . '/modelo/Solicitud.php';
require_once dirname(__DIR__) . '/modelo/Documento.php';
require_once dirname(__DIR__) . '/modelo/EmailService.php';

class SolicitudController {
    private $solicitud;
    private $documento;
    private $emailService;
    
    public function __construct() {
        $this->solicitud = new Solicitud();
        $this->documento = new Documento();
        $this->emailService = new EmailService();
    }
    
    // Procesar nueva solicitud
    public function procesarSolicitud($datos, $archivos) {
        try {
            // Validar datos
            $this->validarDatosSolicitud($datos);
            
            // Verificar si el email ya existe
            if ($this->solicitud->emailExiste($datos['email'])) {
                throw new Exception("Ya existe una solicitud con este email");
            }
            
            // Iniciar transacción
            $pdo = $this->solicitud->getConexion()->conectar();
            $pdo->beginTransaction();
            
            try {
                // Crear solicitud
                $solicitud_id = $this->solicitud->crearSolicitud($datos, $pdo);
                
                // Procesar documentos si existen
                if (!empty($archivos)) {
                    $this->procesarDocumentosSolicitud($solicitud_id, $archivos, $pdo);
                }
                
                // Confirmar transacción
                $pdo->commit();
                
                return [
                    'success' => true,
                    'solicitud_id' => $solicitud_id,
                    'message' => 'Solicitud enviada exitosamente. Te notificaremos cuando sea revisada.'
                ];
                
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                $pdo->rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Procesar documentos de solicitud
    private function procesarDocumentosSolicitud($solicitud_id, $archivos, $pdo = null) {
        $upload_dir = dirname(__DIR__) . '/uploads/documentos_solicitudes/';
        
        // Log para debugging
        error_log("Procesando documentos para solicitud ID: $solicitud_id");
        error_log("Número de archivos recibidos: " . count($archivos));
        
        // Crear directorio si no existe
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
            error_log("Directorio creado: $upload_dir");
        }
        
        foreach ($archivos as $tipo => $archivo) {
            error_log("Procesando archivo tipo: $tipo");
            error_log("Error del archivo: " . $archivo['error']);
            
            if ($archivo['error'] === UPLOAD_ERR_OK) {
                error_log("Archivo válido, procediendo con validación");
                
                try {
                    // Validar archivo
                    $this->validarArchivo($archivo);
                    error_log("Archivo validado correctamente");
                    
                    // Generar nombre único
                    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                    $nombre_archivo = uniqid() . '_' . $tipo . '.' . $extension;
                    $ruta_archivo = $upload_dir . $nombre_archivo;
                    
                    // Guardar ruta relativa desde la raíz del proyecto para la BD
                    $ruta_relativa = 'uploads/documentos_solicitudes/' . $nombre_archivo;
                    
                    error_log("Intentando mover archivo a: $ruta_archivo");
                    
                    // Mover archivo
                    error_log("Intentando mover archivo desde: " . $archivo['tmp_name'] . " a: " . $ruta_archivo);
                    error_log("Archivo temporal existe: " . (file_exists($archivo['tmp_name']) ? 'SÍ' : 'NO'));
                    error_log("Directorio destino existe: " . (is_dir(dirname($ruta_archivo)) ? 'SÍ' : 'NO'));
                    error_log("Permisos de escritura: " . (is_writable(dirname($ruta_archivo)) ? 'SÍ' : 'NO'));
                    
                    if (move_uploaded_file($archivo['tmp_name'], $ruta_archivo)) {
                        error_log("Archivo movido exitosamente");
                        
                        // Guardar en base de datos
                        $resultado = $this->documento->guardarDocumentoSolicitud($solicitud_id, $tipo, $nombre_archivo, $ruta_relativa, $pdo);
                        error_log("Documento guardado en BD: " . ($resultado ? 'OK' : 'ERROR'));
                        
                        if (!$resultado) {
                            throw new Exception("Error al guardar documento en la base de datos");
                        }
                    } else {
                        // Intentar con copy() como fallback
                        error_log("move_uploaded_file falló, intentando con copy()");
                        if (copy($archivo['tmp_name'], $ruta_archivo)) {
                            error_log("Archivo copiado exitosamente");
                            
                            // Guardar en base de datos
                            $resultado = $this->documento->guardarDocumentoSolicitud($solicitud_id, $tipo, $nombre_archivo, $ruta_relativa, $pdo);
                            error_log("Documento guardado en BD: " . ($resultado ? 'OK' : 'ERROR'));
                            
                            if (!$resultado) {
                                throw new Exception("Error al guardar documento en la base de datos");
                            }
                        } else {
                            $error = error_get_last();
                            $error_msg = $error ? $error['message'] : 'Desconocido';
                            error_log("Error al mover/copiar archivo: $error_msg");
                            throw new Exception("Error al procesar el archivo: $error_msg");
                        }
                    }
                } catch (Exception $e) {
                    error_log("Error procesando archivo $tipo: " . $e->getMessage());
                    throw $e;
                }
            } else {
                error_log("Archivo con error: " . $archivo['error']);
            }
        }
        
        error_log("Procesamiento de documentos completado");
    }
    
    // Validar datos de solicitud
    private function validarDatosSolicitud($datos) {
        $campos_requeridos = [
            'nombre', 'apellidos', 'fecha_nacimiento', 'genero',
            'tipo_discapacidad', 'porcentaje_discapacidad', 'telefono', 'email',
            'direccion', 'ciudad', 'codigo_postal'
        ];
        
        foreach ($campos_requeridos as $campo) {
            if (empty($datos[$campo])) {
                throw new Exception("El campo $campo es obligatorio");
            }
        }
        
        // Validar email
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email no es válido");
        }
        
        // Validar porcentaje de discapacidad
        if ($datos['porcentaje_discapacidad'] < 0 || $datos['porcentaje_discapacidad'] > 100) {
            throw new Exception("El porcentaje de discapacidad debe estar entre 0 y 100");
        }
    }
    
    // Validar archivo
    private function validarArchivo($archivo) {
        $tipos_permitidos = [
            'image/jpeg', 'image/jpg', 'image/png', 
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        $tamano_maximo = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($archivo['type'], $tipos_permitidos)) {
            throw new Exception("Tipo de archivo no permitido");
        }
        
        if ($archivo['size'] > $tamano_maximo) {
            throw new Exception("El archivo es demasiado grande (máximo 5MB)");
        }
    }
    
    // Obtener solicitudes para admin
    public function obtenerSolicitudesAdmin($estado = null) {
        try {
            return $this->solicitud->obtenerSolicitudes($estado);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Obtener solicitud por ID
    public function obtenerSolicitud($id) {
        try {
            return $this->solicitud->obtenerSolicitudPorId($id);
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Aprobar solicitud
    public function aprobarSolicitud($solicitud_id, $admin_id) {
        try {
            error_log("=== INICIANDO PROCESO DE APROBACIÓN ===");
            error_log("Solicitud ID: $solicitud_id, Admin ID: $admin_id");
            
            // Obtener datos de la solicitud antes de aprobarla
            $datosSolicitud = $this->solicitud->obtenerSolicitudPorId($solicitud_id);
            if (!$datosSolicitud) {
                throw new Exception("No se encontró la solicitud con ID: $solicitud_id");
            }
            
            // Aprobar solicitud
            error_log("Paso 1: Aprobando solicitud...");
            $this->solicitud->aprobarSolicitud($solicitud_id, $admin_id);
            error_log("✅ Solicitud aprobada correctamente");
            
            // Convertir a usuario
            error_log("Paso 2: Convirtiendo a usuario...");
            $usuario_id = $this->solicitud->convertirAUsuario($solicitud_id);
            error_log("✅ Usuario creado con ID: $usuario_id");
            
            // Enviar notificación por email
            error_log("Paso 3: Enviando notificación por email...");
            $emailEnviado = false;
            try {
                $emailEnviado = $this->emailService->enviarNotificacionAprobacion($datosSolicitud);
                if ($emailEnviado) {
                    error_log("✅ Email de aprobación enviado exitosamente a: " . $datosSolicitud['email']);
                } else {
                    error_log("⚠️ Error al enviar email de aprobación a: " . $datosSolicitud['email']);
                }
            } catch (Exception $emailError) {
                error_log("⚠️ Error en envío de email: " . $emailError->getMessage());
                // No lanzar excepción para no interrumpir el proceso de aprobación
            }
            
            error_log("=== PROCESO DE APROBACIÓN COMPLETADO ===");
            
            $mensaje = 'Solicitud aprobada y usuario creado exitosamente';
            if ($emailEnviado) {
                $mensaje .= '. Email de notificación enviado.';
            } else {
                $mensaje .= '. Email de notificación no enviado.';
            }
            
            return [
                'success' => true,
                'message' => $mensaje,
                'email_enviado' => $emailEnviado,
                'usuario_id' => $usuario_id
            ];
            
        } catch (Exception $e) {
            error_log("❌ ERROR en aprobación: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Rechazar solicitud
    public function rechazarSolicitud($solicitud_id, $admin_id, $motivo) {
        try {
            error_log("=== INICIANDO PROCESO DE RECHAZO ===");
            error_log("Solicitud ID: $solicitud_id, Admin ID: $admin_id");
            
            // Obtener datos de la solicitud antes de rechazarla
            $datosSolicitud = $this->solicitud->obtenerSolicitudPorId($solicitud_id);
            if (!$datosSolicitud) {
                throw new Exception("No se encontró la solicitud con ID: $solicitud_id");
            }
            
            // Rechazar solicitud
            error_log("Paso 1: Rechazando solicitud...");
            $this->solicitud->rechazarSolicitud($solicitud_id, $admin_id, $motivo);
            error_log("✅ Solicitud rechazada correctamente");
            
            // Enviar notificación por email
            error_log("Paso 2: Enviando notificación por email...");
            $emailEnviado = false;
            try {
                $emailEnviado = $this->emailService->enviarNotificacionRechazo($datosSolicitud, $motivo);
                if ($emailEnviado) {
                    error_log("✅ Email de rechazo enviado exitosamente a: " . $datosSolicitud['email']);
                } else {
                    error_log("⚠️ Error al enviar email de rechazo a: " . $datosSolicitud['email']);
                }
            } catch (Exception $emailError) {
                error_log("⚠️ Error en envío de email: " . $emailError->getMessage());
                // No lanzar excepción para no interrumpir el proceso de rechazo
            }
            
            error_log("=== PROCESO DE RECHAZO COMPLETADO ===");
            
            $mensaje = 'Solicitud rechazada exitosamente';
            if ($emailEnviado) {
                $mensaje .= '. Email de notificación enviado.';
            } else {
                $mensaje .= '. Email de notificación no enviado.';
            }
            
            return [
                'success' => true,
                'message' => $mensaje,
                'email_enviado' => $emailEnviado
            ];
            
        } catch (Exception $e) {
            error_log("❌ ERROR en rechazo: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Copiar documentos de solicitud a usuario
    private function copiarDocumentosSolicitud($solicitud_id, $usuario_id) {
        try {
            $documentos = $this->documento->obtenerDocumentosSolicitud($solicitud_id);
            $copiados = 0;
            $errores = 0;
            
            error_log("Iniciando copia de documentos para solicitud {$solicitud_id} -> usuario {$usuario_id}");
            error_log("Total documentos a copiar: " . count($documentos));
            
            if (count($documentos) == 0) {
                error_log("No hay documentos para copiar");
                return; // No hay documentos, no es un error
            }
            
            foreach ($documentos as $doc) {
                try {
                    $origen = dirname(__DIR__) . '/' . $doc['ruta_archivo'];
                    $destino = dirname(__DIR__) . '/uploads/documentos/' . $doc['nombre_archivo'];
                    
                    error_log("Procesando documento: {$doc['tipo_documento']} - {$doc['nombre_archivo']}");
                    error_log("Origen: $origen");
                    error_log("Destino: $destino");
                    
                    // Verificar si el archivo origen existe
                    if (!file_exists($origen)) {
                        error_log("Archivo origen no encontrado: {$origen}");
                        $errores++;
                        continue; // Continuar con el siguiente documento
                    }
                    
                    // Verificar permisos de lectura
                    if (!is_readable($origen)) {
                        error_log("Archivo no es legible: {$origen}");
                        $errores++;
                        continue;
                    }
                    
                    // Crear directorio si no existe
                    $directorio_destino = dirname($destino);
                    if (!file_exists($directorio_destino)) {
                        if (!mkdir($directorio_destino, 0755, true)) {
                            error_log("Error creando directorio: $directorio_destino");
                            $errores++;
                            continue;
                        }
                        error_log("Directorio creado: $directorio_destino");
                    }
                    
                    // Verificar permisos de escritura
                    if (!is_writable($directorio_destino)) {
                        error_log("Sin permisos de escritura en: $directorio_destino");
                        $errores++;
                        continue;
                    }
                    
                    // Verificar si el archivo destino ya existe y eliminarlo si es necesario
                    if (file_exists($destino)) {
                        error_log("Archivo destino ya existe, eliminando: {$destino}");
                        if (!unlink($destino)) {
                            error_log("Error eliminando archivo existente: {$destino}");
                            $errores++;
                            continue;
                        }
                    }
                    
                    // Copiar archivo
                    if (copy($origen, $destino)) {
                        error_log("Archivo copiado exitosamente: {$doc['nombre_archivo']}");
                        
                        // Guardar en tabla de documentos de usuarios con ruta relativa
                        $ruta_relativa = 'uploads/documentos/' . $doc['nombre_archivo'];
                        $resultado = $this->documento->guardarDocumento(
                            $usuario_id, 
                            $doc['tipo_documento'], 
                            $doc['nombre_archivo'], 
                            $ruta_relativa
                        );
                        
                        if ($resultado) {
                            error_log("Documento guardado en BD: {$doc['tipo_documento']} para usuario {$usuario_id}");
                            $copiados++;
                        } else {
                            error_log("Error guardando documento en BD: {$doc['tipo_documento']}");
                            $errores++;
                        }
                    } else {
                        error_log("Error copiando archivo: {$doc['nombre_archivo']}");
                        $errores++;
                    }
                } catch (Exception $e) {
                    error_log("Error procesando documento {$doc['tipo_documento']}: " . $e->getMessage());
                    $errores++;
                }
            }
            
            error_log("Resumen copia documentos: {$copiados} copiados, {$errores} errores");
            
            // No lanzar excepción, solo registrar el resultado
            if ($copiados > 0) {
                error_log("Copia de documentos completada exitosamente. {$copiados} documentos copiados.");
            } else {
                error_log("ADVERTENCIA: No se pudo copiar ningún documento. Se encontraron {$errores} errores.");
            }
            
        } catch (Exception $e) {
            error_log("Error general copiando documentos: " . $e->getMessage());
            // No propagar el error para que la aprobación continúe
        }
    }
    
    // Obtener estadísticas
    public function obtenerEstadisticas() {
        try {
            return $this->solicitud->obtenerEstadisticas();
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Verificar estado de solicitud
    public function verificarEstado($numero_solicitud) {
        try {
            $solicitud = $this->solicitud->obtenerSolicitudPorNumero($numero_solicitud);
            
            if (!$solicitud) {
                return [
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ];
            }
            
            return [
                'success' => true,
                'solicitud' => $solicitud
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
?> 