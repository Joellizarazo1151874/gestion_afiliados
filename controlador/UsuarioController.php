<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../modelo/Usuario.php';
require_once '../controlador/AuthController.php';

class UsuarioController {
    private $usuario;
    private $auth;

    public function __construct() {
        $this->usuario = new Usuario();
        $this->auth = new AuthController();
        $this->auth->requireAuth();
    }

    // Obtener lista de usuarios con filtros
    public function obtenerUsuarios($filtros = []) {
        return $this->usuario->obtenerUsuarios($filtros);
    }

    // Obtener un usuario específico
    public function obtenerUsuario($id) {
        return $this->usuario->obtenerUsuarioPorId($id);
    }

    // Crear nuevo usuario
    public function crearUsuario($datos) {
        // Validaciones
        if (empty($datos['numero_asociado']) || empty($datos['nombre']) || empty($datos['apellidos'])) {
            return ['success' => false, 'message' => 'Los campos obligatorios deben estar completos'];
        }

        if ($this->usuario->existeNumeroAsociado($datos['numero_asociado'])) {
            return ['success' => false, 'message' => 'El número de asociado ya existe'];
        }

        if ($this->usuario->crearUsuario($datos)) {
            return ['success' => true, 'message' => 'Usuario creado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al crear el usuario'];
        }
    }

    // Actualizar usuario
    public function actualizarUsuario($id, $datos) {
        // Validaciones
        if (empty($datos['numero_asociado']) || empty($datos['nombre']) || empty($datos['apellidos'])) {
            return ['success' => false, 'message' => 'Los campos obligatorios deben estar completos'];
        }

        if ($this->usuario->existeNumeroAsociado($datos['numero_asociado'], $id)) {
            return ['success' => false, 'message' => 'El número de asociado ya existe'];
        }

        if ($this->usuario->actualizarUsuario($id, $datos)) {
            return ['success' => true, 'message' => 'Usuario actualizado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar el usuario'];
        }
    }

    // Eliminar usuario
    public function eliminarUsuario($id) {
        if ($this->usuario->eliminarUsuario($id)) {
            return ['success' => true, 'message' => 'Usuario eliminado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al eliminar el usuario'];
        }
    }

    // Obtener estadísticas
    public function obtenerEstadisticas() {
        return $this->usuario->obtenerEstadisticas();
    }

    // Obtener tipos de discapacidad
    public function obtenerTiposDiscapacidad() {
        return $this->usuario->obtenerTiposDiscapacidad();
    }

    // Exportar a Excel
    public function exportarExcel($filtros = []) {
        $usuarios = $this->usuario->obtenerUsuarios($filtros);
        
        // Configurar headers para descarga
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="usuarios_' . date('Y-m-d_H-i-s') . '.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        
        // Crear contenido del Excel con formato mejorado
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        echo '<!--[if gte mso 9]>';
        echo '<xml>';
        echo '<x:ExcelWorkbook>';
        echo '<x:ExcelWorksheets>';
        echo '<x:ExcelWorksheet>';
        echo '<x:Name>Usuarios</x:Name>';
        echo '<x:WorksheetOptions>';
        echo '<x:Print>';
        echo '<x:ValidPrinterInfo/>';
        echo '</x:Print>';
        echo '</x:WorksheetOptions>';
        echo '</x:ExcelWorksheet>';
        echo '</x:ExcelWorksheets>';
        echo '</x:ExcelWorkbook>';
        echo '</xml>';
        echo '<![endif]-->';
        echo '<style>';
        echo 'table { border-collapse: collapse; }';
        echo 'th, td { border: 1px solid #000; padding: 5px; }';
        echo 'th { background-color: #f0f0f0; font-weight: bold; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        
        echo '<table>';
        echo '<tr>';
        echo '<th>Número Asociado</th>';
        echo '<th>Nombre</th>';
        echo '<th>Apellidos</th>';
        echo '<th>Fecha Nacimiento</th>';
        echo '<th>Género</th>';
        echo '<th>Tipo Discapacidad</th>';
        echo '<th>% Discapacidad</th>';
        echo '<th>Teléfono</th>';
        echo '<th>Email</th>';
        echo '<th>Dirección</th>';
        echo '<th>Ciudad</th>';
        echo '<th>Código Postal</th>';
        echo '<th>Fecha Afiliación</th>';
        echo '<th>Estado</th>';
        echo '<th>Observaciones</th>';
        echo '</tr>';
        
        foreach ($usuarios as $usuario) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($usuario['numero_asociado']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['nombre']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['apellidos']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['fecha_nacimiento']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['genero']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['tipo_discapacidad']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['porcentaje_discapacidad']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['telefono']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['email']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['direccion']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['ciudad']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['codigo_postal']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['fecha_afiliacion']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['estado']) . '</td>';
            echo '<td>' . htmlspecialchars($usuario['observaciones']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit();
    }
}
?> 