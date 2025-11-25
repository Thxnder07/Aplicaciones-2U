<?php
require_once __DIR__ . '/../data/NoticiaDAO.php';

class NoticiaService {
    private $noticiaDAO;

    public function __construct() {
        $this->noticiaDAO = new NoticiaDAO();
    }

    // Obtener noticias para la vista pública
    public function listarNoticias() {
        return $this->noticiaDAO->getAll();
    }

    // Obtener noticias destacadas (opcional, para el home)
    public function listarDestacadas() {
        // Podrías filtrar aquí o pedirle a Aquino un método getDestacadas()
        $todas = $this->noticiaDAO->getAll();
        return array_filter($todas, function($n) { return $n['es_destacada'] == 1; });
    }

    // Lógica para crear noticia (Admin)
    public function crearNoticia($titulo, $resumen, $contenido) {
        // 1. Validaciones de Negocio
        if (strlen($titulo) < 5) {
            return ["success" => false, "msg" => "El título debe tener al menos 5 caracteres."];
        }
        if (empty($resumen) || empty($contenido)) {
            return ["success" => false, "msg" => "Todos los campos son obligatorios."];
        }

        // 2. Llamar al DAO (Aquino debe proveer el método insert)
        // Por defecto usaremos una imagen placeholder si no se sube una
        $imagen = 'img/noticias/default.jpg'; 
        
        if ($this->noticiaDAO->insert($titulo, $resumen, $contenido, $imagen)) {
            return ["success" => true, "msg" => "Noticia publicada con éxito."];
        } else {
            return ["success" => false, "msg" => "Error al guardar en la base de datos."];
        }
    }
}
?>
