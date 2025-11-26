<?php
// Importamos el DAO (Capa de Datos) con el que vamos a interactuar
require_once __DIR__ . '/../data/EventoDAO.php';

class EventoService {
    private $eventoDAO;
    // Definimos la carpeta donde se guardarán las imágenes (ruta física)
    private $uploadDir = __DIR__ . '/../../public/img/eventos/';

    public function __construct() {
        $this->eventoDAO = new EventoDAO();
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

        // C. LLAMAR AL DAO (Integración)
        // Preparamos los datos limpios para Antony (Capa de Datos)
        $guardado = $this->eventoDAO->crear(
            trim($postData['titulo']),
            trim($postData['fecha_texto']),
            trim($postData['lugar']),
            (float) $postData['precio'],
            $rutaImagenFinal,
            trim($postData['descripcion_breve'] ?? ''),
            trim($postData['horario'] ?? '')
        );

        if ($guardado) {
            return ["success" => true, "msg" => "Evento registrado exitosamente."];
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
        $rutaImagenFinal = $postData['imagen_actual']; // Campo oculto en el form
        
        if (!empty($fileData['imagen']['name'])) {
            $resultadoImagen = $this->procesarImagen($fileData['imagen']);
            if (!$resultadoImagen['success']) {
                return ["success" => false, "errores" => [$resultadoImagen['msg']]];
            }
            $rutaImagenFinal = $resultadoImagen['ruta'];
        }

        // C. UPDATE EN DAO
        // Nota: Asegúrate que EventoDAO tenga el método actualizar()
        $actualizado = $this->eventoDAO->actualizar(
            $id,
            trim($postData['titulo']),
            trim($postData['fecha_texto']),
            trim($postData['lugar']),
            (float) $postData['precio'],
            $rutaImagenFinal,
            trim($postData['descripcion_breve'] ?? ''),
            trim($postData['horario'] ?? '')
        );

        return $actualizado 
            ? ["success" => true, "msg" => "Evento actualizado correctamente."]
            : ["success" => false, "errores" => ["No se pudo actualizar el evento."]];
    }

    public function eliminarEvento($id) {
        // Regla de negocio: Podríamos verificar si el evento ya pasó antes de borrarlo
        // O borrar la imagen física del servidor para no dejar basura
        return $this->eventoDAO->eliminar($id);
    }

    // ==========================================================
    // 3. MÉTODOS PRIVADOS (Auxiliares de Lógica)
    // ==========================================================

    private function validarDatos($datos) {
        $errores = [];
        if (empty($datos['titulo'])) $errores[] = "El título es obligatorio.";
        if (empty($datos['fecha_texto'])) $errores[] = "La fecha es obligatoria.";
        if (empty($datos['precio']) || !is_numeric($datos['precio']) || $datos['precio'] < 0) {
            $errores[] = "El precio debe ser un número positivo.";
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