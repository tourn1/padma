<?php
// Página de Aromaterapia PADMA - Bienestar Emocional
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LGGNT2486F"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-LGGNT2486F');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aromaterapia PADMA - Bienestar Emocional</title>
    <meta name="description" content="Descubrí el enfoque holístico de la aromaterapia en PADMA. Aceites esenciales que trabajan de manera sinérgica para promover el equilibrio y el bienestar integral.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-main);
            line-height: 1.6;
            padding: 2rem 1.5rem;
        }

        h1, h2, h3, .brand {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
        }

        .container { max-width: 1200px; margin: 0 auto; }

        /* Top Nav */
        #top-nav {
            position: fixed; top: 0; left: 0; width: 100%;
            padding: 1rem 6%;
            display: flex; justify-content: space-between; align-items: center;
            z-index: 1000;
            background: rgba(250, 247, 242, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .brand-logo { text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .brand-logo img { height: 42px; width: 42px; object-fit: cover; border-radius: 50%; }
        .brand-logo-text {
            font-size: 1.6rem; letter-spacing: 1.5px; color: var(--accent-sage-dark);
            font-family: 'Cormorant Garamond', serif; font-weight: 600;
        }

        #top-nav nav ul { display: flex; list-style: none; margin: 0; padding: 0; }

        .btn-nav {
            background-color: var(--accent-sage); color: var(--white) !important;
            padding: 0.6rem 1.4rem; border-radius: 50px; text-decoration: none;
            font-weight: 500; font-size: 0.95rem;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-block;
        }
        .btn-nav:hover { background-color: var(--accent-sage-dark); transform: translateY(-2px); }

        /* Header */
        .header {
            text-align: center; padding: 6rem 0 3rem;
            border-bottom: 1px solid rgba(122, 139, 123, 0.2);
            margin-bottom: 3rem;
        }
        .header h1 { font-size: 2.8rem; letter-spacing: 2px; color: var(--accent-sage-dark); }
        .header p { font-size: 1.1rem; color: var(--text-muted); margin-top: 0.5rem; font-weight: 300; }
        .header .subtitle {
            font-family: 'Cormorant Garamond', serif;
            font-style: italic; font-size: 1.3rem; color: var(--accent-terracotta);
        }

        /* Content section */
        .content-section {
            max-width: 820px; margin: 0 auto;
            display: flex; flex-direction: column; gap: 1.8rem;
        }

        .content-card {
            background: var(--white); border-radius: var(--radius);
            padding: 2rem 2.2rem; box-shadow: var(--shadow-soft);
            border: 1px solid rgba(122, 139, 123, 0.08);
            transition: var(--transition-smooth);
            display: flex; gap: 1.4rem; align-items: flex-start;
        }
        .content-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.07); }

        .content-card .card-icon {
            flex-shrink: 0; width: 44px; height: 44px; border-radius: 50%;
            background: rgba(122, 139, 123, 0.12);
            display: flex; align-items: center; justify-content: center;
            margin-top: 0.15rem;
        }
        .content-card .card-icon i { font-size: 1.1rem; color: var(--accent-sage-dark); }
        .content-card p { font-size: 1rem; color: var(--text-main); line-height: 1.8; }

        /* Divider */
        .divider { display: flex; align-items: center; gap: 1rem; margin: 1rem 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(122,139,123,0.2); }
        .divider i { color: var(--accent-terracotta); font-size: 0.85rem; }

        /* CTA Banner */
        .cta-banner {
            background: linear-gradient(135deg, var(--accent-sage) 0%, var(--accent-sage-dark) 100%);
            border-radius: var(--radius); padding: 2.5rem 2rem;
            text-align: center; margin-top: 1rem; box-shadow: var(--shadow-soft);
        }
        .cta-banner h2 { font-size: 1.9rem; color: var(--white); margin-bottom: 0.5rem; }
        .cta-banner p { color: rgba(255,255,255,0.85); font-size: 0.95rem; margin-bottom: 1.5rem; }

        .btn-cta {
            background-color: var(--white); color: var(--accent-sage-dark);
            text-decoration: none; padding: 0.8rem 1.8rem; border-radius: 50px;
            font-weight: 600; font-size: 0.95rem;
            display: inline-flex; align-items: center; gap: 8px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-cta:hover { background-color: var(--bg-secondary); transform: translateY(-2px); }

        /* Footer */
        .footer {
            text-align: center; margin-top: 4rem; padding-top: 2rem;
            border-top: 1px solid rgba(122, 139, 123, 0.15);
            color: var(--text-muted); font-size: 0.9rem;
        }
        .footer i { color: var(--accent-terracotta); margin: 0 0.3rem; }

        /* Responsive */
        @media (max-width: 640px) {
            .header h1 { font-size: 2rem; }
            .content-card { flex-direction: column; padding: 1.5rem; gap: 1rem; }
            .cta-banner { padding: 2rem 1.2rem; }
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
            <h1>Aromaterapia</h1>
            <p class="subtitle">Terapia Holística &amp; Bienestar Integral</p>
            <p>El poder de los aceites esenciales para el equilibrio del cuerpo, la mente y el espíritu</p>
        </header>

        <!-- Contenido editorial -->
        <section class="content-section">

            <div class="content-card">
                <div class="card-icon"><i class="fas fa-globe-americas"></i></div>
                <p>La aromaterapia en sí misma puede ser llamada una terapia holística si analiza el contexto cultural y social en el cual se desarrolla el problema y la solución del mismo. Por este motivo es importante observar los patrones de pensamiento, comportamiento, trabajo y cultura que pueden ser el origen de los problemas en primer lugar.</p>
            </div>

            <div class="content-card">
                <div class="card-icon"><i class="fas fa-circle-nodes"></i></div>
                <p>De la misma forma en que los seres humanos no existen de manera aislada, sino que pertenecen a un conjunto social, los órganos del cuerpo tampoco se sostienen independientemente. La aromaterapia aborda al ser humano como un sistema integral, aunque provee soluciones para problemas específicos.</p>
            </div>

            <div class="content-card">
                <div class="card-icon"><i class="fas fa-flask"></i></div>
                <p>La aromaterapia trabaja de manera sinérgica. Esto significa que el efecto general de una mezcla de aceites esenciales aromáticos bien combinados es mucho más amplio que la suma de sus constituyentes individuales.</p>
            </div>

            <div class="content-card">
                <div class="card-icon"><i class="fas fa-heart-pulse"></i></div>
                <p>El enfoque "holístico" en aromaterapia propone la salud como el estado ideal. El énfasis está puesto en ayudar a las personas a ayudarse ellas mismas con educación sobre prevención de problemas y con el incentivo de un estilo de vida sano.</p>
            </div>

            <div class="content-card">
                <div class="card-icon"><i class="fas fa-scale-balanced"></i></div>
                <p>Si los aceites esenciales aromáticos son utilizados de manera correcta, pueden ayudar a promover y sustentar la capacidad que posee el cuerpo humano de mantener una condición estable, equilibrada y balanceada.</p>
            </div>

            <div class="divider"><i class="fas fa-spa"></i></div>

            <!-- CTA -->
            <div class="cta-banner">
                <h2>Descubrí nuestros productos</h2>
                <p>Explorá nuestra línea de aceites esenciales y productos de aromaterapia 100% naturales</p>
                <a href="catalogo.php" class="btn-cta">
                    <i class="fas fa-leaf"></i> Ver Catálogo
                </a>
            </div>

        </section>

        <!-- Pie de página -->
        <footer class="footer">
            <p><i class="fas fa-leaf"></i> PADMA · Bienestar Emocional <i class="fas fa-spa"></i></p>
            <p style="font-size:0.8rem; margin-top:0.3rem;">Aromaterapia natural · Productos elaborados con esencias puras</p>
        </footer>

    </div>
</body>

</html>
