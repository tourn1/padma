<?php
// Catálogo de productos PADMA - Bienestar Emocional
$catalogFile = dirname(__FILE__) . '/admin/upload/catalog.txt';

$productos = [];
$loadedFromCatalog = false;

if (file_exists($catalogFile)) {
    $content = file_get_contents($catalogFile);
    $savedProducts = json_decode($content, true);
    if (is_array($savedProducts)) {
        // Ordenar por el campo 'orden'
        usort($savedProducts, function ($a, $b) {
            return ($a['orden'] ?? 0) <=> ($b['orden'] ?? 0);
        });

        foreach ($savedProducts as $sp) {
            // Mostrar solo si está activo
            if (!isset($sp['activo']) || $sp['activo'] === true) {
                $productos[] = [
                    'titulo' => $sp['nombre'],
                    'descripcion' => $sp['descripcion'],
                    'imagen' => $sp['imagen'] ?? '',
                    'imagenes' => $sp['imagenes'] ?? []
                ];
            }
        }
        $loadedFromCatalog = true;
    }
}

if (!$loadedFromCatalog) {
    // Fallback: Productos estáticos originales
    $productos = [
        /*[
            'titulo' => 'Roll on Concentrado Natural - Anti-estrés (Pitta)',
            'descripcion' => 'Reduce el estrés, el embotamiento y cansancio mental. Libera las tensiones mentales y abre las vías respiratorias.',
            'imagen' => ''
        ],
        [
            'titulo' => 'Roll on Concentrado Natural - Insomnio (Vata)',
            'descripcion' => 'Reduce la ansiedad, el nerviosismo y el insomnio. Calmante, reduce la culpa, libera las emociones, aquieta la mente.',
            'imagen' => ''
        ],
        [
            'titulo' => 'Roll on Concentrado Natural - Energía (Kapha)',
            'descripcion' => 'Reduce la depresión, fatiga y cansancio. Reduce la ansiedad y al mismo tiempo energiza. Regula los humores y aumenta la autoconfianza.',
            'imagen' => ''
        ],
        [
            'titulo' => 'Set de Roll on Concentrado Natural (Energía - Stress - Insomnio)',
            'descripcion' => '• Energía (Kapha): Reduce la depresión, fatiga y cansancio. Reduce la ansiedad y al mismo tiempo energiza. Regula los humores y aumenta la autoconfianza.<br>• Stress (Pitta): Reduce el estrés, el embotamiento y cansancio mental. Libera las tensiones mentales y abre las vías respiratorias.<br>• Insomnio (Vata): Reduce la ansiedad, el nerviosismo y el insomnio. Calmante, reduce la culpa, libera las emociones, aquieta la mente.',
            'imagen' => ''
        ],
        [
            'titulo' => 'Aceites esenciales: Naranja, Eucalipto y Neroli',
            'descripcion' => '<strong>Naranja:</strong> Ayuda a aplacar los enojos e irritaciones, es antidepresivo, lo llaman anti conflicto. Se puede utilizar en difusores, hornillo a vela o eléctrico, inhalaciones, o diluido en aceite vehicular o crema sobre la piel.<br><strong>Eucalipto:</strong> Además de su fresco aroma, tiene importantes propiedades anti bacterianas para el dolor e inflamación de las membranas de las mucosas del tracto respiratorio, tos, asma, bronquitis, dolor sinusal, e infecciones respiratorias. Ayuda a prepararse para los cambios bruscos de estación. Se puede utilizar en difusores, hornillo a vela o eléctrico, inhalaciones, o diluido en aceite vehicular o crema sobre la piel.<br><strong>Neroli:</strong> Estimula los afectos y renueva la seguridad en uno mismo. Estimula la renovación celular, nutre, hidrata, y tonifica la piel. Se puede utilizar en difusores, hornillo a vela o eléctrico, inhalaciones, o diluido en aceite vehicular o crema sobre la piel.',
            'imagen' => ''
        ],
        [
            'titulo' => 'Aceites esenciales: Bergamota, Menta y Geranio',
            'descripcion' => '<strong>Bergamota:</strong> El aceite esencial de Bergamota reduce la ansiedad, alivia la depresión, mejora el humor, la aplicación del aceite diluido sobre la piel es capaz de reducir la presión arterial, tanto el alta como la baja. Se puede utilizar en difusores, hornillo a vela o eléctrico, inhalaciones, o diluido en aceite vehicular o crema sobre la piel.<br><strong>Menta:</strong> Tiene efectos estimulantes y es usado para reducir el estrés y aliviar las tensiones y los nervios, así como para mejorar la memoria y la concentración. Por ello es muy común usarlo para el dolor de cabeza y las migrañas. Se puede utilizar en difusores, hornillo a vela o eléctrico, inhalaciones, o diluido en aceite vehicular o crema sobre la piel.<br><strong>Geranio:</strong> Es armonizante, ayuda a transitar los ciclos de la mujer. También es astringente, estimulante circulatorio ayuda a mejorar la retención de líquidos, apto para pieles grasas. Se puede utilizar en difusores, hornillo a vela o eléctrico, inhalaciones, o diluido en aceite vehicular o crema sobre la piel.',
            'imagen' => ''
        ],
        [
            'titulo' => 'Línea completa de Aceites esenciales',
            'descripcion' => '<strong>Tea Tree:</strong> Destaca por sus propiedades antisépticas, anti fúngicas y antibióticas. Es también cicatrizante y tiene propiedades calmantes y purificantes. Se puede utilizar en difusores, hornillo a vela o eléctrico, inhalaciones, o directamente sobre la piel.<br><strong>Lavanda:</strong> Su aroma calma y aquieta los nervios, la ansiedad y el estrés ayudando a conciliar el sueño. Es el aceite ideal para tener en el botiquín de primeros auxilios en el caso de quemaduras, eczemas y alergias. Se puede utilizar en difusores, hornillo a vela o eléctrico, inhalaciones, o diluido en aceite vehicular o crema sobre la piel.<br><strong>Eucalipto:</strong> Además de su fresco aroma, tiene importantes propiedades anti bacterianas para el dolor e inflamación de las membranas de las mucosas del tracto respiratorio, tos, asma, bronquitis, dolor sinusal, e infecciones respiratorias. Ayuda a prepararse para los cambios bruscos de estación.<br><strong>Naranja:</strong> Ayuda a aplacar los enojos e irritaciones, es antidepresivo, lo llaman anti conflicto.<br><strong>Menta:</strong> Tiene efectos estimulantes y es usado para reducir el estrés y aliviar las tensiones y los nervios, así como para mejorar la memoria y la concentración.<br><em>Nota: la línea incluye además Romero, Manzanilla, Geranio, Jazmín, Neroli y Limón.</em>',
            'imagen' => ''
        ],
        [
            'titulo' => 'Body Splash: Mango y Maracuyá / Uva / Vainilla y Benjui',
            'descripcion' => '<strong>Mango-Maracuyá:</strong> Contiene esencia de mango-maracuyá. Reduce el cansancio mental, libera tensiones.<br><strong>Uva:</strong> Contiene esencia de uva. Activa el sistema nervioso reduciendo el cansancio, la fatiga y el agotamiento. Suministra energía y vitalidad. Alivia los estados de bloqueos psico-emocionales.<br><strong>Vainilla y Benjui:</strong> Contiene esencia de benjui y vainilla. Reduce la ansiedad, dando calma, paz, prosperidad.',
            'imagen' => ''
        ],
        [
            'titulo' => 'Splash Meditación',
            'descripcion' => 'Contiene esencias puras de lavanda y sándalo. La lavanda calma la energía del espacio y el individuo, el sándalo proyecta un lugar de tranquilidad, ayudando a mantenerse despierto. Ideal para la meditación.',
            'imagen' => ''
        ],
        [
            'titulo' => 'Splash Sueña',
            'descripcion' => 'Contiene esencias de lavanda, melisa y manzanilla. Induce al descanso relajando, sacando tensión nerviosa, sedante natural, ayuda al buen dormir. Ideal para niños a partir de los 3 meses. Se utiliza rociando la almohada antes de dormir.',
            'imagen' => ''
        ]*/
    ];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LGGNT2486F"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-LGGNT2486F');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo PADMA - Bienestar Emocional</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* --- Estilos inspirados en padmayyoga.com --- */
        :root {
            --bg-primary: #FAF7F2;
            --bg-secondary: #F2ECE1;
            --accent-sage: #7A8B7B;
            --accent-sage-dark: #586759;
            --accent-terracotta: #D08C70;
            --text-main: #2C332C;
            --text-muted: #656E65;
            --white: #FFFFFF;
            --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.05);
            --transition-smooth: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            --radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-main);
            line-height: 1.6;
            padding: 2rem 1.5rem;
        }

        h1,
        h2,
        h3,
        .brand {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* --- Top Nav --- */
        #top-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 1rem 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            background: rgba(250, 247, 242, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .brand-logo {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo img {
            height: 42px;
            width: 42px;
            object-fit: cover;
            border-radius: 50%;
        }

        .brand-logo-text {
            font-size: 1.6rem;
            letter-spacing: 1.5px;
            color: var(--accent-sage-dark);
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
        }

        #top-nav nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .btn-nav {
            background-color: var(--accent-sage);
            color: var(--white) !important;
            padding: 0.6rem 1.4rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-block;
        }

        .btn-nav:hover {
            background-color: var(--accent-sage-dark);
            transform: translateY(-2px);
        }

        /* --- Header --- */
        .header {
            text-align: center;
            padding: 6rem 0 3rem;
            /* padding top para no quedar tapado por top-nav */
            border-bottom: 1px solid rgba(122, 139, 123, 0.2);
            margin-bottom: 3rem;
        }

        .header h1 {
            font-size: 2.8rem;
            letter-spacing: 2px;
            color: var(--accent-sage-dark);
        }

        .header p {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            font-weight: 300;
        }

        .header .subtitle {
            font-family: 'Cormorant Garamond', serif;
            font-style: italic;
            font-size: 1.3rem;
            color: var(--accent-terracotta);
        }

        /* --- Grid de productos --- */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
        }

        /* --- Tarjeta de producto --- */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.8rem 1.8rem 2rem;
            box-shadow: var(--shadow-soft);
            transition: var(--transition-smooth);
            border: 1px solid rgba(122, 139, 123, 0.08);
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.07);
        }

        .card h3 {
            font-size: 1.5rem;
            color: var(--accent-sage-dark);
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .card .product-number {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: var(--accent-terracotta);
            background: rgba(208, 140, 112, 0.1);
            padding: 0.2rem 0.8rem;
            border-radius: 50px;
            margin-bottom: 1rem;
            align-self: flex-start;
        }

        .card .descripcion {
            font-size: 0.95rem;
            color: var(--text-main);
            line-height: 1.7;
            flex: 1;
        }

        .card .descripcion.collapsed {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            max-height: calc(1.7em * 4);
            /* Fallback para navegadores antiguos */
        }

        .card .descripcion strong {
            color: var(--accent-sage-dark);
            font-weight: 600;
        }

        .card .descripcion em {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .btn-ver-mas {
            background: none;
            border: none;
            color: var(--accent-sage-dark);
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            padding: 0;
            margin-top: 0rem;
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
            font-family: inherit;
        }

        .btn-ver-mas:hover {
            color: var(--accent-terracotta);
        }

        .btn-consult {
            background-color: var(--accent-sage);
            color: var(--white);
            text-decoration: none;
            padding: 0.75rem 1.2rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            width: 100%;
            margin-top: 1rem;
        }

        .btn-consult:hover {
            background-color: var(--accent-sage-dark);
        }

        /* --- Footer --- */
        .footer {
            text-align: center;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(122, 139, 123, 0.15);
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .footer i {
            color: var(--accent-terracotta);
            margin: 0 0.3rem;
        }

        /* --- Animaciones y utils --- */
        @media (max-width: 640px) {
            .header h1 {
                font-size: 2rem;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 1.5rem;
            }
        }

        /* --- Modal Galería --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            padding-top: 5vh;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 75vh;
            object-fit: contain;
            border-radius: 8px;
            animation: zoom 0.3s;
        }

        #modalCaption {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            text-align: center;
            color: #ccc;
            padding: 15px 0;
            font-size: 1.2rem;
            font-family: 'Cormorant Garamond', serif;
            animation: zoom 0.3s;
        }

        @keyframes zoom {
            from {
                transform: scale(0.9)
            }

            to {
                transform: scale(1)
            }
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
            z-index: 2001;
        }

        .modal-close:hover,
        .modal-close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-nav {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -50px;
            color: white;
            font-weight: bold;
            font-size: 30px;
            transition: 0.3s ease;
            user-select: none;
            z-index: 2001;
        }

        .modal-prev {
            left: 10px;
            border-radius: 0 3px 3px 0;
        }

        .modal-next {
            right: 10px;
            border-radius: 3px 0 0 3px;
        }

        .modal-prev:hover,
        .modal-next:hover {
            background-color: rgba(0, 0, 0, 0.6);
        }
    </style>
</head>

<body>
    <!-- Top Nav -->
    <header id="top-nav">
        <a href="index.php" class="brand-logo">
            <img src="assets/img/logo-padma.jpg" alt="Padma Yoga Logo">
            <span class="brand-logo-text">PADMA YOGA</span>
        </a>
        <nav>
            <ul>
                <li><a href="index.php" class="btn-nav">Inicio</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">

        <!-- Encabezado -->
        <header class="header">
            <h1>Catálogo PADMA</h1>
            <p class="subtitle">Bienestar Emocional</p>
            <p>Cosmética natural y hogar · Productos 100% naturales para el cuidado consciente</p>
        </header>

        <!-- Grid de productos -->
        <div class="grid">
            <?php foreach ($productos as $index => $producto): ?>
                <div class="card">
                    <?php if (!empty($producto['imagen']) && file_exists(dirname(__FILE__) . '/assets/img/catalogo/' . $producto['imagen'])): ?>
                        <div class="product-img"
                            style="margin-bottom: 1.2rem; overflow: hidden; border-radius: 8px; aspect-ratio: 4/3; background: #f2ece1;">
                            <img src="assets/img/catalogo/<?php echo htmlspecialchars($producto['imagen']); ?>"
                                alt="<?php echo htmlspecialchars($producto['titulo']); ?>"
                                style="width: 100%; height: 100%; object-fit: cover; display: block; cursor: pointer;"
                                class="gallery-img" data-caption="<?php echo htmlspecialchars($producto['titulo']); ?>"
                                data-imagenes="<?php echo htmlspecialchars(json_encode($producto['imagenes'])); ?>">
                        </div>
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($producto['titulo']); ?></h3>
                    <div class="descripcion collapsed" data-expandable="true" style="margin-bottom: 1rem;">
                        <?php echo $producto['descripcion']; ?>
                    </div>
                    <button class="btn-ver-mas" onclick="toggleDesc(this)" style="display:none; margin-bottom: 1rem;">
                        <i class="fas fa-chevron-down"></i> Ver más
                    </button>
                    <a href="https://wa.me/5491151028042?text=<?php echo urlencode('Hola! Quiero consultar sobre ' . $producto['titulo']); ?>"
                        target="_blank" class="btn-consult" style="margin-top: auto; align-self: flex-start;">
                        <i class="fa-brands fa-whatsapp"></i> Consultar Disponibilidad
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pie de página -->
        <footer class="footer">
            <p><i class="fas fa-leaf"></i> PADMA · Bienestar Emocional <i class="fas fa-spa"></i></p>
            <p style="font-size:0.8rem; margin-top:0.3rem;">Cosmética natural y hogar · Productos elaborados con
                esencias puras</p>
        </footer>

    </div>
    <script>
        // Mostrar botón "Ver más" solo si el texto fue recortado
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.descripcion[data-expandable]').forEach(function (el) {
                // Si el contenido no supera 4 líneas, no mostramos el botón
                if (el.scrollHeight > el.clientHeight + 2) {
                    var btn = el.nextElementSibling;
                    if (btn && btn.classList.contains('btn-ver-mas')) {
                        btn.style.display = 'inline-flex';
                    }
                } else {
                    // Sin overflow: quitar la clase collapsed para no aplicar clamp innecesario
                    el.classList.remove('collapsed');
                }
            });
        });

        function toggleDesc(btn) {
            var el = btn.previousElementSibling;
            var isCollapsed = el.classList.contains('collapsed');
            if (isCollapsed) {
                el.classList.remove('collapsed');
                btn.innerHTML = '<i class="fas fa-chevron-up"></i> Ver menos';
            } else {
                el.classList.add('collapsed');
                btn.innerHTML = '<i class="fas fa-chevron-down"></i> Ver más';
            }
        }

        // --- Lógica Modal Galería ---
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImg');
            const captionText = document.getElementById('modalCaption');
            const imagesNodes = document.querySelectorAll('.gallery-img');
            const closeBtn = document.querySelector('.modal-close');
            const prevBtn = document.querySelector('.modal-prev');
            const nextBtn = document.querySelector('.modal-next');

            let currentProductImages = [];
            let currentImageIndex = 0;
            let currentFallbackCaption = "";

            const openModal = (productImages, fallbackImage, fallbackCaption) => {
                currentProductImages = productImages;
                currentFallbackCaption = fallbackCaption;

                if (currentProductImages.length === 0 && fallbackImage) {
                    currentProductImages = [{ archivo: fallbackImage, nombre: fallbackCaption }];
                }

                if (currentProductImages.length === 0) return;

                currentImageIndex = 0;
                updateModalContent();

                if (currentProductImages.length > 1) {
                    if (prevBtn) prevBtn.style.display = 'block';
                    if (nextBtn) nextBtn.style.display = 'block';
                } else {
                    if (prevBtn) prevBtn.style.display = 'none';
                    if (nextBtn) nextBtn.style.display = 'none';
                }

                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            };

            const updateModalContent = () => {
                const imgData = currentProductImages[currentImageIndex];
                // Extraer solo el nombre del archivo de la ruta actual si es necesario, 
                // pero los datos JSON ya traen el filename
                modalImg.src = 'assets/img/catalogo/' + imgData.archivo;
                captionText.innerHTML = imgData.nombre || currentFallbackCaption;
            };

            imagesNodes.forEach((imgNode) => {
                imgNode.addEventListener('click', () => {
                    let productImages = [];
                    try {
                        productImages = JSON.parse(imgNode.getAttribute('data-imagenes'));
                    } catch (e) { }

                    const fallbackImageSrc = imgNode.getAttribute('src').split('/').pop();
                    const fallbackCaption = imgNode.getAttribute('data-caption');

                    openModal(productImages, fallbackImageSrc, fallbackCaption);
                });
            });

            if (closeBtn) {
                closeBtn.onclick = function () {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                };
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (currentProductImages.length > 1) {
                        currentImageIndex = (currentImageIndex - 1 + currentProductImages.length) % currentProductImages.length;
                        updateModalContent();
                    }
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (currentProductImages.length > 1) {
                        currentImageIndex = (currentImageIndex + 1) % currentProductImages.length;
                        updateModalContent();
                    }
                });
            }

            modal.onclick = function (event) {
                if (event.target === modal || event.target === captionText) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            };

            // Navegación por teclado
            document.addEventListener('keydown', (e) => {
                if (modal.style.display === 'block') {
                    if (e.key === 'Escape') {
                        modal.style.display = 'none';
                        document.body.style.overflow = 'auto';
                    } else if (e.key === 'ArrowLeft' && prevBtn && currentProductImages.length > 1) {
                        prevBtn.click();
                    } else if (e.key === 'ArrowRight' && nextBtn && currentProductImages.length > 1) {
                        nextBtn.click();
                    }
                }
            });
        });
    </script>

    <!-- Modal HTML -->
    <div id="imageModal" class="modal">
        <span class="modal-close">&times;</span>
        <a class="modal-nav modal-prev">&#10094;</a>
        <a class="modal-nav modal-next">&#10095;</a>
        <img class="modal-content" id="modalImg">
        <div id="modalCaption"></div>
    </div>
</body>

</html>