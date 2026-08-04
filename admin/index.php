<?php
session_start();

// Si ya tiene sesión activa, redirigir al panel de administración
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validación de usuario y contraseña hardcodeados
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}

// Determinar ruta de logo para mostrar
$logoPath = file_exists(__DIR__ . '/upload/logo-padma.jpg') ? 'upload/logo-padma.jpg' : '../assets/img/logo-padma.jpg';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Iniciar Sesión | Admin Padma Yoga</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #f7f9f6;
            --card-bg: #ffffff;
            --primary-color: #4a6352;
            --primary-hover: #3b5042;
            --accent-color: #8c9e8e;
            --text-color: #2c3531;
            --text-light: #667069;
            --error-color: #c53030;
            --error-bg: #fff5f5;
            --error-border: #feb2b2;
            --radius: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at 10% 20%, rgba(140, 158, 142, 0.08) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(74, 99, 82, 0.08) 0%, transparent 40%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--text-color);
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 40px 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(140, 158, 142, 0.2);
            text-align: center;
        }

        .login-logo {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent-color);
            box-shadow: 0 4px 14px rgba(74, 99, 82, 0.15);
            margin-bottom: 16px;
        }

        .login-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 4px;
        }

        .login-subtitle {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 28px;
            letter-spacing: 0.5px;
        }

        .error-message {
            background: var(--error-bg);
            color: var(--error-color);
            border: 1px solid var(--error-border);
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 1rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.95rem;
            color: var(--text-color);
            transition: all 0.2s ease;
            background: #fafafa;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(74, 99, 82, 0.15);
        }

        .btn-submit {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.1s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .login-footer {
            margin-top: 24px;
            font-size: 0.85rem;
        }

        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }

        .login-footer a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">
            <img src="<?php echo htmlspecialchars($logoPath); ?>" alt="Padma Yoga Logo" class="login-logo">
            <h1 class="login-title">PADMA YOGA</h1>
            <p class="login-subtitle">Panel de Administración</p>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php">
                <div class="form-group">
                    <label for="username" class="form-label">Usuario</label>
                    <div class="input-container">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="username" name="username" class="form-input" placeholder="Ingresa tu usuario" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-container">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Ingresa tu contraseña" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Iniciar Sesión</span>
                </button>
            </form>

            <div class="login-footer">
                <a href="../index.php" target="_blank">
                    <i class="fa-solid fa-arrow-left"></i> Volver al sitio principal
                </a>
            </div>
        </div>
    </div>

</body>

</html>
