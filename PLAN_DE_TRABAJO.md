# 📋 PLAN DE TRABAJO - Sistema de Gestión de Congresos y Seminarios

## 📊 ANÁLISIS DEL ESTADO ACTUAL DEL PROYECTO

### ✅ **Lo que ya existe:**
- Estructura MVC básica (Data, Business, Presentation)
- Base de datos con tablas: `usuarios`, `eventos`, `ponentes`, `patrocinadores`, `noticias`
- `UsuarioDAO` con métodos básicos (obtenerPorEmail, registrar)
- `EventoDAO` con CRUD completo (crear, leer, actualizar, eliminar)
- `EventoService` con validaciones y manejo de imágenes
- Vistas de administración para eventos (crear, listar, editar)
- Sistema de rutas básico en `index.php`
- Templates `header.php` y `footer.php`

### ❌ **Lo que falta implementar:**
- Sistema de autenticación (login, logout, sesiones)
- Dashboard para Administrador
- Dashboard para Usuario
- Tabla de inscripciones en BD
- Tabla de cursos en BD
- Sistema de cursos por evento
- Gestión de estado de eventos (Activo/Inactivo)
- Validación de cupos
- Sistema de mensajes/notificaciones
- Protección de rutas administrativas
- Vista de registro de usuarios
- Vista de perfil de usuario (opcional)

---

## 🎯 PLAN DE TRABAJO DETALLADO

### **FASE 1: ACTUALIZACIÓN DE BASE DE DATOS** ⚙️

#### 1.1 Modificar tabla `eventos`
- [ ] Agregar campo `estado` ENUM('activo', 'inactivo') DEFAULT 'activo'
- [ ] Agregar campo `cupos` INT DEFAULT 100
- [ ] Agregar campo `cupos_disponibles` INT (calculado o manual)
- [ ] Agregar campo `fecha_inicio` DATE (ya existe pero verificar uso)

#### 1.2 Crear tabla `cursos`
```sql
CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    fecha DATE,
    horario VARCHAR(100),
    ponente_id INT,
    cupos INT DEFAULT 50,
    precio DECIMAL(10, 2),
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (ponente_id) REFERENCES ponentes(id) ON DELETE SET NULL
);
```

#### 1.3 Crear tabla `inscripciones`
```sql
CREATE TABLE inscripciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    evento_id INT NOT NULL,
    curso_id INT NULL, -- NULL si se inscribe solo al evento
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('confirmada', 'cancelada') DEFAULT 'confirmada',
    UNIQUE KEY unique_inscripcion (usuario_id, evento_id, curso_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (evento_id) REFERENCES eventos(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
);
```

#### 1.4 Crear tabla `log_acciones` (Opcional - Requisito 11)
```sql
CREATE TABLE log_acciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion VARCHAR(100) NOT NULL, -- 'crear_evento', 'editar_evento', etc.
    entidad VARCHAR(50) NOT NULL, -- 'evento', 'curso', 'inscripcion'
    entidad_id INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    detalles TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
```

---

### **FASE 2: SISTEMA DE AUTENTICACIÓN** 🔐

#### 2.1 Crear `UsuarioService` (Business Layer)
- [ ] Crear `app/business/UsuarioService.php`
- [ ] Método `login($email, $password)` - Validar credenciales
- [ ] Método `registrar($nombre, $email, $password)` - Validar email único
- [ ] Método `verificarEmailExistente($email)` - Validación de duplicados
- [ ] Usar `password_hash()` y `password_verify()` para seguridad

#### 2.2 Actualizar `UsuarioDAO` (Data Layer)
- [ ] Método `obtenerPorId($id)` - Para sesiones
- [ ] Método `verificarEmail($email)` - Retornar true/false si existe
- [ ] Método `actualizarUsuario($id, $datos)` - Para perfil (opcional)

#### 2.3 Crear vistas de autenticación
- [ ] `app/presentation/auth/login.php` - Formulario de login
- [ ] `app/presentation/auth/registro.php` - Formulario de registro
- [ ] Diseño consistente con el resto del sitio

