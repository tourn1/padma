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

// Configuración de rutas
$catalogFile = dirname(__FILE__) . '/upload/catalog.txt';
$imgUploadDir = dirname(dirname(__FILE__)) . '/assets/img/catalogo/';

// Crear directorios si no existen
if (!file_exists(dirname($catalogFile))) {
    mkdir(dirname($catalogFile), 0755, true);
}
if (!file_exists($imgUploadDir)) {
    mkdir($imgUploadDir, 0755, true);
}

// Cargar productos existentes
$products = [];
if (file_exists($catalogFile)) {
    $content = file_get_contents($catalogFile);
    $products = json_decode($content, true);
    if (!is_array($products)) {
        $products = [];
    } else {
        // Ordenar productos por el campo 'orden'
        usort($products, function($a, $b) {
            return ($a['orden'] ?? 0) <=> ($b['orden'] ?? 0);
        });
    }
}

$successMessage = '';
$errorMessages = [];
$action = $_GET['action'] ?? 'list';
$editId = $_GET['id'] ?? '';

// Calcular el próximo número de orden disponible
$maxOrden = 0;
foreach ($products as $p) {
    if (isset($p['orden']) && (int)$p['orden'] > $maxOrden) {
        $maxOrden = (int)$p['orden'];
    }
}
$nextOrden = $maxOrden + 1;

// Obtener producto a editar si aplica
$editProduct = null;
if ($action === 'edit' && !empty($editId)) {
    foreach ($products as $p) {
        if ($p['id'] === $editId) {
            $editProduct = $p;
            break;
        }
    }
    if (!$editProduct) {
        $errorMessages[] = "El producto que intenta editar no existe.";
        $action = 'list';
    }
}

