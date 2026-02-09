<?php
// Evitar acceso directo
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)) . '/');
}
require_once ROOT_PATH . 'includes/autoload.php';
require_once ROOT_PATH . 'includes/auth.php';

$usuario = getUsuarioActual();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sistema RIAAC'; ?></title>

    <!-- Fuentes Google -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Material Symbols de Google -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://unpkg.com/akar-icons-fonts"></script>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />

    <!-- Modern UI CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/modern-ui.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    

    <!-- Variables globales de PHP a JavaScript -->
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
</head>

<body>
    <!-- Navbar Flotante -->
    <nav class="navbar" id="navbar">
        <div class="navbar-left">
            <div class="search-bar">
                <i class="ai-search"></i>
                <input type="text" placeholder="Buscar en el sistema...">
            </div>
        </div>

        <div class="navbar-right">
            <button class="nav-btn" title="Notificaciones">
                <i class="ai-bell"></i>
                <span class="nav-badge">3</span>
            </button>
            <button class="nav-btn" title="Mensajes">
                <i class="ai-envelope"></i>
                <span class="nav-badge">5</span>
            </button>
            <button class="nav-btn" title="Ayuda">
                <i class="ai-question"></i>
            </button>

            <!-- Dropdown de usuario -->
            <div class="dropdown">
                <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($usuario['nombre']); ?>&background=3498db&color=fff" alt="User">
                    <div class="user-info">
                        <span><?php echo $usuario['nombre']; ?></span>
                        <small><?php echo ucfirst($usuario['rol']); ?></small>
                    </div>
                    <i class="ai-chevron-down"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <h6 class="dropdown-header"><?php echo ucfirst($usuario['rol']); ?></h6>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>views/perfil.php"><i class="bi bi-person"></i> Mi Perfil</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>views/configuracion.php"><i class="bi bi-gear"></i> Configuración</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>controllers/AuthController.php?action=logout"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar Colapsable -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-left">
            <div class="logo-container" id="menuToggle">
                <img src="<?php echo BASE_URL; ?>assets/images/logo2.png" alt="RIAAC" onerror="this.src='https://ui-avatars.com/api/?name=RIAAC&background=3498db&color=fff&size=40'">
            </div>
            <button title="Inicio">
                <i class="ai-home-alt1"></i>
            </button>
            <button title="Agregar">
                <i class="ai-plus"></i>
            </button>
            <div class="sidebar-bottom">
                <button title="Configuración">
                    <i class="ai-gear"></i>
                </button>
                <button title="Cerrar Sesión" onclick="window.location.href='<?php echo BASE_URL; ?>controllers/AuthController.php?action=logout'">
                    <i class="ai-sign-out"></i>
                </button>
            </div>
        </div>
        <div class="sidebar-right">
            <div class="sidebar-right-inner">
                <div class="header">
                    <div>
                        <h2>RIAAC</h2>
                        <h3>Sistema de Inventario</h3>
                    </div>
                    <i class="ai-chevron-down"></i>
                </div>
                <nav>
                    <a href="<?php echo BASE_URL; ?>views/dashboard/index.php" class="menu-item">
                        <i class="ai-dashboard"></i>
                        <span>Dashboard</span>
                        <i class="ai-arrow-right"></i>
                    </a>
                    <a href="<?php echo BASE_URL; ?>views/clientes/index.php" class="menu-item">
                        <i class="ai-people-multiple"></i>
                        <span>Clientes</span>
                        <i class="ai-arrow-right"></i>
                    </a>
                    <a href="<?php echo BASE_URL; ?>views/hosting/index.php" class="menu-item">
                        <i class="ai-globe"></i>
                        <span>Hosting/Dominios</span>
                        <i class="ai-arrow-right"></i>
                    </a>
                    <a href="<?php echo BASE_URL; ?>views/reparaciones/index.php" class="menu-item">
                        <i class="ai-sparkles"></i>
                        <span>Reparaciones</span>
                        <i class="ai-arrow-right"></i>
                    </a>
                    <button>
                        <i class="ai-shopping-bag"></i>
                        <span>Productos</span>
                        <i class="ai-chevron-down"></i>
                    </button>
                    <ul class="submenu">
                        <li>
                            <a href="<?php echo BASE_URL; ?>views/productos/index.php">
                               <i class="ai-shipping-box-v1"></i>&nbsp;Inventario
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>views/proveedores/index.php">
                                <i class="ai-truck"></i>&nbsp;Proveedores
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>views/ventas/index.php">
                                <i class="ai-receipt"></i>&nbsp;Ventas
                            </a>
                        </li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>views/garantias/index.php" class="menu-item">
                        <i class="ai-fire"></i>
                        <span>Garantías</span>
                        <i class="ai-arrow-right"></i>
                    </a>
                </nav>
            </div>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <main class="main-content">