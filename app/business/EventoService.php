<?php
// Importamos el DAO (Capa de Datos) con el que vamos a interactuar
require_once __DIR__ . '/../data/EventoDAO.php';
require_once __DIR__ . '/LogService.php';

class EventoService {
    private $eventoDAO;
    private $logService;
    // Definimos la carpeta donde se guardarán las imágenes (ruta física)
    private $uploadDir = __DIR__ . '/../../public/img/eventos/';

    public function __construct() {
        $this->eventoDAO = new EventoDAO();
        $this->logService = new LogService();
    }

    // ==========================================================
    // 1. LÓGICA DE LECTURA (Listar y Ver Detalle)
    // ==========================================================

    public function listarEventos() {
        return $this->eventoDAO->obtenerTodos();
    }

    public function obtenerEvento($id) {
        if (!is_numeric($id)) {
            return null; // Validación básica de tipo
        }
        return $this->eventoDAO->obtenerPorId($id);
    }

    // ==========================================================
    // 2. LÓGICA DE ESCRITURA (Validaciones + Control de Imágenes)
    // ==========================================================

    public function registrarEvento($postData, $fileData) {
        // A. VALIDACIÓN DE DATOS (Reglas de Negocio)
        $errores = $this->validarDatos($postData);
        
        // Validar que la imagen sea obligatoria al crear
        if (empty($fileData['imagen']['name'])) {
            $errores[] = "La imagen del evento es obligatoria.";
        }

        if (!empty($errores)) {
            return ["success" => false, "errores" => $errores];
        }

        // B. CONTROL DE IMÁGENES
        $resultadoImagen = $this->procesarImagen($fileData['imagen']);
        if (!$resultadoImagen['success']) {
            return ["success" => false, "errores" => [$resultadoImagen['msg']]];
        }
        $rutaImagenFinal = $resultadoImagen['ruta']; // Ej: img/eventos/foto.jpg

        // C. PREPARAR DATOS ADICIONALES
        $estado = $postData['estado'] ?? 'activo';
        $cupos = isset($postData['cupos']) && $postData['cupos'] > 0 ? (int)$postData['cupos'] : 100;
        $fecha_inicio = !empty($postData['fecha_inicio']) ? $postData['fecha_inicio'] : null;
        $destacado = isset($postData['destacado']) ? 1 : 0;

        // D. LLAMAR AL DAO (Integración)
        $guardado = $this->eventoDAO->crear(
            trim($postData['titulo']),
            trim($postData['fecha_texto']),
            trim($postData['lugar']),
            (float) $postData['precio'],
            $rutaImagenFinal,
            trim($postData['descripcion_breve'] ?? ''),
            trim($postData['horario'] ?? ''),
            $estado,
            $cupos,
            $fecha_inicio,
            $destacado
        );

        if ($guardado) {
            // Obtener el ID del evento recién creado
            $evento_id = $this->eventoDAO->obtenerUltimoId();
            
            // Registrar en log si hay usuario en sesión
            if (isset($_SESSION['usuario_id'])) {
                $this->logService->logCrearEvento(
                    $_SESSION['usuario_id'],
                    $evento_id,
                    trim($postData['titulo'])
                );
            }
            
            return ["success" => true, "msg" => "Evento registrado exitosamente.", "evento_id" => $evento_id];
        } else {
            return ["success" => false, "errores" => ["Error en base de datos al guardar."]];
        }
    }

    public function actualizarEvento($id, $postData, $fileData) {
        // A. VALIDACIÓN
        $errores = $this->validarDatos($postData);
        if (!empty($errores)) {
            return ["success" => false, "errores" => $errores];
        }

        // B. IMAGEN (Opcional en edición)
        // Si subieron una nueva, la procesamos. Si no, mantenemos la anterior.
        $rutaImagenFinal = $postData['imagen_actual'] ?? ''; // Campo oculto en el form
        
        if (!empty($fileData['imagen']['name'])) {
            $resultadoImagen = $this->procesarImagen($fileData['imagen']);
            if (!$resultadoImagen['success']) {
                return ["success" => false, "errores" => [$resultadoImagen['msg']]];
            }
            $rutaImagenFinal = $resultadoImagen['ruta'];
        }

        // C. PREPARAR DATOS ADICIONALES
        $estado = $postData['estado'] ?? null;
        $cupos = isset($postData['cupos']) && $postData['cupos'] > 0 ? (int)$postData['cupos'] : null;
        $fecha_inicio = !empty($postData['fecha_inicio']) ? $postData['fecha_inicio'] : null;
        $destacado = isset($postData['destacado']) ? 1 : 0;

        // D. OBTENER TÍTULO PARA EL LOG
        $eventoActual = $this->eventoDAO->obtenerPorId($id);
        $tituloEvento = $eventoActual ? $eventoActual['titulo'] : trim($postData['titulo']);

        // E. UPDATE EN DAO
        $actualizado = $this->eventoDAO->actualizar(
            $id,
            trim($postData['titulo']),
            trim($postData['fecha_texto']),
            trim($postData['lugar']),
            (float) $postData['precio'],
            $rutaImagenFinal,
            trim($postData['descripcion_breve'] ?? ''),
            trim($postData['horario'] ?? ''),
            $estado,
            $cupos,
            $fecha_inicio,
            $destacado
        );

        if ($actualizado) {
            // Registrar en log si hay usuario en sesión
            if (isset($_SESSION['usuario_id'])) {
                $this->logService->logActualizarEvento(
                    $_SESSION['usuario_id'],
                    $id,
                    $tituloEvento
                );
            }
            return ["success" => true, "msg" => "Evento actualizado correctamente."];
        } else {
            return ["success" => false, "errores" => ["No se pudo actualizar el evento."]];
        }
    }

