<?php
/**
 * SigaTI - Vista de Login (Fiel al diseño SIGEI-PC)
 */
if (count(get_included_files()) === 1) exit("Acceso denegado.");

$pageTitle = "Iniciar Sesión";
include __DIR__ . '/../layouts/header.php';
?>

<div class="container login-wrapper">
    <div class="text-center col ">
        <div class="card login-card mx-auto">
            <div class="card-body p-0">
                
                <div class="login-logo-box">
                    <i class="bi bi-pc-display-horizontal"></i>
                </div>
                
                <h2 class="fw-bold text-dark m-0 fs-3">SigaTI</h2>
                <p class="text-muted small mb-4">Gestión de Inventario</p>

                <?php if (!empty($errorLogin)): ?>
                    <div class="alert alert-danger py-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo htmlspecialchars($errorLogin); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?action=procesar_login" method="POST" autocomplete="off">
                    
                    <div class="input-group-custom">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuario o Correo Electrónico" required autofocus>
                    </div>
                    
                    <div class="input-group-custom">
                        <i class="bi bi-key input-icon"></i>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                    </div>

                    <button type="submit" class="btn btn-access w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-laptop"></i> Acceder al Sistema
                    </button>
                </form>

            </div>
        </div>
        
        <p class="login-footer-text">© 2026 SigaTI - Aseguras tu futuro.</p>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>