#### 2.4 Crear controlador de autenticación
- [ ] `app/presentation/auth/procesar_login.php` - Procesar POST de login
- [ ] `app/presentation/auth/procesar_registro.php` - Procesar POST de registro
- [ ] `app/presentation/auth/logout.php` - Cerrar sesión
- [ ] Iniciar sesiones PHP con `session_start()`
- [ ] Guardar `$_SESSION['usuario_id']`, `$_SESSION['rol']`, `$_SESSION['nombre']`

#### 2.5 Crear middleware de autenticación
- [ ] `app/middleware/AuthMiddleware.php` - Verificar si usuario está logueado
- [ ] `app/middleware/AdminMiddleware.php` - Verificar si es administrador
- [ ] Aplicar middleware a rutas administrativas

#### 2.6 Actualizar rutas en `index.php`
- [ ] Agregar ruta `'login' => 'app/presentation/auth/login.php'`
- [ ] Agregar ruta `'registro' => 'app/presentation/auth/registro.php'`
- [ ] Agregar ruta `'logout' => 'app/presentation/auth/logout.php'`

---

### **FASE 3: DASHBOARD DE ADMINISTRADOR** 👨‍💼

#### 3.1 Crear `DashboardService` (Business Layer)
- [ ] `app/business/DashboardService.php`
- [ ] Método `obtenerEstadisticas()` - Retornar:
  - Total de eventos creados
  - Total de usuarios registrados
  - Total de inscritos por evento
  - Total de inscritos por curso
- [ ] Método `obtenerInscripcionesPorEvento($evento_id)`
- [ ] Método `obtenerInscripcionesPorCurso($curso_id)`

#### 3.2 Crear `InscripcionDAO` (Data Layer)
- [ ] `app/data/InscripcionDAO.php`
- [ ] Método `obtenerPorEvento($evento_id)`
- [ ] Método `obtenerPorCurso($curso_id)`
- [ ] Método `obtenerPorUsuario($usuario_id)`
- [ ] Método `contarPorEvento($evento_id)`
- [ ] Método `contarPorCurso($curso_id)`
- [ ] Método `obtenerTodas()` - Para listado completo

#### 3.3 Crear `CursoDAO` (Data Layer)
- [ ] `app/data/CursoDAO.php`
- [ ] Método `crear($datos)`
- [ ] Método `obtenerPorEvento($evento_id)`
- [ ] Método `obtenerPorId($id)`
- [ ] Método `actualizar($id, $datos)`
- [ ] Método `eliminar($id)`

#### 3.4 Crear `CursoService` (Business Layer)
- [ ] `app/business/CursoService.php`
- [ ] Validaciones de negocio
- [ ] Métodos CRUD completos

#### 3.5 Crear vistas del Dashboard Admin
- [ ] `app/presentation/admin/dashboard/index.php` - Vista principal
  - Cards con estadísticas
  - Gráficas simples (usar Chart.js o similar - opcional)
  - Accesos rápidos a módulos
- [ ] `app/presentation/admin/dashboard/estadisticas.php` - Vista detallada de estadísticas

#### 3.6 Crear vistas de gestión de cursos
- [ ] `app/presentation/admin/cursos/index.php` - Listar cursos por evento
- [ ] `app/presentation/admin/cursos/crear.php` - Crear nuevo curso
- [ ] `app/presentation/admin/cursos/editar.php` - Editar curso

#### 3.7 Crear vistas de inscripciones
- [ ] `app/presentation/admin/inscripciones/index.php` - Listado general
- [ ] `app/presentation/admin/inscripciones/por_evento.php` - Filtrar por evento
- [ ] `app/presentation/admin/inscripciones/por_curso.php` - Filtrar por curso

