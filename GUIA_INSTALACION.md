# 📚 Guía de Instalación y Configuración - EventHub

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior (o MariaDB 10.3+)
- Servidor web (Apache/Nginx) con mod_rewrite habilitado
- Extensiones PHP requeridas:
  - PDO
  - PDO_MySQL
  - GD (para procesamiento de imágenes)
  - mbstring
  - session

## Instalación

### 1. Clonar/Descargar el Proyecto

```bash
# Si usas Git
git clone [url-del-repositorio]
cd Aplicaciones-2U
```

### 2. Configurar Base de Datos

1. Crear la base de datos:
```sql
CREATE DATABASE eventhub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Importar el esquema:
```bash
mysql -u root -p eventhub_db < eventhub_db.sql
```

O desde phpMyAdmin:
- Seleccionar la base de datos `eventhub_db`
- Ir a la pestaña "Importar"
- Seleccionar el archivo `eventhub_db.sql`

### 3. Configurar Credenciales de Base de Datos

#### Opción A: Usando Variables de Entorno (Recomendado para Producción)

1. Crear archivo `.env` en la raíz del proyecto:
```env
DB_HOST=localhost
DB_NAME=eventhub_db
DB_USER=tu_usuario
DB_PASS=tu_contraseña
```

2. Modificar `app/config/config.php` para leer del archivo `.env`:
```php
// Agregar al inicio del archivo
if (file_exists(__DIR__ . '/../../.env')) {
    $lines = file(__DIR__ . '/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}
```

#### Opción B: Modificar Directamente `app/config/config.php`

Editar las constantes:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventhub_db');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 4. Configurar Modo de Aplicación

En `app/config/config.php`, cambiar según el entorno:

**Desarrollo:**
```php
define('APP_MODE', 'development');
```

**Producción:**
```php
define('APP_MODE', 'production');
```

### 5. Configurar Permisos de Carpetas

```bash
# En Linux/Mac
chmod 755 public/img/eventos
chmod 755 public/img/noticias
chmod 755 logs  # Si existe la carpeta de logs
```

### 6. Configurar Servidor Web

#### Apache

1. Habilitar mod_rewrite:
```bash
sudo a2enmod rewrite
```

2. Crear archivo `.htaccess` en la raíz (si no existe):
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?view=$1 [L,QSA]
</IfModule>
```

3. Configurar VirtualHost (opcional):
```apache
<VirtualHost *:80>
    ServerName eventhub.local
    DocumentRoot /ruta/a/Aplicaciones-2U
    <Directory /ruta/a/Aplicaciones-2U>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name eventhub.local;
    root /ruta/a/Aplicaciones-2U;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?view=$uri&$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### 7. Crear Usuario Administrador

Ejecutar en MySQL:
```sql
INSERT INTO usuarios (nombre, email, password, rol) 
VALUES (
    'Administrador', 
    'admin@eventhub.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'admin'
);
```

**IMPORTANTE**: Cambiar la contraseña después del primer login.

Para generar un hash de contraseña:
```php
<?php
echo password_hash('tu_contraseña', PASSWORD_DEFAULT);
?>
```

O usar el script: `app/utils/generar_hash_password.php`

## Verificación de Instalación

1. Acceder a: `http://localhost/Aplicaciones-2U/` o `http://eventhub.local/`
2. Verificar que la página principal carga correctamente
3. Probar registro de usuario
4. Probar login con usuario administrador
5. Verificar acceso al panel de administración

## Configuración de Producción

### Checklist Pre-Producción

- [ ] Cambiar `APP_MODE` a `'production'` en `app/config/config.php`
- [ ] Configurar credenciales de BD seguras
- [ ] Eliminar archivos de prueba (ya eliminados)
- [ ] Verificar que `.env` no se suba al repositorio (agregar a `.gitignore`)
- [ ] Configurar HTTPS
- [ ] Configurar backup automático de BD
- [ ] Revisar permisos de archivos y carpetas
- [ ] Configurar límites de PHP (upload_max_filesize, post_max_size)
- [ ] Configurar timezone correcto
- [ ] Verificar que los logs no sean accesibles públicamente

### Configuración PHP para Producción

En `php.ini`:
```ini
display_errors = Off
log_errors = On
error_log = /ruta/a/logs/php_errors.log
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 30
memory_limit = 128M
```

### Seguridad Adicional

1. **Proteger archivos sensibles**:
   - Crear `.htaccess` en `app/config/`:
   ```apache
   Deny from all
   ```

2. **Proteger carpeta de logs**:
   - Crear `.htaccess` en `logs/`:
   ```apache
   Deny from all
   ```

3. **Configurar CORS** (si es necesario):
   - En `app/config/config.php` o `.htaccess`

## Solución de Problemas

### Error: "Error de conexión a la base de datos"
- Verificar credenciales en `app/config/config.php`
- Verificar que MySQL esté corriendo
- Verificar que la base de datos existe

### Error: "404 - Página no encontrada"
- Verificar que mod_rewrite está habilitado
- Verificar configuración de `.htaccess`
- Verificar rutas en `index.php`

### Error: "Cannot modify header information"
- Verificar que no hay output antes de `header()`
- Verificar que no hay espacios en blanco antes de `<?php`
- Verificar BOM en archivos

### Error: "Session already started"
- Verificar que `session_start()` solo se llama una vez
- Verificar que no hay múltiples includes de archivos con sesión

## Soporte

Para problemas adicionales, revisar:
- `CHECKLIST_PRODUCCION.md` - Lista de verificación de producción
- Logs de errores en `logs/php_errors.log` (si está configurado)

