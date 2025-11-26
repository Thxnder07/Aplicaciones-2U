-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2025 at 06:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eventhub_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `ponente_id` int(11) DEFAULT NULL,
  `cupos` int(11) DEFAULT 50,
  `cupos_disponibles` int(11) DEFAULT 50,
  `precio` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `fecha_texto` varchar(100) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `descripcion_breve` text DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `destacado` tinyint(1) DEFAULT 1,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `cupos` int(11) DEFAULT 100,
  `cupos_disponibles` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `eventos`
--

INSERT INTO `eventos` (`id`, `titulo`, `fecha_texto`, `fecha_inicio`, `descripcion_breve`, `lugar`, `horario`, `precio`, `imagen`, `destacado`, `estado`, `cupos`, `cupos_disponibles`) VALUES
(1, 'Congreso de Tecnología Digital', '15-17 de marzo, 2025', NULL, 'Un evento imperdible sobre inteligencia artificial, blockchain y desarrollo web.', 'Centro de Convenciones Madrid', '9:00 - 18:00', 299.00, 'img/eventos/tecnologia.jpg', 1, 'activo', 100, 100),
(2, 'Seminario de Marketing Digital', '22 de abril, 2025', NULL, 'Aprende estrategias reales y efectivas para el crecimiento digital.', 'Hotel Business Center Barcelona', '10:00 - 17:00', 149.00, 'img/eventos/marketing.jpg', 1, 'activo', 100, 100),
(3, 'Congreso Internacional de Salud', '5-7 de mayo, 2025', NULL, 'El evento más importante del año en medicina y telemedicina.', 'Palacio de Congresos Valencia', '8:30 - 19:00', 450.00, 'img/eventos/salud.jpg', 1, 'activo', 100, 100),
(4, 'Foro Global de Innovación y Startups', '10-12 de junio, 2025', NULL, 'Reúne a emprendedores, inversores y líderes del ecosistema.', 'Lisboa Convention Center', '9:30 - 18:30', 320.00, 'img/eventos/startups.jpg', 1, 'activo', 100, 100),
(5, 'Cumbre de Sostenibilidad', '3-4 de julio, 2025', NULL, 'Debate global sobre energías renovables y gestión ambiental.', 'Centro Ecológico de Berlín', '9:00 - 17:30', 200.00, 'img/eventos/sostenibilidad.jpg', 1, 'activo', 100, 100),
(6, 'Festival de Arte Digital', '18-20 de agosto, 2025', NULL, 'Una experiencia inmersiva donde el arte y la tecnología se fusionan.', 'Museo de Arte Contemporáneo', '11:00 - 20:00', 180.00, 'img/eventos/arte-digital.jpg', 1, 'activo', 100, 100);

-- --------------------------------------------------------

--
-- Table structure for table `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `curso_id` int(11) DEFAULT NULL COMMENT 'NULL si se inscribe solo al evento',
  `fecha_inscripcion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('confirmada','cancelada') DEFAULT 'confirmada',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_acciones`
--

CREATE TABLE `log_acciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(100) NOT NULL COMMENT 'crear_evento, editar_evento, eliminar_evento, etc.',
  `entidad` varchar(50) NOT NULL COMMENT 'evento, curso, inscripcion, usuario',
  `entidad_id` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `detalles` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `noticias`
--

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `fecha_publicacion` date DEFAULT curdate(),
  `resumen` text DEFAULT NULL,
  `contenido_completo` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `es_destacada` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `fecha_publicacion`, `resumen`, `contenido_completo`, `imagen`, `es_destacada`) VALUES
