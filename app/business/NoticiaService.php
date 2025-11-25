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

    // CREAR
    public function guardarNoticia($datos, $archivoImagen) {
        $titulo = $datos['titulo'];
        $resumen = $datos['resumen'];
        $contenido = $datos['contenido'];
        
        // Lógica simple de subida de imagen
        $rutaImagen = 'img/noticias/default.jpg';
        if (isset($archivoImagen['name']) && $archivoImagen['error'] == 0) {
            $nombreArchivo = time() . "_" . $archivoImagen['name'];
            $destino = __DIR__ . '/../public/img/noticias/' . $nombreArchivo;
            if(move_uploaded_file($archivoImagen['tmp_name'], $destino)){
                $rutaImagen = 'img/noticias/' . $nombreArchivo;
            }
        }

        return $this->noticiaDAO->insert($titulo, $resumen, $contenido, $rutaImagen);
    }

    // ELIMINAR
    public function eliminarNoticia($id) {
        return $this->noticiaDAO->delete($id);
    }

    // EDITAR (Opcional si te da tiempo, sino con Crear y Borrar basta para aprobar)
    public function actualizarNoticia($id, $datos) {
        // Lógica similar a guardar, llamando a $this->noticiaDAO->update(...)
    }
}
?>
