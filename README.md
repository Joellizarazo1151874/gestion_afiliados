# Sistema de Gestión de Usuarios - Familia unida por la discapacidad

## Descripción
Sistema web completo para la gestión de usuarios/asociados de la organización "Familia unida por la discapacidad". Permite el control total de todos los asociados con funcionalidades de login, dashboard, gestión de usuarios y exportación a Excel.

## Características Principales

### 🔐 Autenticación y Seguridad
- Sistema de login seguro con hash de contraseñas
- Control de sesiones
- Protección de rutas

### 📊 Dashboard
- Estadísticas en tiempo real
- Vista general de usuarios activos, inactivos y suspendidos
- Interfaz moderna y responsiva

### 👥 Gestión de Usuarios
- **Crear nuevos usuarios** con información completa
- **Ver detalles** de cada usuario
- **Editar información** existente
- **Eliminar usuarios** con confirmación
- **Búsqueda y filtros** avanzados

### 📋 Información Capturada
- Número de asociado (único)
- Datos personales (nombre, apellidos, fecha nacimiento, género)
- Información de discapacidad (tipo y porcentaje)
- Datos de contacto (teléfono, email, dirección)
- Estado del asociado (Activo, Inactivo, Suspendido)
- Fecha de afiliación
- Observaciones

### 🔍 Filtros de Búsqueda
- Búsqueda por nombre, apellidos o número de asociado
- Filtro por estado
- Filtro por tipo de discapacidad
- Filtro por rango de fechas de afiliación

### 📥 Exportación a Excel
- Exportar lista completa de usuarios
- Incluye todos los filtros aplicados
- Formato Excel compatible

## Requisitos del Sistema

### Software Necesario
- **XAMPP** (Apache + MySQL + PHP)
- **PHP 7.4 o superior**
- **MySQL 5.7 o superior**
- **Navegador web moderno**

### Extensiones PHP Requeridas
- PDO
- PDO_MySQL
- mbstring

## Instalación

