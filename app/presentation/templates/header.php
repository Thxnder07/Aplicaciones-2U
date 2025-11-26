<?php
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$base_url = $path . '/public/';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'EventHub'; ?></title>
    
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/styles.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="icon" type="image/svg+xml" href="<?php echo $base_url; ?>img/logo.svg">
</head>
<body>

    <header>
        <div class="container">
            <div class="logo">
                <a href="index.php?view=home">
                    <img src="<?php echo $base_url; ?>img/logo.svg" alt="EventHub Logo">
                </a>
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php?view=home" class="<?php echo ($page == 'home') ? 'active' : ''; ?>">Inicio</a></li>
                    <li><a href="index.php?view=eventos" class="<?php echo ($page == 'eventos') ? 'active' : ''; ?>">Eventos</a></li>
                    <li><a href="index.php?view=ponentes" class="<?php echo ($page == 'ponentes') ? 'active' : ''; ?>">Ponentes</a></li>
                    <li><a href="index.php?view=patrocinadores" class="<?php echo ($page == 'patrocinadores') ? 'active' : ''; ?>">Patrocinadores</a></li>
                    <li><a href="index.php?view=noticias" class="<?php echo ($page == 'noticias') ? 'active' : ''; ?>">Noticias</a></li>
                    <li><a href="index.php?view=contacto" class="<?php echo ($page == 'contacto') ? 'active' : ''; ?>">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>