# 🎓 EventHub - Portal de Congresos y Seminarios

<div align="center">

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**Sistema web completo para la gestión de eventos académicos, congresos y seminarios**

[Características](#-características) • [Instalación](#-instalación) • [Configuración](#-configuración) • [Uso](#-uso)

</div>

---

## 📋 Descripción

EventHub es una plataforma web desarrollada en PHP que permite la gestión integral de eventos académicos, congresos y seminarios. El sistema ofrece funcionalidades completas para administradores y usuarios finales, incluyendo gestión de eventos, cursos, noticias, inscripciones y un panel administrativo robusto.

### 🎯 Objetivo del Proyecto

Desarrollar un portal web funcional que permita:
- **Administradores**: Gestionar eventos, cursos, noticias e inscripciones de manera eficiente
- **Usuarios**: Explorar eventos, inscribirse y acceder a información relevante
- **Organización**: Centralizar la información de eventos académicos en un solo lugar

---

## ✨ Características

### 🔐 Autenticación y Autorización
- ✅ Sistema de registro e inicio de sesión seguro
- ✅ Contraseñas hasheadas con `password_hash()`
- ✅ Roles de usuario (Administrador / Usuario)
- ✅ Middleware de protección de rutas
- ✅ Sesiones seguras con regeneración de ID

### 📅 Gestión de Eventos
- ✅ CRUD completo de eventos
- ✅ Gestión de imágenes y recursos multimedia
- ✅ Control de cupos disponibles
- ✅ Estados de eventos (activo/inactivo)
- ✅ Eventos destacados
- ✅ Vista detallada de eventos con información completa

### 📚 Gestión de Cursos
- ✅ CRUD completo de cursos
- ✅ Asociación de cursos a eventos
- ✅ Gestión de ponentes por curso
- ✅ Control de cupos y disponibilidad

### 📰 Gestión de Noticias
- ✅ CRUD completo de noticias
- ✅ Noticias destacadas
- ✅ Gestión de imágenes
- ✅ Vista pública de noticias

### 👥 Sistema de Inscripciones
- ✅ Inscripción a eventos
- ✅ Inscripción a cursos específicos
- ✅ Control automático de cupos
- ✅ Validación de inscripciones duplicadas
- ✅ Dashboard de inscripciones para usuarios

### 🛡️ Seguridad
- ✅ Prepared Statements (PDO) en todas las consultas
- ✅ Sanitización de entradas y salidas
- ✅ Protección CSRF (implementada)
- ✅ Validación de tipos de archivo
- ✅ Manejo seguro de errores
- ✅ Configuración de producción/desarrollo

### 📊 Panel Administrativo
- ✅ Dashboard con estadísticas
- ✅ Gestión completa de eventos, cursos y noticias
- ✅ Visualización de inscripciones
- ✅ Sistema de logs de acciones administrativas
- ✅ Interfaz intuitiva y responsiva

### 🎨 Interfaz de Usuario
- ✅ Diseño moderno y minimalista
- ✅ Responsive design (móvil, tablet, desktop)
- ✅ Navegación intuitiva
- ✅ Mensajes flash para feedback al usuario
- ✅ Optimización de imágenes y recursos

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 7.4+** - Lenguaje de programación
- **MySQL 5.7+** - Base de datos relacional
- **PDO** - Capa de abstracción de base de datos

### Frontend
- **HTML5** - Estructura semántica
- **CSS3** - Estilos y diseño responsivo
- **Bootstrap 5.3** - Framework CSS
- **JavaScript (Vanilla)** - Interactividad
- **Font Awesome** - Iconografía

### Arquitectura
- **Patrón MVC** - Separación de responsabilidades
- **DAO (Data Access Object)** - Acceso a datos
- **Service Layer** - Lógica de negocio
- **Middleware** - Protección de rutas

---

## 📁 Estructura del Proyecto

```
Aplicaciones-2U/
│
├── app/
│   ├── business/          # Capa de lógica de negocio
│   │   ├── EventoService.php
│   │   ├── CursoService.php
│   │   ├── NoticiaService.php
│   │   ├── InscripcionService.php
│   │   ├── UsuarioService.php
│   │   └── LogService.php
│   │
│   ├── data/              # Capa de acceso a datos (DAO)
│   │   ├── Database.php
│   │   ├── EventoDAO.php
│   │   ├── CursoDAO.php
│   │   ├── NoticiaDAO.php
│   │   ├── InscripcionDAO.php
│   │   └── UsuarioDAO.php
│   │
│   ├── presentation/      # Capa de presentación (Vistas)
│   │   ├── admin/         # Vistas administrativas
│   │   ├── auth/          # Autenticación
│   │   ├── eventos/       # Vistas públicas de eventos
│   │   ├── usuario/       # Dashboard de usuario
│   │   └── templates/     # Plantillas reutilizables
│   │
│   ├── middleware/        # Middleware de seguridad
│   │   ├── AuthMiddleware.php
│   │   └── AdminMiddleware.php
│   │
│   ├── config/           # Configuración
│   │   ├── config.php
│   │   └── session.php
│   │
│   └── utils/            # Utilidades
│       ├── MessageHandler.php
│       └── CSRFProtection.php
│
├── public/               # Recursos públicos
│   ├── css/
│   ├── img/
│   └── js/
│
├── eventhub_db.sql      # Script de base de datos
├── index.php            # Punto de entrada (Router)
└── README.md            # Este archivo
```

---

## 📦 Requisitos del Sistema

### Servidor
- PHP 7.4 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Servidor web (Apache/Nginx)
- mod_rewrite habilitado (Apache)

### Extensiones PHP
- `PDO`
- `PDO_MySQL`
- `GD` (para procesamiento de imágenes)
- `mbstring`
- `session`

---

## 🚀 Instalación

### 1. Clonar el Repositorio

```bash
git clone [url-del-repositorio]
cd Aplicaciones-2U
```

### 2. Configurar Base de Datos

```sql
-- Crear base de datos
CREATE DATABASE eventhub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Importar esquema
mysql -u root -p eventhub_db < eventhub_db.sql
```

### 3. Configurar Credenciales

Editar `app/config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventhub_db');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 4. Configurar Servidor Web

#### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?view=$1 [L,QSA]
</IfModule>
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?view=$uri&$args;
}
```

### 5. Configurar Permisos

```bash
chmod 755 public/img/eventos
chmod 755 public/img/noticias
```

---

## ⚙️ Configuración

### Modo de Aplicación

En `app/config/config.php`:

**Desarrollo:**
```php
define('APP_MODE', 'development');
```

**Producción:**
```php
define('APP_MODE', 'production');
```

### Crear Usuario Administrador

```sql
INSERT INTO usuarios (nombre, email, password, rol) 
VALUES (
    'Administrador', 
    'admin@eventhub.com', 
    '$2y$10$[hash_generado]', 
    'admin'
);
```

Para generar hash de contraseña:
```php
<?php
echo password_hash('tu_contraseña', PASSWORD_DEFAULT);
?>
```

---

## 📖 Uso

### Acceso Público
- **Home**: `http://localhost/Aplicaciones-2U/`
- **Eventos**: `http://localhost/Aplicaciones-2U/?view=eventos`
- **Noticias**: `http://localhost/Aplicaciones-2U/?view=noticias`
- **Contacto**: `http://localhost/Aplicaciones-2U/?view=contacto`

### Acceso de Usuario
- **Registro**: `http://localhost/Aplicaciones-2U/?view=registro`
- **Login**: `http://localhost/Aplicaciones-2U/?view=login`
- **Dashboard**: `http://localhost/Aplicaciones-2U/?view=usuario/dashboard`

### Acceso de Administrador
- **Login**: `http://localhost/Aplicaciones-2U/?view=login`
- **Dashboard**: `http://localhost/Aplicaciones-2U/?view=admin/dashboard`
- **Eventos**: `http://localhost/Aplicaciones-2U/?view=admin/eventos/index`
- **Cursos**: `http://localhost/Aplicaciones-2U/?view=admin/cursos/index`
- **Noticias**: `http://localhost/Aplicaciones-2U/?view=admin/noticias/index`
- **Inscripciones**: `http://localhost/Aplicaciones-2U/?view=admin/inscripciones/index`
- **Logs**: `http://localhost/Aplicaciones-2U/?view=admin/log/index`

---

## 🔒 Seguridad

### Implementado
- ✅ Contraseñas hasheadas con `password_hash()`
- ✅ Prepared Statements en todas las consultas
- ✅ Sanitización de entradas y salidas
- ✅ Protección de rutas con middleware
- ✅ Validación de tipos de archivo
- ✅ Manejo seguro de errores
- ✅ Sesiones seguras

### Recomendaciones para Producción
- 🔐 Configurar HTTPS
- 🔐 Implementar rate limiting en login
- 🔐 Configurar backup automático de BD
- 🔐 Revisar permisos de archivos
- 🔐 Configurar firewall del servidor

---

## 📊 Estado del Proyecto

### ✅ Completado
- [x] Sistema de autenticación completo
- [x] CRUD de eventos
- [x] CRUD de cursos
- [x] CRUD de noticias
- [x] Sistema de inscripciones
- [x] Panel administrativo
- [x] Dashboard de usuario
- [x] Sistema de logs
- [x] Gestión de imágenes
- [x] Control de cupos
- [x] Validaciones y seguridad básica
- [x] Interfaz responsiva

### 🔄 En Mejora Continua
- [ ] Optimización de consultas
- [ ] Caché de consultas frecuentes
- [ ] Mejora de validaciones CSRF
- [ ] Sistema de notificaciones por email
- [ ] Exportación de reportes (PDF/Excel)

---

## 👥 Equipo de Desarrollo

Este proyecto fue desarrollado como parte del curso de **Aplicaciones Distribuidas 1** - 2da Unidad.

### Responsabilidades por Módulo

| Desarrollador | Módulo | Estado |
|--------------|--------|--------|
| **Ferrer Chujutalli John Devon** | Vistas y Diseño Frontend | ✅ Completo |
| **Rodriguez Melendez Jeffri Fabricio** | Módulo de Eventos | ✅ Completo |
| **Chumacero Chavarry Albert Sebastian** | Noticias y Contacto | ✅ Completo |
| **Aquino Rumualdo Antony Jampol** | Base de Datos y Configuración | ✅ Completo |
| **Alvarez Paredes Jose Luis** | Login, Seguridad y Panel Admin | ✅ Completo |
| **Llumpo Cumpa Aaron Amir** | Ponentes y Patrocinadores | ✅ Completo |

---

## 📝 Licencia

Este proyecto es desarrollado con fines educativos como parte del curso de Aplicaciones Distribuidas 1.

---

## 🤝 Contribuciones

Este es un proyecto académico. Para sugerencias o mejoras, contactar al equipo de desarrollo.

---

## 📞 Soporte

Para problemas o consultas:
1. Revisar la documentación del código
2. Verificar los logs de errores
3. Contactar al equipo de desarrollo

---

<div align="center">

**Desarrollado con ❤️ para la gestión de eventos académicos**

*Última actualización: 2025*

</div>
