# 📋 INSTRUCCIONES - Fase 2: Sistema de Autenticación

## ✅ COMPONENTES IMPLEMENTADOS

### 1. **Capa de Datos (Data Layer)**
- ✅ `app/data/UsuarioDAO.php` - Actualizado con métodos adicionales:
  - `obtenerPorId($id)` - Para sesiones
  - `verificarEmail($email)` - Validar email único
  - `contarUsuarios()` - Para dashboard
  - `actualizarUsuario($id, $nombre, $email)` - Para perfil

### 2. **Capa de Negocio (Business Layer)**
- ✅ `app/business/UsuarioService.php` - Servicio completo:
  - `login($email, $password)` - Autenticación
  - `registrar($nombre, $email, $password)` - Registro con validaciones
  - `verificarEmailExistente($email)` - Validación de duplicados
  - `obtenerUsuario($id)` - Obtener datos de usuario
  - `contarUsuarios()` - Estadísticas

### 3. **Utilidades**
- ✅ `app/utils/MessageHandler.php` - Sistema de mensajes flash
- ✅ `app/utils/generar_hash_password.php` - Script para generar hashes
- ✅ `app/config/session.php` - Configuración de sesiones

### 4. **Middleware**
- ✅ `app/middleware/AuthMiddleware.php` - Verificación de autenticación
- ✅ `app/middleware/AdminMiddleware.php` - Verificación de rol admin

### 5. **Vistas de Autenticación**
- ✅ `app/presentation/auth/login.php` - Formulario de login
- ✅ `app/presentation/auth/registro.php` - Formulario de registro
- ✅ `app/presentation/auth/procesar_login.php` - Procesador de login
- ✅ `app/presentation/auth/procesar_registro.php` - Procesador de registro
- ✅ `app/presentation/auth/logout.php` - Cerrar sesión

### 6. **Actualizaciones**
- ✅ `index.php` - Rutas actualizadas
- ✅ `app/presentation/templates/header.php` - Navegación según rol
- ✅ `public/css/styles.css` - Estilos para navegación de usuario

---

## 🚀 PASOS PARA CONFIGURAR Y PROBAR

### **Paso 1: Actualizar Base de Datos**

1. **Ejecutar el script de migración** (si aún no lo has hecho):
   ```sql
   -- Ejecutar migracion_bd.sql en tu base de datos
   ```

2. **Generar hash de contraseña para el admin:**
   ```bash
   php app/utils/generar_hash_password.php admin123
   ```
   
   Esto generará un hash. Copia el hash y actualiza la BD:
   ```sql
   UPDATE usuarios 
   SET password = '[hash_generado]' 
   WHERE email = 'admin@eventhub.com';
   ```

   O ejecuta directamente en PHP:
   ```php
   <?php
   echo password_hash('admin123', PASSWORD_DEFAULT);
   ?>
   ```

### **Paso 2: Verificar Rutas**

Asegúrate de que las rutas en `index.php` incluyan:
- `login` → `app/presentation/auth/login.php`
- `registro` → `app/presentation/auth/registro.php`
- `logout` → `app/presentation/auth/logout.php`

### **Paso 3: Probar el Sistema**

#### **3.1 Probar Registro de Usuario:**
1. Ir a: `http://localhost/tu-proyecto/index.php?view=registro`
2. Completar el formulario:
   - Nombre: Juan Pérez
   - Email: juan@example.com
   - Contraseña: 123456
   - Confirmar contraseña: 123456
3. Debería redirigir al login con mensaje de éxito

#### **3.2 Probar Login de Usuario:**
1. Ir a: `http://localhost/tu-proyecto/index.php?view=login`
2. Ingresar credenciales del usuario registrado
3. Debería redirigir al dashboard de usuario (aún no implementado, mostrará error 404)

#### **3.3 Probar Login de Admin:**
1. Ir a: `http://localhost/tu-proyecto/index.php?view=login`
2. Ingresar:
   - Email: admin@eventhub.com
   - Contraseña: admin123
3. Debería redirigir al dashboard de admin (aún no implementado, mostrará error 404)

#### **3.4 Verificar Navegación:**
- Cuando NO estés logueado: Deberías ver "Iniciar Sesión" y "Registrarse" en el header
- Cuando estés logueado como usuario: Deberías ver "Mi Dashboard" y "Cerrar Sesión"
- Cuando estés logueado como admin: Deberías ver "Dashboard Admin" y "Cerrar Sesión"

#### **3.5 Probar Logout:**
1. Estar logueado
2. Hacer clic en "Cerrar Sesión" o ir a: `index.php?view=logout`
3. Debería cerrar sesión y redirigir al home

---

## 🔒 SEGURIDAD IMPLEMENTADA

✅ **Contraseñas hasheadas** con `password_hash()` y `password_verify()`
✅ **Validación de email único** en registro
✅ **Sanitización de entradas** en formularios
✅ **Sesiones seguras** con regeneración periódica de ID
✅ **Protección CSRF** (preparado, puede mejorarse)
✅ **Prepared statements** en todas las consultas (PDO)

---

## ⚠️ NOTAS IMPORTANTES

1. **Dashboards aún no implementados:**
   - Las rutas `admin/dashboard` y `usuario/dashboard` mostrarán error 404
   - Esto es normal, se implementarán en las siguientes fases

2. **Mensajes Flash:**
   - Los mensajes se muestran una sola vez
   - Se guardan en sesión y se eliminan después de mostrarse

3. **Rutas relativas:**
   - Asegúrate de que las rutas en los archivos de autenticación sean correctas
   - Si tienes problemas, verifica las rutas relativas desde `app/presentation/auth/`

4. **Base de datos:**
   - Asegúrate de que la tabla `usuarios` tenga el campo `rol` con valores 'admin' o 'usuario'
   - El usuario admin debe tener `rol = 'admin'`

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### **Error: "Credenciales incorrectas"**
- Verifica que el hash de la contraseña esté correcto en la BD
- Asegúrate de usar `password_verify()` (no comparar hashes directamente)

### **Error: "Email ya registrado"**
- El email debe ser único en la tabla `usuarios`
- Verifica la constraint UNIQUE en la columna email

### **Error: Redirección no funciona**
- Verifica que las rutas en `index.php` estén correctas
- Asegúrate de que no haya output antes de `header()`

### **Error: Sesión no persiste**
- Verifica que `session_start()` se llame antes de cualquier output
- Revisa la configuración de PHP para sesiones

### **Error: "Cannot modify header information"**
- No debe haber ningún output (echo, HTML, espacios) antes de `header()`
- Verifica que no haya espacios antes de `<?php` en los archivos

---

## 📝 PRÓXIMOS PASOS

Una vez que la Fase 2 esté funcionando correctamente:

1. **Fase 3:** Implementar Dashboard de Administrador
2. **Fase 4:** Implementar Dashboard de Usuario
3. **Fase 5:** Sistema de Inscripciones

---

## ✅ CHECKLIST DE VERIFICACIÓN

- [ ] Script de migración ejecutado
- [ ] Hash de contraseña del admin actualizado en BD
- [ ] Registro de usuario funciona
- [ ] Login de usuario funciona
- [ ] Login de admin funciona
- [ ] Logout funciona
- [ ] Navegación muestra opciones correctas según rol
- [ ] Mensajes flash se muestran correctamente
- [ ] Validación de email único funciona
- [ ] Validación de contraseñas funciona

---

**Fecha de implementación:** $(date)
**Estado:** ✅ Completado

