# Guía Rápida de Instalación - Sistema RIAAC

## Paso 1: Importar la Base de Datos

1. Abre **phpMyAdmin**: http://localhost/phpmyadmin/
2. Crea una nueva base de datos llamada: **`inventario_riaac`**
3. Selecciona la base de datos
4. Ve a la pestaña **"Importar"**
5. Selecciona el archivo: `C:\wamp642025\www\inventario_riaac\database\database.sql`
6. Haz clic en **"Continuar"**
7. Verifica que se crearon 10 tablas correctamente

## Paso 2: Verificar Configuración

El archivo `config/database.php` ya está configurado con:

- Host: localhost
- Usuario: root
- Contraseña: (vacía)
- Base de datos: inventario_riaac

Si tu MySQL tiene una contraseña diferente, edita `config/database.php`.

## Paso 3: Acceder al Sistema

1. Abre tu navegador
2. Ve a: **http://localhost/inventario_riaac/**
3. Serás redirigido automáticamente al login

## Paso 4: Iniciar Sesión

**Credenciales por defecto:**

- Usuario: `admin`
- Contraseña: `admin123`

## ¿Qué hacer si hay errores?

### Error: "Base de datos no encontrada"

➡️ Asegúrate de haber importado el SQL en phpMyAdmin

### Error: "Access denied for user 'root'"

➡️ Edita `config/database.php` y cambia la contraseña de MySQL

### Página en blanco

➡️ Activa los errores de PHP en `config/config.php` (ya está activado)

### Error 404

➡️ Verifica que WAMP esté corriendo y que la URL sea correcta

## ✅ Checklist

- [ ] WAMP está corriendo (ícono verde)
- [ ] Base de datos `inventario_riaac` creada
- [ ] SQL importado (10 tablas)
- [ ] Acceder a http://localhost/inventario_riaac/
- [ ] Login con admin/admin123

---

**¡Listo!** Una vez dentro, podrás acceder a todos los módulos del sistema.