    public function eliminarEvento($id) {
        // Obtener información del evento antes de eliminarlo para el log
        $evento = $this->eventoDAO->obtenerPorId($id);
        
        if (!$evento) {
            return false;
        }
        
        $tituloEvento = $evento['titulo'];
        
        // Regla de negocio: Podríamos verificar si el evento ya pasó antes de borrarlo
        // O borrar la imagen física del servidor para no dejar basura
        $eliminado = $this->eventoDAO->eliminar($id);
        
        if ($eliminado && isset($_SESSION['usuario_id'])) {
            // Registrar en log
            $this->logService->logEliminarEvento(
                $_SESSION['usuario_id'],
                $id,
                $tituloEvento
            );
        }
        
        return $eliminado;
    }

    /**
     * Cambiar estado del evento (activo/inactivo)
     */
    public function cambiarEstado($id, $nuevoEstado) {
        // Validar que el estado sea válido
        if (!in_array($nuevoEstado, ['activo', 'inactivo'])) {
            return ["success" => false, "mensaje" => "Estado inválido."];
        }
        
        // Obtener información del evento para el log
        $evento = $this->eventoDAO->obtenerPorId($id);
        
        if (!$evento) {
            return ["success" => false, "mensaje" => "Evento no encontrado."];
        }
        
        $cambiado = $this->eventoDAO->cambiarEstado($id, $nuevoEstado);
        
        if ($cambiado) {
            // Registrar en log si hay usuario en sesión
            if (isset($_SESSION['usuario_id'])) {
                $this->logService->registrarAccion(
                    $_SESSION['usuario_id'],
                    'cambiar_estado_evento',
                    'evento',
                    $id,
                    "Cambió el estado del evento '{$evento['titulo']}' a {$nuevoEstado}"
                );
            }
            return ["success" => true, "mensaje" => "Estado del evento actualizado correctamente."];
        } else {
            return ["success" => false, "mensaje" => "No se pudo cambiar el estado del evento."];
        }
    }

    // ==========================================================
    // 3. MÉTODOS PRIVADOS (Auxiliares de Lógica)
    // ==========================================================

    private function validarDatos($datos) {
        $errores = [];
        
        // Validar título
        if (empty($datos['titulo'])) {
            $errores[] = "El título es obligatorio.";
        } elseif (strlen($datos['titulo']) > 150) {
            $errores[] = "El título no puede exceder 150 caracteres.";
        }
        
        // Validar fecha_texto
        if (empty($datos['fecha_texto'])) {
            $errores[] = "La fecha es obligatoria.";
        }
        
        // Validar lugar
        if (empty($datos['lugar'])) {
            $errores[] = "El lugar es obligatorio.";
        } elseif (strlen($datos['lugar']) > 150) {
            $errores[] = "El lugar no puede exceder 150 caracteres.";
        }
        
        // Validar precio
        if (empty($datos['precio']) || !is_numeric($datos['precio']) || $datos['precio'] < 0) {
            $errores[] = "El precio debe ser un número positivo.";
        }
        
        // Validar cupos (si se proporciona)
        if (isset($datos['cupos']) && $datos['cupos'] !== '') {
            if (!is_numeric($datos['cupos']) || $datos['cupos'] < 0) {
                $errores[] = "Los cupos deben ser un número positivo.";
            }
        }
        
        // Validar estado (si se proporciona)
        if (isset($datos['estado']) && !in_array($datos['estado'], ['activo', 'inactivo'])) {
            $errores[] = "El estado debe ser 'activo' o 'inactivo'.";
        }
        
        // Validar fecha_inicio (si se proporciona)
        if (!empty($datos['fecha_inicio'])) {
            $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha_inicio']);
            if (!$fecha || $fecha->format('Y-m-d') !== $datos['fecha_inicio']) {
                $errores[] = "La fecha de inicio debe tener un formato válido (YYYY-MM-DD).";
            }
        }
        
        return $errores;
    }

    private function procesarImagen($archivo) {
        // 1. Validar error de subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return ["success" => false, "msg" => "Error al subir el archivo."];
        }

        // 2. Validar extensiones permitidas (Control de Seguridad)
        $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $permitidos)) {
            return ["success" => false, "msg" => "Formato no permitido. Solo JPG, PNG o WEBP."];
        }

        // 3. Validar tamaño (Máximo 2MB)
        if ($archivo['size'] > 2 * 1024 * 1024) {
            return ["success" => false, "msg" => "La imagen es muy pesada (Máx 2MB)."];
        }

        // 4. Generar nombre único y mover
        // Guardamos como "time_nombrefile.jpg" para evitar duplicados
        $nuevoNombre = time() . "_" . basename($archivo['name']);
        $destinoFisico = $this->uploadDir . $nuevoNombre;
        
        // Ruta relativa para guardar en BD (lo que necesita el HTML para mostrarla)
        $rutaBD = "img/eventos/" . $nuevoNombre; 

        if (move_uploaded_file($archivo['tmp_name'], $destinoFisico)) {
            return ["success" => true, "ruta" => $rutaBD];
        } else {
            return ["success" => false, "msg" => "No se pudo mover la imagen a la carpeta destino."];
        }
    }
}
?>