#### 3.8 Actualizar vistas de eventos (Admin)
- [ ] Agregar campo `estado` (Activo/Inactivo) en `crear.php` y `index.php`
- [ ] Agregar campo `cupos` en formulario de creación
- [ ] Mostrar estado y cupos disponibles en listado
- [ ] Botón para cambiar estado del evento

#### 3.9 Sistema de mensajes/notificaciones
- [ ] Crear `app/utils/MessageHandler.php` - Clase para manejar mensajes flash
- [ ] Implementar mensajes de éxito/error después de cada operación
- [ ] Mostrar mensajes en todas las vistas administrativas

#### 3.10 Actualizar rutas en `index.php`
- [ ] Agregar ruta `'admin/dashboard' => 'app/presentation/admin/dashboard/index.php'`
- [ ] Agregar rutas para cursos e inscripciones

---

### **FASE 4: DASHBOARD DE USUARIO** 👤

#### 4.1 Crear `InscripcionService` (Business Layer)
- [ ] `app/business/InscripcionService.php`
- [ ] Método `inscribirUsuario($usuario_id, $evento_id, $curso_id = null)`
  - Validar disponibilidad de cupos
  - Validar doble inscripción (UNIQUE constraint + validación)
  - Decrementar cupos disponibles
  - Retornar mensaje de éxito/error
- [ ] Método `verificarInscripcion($usuario_id, $evento_id, $curso_id = null)`
- [ ] Método `obtenerEventosDisponibles($usuario_id)` - Eventos donde NO está inscrito
- [ ] Método `obtenerEventosInscritos($usuario_id)` - Eventos donde SÍ está inscrito
- [ ] Método `cancelarInscripcion($inscripcion_id)` - Opcional

#### 4.2 Actualizar `EventoDAO`
- [ ] Método `obtenerEventosActivos()` - Solo eventos con estado 'activo'
- [ ] Método `obtenerEventosDisponibles()` - Activos y con cupos > 0
- [ ] Método `decrementarCupos($evento_id)` - Al inscribirse
- [ ] Método `incrementarCupos($evento_id)` - Al cancelar (opcional)

#### 4.3 Crear vistas del Dashboard Usuario
- [ ] `app/presentation/usuario/dashboard/index.php` - Vista principal
  - Sección: "Eventos Disponibles" (aún puede inscribirse)
  - Sección: "Mis Inscripciones" (eventos inscritos)
  - Cards visuales con información de eventos
- [ ] `app/presentation/usuario/dashboard/eventos_disponibles.php` - Listado detallado
- [ ] `app/presentation/usuario/dashboard/mis_inscripciones.php` - Listado de inscripciones

#### 4.4 Crear vista de detalle de evento (Usuario)
- [ ] `app/presentation/usuario/eventos/detalle.php`
  - Nombre completo
  - Fecha y horario
  - Ubicación
  - Lista de expositores/ponentes
  - Cupos disponibles
  - Descripción completa
  - Botón de inscripción (si está disponible)
  - Mensaje si ya está inscrito
  - Mensaje si evento lleno

#### 4.5 Crear vista de inscripción
- [ ] `app/presentation/usuario/inscripciones/procesar.php` - Procesar inscripción
- [ ] Validaciones y mensajes claros:
  - "Inscripción exitosa"
  - "Evento lleno"
  - "Ya estás inscrito a este evento"

#### 4.6 Crear vista de perfil (Opcional - Requisito 28)
- [ ] `app/presentation/usuario/perfil/index.php` - Ver perfil
- [ ] `app/presentation/usuario/perfil/editar.php` - Editar perfil

#### 4.7 Actualizar rutas en `index.php`
- [ ] Agregar rutas para dashboard usuario
- [ ] Agregar rutas para inscripciones

---

### **FASE 5: MEJORAS Y FUNCIONALIDADES ADICIONALES** ✨

#### 5.1 Sistema de log de acciones (Opcional - Requisito 11)
- [ ] Crear `LogDAO` en `app/data/LogDAO.php`
- [ ] Crear `LogService` en `app/business/LogService.php`
- [ ] Registrar acciones en cada operación administrativa
- [ ] Vista `app/presentation/admin/log/index.php` - Ver registro de acciones