### 1. Configurar XAMPP
1. Descargar e instalar XAMPP desde [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Iniciar Apache y MySQL desde el panel de control de XAMPP

### 2. Clonar/Descargar el Proyecto
1. Colocar todos los archivos en la carpeta: `C:\xampp\htdocs\gestionusuarios\`
2. La estructura debe quedar así:
```
gestionusuarios/
├── controlador/
│   ├── AuthController.php
│   └── UsuarioController.php
├── modelo/
│   ├── conexion.php
│   ├── Administrador.php
│   └── Usuario.php
├── vista/
│   ├── index.php
│   ├── login.php
│   ├── procesar_usuario.php
│   ├── exportar_excel.php
│   ├── ver_usuario.php
│   └── editar_usuario.php
├── database.sql
└── README.md
```

### 3. Crear la Base de Datos
1. Abrir phpMyAdmin: `http://localhost/phpmyadmin`
2. Crear una nueva base de datos llamada `gestion_usuarios`
3. Importar el archivo `database.sql` en la base de datos creada

### 4. Configurar la Conexión
1. Verificar que los datos de conexión en `modelo/conexion.php` sean correctos:
   - Host: `localhost`
   - Usuario: `root`
   - Contraseña: `` (vacía por defecto en XAMPP)
   - Base de datos: `gestion_usuarios`

### 5. Acceder al Sistema
1. Abrir el navegador
2. Ir a: `http://localhost/gestionusuarios/vista/login.php`
3. Usar las credenciales por defecto:
   - **Usuario:** `admin`
   - **Contraseña:** `admin123`

## Uso del Sistema

### Login
1. Acceder a la página de login
2. Ingresar usuario y contraseña
3. El sistema redirigirá automáticamente al dashboard

### Dashboard
- **Estadísticas:** Ver números totales de usuarios por estado
- **Filtros:** Buscar y filtrar usuarios según diferentes criterios
- **Acciones rápidas:** Botones para crear nuevo usuario y exportar Excel

### Gestión de Usuarios

#### Crear Nuevo Usuario
1. Hacer clic en "Nuevo Usuario" en el dashboard
2. Llenar el formulario con la información requerida
3. Los campos marcados con * son obligatorios
4. Hacer clic en "Guardar Usuario"

#### Ver Usuario
1. En la lista de usuarios, hacer clic en el botón "Ver" (ojo)
2. Se mostrará toda la información del usuario en detalle
3. Opciones para editar o volver al dashboard

#### Editar Usuario
1. Hacer clic en el botón "Editar" (lápiz) en la lista o desde la vista de detalles
2. Modificar los campos necesarios
3. Guardar los cambios

#### Eliminar Usuario
1. Hacer clic en el botón "Eliminar" (basura) en la lista
2. Confirmar la eliminación
3. El usuario será eliminado permanentemente

### Exportar a Excel
1. Aplicar los filtros deseados en el dashboard
2. Hacer clic en "Exportar Excel"
3. El archivo se descargará automáticamente con todos los datos y filtros aplicados

## Estructura de la Base de Datos

### Tabla: administradores
- `id` - ID único del administrador
- `usuario` - Nombre de usuario para login
- `password` - Contraseña hasheada
- `nombre` - Nombre completo del administrador
- `email` - Email del administrador
- `fecha_creacion` - Fecha de creación del registro

### Tabla: usuarios
- `id` - ID único del usuario
- `numero_asociado` - Número único de asociado
- `nombre` - Nombre del usuario
- `apellidos` - Apellidos del usuario
- `fecha_nacimiento` - Fecha de nacimiento
- `genero` - Género (Masculino, Femenino, Otro)
- `tipo_discapacidad` - Tipo de discapacidad
- `porcentaje_discapacidad` - Porcentaje de discapacidad
- `telefono` - Número de teléfono
- `email` - Dirección de email
- `direccion` - Dirección completa
- `ciudad` - Ciudad
- `codigo_postal` - Código postal
- `fecha_afiliacion` - Fecha de afiliación a la organización
- `estado` - Estado del usuario (Activo, Inactivo, Suspendido)
- `observaciones` - Observaciones adicionales
- `fecha_registro` - Fecha de registro en el sistema
- `fecha_actualizacion` - Fecha de última actualización

## Seguridad

### Medidas Implementadas
- **Hash de contraseñas:** Uso de `password_hash()` y `password_verify()`
- **Prepared Statements:** Prevención de inyección SQL
- **Validación de entrada:** Sanitización de datos de entrada
- **Control de sesiones:** Verificación de autenticación en todas las páginas
- **Escape de salida:** Uso de `htmlspecialchars()` para prevenir XSS

### Recomendaciones de Seguridad
1. **Cambiar contraseña por defecto:** Cambiar la contraseña del administrador después del primer login
2. **Configurar HTTPS:** En producción, usar certificado SSL
3. **Backup regular:** Realizar copias de seguridad de la base de datos
4. **Actualizar PHP:** Mantener PHP actualizado para parches de seguridad

## Personalización

### Cambiar Credenciales de Administrador
1. Acceder a phpMyAdmin
2. Ir a la tabla `administradores`
3. Editar el registro del administrador
4. Para cambiar la contraseña, usar: `password_hash('nueva_contraseña', PASSWORD_DEFAULT)`

### Modificar Tipos de Discapacidad
Los tipos de discapacidad se pueden agregar directamente desde el formulario de usuarios. El sistema automáticamente los incluirá en los filtros.

### Personalizar Interfaz
- Los estilos están en archivos CSS inline
- Se puede modificar el diseño editando las secciones `<style>` en cada archivo
- Se usa Bootstrap 5 para el diseño responsivo

## Soporte y Mantenimiento

### Logs del Sistema
- Los errores de PHP se registran en los logs de Apache
- Verificar logs en: `C:\xampp\apache\logs\error.log`

### Backup de Base de Datos
1. En phpMyAdmin, seleccionar la base de datos `gestion_usuarios`
2. Ir a "Exportar"
3. Seleccionar "SQL" como formato
4. Descargar el archivo de backup

### Restaurar Base de Datos
1. Crear una nueva base de datos vacía
2. Importar el archivo de backup desde phpMyAdmin

## Tecnologías Utilizadas

- **Backend:** PHP 7.4+
- **Base de Datos:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework CSS:** Bootstrap 5
- **Iconos:** Font Awesome 6
- **Tablas:** DataTables
- **Patrón:** MVC (Modelo-Vista-Controlador)

## Licencia
Este proyecto es de uso libre para organizaciones sin fines de lucro que trabajen con personas discapacitadas.

---

**Desarrollado para organizaciones de personas discapacitadas**
*Sistema completo de gestión de asociados con funcionalidades avanzadas* 