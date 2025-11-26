<?php
require_once __DIR__ . '/../data/NoticiaDAO.php';

class NoticiaService {
    private $noticiaDAO;

    public function __construct() {
        $this->noticiaDAO = new NoticiaDAO();
    }

    public function listarNoticias() {
        return $this->noticiaDAO->getAll();
    }

    public function obtenerPorId($id) {
        return $this->noticiaDAO->getById($id);
    }

    // CREAR O ACTUALIZAR
    public function guardarNoticia($datos, $archivoImagen = null) {
        $titulo = trim($datos['titulo'] ?? '');
        $resumen = trim($datos['resumen'] ?? '');
        $contenido = trim($datos['contenido'] ?? '');
        $es_destacada = isset($datos['es_destacada']) ? 1 : 0;
        
        // Validaciones básicas
        if (empty($titulo)) {
            return false;
        }
        
        // Procesar imagen
        $rutaImagen = null;
        if ($archivoImagen && isset($archivoImagen['name']) && $archivoImagen['error'] == 0) {
            // Validar tipo de archivo
            $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($archivoImagen['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $permitidos)) {
                $nombreArchivo = time() . "_" . basename($archivoImagen['name']);
                $destino = __DIR__ . '/../../public/img/noticias/' . $nombreArchivo;
                
                if (move_uploaded_file($archivoImagen['tmp_name'], $destino)) {
                    $rutaImagen = 'img/noticias/' . $nombreArchivo;
                }
            }
        }
        
        // Si es edición (tiene ID)
        if (!empty($datos['id'])) {
            // Si no se subió nueva imagen, mantener la actual
            if ($rutaImagen === null) {
                $noticiaActual = $this->noticiaDAO->getById($datos['id']);
                if ($noticiaActual && !empty($noticiaActual['imagen'])) {
                    $rutaImagen = $noticiaActual['imagen'];
                }
            }
            
            return $this->noticiaDAO->update($datos['id'], $titulo, $resumen, $contenido, $rutaImagen, $es_destacada);
        } else {
            // Es creación nueva
            if ($rutaImagen === null) {
                $rutaImagen = 'img/noticias/default.jpg';
            }
            return $this->noticiaDAO->insert($titulo, $resumen, $contenido, $rutaImagen, $es_destacada);
        }
    }

    // ELIMINAR
    public function eliminarNoticia($id) {
        return $this->noticiaDAO->delete($id);
    }

    // EDITAR (alias para mantener compatibilidad)
    public function actualizarNoticia($id, $datos, $archivoImagen = null) {
        $datos['id'] = $id;
        return $this->guardarNoticia($datos, $archivoImagen);
    }
}
?>
