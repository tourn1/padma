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
$usersFile = dirname(__FILE__) . '/upload/users.txt';

// Crear directorio si no existe
if (!file_exists(dirname($usersFile))) {
    mkdir(dirname($usersFile), 0755, true);
}

// Si users.txt no existe, lo creamos con el usuario por defecto admin/admin si es posible
if (!file_exists($usersFile)) {
    $defaultUsers = [
        [
            'id' => uniqid('usr_', true),
            'usuario' => 'admin',
            'clave' => 'admin',
            'activo' => true
        ]
    ];
    file_put_contents($usersFile, json_encode($defaultUsers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Cargar usuarios existentes
$users = [];
if (file_exists($usersFile)) {
    $content = file_get_contents($usersFile);
    $users = json_decode($content, true);
    if (!is_array($users)) {
        $users = [];
    }
}

$successMessage = '';
$errorMessages = [];
$action = $_GET['action'] ?? 'list';
$editId = $_GET['id'] ?? '';

// Obtener usuario a editar si aplica
$editUser = null;
if ($action === 'edit' && !empty($editId)) {
    foreach ($users as $u) {
        if ($u['id'] === $editId) {
            $editUser = $u;
            break;
        }
    }
    if (!$editUser) {
        $errorMessages[] = "El usuario que intenta editar no existe.";
        $action = 'list';
    }
}

// Procesar formularios POST (Alta y Edición)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = trim($_POST['clave'] ?? '');
    $activo = isset($_POST['activo']) ? true : false;
    $userId = $_POST['user_id'] ?? '';
    
    if (empty($usuario)) {
        $errorMessages[] = "El nombre de usuario es obligatorio.";
    }
    if (empty($clave)) {
        $errorMessages[] = "La contraseña es obligatoria.";
    }

    // Verificar usuario duplicado
    foreach ($users as $u) {
        if (strtolower($u['usuario']) === strtolower($usuario) && $u['id'] !== $userId) {
            $errorMessages[] = "Ya existe un usuario con ese nombre.";
            break;
        }
    }

    // Si no hay errores, guardar cambios
    if (empty($errorMessages)) {
        // Validar permisos de escritura
        $isWritable = is_writable(file_exists($usersFile) ? $usersFile : dirname($usersFile));

        if (!$isWritable) {
            $errorMessages[] = "Error de permisos: No se puede escribir en '" . basename($usersFile) . "'. Otorga permisos de escritura en el servidor.";
        } else {
            if ($action === 'add') {
                $newUser = [
                    'id' => uniqid('usr_', true),
                    'usuario' => $usuario,
                    'clave' => $clave,
                    'activo' => $activo
                ];
                $users[] = $newUser;
                $successMessage = "¡Usuario agregado con éxito!";
            } elseif ($action === 'edit' && $editUser) {
                // Actualizar usuario en el array
                foreach ($users as $key => $u) {
                    if ($u['id'] === $userId) {
                        $users[$key]['usuario'] = $usuario;
                        $users[$key]['clave'] = $clave;
                        $users[$key]['activo'] = $activo;
                        break;
                    }
                }
                $successMessage = "¡Usuario modificado con éxito!";
            }

            // Guardar JSON
            if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false) {
                $_SESSION['flash_success'] = $successMessage;
                header('Location: users.php');
                exit;
            } else {
                $errorMessages[] = "Error al escribir en users.txt. Verifique el espacio del servidor.";
            }
        }
    }
}

// Procesar eliminación (GET)
if ($action === 'delete' && !empty($editId)) {
    $foundKey = -1;
    foreach ($users as $key => $u) {
        if ($u['id'] === $editId) {
            $foundKey = $key;
            break;
        }
    }

    if ($foundKey !== -1) {
        // Evitar eliminar el último usuario admin activo si es el único
        $activeAdmins = 0;
        foreach ($users as $u) {
            if ($u['activo'] && $u['id'] !== $editId) {
                $activeAdmins++;
            }
        }
        
        if ($activeAdmins === 0) {
            $_SESSION['flash_error'] = "No puedes eliminar el único usuario activo del sistema.";
        } else {
            if (is_writable($usersFile)) {
                array_splice($users, $foundKey, 1);
                if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false) {
                    $_SESSION['flash_success'] = "¡Usuario eliminado con éxito!";
                } else {
                    $_SESSION['flash_error'] = "No se pudo actualizar el archivo users.txt al eliminar.";
                }
            } else {
                $_SESSION['flash_error'] = "Error de permisos: No se puede escribir en el archivo users.txt.";
            }
        }
    } else {
        $_SESSION['flash_error'] = "El usuario que intenta eliminar no existe.";
    }
    header('Location: users.php');
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

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Administrar Usuarios | Padma Yoga</title>
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

        .form-input {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #cbd5e0;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.2);
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
                <i class="fa-solid fa-file-pen"></i> Contenido Web
            </a>
            <a href="products.php" class="btn-admin-view">
                <i class="fa-solid fa-boxes-stacked"></i> Catálogo de Productos
            </a>
            <a href="users.php" class="btn-admin-view" style="font-weight: bold; border-bottom: 2px solid var(--admin-accent); padding-bottom: 4px;">
                <i class="fa-solid fa-users"></i> Usuarios
            </a>
            <a href="../index.php" target="_blank" class="btn-admin-view">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver Sitio
            </a>
            <a href="users.php?action=logout" class="btn-admin-logout" title="Cerrar Sesión">
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
                <h1 class="panel-title"><?php echo $action === 'add' ? 'Agregar Nuevo Usuario' : 'Editar Usuario'; ?></h1>
                <a href="users.php" class="btn-action btn-secondary"><i class="fa-solid fa-arrow-left"></i> Volver al Listado</a>
            </div>

            <div class="product-form-card">
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?php echo e($editUser['id'] ?? ''); ?>">

                    <div class="form-group">
                        <label class="form-label" for="usuario">Nombre de Usuario</label>
                        <input type="text" id="usuario" name="usuario" class="form-input" value="<?php echo e($editUser['usuario'] ?? ''); ?>" required placeholder="Ej. juanperez" style="max-width: 400px;">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="clave">Contraseña</label>
                        <input type="text" id="clave" name="clave" class="form-input" value="<?php echo e($editUser['clave'] ?? ''); ?>" required placeholder="Escriba la clave" style="max-width: 400px;">
                    </div>

                    <div class="form-group">
                        <label class="checkbox-group">
                            <input type="checkbox" name="activo" value="1" class="checkbox-input" <?php echo ($action === 'add' || (isset($editUser['activo']) && $editUser['activo'] === true)) ? 'checked' : ''; ?>>
                            <span class="form-label" style="margin-bottom: 0;">Usuario Activo (Puede iniciar sesión)</span>
                        </label>
                    </div>

                    <div style="margin-top: 30px; display: flex; gap: 15px;">
                        <button type="submit" class="btn-action">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar Usuario
                        </button>
                        <a href="users.php" class="btn-action btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <!-- Listado de Usuarios -->
            <div class="panel-header">
                <h1 class="panel-title">Gestión de Usuarios</h1>
                <a href="users.php?action=add" class="btn-action"><i class="fa-solid fa-plus"></i> Agregar Usuario</a>
            </div>

            <div class="products-table-wrapper">
                <?php if (empty($users)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-users"></i>
                        <p>No se encontraron usuarios cargados.</p>
                        <p style="font-size: 0.9rem; margin-top: 10px;">Haz clic en "Agregar Usuario" para comenzar.</p>
                    </div>
                <?php else: ?>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Contraseña</th>
                                <th style="width: 150px;">Estado</th>
                                <th style="width: 120px; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td style="font-weight: 600; color: #2d3748;"><?php echo e($u['usuario']); ?></td>
                                    <td style="color: #4a5568;"><?php echo e($u['clave']); ?></td>
                                    <td>
                                        <?php if (isset($u['activo']) && $u['activo'] === true): ?>
                                            <span class="badge badge-active">Activo</span>
                                        <?php else: ?>
                                            <span class="badge badge-inactive">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-links" style="justify-content: center;">
                                            <a href="users.php?action=edit&id=<?php echo urlencode($u['id']); ?>" class="action-link" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="users.php?action=delete&id=<?php echo urlencode($u['id']); ?>" class="action-link delete-link" title="Eliminar" onclick="return confirm('¿Está seguro de que desea eliminar el usuario &quot;<?php echo e($u['usuario']); ?>&quot;?');">
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

</body>

</html>
