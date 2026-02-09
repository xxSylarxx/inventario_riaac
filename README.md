# Sistema de Gestión RIAAC

Sistema web administrativo para pequeños negocios de servicios tecnológicos.

## 🚀 Características

- ✅ Gestión completa de clientes (personas y empresas)
- ✅ Control de hosting y dominios con alertas de vencimiento
- ✅ Registro de reparaciones de computadoras
- ✅ Inventario y ventas de productos con comprobantes
- ✅ Gestión unificada de garantías (productos y servicios)
- ✅ Dashboard con estadísticas en tiempo real
- ✅ Sistema de usuarios con roles (Administrador/Técnico)
- ✅ Interfaz responsive con Bootstrap 5

## 📋 Requisitos

- PHP 8.0 o superior
- MySQL 8.0 o superior
- Apache con mod_rewrite habilitado
- Extensiones PHP: PDO, PDO_MySQL, GD

## 🛠️ Instalación

### 1. Clonar/Descargar el proyecto

Descarga el proyecto en la carpeta de tu servidor web (por ejemplo: `C:\wamp64\www\inventario_riaac`).

### 2. Crear la base de datos

1. Accede a phpMyAdmin o tu gestor MySQL
2. Crea una nueva base de datos llamada `inventario_riaac`
3. Importa el archivo `database/database.sql`
4. Verifica que se hayan creado las 10 tablas correctamente

### 3. Configurar la conexión

Edita el archivo `config/database.php` con tus credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'inventario_riaac');
define('DB_USER', 'root');            // Tu usuario MySQL
define('DB_PASS', '');                 // Tu contraseña MySQL
```

### 4. Configurar URL base

Edita el archivo `config/config.php`:

```php
define('BASE_URL', 'http://localhost/inventario_riaac/');
```

### 5. Permisos de carpeta

Asegúrate de que la carpeta `uploads/` tenga permisos de escritura:

```bash
chmod 755 uploads/
```

### 6. Acceder al sistema

Abre tu navegador en: `http://localhost/inventario_riaac/`

**Credenciales por defecto:**

- Usuario: `admin`
- Contraseña: `admin123`

> ⚠️ **IMPORTANTE**: Cambia la contraseña del administrador inmediatamente después del primer acceso.

## 📁 Estructura del Proyecto

```
inventario_riaac/
├── config/              # Archivos de configuración
├── controllers/         # Controladores (lógica de negocio)
├── models/             # Modelos (acceso a datos)
├── views/              # Vistas (interfaz de usuario)
│   ├── auth/           # Login
│   ├── dashboard/      # Panel principal
│   ├── clientes/       # CRUD clientes
│   ├── hosting/        # CRUD hosting/dominios
│   ├── reparaciones/   # CRUD reparaciones
│   ├── productos/      # CRUD productos
│   ├── ventas/         # CRUD ventas
│   ├── garantias/      # Vista de garantías
│   └── layouts/        # Plantillas header/footer
├── assets/             # CSS, JavaScript
│   ├── css/            # Estilos personalizados
│   └── js/             # Scripts
├── database/           # Script SQL
├── includes/           # Helpers y autoload
├── uploads/            # Archivos subidos (comprobantes)
├── index.php           # Punto de entrada
└── .htaccess           # Configuración Apache
```

## 🎯 Módulos del Sistema

### 1. Clientes

- Registro de personas y empresas
- DNI/RUC, contactos, WhatsApp
- Estados activo/inactivo

### 2. Hosting y Dominios

- Registro de servicios anuales
- Cálculo automático de vencimiento
- Alertas a 30, 15 y 5 días antes

### 3. Reparaciones

- Registro de servicios técnicos
- Control de garantía de servicio
- Estados de trabajo (pendiente, en proceso, entregado)

### 4. Productos

- Inventario con stock
- Categorías y proveedores
- Control de precios de compra

### 5. Ventas

- Registro de ventas de productos
- Subida de comprobantes (PDF/Imágenes)
- Control de garantía de producto
- Descuento automático de stock

### 6. Garantías

- Vista unificada de productos y servicios
- Filtros por estado (vigente/vencida/por vencer)
- Separación por tabs

### 7. Dashboard

- Estadísticas generales
- Alertas de vencimientos próximos
- Actividad reciente

## 🔐 Seguridad

- Contraseñas hasheadas con bcrypt
- Protección contra inyección SQL (PDO Prepared Statements)
- Sanitización de entradas
- Validación de sesiones
- Protección de archivos sensibles vía `.htaccess`

## 🎨 Tecnologías Utilizadas

- **Backend**: PHP 8 (sin frameworks)
- **Base de Datos**: MySQL 8
- **Frontend**: HTML5, CSS3, JavaScript (ES6)
- **Framework CSS**: Bootstrap 5.3
- **Librerías**:
  - jQuery 3.7
  - DataTables 1.13 (tablas interactivas)
  - SweetAlert2 (alertas modernas)
  - Bootstrap Icons

## 📝 Notas Adicionales

### Alertas de Vencimiento

El sistema genera alertas automáticamente. Para activar el sistema de alertas en producción, configura un cron job:

```bash
# Ejecutar diariamente a las 8:00 AM
0 8 * * * php /ruta/a/tu/proyecto/cron/generar_alertas.php
```

### Respaldos

Es recomendable hacer respaldos periódicos de:

1. Base de datos: `mysqldump inventario_riaac > backup.sql`
2. Carpeta `uploads/` con los comprobantes

### Producción

Antes de subir a producción:

1. Cambiar `display_errors = 0` en `config/config.php`
2. Habilitar HTTPS en `.htaccess`
3. Cambiar credenciales por defecto
4. Configurar respaldos automáticos

## 🤝 Soporte

Para reportar problemas o sugerencias, contacta al administrador del sistema.

---

**Desarrollado con ❤️ para RIAAC Servicios Tecnológicos**
