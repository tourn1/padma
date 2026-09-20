<?php
session_start();

// Validar sesión iniciada
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Manejo de Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    session_destroy();
    header('Location: index.php');
    exit;
}

// Configuración de rutas y persistencia
$textFile = dirname(__FILE__) . '/text.txt';
$uploadDir = dirname(__FILE__) . '/upload/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Cargar textos y enlaces existentes o inicializar vacíos
$defaultTexts = [
    "brand_name" => "PADMA YOGA",
    "nav_inicio" => "Inicio",
    "nav_productos" => "Productos Naturales",
    "nav_nosotros" => "Nosotros",
    "nav_clases" => "Clases",
    "nav_comunidad" => "Comunidad",
    "nav_btn_catalogo" => "Ver Catálogo",
    "hero_tag" => "Bienestar Holístico & Cosmética Natural",
    "hero_title" => "Productos orgánicos y yoga para tu bienestar diario",
    "hero_desc" => "Descubre nuestro catálogo de productos 100% naturales para el cuidado consciente, junto con nuestras clases de yoga diseñadas para reconectar con tu esencia.",
    "hero_btn_prod" => "Ver Productos Naturales",
    "hero_btn_clases" => "Clases de Yoga",
    "link_hero_btn1" => "#productos",
    "link_hero_btn2" => "#clases",
    "prod_tag" => "Cosmética & Aromaterapia",
    "prod_title" => "Catálogo de Productos Naturales",
    "prod_desc" => "Elaborados artesanalmente con ingredientes botánicos orgánicos, libres de químicos para nutrir tu cuerpo y mente.",
    "p1_badge" => "100% Orgánico",
    "p1_title" => "Aceites Esenciales Puros",
    "p1_desc" => "Mezclas botánicas concentradas para aromaterapia, relajación profunda y alivio del estrés cotidiano.",
    "p1_btn" => "Consultar Disponibilidad",
    "link_p1_wa" => "https://wa.me/?text=Hola!%20Quiero%20consultar%20sobre%20los%20Aceites%20Esenciales%20Puros",
    "p2_badge" => "Artesanal",
    "p2_title" => "Cremas & Bálsamos Botánicos",
    "p2_desc" => "Formulaciones nutritivas base plantas y mantecas naturales para restaurar e hidratar tu piel en profundidad.",
    "p2_btn" => "Consultar Disponibilidad",
    "link_p2_wa" => "https://wa.me/?text=Hola!%20Quiero%20consultar%20sobre%20las%20Cremas%20y%20Bálsamos%20Botánicos",
    "p3_badge" => "Cruelty Free",
    "p3_title" => "Sahumerios & Atados de Hierbas",
    "p3_desc" => "Hierbas sagradas y resinas naturales para saumar, limpiar las energías de tus espacios y crear ambientes de paz.",
    "p3_btn" => "Consultar Disponibilidad",
    "link_p3_wa" => "https://wa.me/?text=Hola!%20Quiero%20consultar%20sobre%20los%20Sahumerios%20y%20Atados%20de%20Hierbas",
    "about_title" => "Nuestra Filosofía Padma",
    "about_p1" => "La flor de loto (Padma) simboliza la pureza del cuerpo y la mente que florece en medio de la rutina. Creemos en un estilo de vida consciente, donde los productos que aplicas en tu piel y el movimiento de tu cuerpo vibran en sintonía.",
    "about_p2" => "Seleccionamos materias primas sustentables y cultivamos un espacio de práctica amoroso para tu transformación integral.",
    "classes_title" => "Prácticas de Yoga",
    "classes_desc" => "Movimiento y respiración para conectar con tu presencia y paz interior.",
    "c1_title" => "Vinyasa Flow",
    "c1_desc" => "Secuencias dinámicas coordinadas con la respiración para generar calor interior, fuerza y flexibilidad.",
    "c2_title" => "Hatha Yoga",
    "c2_desc" => "Práctica consciente enfocada en la alineación, sostén de posturas y búsqueda de calma en el movimiento.",
    "c3_title" => "Pranayama & Meditación",
    "c3_desc" => "Sesiones guiadas para calmar el sistema nervioso, liberar el estrés y profundizar en el autoconocimiento.",
    "c1_type" => "image",
    "c1_img_pos" => "50",
    "c1_video_file" => "",
    "c2_type" => "image",
    "c2_img_pos" => "50",
    "c2_video_file" => "",
    "c3_type" => "image",
    "c3_img_pos" => "50",
    "c3_video_file" => "",
    "insta_title" => "Comunidad @padma.y.yoga",
    "insta_desc" => "Descubre más detalles de nuestros productos naturales, tips de bienestar y novedades en Instagram.",
    "link_insta_1" => "https://www.instagram.com/padma.y.yoga/",
    "link_insta_2" => "https://www.instagram.com/padma.y.yoga/",
    "link_insta_3" => "https://www.instagram.com/padma.y.yoga/",
    "link_insta_4" => "https://www.instagram.com/padma.y.yoga/",
    "contact_title" => "¿Quieres hacer un pedido o unirte a clase?",
    "contact_desc" => "Escríbenos por Instagram o WhatsApp para hacernos tu pedido de productos naturales o solicitar información sobre clases de yoga.",
    "contact_btn" => "Contactar vía Instagram",
    "link_contact_btn" => "https://www.instagram.com/padma.y.yoga/",
    "link_contact_insta" => "https://www.instagram.com/padma.y.yoga/",
    "link_contact_wa" => "https://wa.me/",
    "footer_text" => "© 2026 Padma Yoga. Todos los derechos reservados."
];