#### 5.2 Gráficas en Dashboard Admin (Opcional - Requisito 5)
- [ ] Integrar Chart.js o similar
- [ ] Gráfica de inscripciones por evento (barras)
- [ ] Gráfica de eventos por estado (pie)
- [ ] Gráfica de inscripciones por mes (línea)

#### 5.3 Mejoras de seguridad
- [ ] Validar CSRF tokens en formularios
- [ ] Sanitizar todas las entradas de usuario
- [ ] Validar permisos en cada acción administrativa
- [ ] Proteger contra SQL Injection (ya usando PDO preparado)

#### 5.4 Mejoras de UX
- [ ] Mensajes toast/alert modernos
- [ ] Confirmaciones antes de eliminar
- [ ] Loading states en botones
- [ ] Validación en frontend (JavaScript)

---

### **FASE 6: ACTUALIZACIÓN DE TEMPLATES Y NAVEGACIÓN** 🎨

#### 6.1 Actualizar `header.php`
- [ ] Mostrar opciones según rol (Admin/Usuario/No logueado)
- [ ] Botón "Iniciar Sesión" si no está logueado
- [ ] Menú "Dashboard" si está logueado
- [ ] Botón "Cerrar Sesión" si está logueado
- [ ] Mostrar nombre del usuario logueado

#### 6.2 Crear template de admin
- [ ] `app/presentation/templates/admin_header.php` - Header específico para admin
- [ ] Menú lateral o superior con opciones:
  - Dashboard
  - Eventos
  - Cursos
  - Inscripciones
  - Usuarios (opcional)
  - Log de acciones (opcional)

#### 6.3 Crear template de usuario
- [ ] `app/presentation/templates/usuario_header.php` - Header específico para usuario
- [ ] Menú con opciones:
  - Dashboard
  - Eventos Disponibles
  - Mis Inscripciones
  - Mi Perfil (opcional)

---

## 📝 ORDEN DE IMPLEMENTACIÓN RECOMENDADO

### **Sprint 1: Fundamentos (Fase 1 + Fase 2)**
1. Actualizar base de datos (tablas nuevas y modificaciones)
2. Implementar sistema de autenticación completo
3. Proteger rutas administrativas

### **Sprint 2: Dashboard Admin Básico (Fase 3 parcial)**
1. Crear DashboardService y DAOs necesarios
2. Crear vista principal del dashboard con estadísticas
3. Implementar sistema de mensajes

### **Sprint 3: Gestión Completa Admin (Fase 3 completa)**
1. CRUD de cursos
2. Gestión de estado de eventos
3. Vista de inscripciones
4. Actualizar gestión de eventos con nuevos campos

### **Sprint 4: Dashboard Usuario (Fase 4)**
1. InscripcionService y lógica de negocio
2. Dashboard usuario con eventos disponibles e inscritos
3. Vista de detalle de evento
4. Proceso de inscripción con validaciones

### **Sprint 5: Mejoras y Pulido (Fase 5 + Fase 6)**
1. Log de acciones (opcional)
2. Gráficas (opcional)
3. Actualizar templates y navegación
4. Mejoras de UX y seguridad

---

## 🔍 ARCHIVOS A CREAR/MODIFICAR

### **Nuevos archivos a crear:**
```
app/data/
  - CursoDAO.php
  - InscripcionDAO.php
  - LogDAO.php (opcional)

app/business/
  - UsuarioService.php
  - CursoService.php
  - InscripcionService.php
  - DashboardService.php
  - LogService.php (opcional)

app/middleware/
  - AuthMiddleware.php
  - AdminMiddleware.php

app/utils/
  - MessageHandler.php

app/presentation/auth/
  - login.php
  - registro.php
  - procesar_login.php
  - procesar_registro.php
  - logout.php

app/presentation/admin/
  - dashboard/index.php
  - dashboard/estadisticas.php
  - cursos/index.php
  - cursos/crear.php
  - cursos/editar.php
  - inscripciones/index.php
  - inscripciones/por_evento.php
  - inscripciones/por_curso.php
  - log/index.php (opcional)

app/presentation/usuario/
  - dashboard/index.php
  - dashboard/eventos_disponibles.php
  - dashboard/mis_inscripciones.php
  - eventos/detalle.php
  - inscripciones/procesar.php
  - perfil/index.php (opcional)
  - perfil/editar.php (opcional)

app/presentation/templates/
  - admin_header.php
  - usuario_header.php
```