// Procesar formularios POST (Alta y Edición)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $activo = isset($_POST['activo']) ? true : false;
    $destacado = isset($_POST['destacado']) ? true : false;
    $orden = isset($_POST['orden']) ? (int)$_POST['orden'] : 0;
    $productId = $_POST['product_id'] ?? '';
    
    if (empty($nombre)) {
        $errorMessages[] = "El nombre del producto es obligatorio.";
    }

    // Procesar imágenes existentes
    $imagenes = [];
    if ($action === 'edit' && $editProduct) {
        // Compatibilidad: si tiene 'imagen' pero no 'imagenes', convertir a array
        $existingImages = $editProduct['imagenes'] ?? [];
        if (empty($existingImages) && !empty($editProduct['imagen'])) {
            $existingImages[] = ['archivo' => $editProduct['imagen'], 'nombre' => 'Principal'];
        }

        // Obtener imágenes a eliminar y nombres
        $imagenesAEliminar = isset($_POST['eliminar_imagenes']) ? $_POST['eliminar_imagenes'] : [];
        $nombresExistentes = isset($_POST['nombres_imagenes_existentes']) ? $_POST['nombres_imagenes_existentes'] : [];

        foreach ($existingImages as $index => $img) {
            if (in_array((string)$index, $imagenesAEliminar, true)) {
                // Eliminar archivo
                if (file_exists($imgUploadDir . $img['archivo'])) {
                    @unlink($imgUploadDir . $img['archivo']);
                }
            } else {
                // Conservar y actualizar nombre si es necesario
                if (isset($nombresExistentes[$index])) {
                    $img['nombre'] = trim($nombresExistentes[$index]);
                }
                $imagenes[] = $img;
            }
        }
    }

    // Procesar subida de nuevas imágenes
    if (isset($_FILES['imagenes_nuevas'])) {
        $nombresNuevos = isset($_POST['nombres_imagenes_nuevas']) ? $_POST['nombres_imagenes_nuevas'] : [];
        
        // Cuando se usa input name="array[]", $_FILES es un array de arrays
        $fileCount = is_array($_FILES['imagenes_nuevas']['name']) ? count($_FILES['imagenes_nuevas']['name']) : 0;
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['imagenes_nuevas']['error'][$i] === UPLOAD_ERR_OK) {
                if (!is_writable($imgUploadDir)) {
                    $errorMessages[] = "Error de permisos: La carpeta de imágenes 'assets/img/catalogo/' no tiene permisos de escritura.";
                    break;
                }
                
                $tmpPath = $_FILES['imagenes_nuevas']['tmp_name'][$i];
                $fileExtension = pathinfo($_FILES['imagenes_nuevas']['name'][$i], PATHINFO_EXTENSION);
                
                $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nombre));
                $newFilename = $cleanName . '_' . time() . '_' . $i . '.' . $fileExtension;
                $targetPath = $imgUploadDir . $newFilename;
                
                $imageInfo = @getimagesize($tmpPath);
                if ($imageInfo !== false) {
                    if (move_uploaded_file($tmpPath, $targetPath)) {
                        $nombreImg = isset($nombresNuevos[$i]) && trim($nombresNuevos[$i]) !== '' ? trim($nombresNuevos[$i]) : 'Imagen ' . (count($imagenes) + 1);
                        $imagenes[] = [
                            'archivo' => $newFilename,
                            'nombre' => $nombreImg
                        ];
                    } else {
                        $errorMessages[] = "No se pudo guardar una de las imágenes subidas.";
                    }
                }
            } elseif ($_FILES['imagenes_nuevas']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $errorMessages[] = "Error al subir imagen (Código: " . $_FILES['imagenes_nuevas']['error'][$i] . ").";
            }
        }
    }
    // Si no hay errores, guardar cambios
    if (empty($errorMessages)) {
        // Validar permisos de escritura en catalog.txt
        $isWritable = false;
        if (file_exists($catalogFile)) {
            $isWritable = is_writable($catalogFile);
        } else {
            $isWritable = is_writable(dirname($catalogFile));
        }

        if (!$isWritable) {
            $errorMessages[] = "Error de permisos: No se puede escribir en '" . basename($catalogFile) . "'. Otorga permisos de escritura en el servidor.";
        } else {
            if ($action === 'add') {
                // Generar nuevo ID único
                $newProduct = [
                    'id' => uniqid('prod_', true),
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'imagen' => isset($imagenes[0]) ? $imagenes[0]['archivo'] : '',
                    'imagenes' => $imagenes,
                    'activo' => $activo,
                    'destacado' => $destacado,
                    'orden' => $orden
                ];
                $products[] = $newProduct;
                $successMessage = "¡Producto agregado con éxito!";
            } elseif ($action === 'edit' && $editProduct) {
                // Actualizar producto en el array
                foreach ($products as $key => $p) {
                    if ($p['id'] === $productId) {
                        $products[$key]['nombre'] = $nombre;
                        $products[$key]['descripcion'] = $descripcion;
                        $products[$key]['imagen'] = isset($imagenes[0]) ? $imagenes[0]['archivo'] : '';
                        $products[$key]['imagenes'] = $imagenes;
                        $products[$key]['activo'] = $activo;
                        $products[$key]['destacado'] = $destacado;
                        $products[$key]['orden'] = $orden;
                        break;
                    }
                }
                $successMessage = "¡Producto modificado con éxito!";
            }

            // Guardar JSON
            if (file_put_contents($catalogFile, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false) {
                // Redirigir o volver al listado
                $_SESSION['flash_success'] = $successMessage;
                header('Location: products.php');
                exit;
            } else {
                $errorMessages[] = "Error al escribir en catalog.txt. Verifique el espacio del servidor.";
            }
        }
    }
}