$texts = $defaultTexts;
if (file_exists($textFile)) {
    $saved = json_decode(file_get_contents($textFile), true);
    if (is_array($saved)) {
        $texts = array_merge($defaultTexts, $saved);
    }
}

// Mapa de imágenes
$imageMap = [
    'img_logo' => 'logo-padma.jpg',
    'img_p1' => 'producto-aceites.jpg',
    'img_p2' => 'producto-cremas.jpg',
    'img_p3' => 'producto-sahumerios.jpg',
    'img_about' => 'foto-nosotros.jpg',
    'img_c1' => 'clase-vinyasa.jpg',
    'img_c2' => 'clase-hatha.jpg',
    'img_c3' => 'clase-meditacion.jpg',
    'img_i1' => 'insta-1.jpg',
    'img_i2' => 'insta-2.jpg',
    'img_i3' => 'insta-3.jpg',
    'img_i4' => 'insta-4.jpg'
];

$successMessage = '';
$errorMessages = [];

// Procesamiento de formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hasErrors = false;

    // 1. Guardar Textos y Links
    if (isset($_POST['texts']) && is_array($_POST['texts'])) {
        foreach ($_POST['texts'] as $k => $v) {
            $texts[$k] = trim($v);
        }

        // Validar permisos de escritura en text.txt (o en el directorio si no existe)
        $isWritable = false;
        if (file_exists($textFile)) {
            $isWritable = is_writable($textFile);
        } else {
            $isWritable = is_writable(dirname($textFile));
        }

        if ($isWritable) {
            $jsonContent = json_encode($texts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if ($jsonContent === false) {
                $errorMessages[] = "Error al formatear los campos como JSON.";
                $hasErrors = true;
            } else {
                $bytes = file_put_contents($textFile, $jsonContent);
                if ($bytes === false) {
                    $errorMessages[] = "No se pudo escribir en el archivo 'text.txt'. Verifique el espacio en disco.";
                    $hasErrors = true;
                }
            }
        } else {
            $pathToCheck = file_exists($textFile) ? $textFile : dirname($textFile);
            $errorMessages[] = "Error de permisos: El servidor no tiene permisos para escribir en '" . basename($pathToCheck) . "' (Ruta: " . realpath($pathToCheck) . "). Por favor, otorgue permisos de escritura (ej. CHMOD 664 o 775/777).";
            $hasErrors = true;
        }
    }

    // 2. Guardar Imágenes
    $uploadedCount = 0;
    foreach ($imageMap as $inputKey => $originalFilename) {
        if (isset($_FILES[$inputKey])) {
            $fileError = $_FILES[$inputKey]['error'];
            if ($fileError === UPLOAD_ERR_OK) {
                // Verificar permisos del directorio de subidas
                if (!is_writable($uploadDir)) {
                    $errorMessages[] = "Error de permisos: La carpeta de subidas 'upload/' no tiene permisos de escritura (Ruta: " . realpath($uploadDir) . ").";
                    $hasErrors = true;
                    continue;
                }

                $tmpPath = $_FILES[$inputKey]['tmp_name'];
                $targetPath = $uploadDir . $originalFilename;

                $isClassImg = in_array($inputKey, ['img_c1', 'img_c2', 'img_c3']);
                $ext = strtolower(pathinfo($_FILES[$inputKey]['name'], PATHINFO_EXTENSION));
                $isVideo = in_array($ext, ['mp4', 'webm', 'mov']);

                if ($isClassImg && $isVideo) {
                    $classNum = substr($inputKey, 5); // '1', '2', '3'
                    $videoFilename = pathinfo($originalFilename, PATHINFO_FILENAME) . '.' . $ext;
                    $targetPath = $uploadDir . $videoFilename;

                    if (move_uploaded_file($tmpPath, $targetPath)) {
                        $uploadedCount++;
                        $texts['c' . $classNum . '_type'] = 'video';
                        $texts['c' . $classNum . '_video_file'] = $videoFilename;
                        file_put_contents($textFile, json_encode($texts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    } else {
                        $errorMessages[] = "No se pudo guardar el video para '" . e($inputKey) . "' en el servidor.";
                        $hasErrors = true;
                    }
                } else {
                    // Validar que sea una imagen válida
                    $imageInfo = @getimagesize($tmpPath);
                    if ($imageInfo === false) {
                        $errorMessages[] = "El archivo subido para '" . e($inputKey) . "' no es una imagen o video válido.";
                        $hasErrors = true;
                        continue;
                    }

                    if (move_uploaded_file($tmpPath, $targetPath)) {
                        $uploadedCount++;
                        if ($isClassImg) {
                            $classNum = substr($inputKey, 5);
                            $texts['c' . $classNum . '_type'] = 'image';
                            file_put_contents($textFile, json_encode($texts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        }
                    } else {
                        $errorMessages[] = "No se pudo guardar la imagen '" . e($originalFilename) . "' en el servidor.";
                        $hasErrors = true;
                    }
                }
            } elseif ($fileError !== UPLOAD_ERR_NO_FILE) {
                $uploadErrorMsg = [
                    UPLOAD_ERR_INI_SIZE   => "El archivo es demasiado grande. El límite del servidor es " . ini_get('upload_max_filesize') . ". Para videos, comprimí el archivo o aumentá el límite en php.ini / .htaccess.",
                    UPLOAD_ERR_FORM_SIZE  => "El archivo supera el tamaño máximo permitido por el formulario.",
                    UPLOAD_ERR_PARTIAL    => "El archivo se subió de forma incompleta. Intentalo de nuevo.",
                    UPLOAD_ERR_NO_TMP_DIR => "Error interno: falta el directorio temporal del servidor.",
                    UPLOAD_ERR_CANT_WRITE => "Error interno: no se puede escribir en el disco del servidor.",
                    UPLOAD_ERR_EXTENSION  => "Una extensión de PHP bloqueó la subida del archivo.",
                ];
                $msg = $uploadErrorMsg[$fileError] ?? "Error desconocido al subir el archivo (código PHP: $fileError).";
                $errorMessages[] = "Error en '". e($inputKey) . "': " . $msg;
                $hasErrors = true;
            }
        }
    }

    if (!$hasErrors) {
        $successMessage = "¡Cambios guardados con éxito! " . ($uploadedCount > 0 ? "($uploadedCount imagen(es) actualizada(s))" : "");
    }
}

// Helper para obtener URL de imagen
function getAdminImgUrl($filename) {
    $uploadPath = __DIR__ . '/upload/' . $filename;
    if (file_exists($uploadPath)) {
        return 'upload/' . $filename . '?v=' . filemtime($uploadPath);
    }
    return '../assets/img/' . $filename;
}

// Helper para escapar HTML
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Panel de Administración | Padma Yoga & Productos Naturales</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Styles principales -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Custom CSS para Administración -->
    <style>
        :root {
            --admin-bar-bg: #2d3748;
            --admin-accent: #38a169;
            --admin-accent-hover: #2f855a;
            --admin-edit-bg: rgba(56, 161, 105, 0.08);
            --admin-edit-border: #38a169;
        }

        /* Barra de administración fija superior */
        .admin-top-bar {
            position: sticky;
            top: 0;
            z-index: 9999;
            background: var(--admin-bar-bg);
            color: #ffffff;
            padding: 12px 24px;
            height: 65px;
            box-sizing: border-box;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .admin-bar-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .admin-bar-title i {
            color: var(--admin-accent);
        }

        .admin-bar-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-admin-save {
            background: var(--admin-accent);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-admin-save:hover {
            background: var(--admin-accent-hover);
            transform: translateY(-1px);
        }

        .btn-admin-view {
            color: #cbd5e0;
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }

        .btn-admin-view:hover {
            color: white;
        }

        .btn-admin-logout {
            color: #feb2b2;
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
            margin-left: 10px;
        }

        .btn-admin-logout:hover {
            color: #fc8181;
        }

        /* Toast / Mensajes */
        .admin-alert {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px 20px;
            text-align: center;
            font-weight: 500;
            position: sticky;
            top: 57px;
            z-index: 9998;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .admin-alert.admin-alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Estilos de inputs editables inline */
        .admin-input, .admin-textarea {
            width: 100%;
            background-color: #ffffff !important;
            border: 1.5px dashed var(--admin-edit-border);
            border-radius: 6px;
            padding: 6px 10px;
            font-family: inherit;
            font-size: inherit;
            font-weight: inherit;
            color: #1a202c !important; /* Texto negro legible en todo momento */
            box-sizing: border-box;
            transition: border-color 0.2s, background-color 0.2s, box-shadow 0.2s;
        }

        .admin-input:focus, .admin-textarea:focus {
            outline: none;
            border-style: solid;
            background-color: #ffffff !important;
            color: #1a202c !important; /* Texto negro legible al enfocar */
            box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.35);
        }

        .admin-textarea {
            resize: vertical;
            min-height: 70px;
        }

        /* Campos de Enlace / Hipervínculo */
        .admin-url-group {
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            background: #ffffff !important;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1.5px dashed var(--admin-edit-border);
            font-size: 0.8rem;
            color: #1a202c !important;
            box-sizing: border-box;
        }

        .admin-url-group i {
            color: #2f855a !important;
            font-size: 0.85rem;
        }

        .admin-input-url {
            flex: 1;
            background: transparent;
            border: none;
            font-family: 'Plus Jakarta Sans', monospace;
            font-size: 0.82rem;
            color: #1a202c !important; /* Texto negro en URLs */
            width: 100%;
        }

        .admin-input-url:focus {
            outline: none;
            background: #ffffff !important;
            color: #1a202c !important;
            border-radius: 4px;
            padding: 2px 6px;
            box-shadow: 0 0 0 2px rgba(56, 161, 105, 0.4);
        }

        /* Image Edit Wrapper */
        .admin-img-container {
            position: relative;
            display: inline-block;
            width: 100%;
            overflow: hidden;
            border-radius: inherit;
        }

        .admin-img-container img {
            display: block;
            width: 100%;
            height: auto;
            transition: filter 0.3s;
        }

        .admin-img-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.55);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
            padding: 10px;
            text-align: center;
        }

        .admin-img-container:hover .admin-img-overlay {
            opacity: 1;
        }

        .admin-img-overlay i {
            font-size: 1.8rem;
            margin-bottom: 6px;
        }

        .admin-img-overlay span {
            font-size: 0.85rem;
            font-weight: 600;
            background: rgba(0, 0, 0, 0.6);
            padding: 4px 10px;
            border-radius: 12px;
        }

        .admin-file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        /* Ajuste específico para el botón de upload en las clases (para no bloquear el arrastre) */
        .class-card .admin-img-overlay {
            top: auto;
            bottom: 12px;
            right: 12px;
            left: auto;
            background: rgba(30, 41, 59, 0.85);
            border-radius: 8px;
            padding: 8px 12px;
            flex-direction: row;
            gap: 8px;
            opacity: 0.9;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .class-card .admin-img-container:hover .admin-img-overlay {
            opacity: 1;
            background: rgba(30, 41, 59, 1);
        }

        .class-card .admin-img-overlay i {
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .class-card .admin-img-overlay span {
            background: transparent;
            padding: 0;
        }

        /* ---- Ajuste de posición por arrastre directo sobre la imagen ---- */
        .class-img {
            overflow: hidden !important;
            position: relative;
        }

        /* Imagen/video draggeable */
        .drag-media {
            width: 100%;
            height: 100% !important;
            object-fit: cover;
            cursor: ns-resize;
            user-select: none;
            -webkit-user-drag: none;
            transition: outline 0.2s !important;
        }

        .drag-media.dragging {
            outline: 2px solid #38a169;
            cursor: grabbing;
            transition: none !important;
        }

        /* Tooltip flotante que muestra la posición mientras se arrastra */
        .drag-tooltip {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(30, 41, 59, 0.9);
            color: #68d391;
            font-size: 0.75rem;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', monospace;
            padding: 4px 12px;
            border-radius: 20px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 20;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
            border: 1px solid rgba(56,161,105,0.4);
        }

        .drag-tooltip.visible {
            opacity: 1;
        }

        /* Hint de arrastre al hacer hover */
        .drag-hint {
            position: absolute;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(30, 41, 59, 0.75);
            color: #e2e8f0;
            font-size: 0.68rem;
            font-weight: 500;
            padding: 3px 10px;
            border-radius: 12px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 20;
            white-space: nowrap;
        }

        .admin-img-container:hover .drag-hint {
            opacity: 1;
        }
    </style>
</head>

<body>

    <form method="POST" enctype="multipart/form-data" id="adminForm">

        <!-- Top Admin Bar -->
        <div class="admin-top-bar">
            <div class="admin-bar-title">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Modo Edición - Padma Admin</span>
                <button type="submit" class="btn-admin-save" style="margin-left: 15px;">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
                </button>
            </div>
            <div class="admin-bar-actions">
                <a href="admin.php" class="btn-admin-view" style="font-weight: bold; border-bottom: 2px solid var(--admin-accent); padding-bottom: 4px;">
                    Contenido Web
                </a>
                <a href="products.php" class="btn-admin-view">
                    Catálogo de Productos
                </a>
                <a href="users.php" class="btn-admin-view">
                    Usuarios
                </a>
                <a href="ajustes.php" class="btn-admin-view">
                    Ajustes
                </a>
                <a href="../index.php" target="_blank" class="btn-admin-view">
                    Ver Sitio
                </a>
                <a href="admin.php?action=logout" class="btn-admin-logout" title="Cerrar Sesión">
                    <i class="fa-solid fa-right-from-bracket"></i> Salir
                </a>
            </div>
        </div>

        <?php if (!empty($successMessage)): ?>
            <div class="admin-alert">
                <i class="fa-solid fa-circle-check"></i> <?php echo e($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessages)): ?>
            <div class="admin-alert admin-alert-danger" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                <div style="font-weight: bold; margin-bottom: 5px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Se encontraron errores al guardar:
                </div>
                <ul style="text-align: left; margin: 0 0 0 20px; padding: 0;">
                    <?php foreach ($errorMessages as $err): ?>
                        <li><?php echo e($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Header Navigation (Los menús no se modifican sus hipervínculos) -->
        <header id="header">
            <a href="#" class="brand-logo">
                <div class="admin-img-container" style="width: 45px; height: 45px; border-radius: 50%;">
                    <img src="<?php echo getAdminImgUrl('logo-padma.jpg'); ?>" id="preview_logo" alt="Padma Yoga Logo">
                    <div class="admin-img-overlay">
                        <i class="fa-solid fa-camera"></i>
                        <input type="file" name="img_logo" accept="image/*" class="admin-file-input" onchange="previewImg(this, 'preview_logo')">
                    </div>
                </div>
                <input type="text" name="texts[brand_name]" value="<?php echo e($texts['brand_name']); ?>" class="admin-input brand-logo-text" style="width: 180px; margin-left: 10px;">
            </a>
            <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú" type="button">
                <i class="fa-solid fa-bars"></i>
            </button>
            <nav>
                <ul id="navMenu">
                    <li><input type="text" name="texts[nav_inicio]" value="<?php echo e($texts['nav_inicio']); ?>" class="admin-input" style="width: 80px;"></li>
                    <li><input type="text" name="texts[nav_productos]" value="<?php echo e($texts['nav_productos']); ?>" class="admin-input" style="width: 150px;"></li>
                    <li><input type="text" name="texts[nav_nosotros]" value="<?php echo e($texts['nav_nosotros']); ?>" class="admin-input" style="width: 90px;"></li>
                    <li><input type="text" name="texts[nav_clases]" value="<?php echo e($texts['nav_clases']); ?>" class="admin-input" style="width: 80px;"></li>
                    <li><input type="text" name="texts[nav_comunidad]" value="<?php echo e($texts['nav_comunidad']); ?>" class="admin-input" style="width: 110px;"></li>
                    <li><input type="text" name="texts[nav_btn_catalogo]" value="<?php echo e($texts['nav_btn_catalogo']); ?>" class="admin-input btn-nav" style="width: 130px; text-align: center;"></li>
                </ul>
            </nav>
        </header>

        <!-- Hero Section -->
        <section class="hero" id="inicio">
            <div class="hero-content">
                <span class="hero-tag" style="display: block; margin-bottom: 10px;">
                    <input type="text" name="texts[hero_tag]" value="<?php echo e($texts['hero_tag']); ?>" class="admin-input" style="text-align: center;">
                </span>
                <h1 style="margin-bottom: 15px;">
                    <input type="text" name="texts[hero_title]" value="<?php echo e($texts['hero_title']); ?>" class="admin-input" style="text-align: center; font-size: inherit; font-family: inherit;">
                </h1>
                <p style="margin-bottom: 20px;">
                    <textarea name="texts[hero_desc]" class="admin-textarea" style="text-align: center;"><?php echo e($texts['hero_desc']); ?></textarea>
                </p>
                <div class="hero-btns" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <div style="display: flex; flex-direction: column; align-items: center; max-width: 250px;">
                        <span class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; width: 100%;">
                            <i class="fa-solid fa-leaf"></i>
                            <input type="text" name="texts[hero_btn_prod]" value="<?php echo e($texts['hero_btn_prod']); ?>" class="admin-input">
                        </span>
                        <div class="admin-url-group" style="width: 100%;">
                            <i class="fa-solid fa-link" title="Enlace / URL"></i>
                            <input type="text" name="texts[link_hero_btn1]" value="<?php echo e($texts['link_hero_btn1']); ?>" class="admin-input-url" placeholder="URL o #seccion">
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; align-items: center; max-width: 250px;">
                        <span class="btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; width: 100%;">
                            <i class="fa-solid fa-spa"></i>
                            <input type="text" name="texts[hero_btn_clases]" value="<?php echo e($texts['hero_btn_clases']); ?>" class="admin-input">
                        </span>
                        <div class="admin-url-group" style="width: 100%;">
                            <i class="fa-solid fa-link" title="Enlace / URL"></i>
                            <input type="text" name="texts[link_hero_btn2]" value="<?php echo e($texts['link_hero_btn2']); ?>" class="admin-input-url" placeholder="URL o #seccion">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Products Catalog Section -->
        <section class="products" id="productos">
            <div class="section-header">
                <span class="tag-badge" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-seedling"></i>
                    <input type="text" name="texts[prod_tag]" value="<?php echo e($texts['prod_tag']); ?>" class="admin-input" style="width: 220px;">
                </span>
                <h2 style="margin: 15px 0;">
                    <input type="text" name="texts[prod_title]" value="<?php echo e($texts['prod_title']); ?>" class="admin-input" style="text-align: center; font-size: inherit;">
                </h2>
                <p>
                    <textarea name="texts[prod_desc]" class="admin-textarea" style="text-align: center;"><?php echo e($texts['prod_desc']); ?></textarea>
                </p>
            </div>

            <div class="products-grid">
                <div class="admin-alert" style="grid-column: 1 / -1; margin-top: 20px;">
                    <i class="fa-solid fa-circle-info"></i> Los productos mostrados en la página de inicio se gestionan desde el <a href="products.php" style="color: inherit; text-decoration: underline; font-weight: bold;">Catálogo de Productos</a>. Se mostrarán automáticamente los 3 productos destacados.
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="nosotros">
            <div class="about-grid">
                <div class="about-image">
                    <div class="admin-img-container">
                        <img src="<?php echo getAdminImgUrl('foto-nosotros.jpg'); ?>" id="preview_about" alt="Espacio Padma Yoga">
                        <div class="admin-img-overlay">
                            <i class="fa-solid fa-camera"></i>
                            <span>Cambiar Imagen Nosotros</span>
                            <input type="file" name="img_about" accept="image/*" class="admin-file-input" onchange="previewImg(this, 'preview_about')">
                        </div>
                    </div>
                </div>
                <div class="about-text">
                    <h3>
                        <input type="text" name="texts[about_title]" value="<?php echo e($texts['about_title']); ?>" class="admin-input" style="font-size: inherit;">
                    </h3>
                    <p>
                        <textarea name="texts[about_p1]" class="admin-textarea"><?php echo e($texts['about_p1']); ?></textarea>
                    </p>
                    <p>
                        <textarea name="texts[about_p2]" class="admin-textarea"><?php echo e($texts['about_p2']); ?></textarea>
                    </p>
                </div>
            </div>
        </section>

        <!-- Classes Section -->
        <section class="classes" id="clases">
            <div class="section-header">
                <h2>
                    <input type="text" name="texts[classes_title]" value="<?php echo e($texts['classes_title']); ?>" class="admin-input" style="text-align: center; font-size: inherit;">
                </h2>
                <p>
                    <textarea name="texts[classes_desc]" class="admin-textarea" style="text-align: center;"><?php echo e($texts['classes_desc']); ?></textarea>
                </p>
            </div>
            <div class="classes-grid">
                <!-- Clase 1 -->
                <div class="class-card">
                    <div class="class-img">
                        <div class="admin-img-container" style="position: relative; height: 100%;">
                            <?php if (($texts['c1_type'] ?? 'image') === 'video' && !empty($texts['c1_video_file'])): ?>
                                <video id="preview_c1" src="<?php echo getAdminImgUrl($texts['c1_video_file']); ?>" autoplay loop muted playsinline class="drag-media" draggable="false" data-pos-input="pos_c1_img_pos"></video>
                            <?php else: ?>
                                <img id="preview_c1" src="<?php echo getAdminImgUrl('clase-vinyasa.jpg'); ?>" alt="Vinyasa Flow" class="drag-media" draggable="false" data-pos-input="pos_c1_img_pos" style="object-position: 50% <?php echo e($texts['c1_img_pos'] ?? '50'); ?>%;">
                            <?php endif; ?>
                            <input type="hidden" id="pos_c1_img_pos" name="texts[c1_img_pos]" value="<?php echo e($texts['c1_img_pos'] ?? '50'); ?>">
                            <div class="drag-tooltip" id="tooltip_c1">↕ <?php echo e($texts['c1_img_pos'] ?? '50'); ?>%</div>
                            <div class="drag-hint">↕ Arrastrar para ajustar</div>
                            <div class="admin-img-overlay">
                                <i class="fa-solid fa-camera"></i>
                                <span>Cambiar Medio</span>
                                <input type="file" name="img_c1" accept="image/*,video/mp4,video/webm" class="admin-file-input" onchange="previewMedia(this, 'preview_c1', 'pos_c1_img_pos')">
                            </div>
                        </div>
                    </div>
                    <div class="class-info">
                        <h3>
                            <input type="text" name="texts[c1_title]" value="<?php echo e($texts['c1_title']); ?>" class="admin-input" style="font-size: inherit;">
                        </h3>
                        <p>
                            <textarea name="texts[c1_desc]" class="admin-textarea"><?php echo e($texts['c1_desc']); ?></textarea>
                        </p>
                    </div>
                </div>

                <!-- Clase 2 -->
                <div class="class-card">
                    <div class="class-img">
                        <div class="admin-img-container" style="position: relative; height: 100%;">
                            <?php if (($texts['c2_type'] ?? 'image') === 'video' && !empty($texts['c2_video_file'])): ?>
                                <video id="preview_c2" src="<?php echo getAdminImgUrl($texts['c2_video_file']); ?>" autoplay loop muted playsinline class="drag-media" draggable="false" data-pos-input="pos_c2_img_pos"></video>
                            <?php else: ?>
                                <img id="preview_c2" src="<?php echo getAdminImgUrl('clase-hatha.jpg'); ?>" alt="Hatha Yoga" class="drag-media" draggable="false" data-pos-input="pos_c2_img_pos" style="object-position: 50% <?php echo e($texts['c2_img_pos'] ?? '50'); ?>%;">
                            <?php endif; ?>
                            <input type="hidden" id="pos_c2_img_pos" name="texts[c2_img_pos]" value="<?php echo e($texts['c2_img_pos'] ?? '50'); ?>">
                            <div class="drag-tooltip" id="tooltip_c2">↕ <?php echo e($texts['c2_img_pos'] ?? '50'); ?>%</div>
                            <div class="drag-hint">↕ Arrastrar para ajustar</div>
                            <div class="admin-img-overlay">
                                <i class="fa-solid fa-camera"></i>
                                <span>Cambiar Medio</span>
                                <input type="file" name="img_c2" accept="image/*,video/mp4,video/webm" class="admin-file-input" onchange="previewMedia(this, 'preview_c2', 'pos_c2_img_pos')">
                            </div>
                        </div>
                    </div>
                    <div class="class-info">
                        <h3>
                            <input type="text" name="texts[c2_title]" value="<?php echo e($texts['c2_title']); ?>" class="admin-input" style="font-size: inherit;">
                        </h3>
                        <p>
                            <textarea name="texts[c2_desc]" class="admin-textarea"><?php echo e($texts['c2_desc']); ?></textarea>
                        </p>
                    </div>
                </div>

                <!-- Clase 3 -->
                <div class="class-card">
                    <div class="class-img">
                        <div class="admin-img-container" style="position: relative; height: 100%;">
                            <?php if (($texts['c3_type'] ?? 'image') === 'video' && !empty($texts['c3_video_file'])): ?>
                                <video id="preview_c3" src="<?php echo getAdminImgUrl($texts['c3_video_file']); ?>" autoplay loop muted playsinline class="drag-media" draggable="false" data-pos-input="pos_c3_img_pos"></video>
                            <?php else: ?>
                                <img id="preview_c3" src="<?php echo getAdminImgUrl('clase-meditacion.jpg'); ?>" alt="Pranayama & Meditación" class="drag-media" draggable="false" data-pos-input="pos_c3_img_pos" style="object-position: 50% <?php echo e($texts['c3_img_pos'] ?? '50'); ?>%;">
                            <?php endif; ?>
                            <input type="hidden" id="pos_c3_img_pos" name="texts[c3_img_pos]" value="<?php echo e($texts['c3_img_pos'] ?? '50'); ?>">
                            <div class="drag-tooltip" id="tooltip_c3">↕ <?php echo e($texts['c3_img_pos'] ?? '50'); ?>%</div>
                            <div class="drag-hint">↕ Arrastrar para ajustar</div>
                            <div class="admin-img-overlay">
                                <i class="fa-solid fa-camera"></i>
                                <span>Cambiar Medio</span>
                                <input type="file" name="img_c3" accept="image/*,video/mp4,video/webm" class="admin-file-input" onchange="previewMedia(this, 'preview_c3', 'pos_c3_img_pos')">
                            </div>
                        </div>
                    </div>
                    <div class="class-info">
                        <h3>
                            <input type="text" name="texts[c3_title]" value="<?php echo e($texts['c3_title']); ?>" class="admin-input" style="font-size: inherit;">
                        </h3>
                        <p>
                            <textarea name="texts[c3_desc]" class="admin-textarea"><?php echo e($texts['c3_desc']); ?></textarea>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Instagram Section -->
        <section class="insta-feed" id="comunidad">
            <div class="section-header">
                <h2>
                    <input type="text" name="texts[insta_title]" value="<?php echo e($texts['insta_title']); ?>" class="admin-input" style="text-align: center; font-size: inherit;">
                </h2>
                <p>
                    <textarea name="texts[insta_desc]" class="admin-textarea" style="text-align: center;"><?php echo e($texts['insta_desc']); ?></textarea>
                </p>
            </div>
            <div class="insta-grid">
                <div class="admin-alert" style="grid-column: 1 / -1; margin-top: 20px;">
                    <i class="fa-brands fa-instagram"></i> El feed de Instagram se carga automáticamente desde la cuenta <strong>@padma.y.yoga</strong> a través del widget de Elfsight. No es editable desde aquí.
                </div>
            </div>
        </section>

        <!-- Contact Banner -->
        <section class="contact">
            <h2>
                <input type="text" name="texts[contact_title]" value="<?php echo e($texts['contact_title']); ?>" class="admin-input" style="text-align: center; font-size: inherit;">
            </h2>
            <p>
                <textarea name="texts[contact_desc]" class="admin-textarea" style="text-align: center;"><?php echo e($texts['contact_desc']); ?></textarea>
            </p>
            <div style="display: flex; flex-direction: column; align-items: center; gap: 10px; max-width: 320px; margin: 0 auto 20px auto;">
                <span class="btn-primary" style="background-color: var(--white); color: var(--accent-sage-dark); display: inline-flex; align-items: center; gap: 8px; width: 100%;">
                    <i class="fa-brands fa-instagram"></i>
                    <input type="text" name="texts[contact_btn]" value="<?php echo e($texts['contact_btn']); ?>" class="admin-input">
                </span>
                <div class="admin-url-group" style="width: 100%;">
                    <i class="fa-solid fa-link" title="Enlace Botón Contactar Instagram"></i>
                    <input type="text" name="texts[link_contact_btn]" value="<?php echo e($texts['link_contact_btn']); ?>" class="admin-input-url" placeholder="URL Instagram">
                </div>
            </div>
            
            <div class="social-links" style="display: flex; flex-direction: column; gap: 10px; align-items: center;">
                <div style="display: flex; gap: 15px;">
                    <span class="social-btn"><i class="fa-brands fa-instagram"></i></span>
                    <span class="social-btn"><i class="fa-brands fa-whatsapp"></i></span>
                </div>
                <div style="display: flex; gap: 15px; width: 100%; max-width: 500px; flex-wrap: wrap;">
                    <div class="admin-url-group" style="flex: 1; min-width: 200px;">
                        <i class="fa-brands fa-instagram"></i>
                        <input type="text" name="texts[link_contact_insta]" value="<?php echo e($texts['link_contact_insta']); ?>" class="admin-input-url" placeholder="URL Instagram Icono">
                    </div>
                    <div class="admin-url-group" style="flex: 1; min-width: 200px;">
                        <i class="fa-brands fa-whatsapp"></i>
                        <input type="text" name="texts[link_contact_wa]" value="<?php echo e($texts['link_contact_wa']); ?>" class="admin-input-url" placeholder="URL WhatsApp Icono">
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer style="padding: 20px;">
            <p>
                <input type="text" name="texts[footer_text]" value="<?php echo e($texts['footer_text']); ?>" class="admin-input" style="text-align: center; font-size: 0.9rem;">
            </p>
        </footer>

    </form>

    <!-- JavaScript -->
    <script src="../assets/js/main.js"></script>
    <script>
        // ===== PREVIEW DE IMAGEN / VIDEO =====
        function previewImg(input, targetId) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            const isVideo = file.type.startsWith('video/');
            const reader = new FileReader();
            reader.onload = function(e) {
                let el = document.getElementById(targetId);
                if (!el) return;
                if (isVideo && el.tagName === 'IMG') {
                    const video = document.createElement('video');
                    video.id = targetId; video.autoplay = true; video.loop = true;
                    video.muted = true; video.setAttribute('playsinline', '');
                    video.className = el.className;
                    video.dataset.posInput = el.dataset.posInput || '';
                    video.draggable = false;
                    video.style.cssText = 'width:100%;height:100%;object-fit:cover;cursor:ns-resize;user-select:none;';
                    el.parentNode.replaceChild(video, el);
                    el = video;
                    initDragMedia(el);
                } else if (!isVideo && el.tagName === 'VIDEO') {
                    const img = document.createElement('img');
                    img.id = targetId; img.alt = '';
                    img.className = el.className;
                    img.dataset.posInput = el.dataset.posInput || '';
                    img.draggable = false;
                    img.style.cssText = 'width:100%;height:100%;object-fit:cover;cursor:ns-resize;user-select:none;';
                    el.parentNode.replaceChild(img, el);
                    el = img;
                    initDragMedia(el);
                }
                el.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        function previewMedia(input, targetId, posInputId) {
            previewImg(input, targetId);
        }

        // ===== ARRASTRE VERTICAL PARA AJUSTAR POSICIÓN =====
        function initDragMedia(el) {
            if (!el || el._dragInit) return;
            el._dragInit = true;

            const posInputId = el.dataset.posInput;
            // El tooltip es el hermano .drag-tooltip dentro del mismo container
            const container = el.closest('.admin-img-container');
            const tooltip = container ? container.querySelector('.drag-tooltip') : null;

            let startY = 0;
            let startPos = 0;
            let dragging = false;
            let tooltipTimer = null;

            function getPos() {
                const input = posInputId ? document.getElementById(posInputId) : null;
                return input ? parseInt(input.value, 10) : 50;
            }

            function setPos(val) {
                val = Math.max(-200, Math.min(300, Math.round(val)));
                el.style.objectPosition = '50% ' + val + '%';
                const input = posInputId ? document.getElementById(posInputId) : null;
                if (input) input.value = val;
                if (tooltip) {
                    tooltip.textContent = '\u2195 ' + val + '%';
                    tooltip.classList.add('visible');
                    clearTimeout(tooltipTimer);
                    tooltipTimer = setTimeout(() => tooltip.classList.remove('visible'), 1200);
                }
            }

            el.addEventListener('mousedown', function(e) {
                // Solo click izquierdo, no en el overlay de cambio de archivo
                if (e.button !== 0) return;
                e.preventDefault();
                dragging = true;
                startY = e.clientY;
                startPos = getPos();
                el.classList.add('dragging');
                if (tooltip) tooltip.classList.add('visible');
            });

            el.addEventListener('dragstart', function(e) {
                e.preventDefault();
            });

            document.addEventListener('mousemove', function(e) {
                if (!dragging) return;
                // Mover hacia arriba = reducir %, mover hacia abajo = aumentar %
                const deltaY = e.clientY - startY;
                const containerH = el.offsetHeight || 200;
                const deltaPct = (deltaY / containerH) * 100;
                setPos(startPos + deltaPct);
            });

            document.addEventListener('mouseup', function() {
                if (!dragging) return;
                dragging = false;
                el.classList.remove('dragging');
                setTimeout(() => { if (tooltip) tooltip.classList.remove('visible'); }, 1200);
            });

            // Soporte táctil
            el.addEventListener('touchstart', function(e) {
                e.preventDefault();
                startY = e.touches[0].clientY;
                startPos = getPos();
                dragging = true;
                el.classList.add('dragging');
            }, { passive: false });

            el.addEventListener('touchmove', function(e) {
                if (!dragging) return;
                e.preventDefault();
                const deltaY = e.touches[0].clientY - startY;
                const containerH = el.offsetHeight || 200;
                const deltaPct = (deltaY / containerH) * 100;
                setPos(startPos + deltaPct);
            }, { passive: false });

            el.addEventListener('touchend', function() {
                dragging = false;
                el.classList.remove('dragging');
                setTimeout(() => { if (tooltip) tooltip.classList.remove('visible'); }, 1200);
            });
        }

        // Inicializar todos los drag-media al cargar
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.drag-media').forEach(initDragMedia);
        });
    </script>
</body>

</html>