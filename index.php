<?php
$textFile = __DIR__ . '/admin/text.txt';
$uploadDir = __DIR__ . '/admin/upload/';

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
    "link_contact_wa" => "https://wa.me/"
];

$texts = $defaultTexts;
if (file_exists($textFile)) {
    $saved = json_decode(file_get_contents($textFile), true);
    if (is_array($saved)) {
        $texts = array_merge($defaultTexts, $saved);
    }
}

function getImgUrl($filename) {
    $uploadPath = __DIR__ . '/admin/upload/' . $filename;
    if (file_exists($uploadPath)) {
        return 'admin/upload/' . $filename . '?v=' . filemtime($uploadPath);
    }
    return 'assets/img/' . $filename;
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Padma Yoga & Productos Naturales | Bienestar Holístico</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- Header Navigation -->
    <header id="header">
        <a href="#" class="brand-logo">
            <img src="<?php echo getImgUrl('logo-padma.jpg'); ?>" alt="Padma Yoga Logo">
            <span class="brand-logo-text"><?php echo e($texts['brand_name']); ?></span>
        </a>
        <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
            <i class="fa-solid fa-bars"></i>
        </button>
        <nav>
            <ul id="navMenu">
                <li><a href="#inicio" class="nav-link"><?php echo e($texts['nav_inicio']); ?></a></li>
                <li><a href="#productos" class="nav-link"><?php echo e($texts['nav_productos']); ?></a></li>
                <li><a href="#nosotros" class="nav-link"><?php echo e($texts['nav_nosotros']); ?></a></li>
                <li><a href="#clases" class="nav-link"><?php echo e($texts['nav_clases']); ?></a></li>
                <li><a href="#comunidad" class="nav-link"><?php echo e($texts['nav_comunidad']); ?></a></li>
                <li><a href="#productos" class="btn-nav"><?php echo e($texts['nav_btn_catalogo']); ?></a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="hero-content reveal">
            <span class="hero-tag"><?php echo e($texts['hero_tag']); ?></span>
            <h1><?php echo e($texts['hero_title']); ?></h1>
            <p><?php echo e($texts['hero_desc']); ?></p>
            <div class="hero-btns">
                <a href="<?php echo e($texts['link_hero_btn1']); ?>" class="btn-primary">
                    <i class="fa-solid fa-leaf"></i> <?php echo e($texts['hero_btn_prod']); ?>

                </a>
                <a href="<?php echo e($texts['link_hero_btn2']); ?>" class="btn-secondary">
                    <i class="fa-solid fa-spa"></i> <?php echo e($texts['hero_btn_clases']); ?>

                </a>
            </div>
        </div>
    </section>

    <!-- Products Catalog Section -->
    <section class="products" id="productos">
        <div class="section-header reveal">
            <span class="tag-badge"><i class="fa-solid fa-seedling"></i> <?php echo e($texts['prod_tag']); ?></span>
            <h2><?php echo e($texts['prod_title']); ?></h2>
            <p><?php echo e($texts['prod_desc']); ?></p>
        </div>
        
        <div class="products-grid">
            <!-- Producto 1 -->
            <div class="product-card reveal">
                <span class="product-badge"><?php echo e($texts['p1_badge']); ?></span>
                <div class="product-img">
                    <img src="<?php echo getImgUrl('producto-aceites.jpg'); ?>" alt="<?php echo e($texts['p1_title']); ?>">
                </div>
                <div class="product-info">
                    <h3><?php echo e($texts['p1_title']); ?></h3>
                    <p><?php echo e($texts['p1_desc']); ?></p>
                    <a href="<?php echo e($texts['link_p1_wa']); ?>" target="_blank" class="btn-consult">
                        <i class="fa-brands fa-whatsapp"></i> <?php echo e($texts['p1_btn']); ?>

                    </a>
                </div>
            </div>

            <!-- Producto 2 -->
            <div class="product-card reveal">
                <span class="product-badge"><?php echo e($texts['p2_badge']); ?></span>
                <div class="product-img">
                    <img src="<?php echo getImgUrl('producto-cremas.jpg'); ?>" alt="<?php echo e($texts['p2_title']); ?>">
                </div>
                <div class="product-info">
                    <h3><?php echo e($texts['p2_title']); ?></h3>
                    <p><?php echo e($texts['p2_desc']); ?></p>
                    <a href="<?php echo e($texts['link_p2_wa']); ?>" target="_blank" class="btn-consult">
                        <i class="fa-brands fa-whatsapp"></i> <?php echo e($texts['p2_btn']); ?>

                    </a>
                </div>
            </div>

            <!-- Producto 3 -->
            <div class="product-card reveal">
                <span class="product-badge"><?php echo e($texts['p3_badge']); ?></span>
                <div class="product-img">
                    <img src="<?php echo getImgUrl('producto-sahumerios.jpg'); ?>" alt="<?php echo e($texts['p3_title']); ?>">
                </div>
                <div class="product-info">
                    <h3><?php echo e($texts['p3_title']); ?></h3>
                    <p><?php echo e($texts['p3_desc']); ?></p>
                    <a href="<?php echo e($texts['link_p3_wa']); ?>" target="_blank" class="btn-consult">
                        <i class="fa-brands fa-whatsapp"></i> <?php echo e($texts['p3_btn']); ?>

                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="nosotros">
        <div class="about-grid">
            <div class="about-image reveal">
                <img src="<?php echo getImgUrl('foto-nosotros.jpg'); ?>" alt="Espacio Padma Yoga">
            </div>
            <div class="about-text reveal">
                <h3><?php echo e($texts['about_title']); ?></h3>
                <p><?php echo e($texts['about_p1']); ?></p>
                <p><?php echo e($texts['about_p2']); ?></p>
            </div>
        </div>
    </section>

    <!-- Classes Section -->
    <section class="classes" id="clases">
        <div class="section-header reveal">
            <h2><?php echo e($texts['classes_title']); ?></h2>
            <p><?php echo e($texts['classes_desc']); ?></p>
        </div>
        <div class="classes-grid">
            <div class="class-card reveal">
                <div class="class-img">
                    <img src="<?php echo getImgUrl('clase-vinyasa.jpg'); ?>" alt="<?php echo e($texts['c1_title']); ?>">
                </div>
                <div class="class-info">
                    <h3><?php echo e($texts['c1_title']); ?></h3>
                    <p><?php echo e($texts['c1_desc']); ?></p>
                </div>
            </div>
            <div class="class-card reveal">
                <div class="class-img">
                    <img src="<?php echo getImgUrl('clase-hatha.jpg'); ?>" alt="<?php echo e($texts['c2_title']); ?>">
                </div>
                <div class="class-info">
                    <h3><?php echo e($texts['c2_title']); ?></h3>
                    <p><?php echo e($texts['c2_desc']); ?></p>
                </div>
            </div>
            <div class="class-card reveal">
                <div class="class-img">
                    <img src="<?php echo getImgUrl('clase-meditacion.jpg'); ?>" alt="<?php echo e($texts['c3_title']); ?>">
                </div>
                <div class="class-info">
                    <h3><?php echo e($texts['c3_title']); ?></h3>
                    <p><?php echo e($texts['c3_desc']); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Instagram Section -->
    <section class="insta-feed" id="comunidad">
        <div class="section-header reveal">
            <h2><?php echo e($texts['insta_title']); ?></h2>
            <p><?php echo e($texts['insta_desc']); ?></p>
        </div>
        <div class="insta-grid">
            <a href="<?php echo e($texts['link_insta_1']); ?>" target="_blank" class="insta-item reveal">
                <img src="<?php echo getImgUrl('insta-1.jpg'); ?>" alt="Publicación Padma Yoga 1">
                <div class="insta-overlay"><i class="fa-brands fa-instagram"></i></div>
            </a>
            <a href="<?php echo e($texts['link_insta_2']); ?>" target="_blank" class="insta-item reveal">
                <img src="<?php echo getImgUrl('insta-2.jpg'); ?>" alt="Publicación Padma Yoga 2">
                <div class="insta-overlay"><i class="fa-brands fa-instagram"></i></div>
            </a>
            <a href="<?php echo e($texts['link_insta_3']); ?>" target="_blank" class="insta-item reveal">
                <img src="<?php echo getImgUrl('insta-3.jpg'); ?>" alt="Publicación Padma Yoga 3">
                <div class="insta-overlay"><i class="fa-brands fa-instagram"></i></div>
            </a>
            <a href="<?php echo e($texts['link_insta_4']); ?>" target="_blank" class="insta-item reveal">
                <img src="<?php echo getImgUrl('insta-4.jpg'); ?>" alt="Publicación Padma Yoga 4">
                <div class="insta-overlay"><i class="fa-brands fa-instagram"></i></div>
            </a>
        </div>
    </section>

    <!-- Contact Banner -->
    <section class="contact reveal">
        <h2><?php echo e($texts['contact_title']); ?></h2>
        <p><?php echo e($texts['contact_desc']); ?></p>
        <a href="<?php echo e($texts['link_contact_btn']); ?>" target="_blank" class="btn-primary" style="background-color: var(--white); color: var(--accent-sage-dark);">
            <i class="fa-brands fa-instagram"></i> <?php echo e($texts['contact_btn']); ?>

        </a>
        <div class="social-links">
            <a href="<?php echo e($texts['link_contact_insta']); ?>" target="_blank" class="social-btn"><i class="fa-brands fa-instagram"></i></a>
            <a href="<?php echo e($texts['link_contact_wa']); ?>" target="_blank" class="social-btn"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p><?php echo e($texts['footer_text']); ?></p>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>

</html>