(1, 'Innovación educativa en el Congreso 2025', '2025-10-20', 'El Congreso presentó nuevas tendencias sobre metodologías digitales.', NULL, 'img/noticias/destacada.jpg', 1),
(2, 'Seminario sobre IA aplicada', '2025-10-14', 'Expertos analizaron cómo las tecnologías de IA revolucionan la enseñanza.', NULL, 'img/noticias/n1.jpg', 0),
(3, 'Éxito total en el Congreso de Innovación', '2025-10-05', 'Más de 1,200 emprendedores se reunieron para compartir estrategias.', NULL, 'img/noticias/n2.jpg', 0),
(4, 'Foro Internacional de Emprendimiento', '2025-09-25', 'Se presentaron proyectos innovadores centrados en sostenibilidad.', NULL, 'img/noticias/n3.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `patrocinadores`
--

CREATE TABLE `patrocinadores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('Principal','Oficial','Colaborador') NOT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patrocinadores`
--

INSERT INTO `patrocinadores` (`id`, `nombre`, `tipo`, `sector`, `descripcion`, `imagen`) VALUES
(1, 'Microsoft', 'Principal', 'Tecnología', 'Líder global en software.', 'img/patrocinadores/microsoft.png'),
(2, 'BBVA', 'Principal', 'Finanzas', 'Banco global comprometido con la innovación.', 'img/patrocinadores/bbva.png'),
(3, 'Google', 'Principal', 'Tecnología', 'Innovación en búsqueda y nube.', 'img/patrocinadores/google.png'),
(4, 'Scotiabank', 'Oficial', 'Banca', 'Soluciones digitales.', 'img/patrocinadores/scotiabank.png'),
(5, 'BCP', 'Oficial', 'Banca', 'Banco líder del Perú.', 'img/patrocinadores/bcp.png'),
(6, 'Meta', 'Oficial', 'Redes Sociales', 'Conexión social global.', 'img/patrocinadores/facebook.png'),
(7, 'AWS', 'Oficial', 'Cloud', 'Servicios en la nube.', 'img/patrocinadores/aws.png'),
(8, 'IBM', 'Oficial', 'Tecnología', 'IA y computación cuántica.', 'img/patrocinadores/ibm.png'),
(9, 'Intel', 'Oficial', 'Hardware', 'Líder en procesadores.', 'img/patrocinadores/intel.png'),
(10, 'Twitch', 'Colaborador', NULL, NULL, 'img/patrocinadores/twitch.png'),
(11, 'Twitter', 'Colaborador', NULL, NULL, 'img/patrocinadores/twitter.png'),
(12, 'LinkedIn', 'Colaborador', NULL, NULL, 'img/patrocinadores/linkedin.png'),
(13, 'Spotify', 'Colaborador', NULL, NULL, 'img/patrocinadores/spotify.png'),
(14, 'Adobe', 'Colaborador', NULL, NULL, 'img/patrocinadores/adobe.png'),
(15, 'Cisco', 'Colaborador', NULL, NULL, 'img/patrocinadores/cisco.png'),
(16, 'Oracle', 'Colaborador', NULL, NULL, 'img/patrocinadores/oracle.png'),
(17, 'Samsung', 'Colaborador', NULL, NULL, 'img/patrocinadores/samsung.png');

-- --------------------------------------------------------

--
-- Table structure for table `ponentes`
--

CREATE TABLE `ponentes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `cargo` varchar(150) DEFAULT NULL,
  `tema` varchar(200) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ponentes`
--

INSERT INTO `ponentes` (`id`, `nombre`, `cargo`, `tema`, `imagen`) VALUES
(1, 'Dr. Carlos Mendoza', 'Consultor en Marketing Digital', 'Tendencias en Publicidad Online 2025', 'img/ponentes/ponente1.jpg'),
(2, 'Lic. Ana Torres', 'Especialista en Finanzas', 'Innovación Financiera y Criptomonedas', 'img/ponentes/ponente2.jpg'),
(3, 'Ing. Roberto López', 'Director de Innovación', 'Transformación Digital en la Industria', 'img/ponentes/ponente3.jpg'),
(4, 'Dra. Lucía Fernández', 'Profesora Investigadora', 'Educación y Tecnología en el Siglo XXI', 'img/ponentes/ponente4.jpg'),
(5, 'Dr. Javier Ramos', 'Experto en Ciberseguridad', 'Protección de Datos en la Era de la IA', 'img/ponentes/ponente5.jpg'),
(6, 'Lic. Mariana Silva', 'Consultora de Innovación', 'Creatividad y Liderazgo', 'img/ponentes/ponente6.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `created_at`) VALUES
(1, 'Admin', 'admin@eventhub.com', '$2y$10$Tkfllv4QG0oSsdQzKkoKieP2xyiT2QYio3UrnWBml7yA9p6OZC4I.', 'admin', '2025-11-26 02:49:05'),
(3, 'Aaron Llumpo', '0202314032@uns.edu.pe', '$2y$10$fr9OwF5QdqhCk8ecG4NGhe5.AuDqmquTwBDDOqQ8bfp8j9y.xFMv.', 'usuario', '2025-11-26 03:01:06'),
(4, 'Yordi Yhonatan', 'yordi@gmail.com', '$2y$10$EfRyR7UrNNW8TOuvk3GLquecIgrI9iTeVjuAZaUOL2LoNktFbAIoS', 'usuario', '2025-11-26 05:25:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_evento` (`evento_id`),
  ADD KEY `idx_ponente` (`ponente_id`);

--
-- Indexes for table `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_inscripcion_evento` (`usuario_id`,`evento_id`),
  ADD UNIQUE KEY `unique_inscripcion_curso` (`usuario_id`,`evento_id`,`curso_id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_evento` (`evento_id`),
  ADD KEY `idx_curso` (`curso_id`),
  ADD KEY `idx_fecha` (`fecha_inscripcion`);

--
-- Indexes for table `log_acciones`
--
ALTER TABLE `log_acciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_entidad` (`entidad`,`entidad_id`),
  ADD KEY `idx_fecha` (`fecha`);

--
-- Indexes for table `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patrocinadores`
--
ALTER TABLE `patrocinadores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ponentes`
--
ALTER TABLE `ponentes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_acciones`
--
ALTER TABLE `log_acciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patrocinadores`
--
ALTER TABLE `patrocinadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `ponentes`
--
ALTER TABLE `ponentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cursos_ibfk_2` FOREIGN KEY (`ponente_id`) REFERENCES `ponentes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscripciones_ibfk_3` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `log_acciones`
--
ALTER TABLE `log_acciones`
  ADD CONSTRAINT `log_acciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
