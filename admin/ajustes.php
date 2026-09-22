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

$errorMessages = [];

// Manejo de Backup
if (isset($_GET['action']) && $_GET['action'] === 'backup') {
    $zip = new ZipArchive();
    $zipName = 'backup_padma_txt_' . date('Y-m-d_H-i-s') . '.zip';
    $tempDir = dirname(__FILE__) . '/upload';
    if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);
    $zipPath = $tempDir . '/' . $zipName;

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $rootDir = dirname(dirname(__FILE__)); // Directorio raíz (padma)
        
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir));
        $hasFiles = false;
        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'txt') {
                $relativePath = substr($file->getPathname(), strlen($rootDir) + 1);
                $zip->addFile($file->getPathname(), $relativePath);
                $hasFiles = true;
            }
        }
        $zip->close();
        
        if ($hasFiles && file_exists($zipPath)) {
            @set_time_limit(0);
            while (ob_get_level()) {
                ob_end_clean();
            }
            $fileSize = filesize($zipPath);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $zipName . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . $fileSize);

            $handle = fopen($zipPath, 'rb');
            if ($handle !== false) {
                while (!feof($handle) && connection_status() == 0) {
                    echo fread($handle, 1024 * 64);
                    flush();
                }
                fclose($handle);
            }
            @unlink($zipPath);
            exit;
        } else {
            $errorMessages[] = "No se encontraron archivos de texto (.txt) para respaldar.";
            if (file_exists($zipPath)) @unlink($zipPath);
        }
    } else {
        $errorMessages[] = "No se pudo crear el archivo ZIP para el backup.";
    }
}

// Manejo de Backup Imágenes
if (isset($_GET['action']) && $_GET['action'] === 'backup_images') {
    $zip = new ZipArchive();
    $zipName = 'backup_padma_images_' . date('Y-m-d_H-i-s') . '.zip';
    $tempDir = dirname(__FILE__) . '/upload';
    if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);
    $zipPath = $tempDir . '/' . $zipName;

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $dirsToBackup = [
            'assets/img/home' => dirname(dirname(__FILE__)) . '/assets/img/home',
            'assets/img/catalogo' => dirname(dirname(__FILE__)) . '/assets/img/catalogo',
            'assets/img' => dirname(dirname(__FILE__)) . '/assets/img',
            'admin/upload' => dirname(__FILE__) . '/upload'
        ];
        
        $hasFiles = false;
        foreach ($dirsToBackup as $prefix => $dirPath) {
            if (is_dir($dirPath)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath));
                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $ext = strtolower($file->getExtension());
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'webm', 'mov'])) {
                            $relativePath = $prefix . '/' . substr($file->getPathname(), strlen($dirPath) + 1);
                            $zip->addFile($file->getPathname(), $relativePath);
                            $hasFiles = true;
                        }
                    }
                }
            }
        }
        $zip->close();
        
        if ($hasFiles && file_exists($zipPath)) {
            // Desactivar límites de tiempo si está permitido
            @set_time_limit(0);

            // Limpiar buffers de salida previos para no saturar memoria
            while (ob_get_level()) {
                ob_end_clean();
            }

            $fileSize = filesize($zipPath);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $zipName . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . $fileSize);

            // Streaming por bloques para evitar saturar memoria en servidores compartidos
            $handle = fopen($zipPath, 'rb');
            if ($handle !== false) {
                while (!feof($handle) && connection_status() == 0) {
                    echo fread($handle, 1024 * 64); // Enviar en bloques de 64KB
                    flush();
                }
                fclose($handle);
            }
            @unlink($zipPath);
            exit;
        } else {
            $errorMessages[] = "No se encontraron imágenes o medios en el directorio de subidas para respaldar.";
            if (file_exists($zipPath)) @unlink($zipPath);
        }
    } else {
        $errorMessages[] = "No se pudo crear el archivo ZIP para el backup de imágenes.";
    }
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
    <title>Ajustes | Padma Yoga</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS para Administración -->
    <style>
        :root {
            --admin-bar-bg: #2d3748;
            --admin-accent: #38a169;
            --admin-accent-hover: #2f855a;
            --bg-primary: #FAF7F2;
            --white: #FFFFFF;
            --radius: 12px;
        }

        body {
            background-color: var(--bg-primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2d3748;
            padding-bottom: 50px;
            margin: 0;
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

        /* Alertas */
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

        /* Contenido del Panel */
        .admin-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(122, 139, 123, 0.15);
            padding-bottom: 15px;
        }

        .panel-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            color: #4a6352;
            font-weight: 600;
            margin: 0;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--admin-accent);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-action:hover {
            background: var(--admin-accent-hover);
            transform: translateY(-1px);
        }

        .setting-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid rgba(122, 139, 123, 0.12);
            text-align: center;
        }
        
        .setting-card i {
            font-size: 3rem;
            color: var(--admin-accent);
            margin-bottom: 20px;
        }
        
        .setting-card p {
            font-size: 1.1rem;
            color: #4a5568;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>

    <!-- Top Admin Bar -->
    <div class="admin-top-bar">
        <div class="admin-bar-title">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Modo Edición - Padma Admin</span>
        </div>
        <div class="admin-bar-actions">
            <a href="admin.php" class="btn-admin-view">
                Contenido Web
            </a>
            <a href="products.php" class="btn-admin-view">
                Catálogo de Productos
            </a>
            <a href="users.php" class="btn-admin-view">
                Usuarios
            </a>
            <a href="ajustes.php" class="btn-admin-view" style="font-weight: bold; border-bottom: 2px solid var(--admin-accent); padding-bottom: 4px;">
                Ajustes
            </a>
            <a href="../index.php" target="_blank" class="btn-admin-view">
                Ver Sitio
            </a>
            <a href="ajustes.php?action=logout" class="btn-admin-logout" title="Cerrar Sesión">
                <i class="fa-solid fa-right-from-bracket"></i> Salir
            </a>
        </div>
    </div>

    <?php if (!empty($errorMessages)): ?>
        <div class="admin-alert admin-alert-danger">
            <div style="font-weight: bold; margin-bottom: 5px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Se encontraron errores:
            </div>
            <ul style="text-align: left; margin: 0 0 0 20px; padding: 0;">
                <?php foreach ($errorMessages as $err): ?>
                    <li><?php echo e($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Contenedor Principal -->
    <div class="admin-container">
        <div class="panel-header">
            <h1 class="panel-title">Ajustes del Sitio</h1>
        </div>

        <div class="setting-card">
            <i class="fa-solid fa-file-zipper"></i>
            <h3>Backup de Textos</h3>
            <p>Descarga un archivo ZIP con todos los archivos de texto (.txt) del sitio para tener una copia de seguridad rápida de todos los contenidos editables.</p>
            <a href="ajustes.php?action=backup" class="btn-action">
                <i class="fa-solid fa-download"></i> Descargar Backup (.zip)
            </a>
        </div>

        <div class="setting-card" style="margin-top: 20px;">
            <i class="fa-solid fa-images"></i>
            <h3>Backup de Imágenes y Medios</h3>
            <p>Descarga un archivo ZIP con todas las imágenes y videos subidos a la plataforma (directorio admin/upload) para tener una copia de seguridad rápida de todos tus recursos multimedia.</p>
            <a href="ajustes.php?action=backup_images" class="btn-action">
                <i class="fa-solid fa-download"></i> Descargar Backup Medios (.zip)
            </a>
        </div>
    </div>

</body>
</html>