### **Archivos a modificar:**
```
- bd.sql (agregar nuevas tablas y campos)
- index.php (agregar nuevas rutas)
- app/data/UsuarioDAO.php (agregar métodos)
- app/data/EventoDAO.php (agregar métodos)
- app/presentation/admin/eventos/crear.php (agregar campos estado y cupos)
- app/presentation/admin/eventos/index.php (mostrar estado y cupos)
- app/presentation/templates/header.php (agregar opciones según rol)
```

---

## ✅ CRITERIOS DE ACEPTACIÓN

### **Módulo de Autenticación:**
- [ ] Login funcional para admin y usuario
- [ ] Validación de credenciales contra BD
- [ ] Logout seguro (destruir sesión)
- [ ] Registro de usuarios con validación de email único
- [ ] Mensaje de confirmación al registrarse

### **Dashboard Administrador:**
- [ ] Muestra total de eventos creados
- [ ] Muestra total de usuarios registrados
- [ ] Muestra total de inscritos por evento
- [ ] Acceso a CRUD de eventos
- [ ] Acceso a gestión de cursos
- [ ] Acceso a listado de inscripciones
- [ ] Gestión de estado de eventos (Activo/Inactivo)
- [ ] Subida de imágenes de eventos
- [ ] Mensajes de confirmación después de cada operación

### **Dashboard Usuario:**
- [ ] Vista de eventos disponibles
- [ ] Vista de eventos inscritos
- [ ] Acceso a información completa de eventos
- [ ] Módulo de inscripción funcional
- [ ] Validación de cupos antes de inscribir
- [ ] Prevención de doble inscripción
- [ ] Mensajes claros (éxito, lleno, ya inscrito)

### **Sistema de Inscripciones:**
- [ ] Inscripción a eventos funcional
- [ ] Verificación de disponibilidad de cupos
- [ ] Restricción UNIQUE en BD + validación en código
- [ ] Decremento de cupos al inscribirse
- [ ] Notificaciones claras al usuario

---

## 📚 NOTAS IMPORTANTES

1. **Seguridad:**
   - Usar `password_hash()` y `password_verify()` para contraseñas
   - Validar sesiones en cada página protegida
   - Sanitizar todas las entradas de usuario
   - Usar prepared statements (ya implementado con PDO)

2. **Base de Datos:**
   - Crear script de migración para actualizar BD existente
   - Probar constraints UNIQUE en inscripciones
   - Considerar índices para mejorar rendimiento

3. **Experiencia de Usuario:**
   - Mensajes claros y descriptivos
   - Confirmaciones antes de acciones destructivas
   - Feedback visual inmediato

4. **Código:**
   - Mantener estructura MVC
   - Separar lógica de negocio de presentación
   - Reutilizar código cuando sea posible
   - Comentar código complejo

---

## 🎯 PRIORIDADES

**ALTA PRIORIDAD (Core del sistema):**
- Autenticación
- Dashboard Admin básico
- Sistema de inscripciones
- Gestión de cursos

**MEDIA PRIORIDAD (Mejoras importantes):**
- Dashboard Usuario completo
- Gestión de estado de eventos
- Sistema de mensajes

**BAJA PRIORIDAD (Opcional/Nice to have):**
- Log de acciones
- Gráficas
- Perfil de usuario
- Cancelación de inscripciones

---

**Fecha de creación:** $(date)
**Última actualización:** $(date)