// Procesar eliminación (GET)
if ($action === 'delete' && !empty($editId)) {
    $foundKey = -1;
    $imageToDelete = '';
    foreach ($products as $key => $p) {
        if ($p['id'] === $editId) {
            $foundKey = $key;
            $imageToDelete = $p['imagen'];
            break;
        }
    }

    if ($foundKey !== -1) {
        // Validar permisos de escritura en catalog.txt antes de modificar
        if (is_writable($catalogFile)) {
            // Eliminar imagen física
            if (!empty($imageToDelete) && file_exists($imgUploadDir . $imageToDelete)) {
                @unlink($imgUploadDir . $imageToDelete);
            }
            // Eliminar del array
            array_splice($products, $foundKey, 1);
            // Guardar
            if (file_put_contents($catalogFile, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false) {
                $_SESSION['flash_success'] = "¡Producto eliminado con éxito!";
            } else {
                $_SESSION['flash_error'] = "No se pudo actualizar el archivo catalog.txt al eliminar.";
            }
        } else {
            $_SESSION['flash_error'] = "Error de permisos: No se puede escribir en el archivo catalog.txt para eliminar el producto.";
        }
    } else {
        $_SESSION['flash_error'] = "El producto que intenta eliminar no existe.";
    }
    header('Location: products.php');
    exit;
}

// Cargar mensajes flash desde la sesión
if (isset($_SESSION['flash_success'])) {
    $successMessage = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    $errorMessages[] = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
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
    <title>Administrar Productos | Padma Yoga</title>
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
            --admin-danger: #e53e3e;
            --admin-danger-hover: #c53030;
            --bg-primary: #FAF7F2;
            --white: #FFFFFF;
            --radius: 12px;
        }

        body {
            background-color: var(--bg-primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2d3748;
            padding-bottom: 50px;
        }

        /* Barra de administración fija superior */
        .admin-top-bar {
            position: sticky;
            top: 0;
            z-index: 9999;
            background: var(--admin-bar-bg);
            color: #ffffff;
            padding: 12px 24px;
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
        }

        .btn-action:hover {
            background: var(--admin-accent-hover);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: var(--admin-danger);
        }

        .btn-danger:hover {
            background: var(--admin-danger-hover);
        }

        .btn-secondary {
            background: #718096;
        }

        .btn-secondary:hover {
            background: #4a5568;
        }

        /* Formulario */
        .product-form-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid rgba(122, 139, 123, 0.12);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #cbd5e0;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            box-sizing: border-box;
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.2);
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-input {
            width: 20px;
            height: 20px;
            accent-color: var(--admin-accent);
        }

        /* Lista de productos */
        .products-table-wrapper {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid rgba(122, 139, 123, 0.12);
            overflow: hidden;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .products-table th, .products-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #edf2f7;
        }

        .products-table th {
            background: #f7fafc;
            font-weight: 600;
            color: #4a5568;
        }

        .product-img-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            background: #e2e8f0;
            border: 1px solid #e2e8f0;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-active {
            background: #c6f6d5;
            color: #22543d;
        }

        .badge-inactive {
            background: #fed7d7;
            color: #742a2a;
        }

        .action-links {
            display: flex;
            gap: 12px;
        }

        .action-link {
            color: #4a5568;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.2s;
        }

        .action-link:hover {
            color: var(--admin-accent);
        }

        .action-link.delete-link:hover {
            color: var(--admin-danger);
        }

        .empty-state {
            padding: 40px;
            text-align: center;
            color: #718096;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #cbd5e0;
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
            <a href="products.php" class="btn-admin-view" style="font-weight: bold; border-bottom: 2px solid var(--admin-accent); padding-bottom: 4px;">
                Catálogo de Productos
            </a>
            <a href="users.php" class="btn-admin-view">
                Usuarios
            </a>
            <a href="../index.php" target="_blank" class="btn-admin-view">
                Ver Sitio
            </a>
            <a href="products.php?action=logout" class="btn-admin-logout" title="Cerrar Sesión">
                <i class="fa-solid fa-right-from-bracket"></i> Salir
            </a>
        </div>
    </div>

    <!-- Mensajes de Estado -->
    <?php if (!empty($successMessage)): ?>
        <div class="admin-alert">
            <i class="fa-solid fa-circle-check"></i> <?php echo e($successMessage); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessages)): ?>
        <div class="admin-alert admin-alert-danger">
            <div style="font-weight: bold; margin-bottom: 5px;">
                <i class="fa-solid fa-triangle-exclamation"></i> Se encontraron errores al procesar:
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

        <?php if ($action === 'add' || $action === 'edit'): ?>
            <!-- Formulario Alta / Modificación -->
            <div class="panel-header">
                <h1 class="panel-title"><?php echo $action === 'add' ? 'Agregar Nuevo Producto' : 'Editar Producto'; ?></h1>
                <a href="products.php" class="btn-action btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver al Listado</a>
            </div>

            <div class="product-form-card">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo e($editProduct['id'] ?? ''); ?>">

                    <div class="form-group">
                        <label class="form-label" for="nombre">Nombre del Producto</label>
                        <input type="text" id="nombre" name="nombre" class="form-input" value="<?php echo e($editProduct['nombre'] ?? ''); ?>" required placeholder="Ej. Roll on Concentrado Natural - Anti-estrés">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-textarea" placeholder="Describe los beneficios, ingredientes y formas de uso del producto..." required><?php echo e($editProduct['descripcion'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="orden">Orden (menor número aparece primero)</label>
                        <input type="number" id="orden" name="orden" class="form-input" value="<?php echo e($editProduct['orden'] ?? $nextOrden); ?>" required style="max-width: 150px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Imágenes del Producto</label>
                        
                        <!-- Imágenes existentes -->
                        <?php 
                        $existingImages = $editProduct['imagenes'] ?? [];
                        if (empty($existingImages) && !empty($editProduct['imagen'])) {
                            $existingImages[] = ['archivo' => $editProduct['imagen'], 'nombre' => 'Principal'];
                        }
                        if (!empty($existingImages)): 
                        ?>
                            <div style="margin-bottom: 15px; border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px;">
                                <span style="font-size: 0.85rem; color: #718096; display: block; margin-bottom: 10px;">Imágenes actuales:</span>
                                <?php foreach ($existingImages as $index => $img): ?>
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; background: #f7fafc; padding: 10px; border-radius: 6px;">
                                        <img src="../assets/img/catalogo/<?php echo e($img['archivo']); ?>" class="product-img-thumb" style="width: 50px; height: 50px; min-width: 50px; object-fit: cover;" alt="Vista previa">
                                        <div style="flex: 1;">
                                            <input type="text" name="nombres_imagenes_existentes[<?php echo $index; ?>]" class="form-input" style="padding: 5px; font-size: 0.9rem;" value="<?php echo e($img['nombre'] ?? ''); ?>" placeholder="Nombre de imagen">
                                        </div>
                                        <div>
                                            <label style="font-size: 0.85rem; color: #e53e3e; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                                <input type="checkbox" name="eliminar_imagenes[]" value="<?php echo $index; ?>"> Eliminar
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Nuevas imágenes -->
                        <div id="nuevas-imagenes-container">
                            <div class="nueva-imagen-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                <input type="file" name="imagenes_nuevas[]" class="form-input" accept="image/*" style="flex: 1;">
                                <input type="text" name="nombres_imagenes_nuevas[]" class="form-input" style="flex: 1;" placeholder="Nombre (ej. Frente, Dorso)">
                            </div>
                        </div>
                        <button type="button" class="btn-action btn-secondary" style="font-size: 0.85rem; padding: 5px 10px;" onclick="agregarFilaImagen()">
                            <i class="fa-solid fa-plus"></i> Añadir otra imagen
                        </button>
                    </div>

                    <script>
                        function agregarFilaImagen() {
                            const container = document.getElementById('nuevas-imagenes-container');
                            const row = document.createElement('div');
                            row.className = 'nueva-imagen-row';
                            row.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
                            row.innerHTML = `
                                <input type="file" name="imagenes_nuevas[]" class="form-input" accept="image/*" style="flex: 1;">
                                <input type="text" name="nombres_imagenes_nuevas[]" class="form-input" style="flex: 1;" placeholder="Nombre (ej. Frente, Dorso)">
                                <button type="button" class="btn-action btn-secondary" style="padding: 0 10px; color: #e53e3e; border-color: #fc8181; background: transparent;" onclick="this.parentElement.remove()">X</button>
                            `;
                            container.appendChild(row);
                        }
                    </script>
                    <div class="form-group">
                        <label class="checkbox-group">
                            <input type="checkbox" name="activo" value="1" class="checkbox-input" <?php echo ($action === 'add' || (isset($editProduct['activo']) && $editProduct['activo'] === true)) ? 'checked' : ''; ?>>
                            <span class="form-label" style="margin-bottom: 0;">Producto Activo (Se mostrará en la web pública)</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-group">
                            <input type="checkbox" name="destacado" value="1" class="checkbox-input" <?php echo (isset($editProduct['destacado']) && $editProduct['destacado'] === true) ? 'checked' : ''; ?>>
                            <span class="form-label" style="margin-bottom: 0;">Producto destacado</span>
                        </label>
                    </div>

                    <div style="margin-top: 30px; display: flex; gap: 15px;">
                        <button type="submit" class="btn-action">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar Producto
                        </button>
                        <a href="products.php" class="btn-action btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <!-- Listado de Productos -->
            <div class="panel-header">
                <h1 class="panel-title">Catálogo de Productos</h1>
                <a href="products.php?action=add" class="btn-action"><i class="fa-solid fa-plus"></i> Agregar Producto</a>
            </div>

            <div class="products-table-wrapper">
                <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-box-open"></i>
                        <p>No se encontraron productos cargados en el catálogo.</p>
                        <p style="font-size: 0.9rem; margin-top: 10px;">Haz clic en "Agregar Producto" para comenzar.</p>
                    </div>
                <?php else: ?>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Imagen</th>
                                <th style="width: 80px; text-align: center;">Orden</th>
                                <th>Nombre</th>
                                <!-- <th>Descripción</th> -->
                                <th style="width: 100px;">Estado</th>
                                <th style="width: 100px; text-align: center;">Destacado</th>
                                <th style="width: 100px; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $mainImage = '';
                                        if (!empty($p['imagenes']) && isset($p['imagenes'][0]['archivo']) && file_exists($imgUploadDir . $p['imagenes'][0]['archivo'])) {
                                            $mainImage = $p['imagenes'][0]['archivo'];
                                        } elseif (!empty($p['imagen']) && file_exists($imgUploadDir . $p['imagen'])) {
                                            $mainImage = $p['imagen'];
                                        }
                                        ?>
                                        <?php if ($mainImage !== ''): ?>
                                            <img src="../assets/img/catalogo/<?php echo e($mainImage); ?>" class="product-img-thumb" alt="<?php echo e($p['nombre']); ?>" style="cursor: pointer;" onclick="openImageModal(this.src)">
                                            <?php if (!empty($p['imagenes']) && count($p['imagenes']) > 1): ?>
                                                <div style="font-size: 0.75rem; text-align: center; color: #718096; margin-top: 5px;">+<?php echo (count($p['imagenes']) - 1); ?> imgs</div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="product-img-thumb" style="display: flex; align-items: center; justify-content: center; color: #a0aec0;">
                                                <i class="fa-solid fa-image"></i>
                                            </div>                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center; font-weight: bold; color: #718096;"><?php echo e($p['orden'] ?? 0); ?></td>
                                    <td style="font-weight: 600; color: #2d3748;"><?php echo e($p['nombre']); ?></td>
                                    <!-- <td style="font-size: 0.9rem; color: #4a5568; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo strip_tags($p['descripcion']); ?>
                                    </td> -->
                                    <td>
                                        <?php if (isset($p['activo']) && $p['activo'] === true): ?>
                                            <span class="badge badge-active">Activo</span>
                                        <?php else: ?>
                                            <span class="badge badge-inactive">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if (isset($p['destacado']) && $p['destacado'] === true): ?>
                                            <i class="fa-solid fa-star" style="color: #ecc94b;" title="Destacado"></i>
                                        <?php else: ?>
                                            <span style="color: #cbd5e0;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-links" style="justify-content: center;">
                                            <a href="products.php?action=edit&id=<?php echo urlencode($p['id']); ?>" class="action-link" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="products.php?action=delete&id=<?php echo urlencode($p['id']); ?>" class="action-link delete-link" title="Eliminar" onclick="return confirm('¿Está seguro de que desea eliminar el producto &quot;<?php echo e($p['nombre']); ?>&quot;?');">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>

    <!-- Modal de Imagen -->
    <div id="imageModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.8); align-items: center; justify-content: center;">
        <span class="close" onclick="closeImageModal()" style="position: absolute; top: 20px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer;">&times;</span>
        <img class="modal-content" id="modalImage" style="margin: auto; display: block; max-width: 90%; max-height: 90%; border-radius: 8px;">
    </div>

    <script>
        function openImageModal(src) {
            document.getElementById('imageModal').style.display = 'flex';
            document.getElementById('modalImage').src = src;
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera de la imagen
        window.onclick = function(event) {
            var modal = document.getElementById('imageModal');
            if (event.target == modal) {
                closeImageModal();
            }
        }
    </script>
</body>

</html>
