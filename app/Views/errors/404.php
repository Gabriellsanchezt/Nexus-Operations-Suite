<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Módulo No Encontrado | Nexus Operations Suite</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= function_exists('asset_url') ? asset_url('/assets/css/style.css') : (function_exists('url') ? url('/assets/css/style.css') : '/assets/css/style.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #0b1329 0%, #172554 45%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #f8fafc;
            padding: 20px;
        }
        .error-card {
            background: rgba(255, 255, 255, 0.98);
            color: #0f172a;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
            max-width: 580px;
            width: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .error-card-header {
            background: #0f172a;
            color: #ffffff;
            padding: 32px 28px 24px;
            text-align: center;
            position: relative;
        }
        .error-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 4px 14px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .error-code-watermark {
            font-size: 5.5rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.05em;
            background: linear-gradient(180deg, #38bdf8 0%, #1e40af 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 12px 0 6px;
        }
        .error-path-chip {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            border-radius: 8px;
            padding: 8px 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.82rem;
            word-break: break-all;
        }
    </style>
</head>
<body>

<div class="error-card">
    <div class="error-card-header">
        <div class="error-badge-pill mb-2">
            <i class="bi bi-compass"></i> Protocolo de Ruta
        </div>
        <div class="error-code-watermark">404</div>
        <h4 class="fw-bold mb-1 text-white">Módulo o Recurso No Encontrado</h4>
        <p class="text-slate-400 small mb-0 text-white-50">
            La dirección a la que intenta ingresar no está registrada en el sistema de operaciones
        </p>
    </div>

    <div class="p-4 p-md-5">
        <div class="mb-3">
            <label class="form-label text-muted small fw-semibold text-uppercase">Ruta solicitada:</label>
            <div class="error-path-chip d-flex align-items-center gap-2">
                <i class="bi bi-link-45deg text-secondary fs-5"></i>
                <span><?= htmlspecialchars($requestedPath ?? $_SERVER['REQUEST_URI'] ?? '/desconocido') ?></span>
            </div>
        </div>

        <p class="text-secondary small mb-4 lh-base">
            Es posible que el enlace haya sido reubicado como parte de las actualizaciones de la suite, 
            que requiera un rol de acceso específico (Jefe, Asistente, Marketing, Social Manager), o que la dirección contenga una errata tipográfica.
        </p>

        <div class="d-grid gap-2 mb-3">
            <a href="<?= function_exists('url') ? url('/dashboard') : '/dashboard' ?>" class="btn btn-dark py-2 d-flex align-items-center justify-content-center gap-2 fw-semibold">
                <i class="bi bi-speedometer2"></i>
                <span>Volver al Dashboard Principal</span>
            </a>
            <div class="row g-2">
                <div class="col-6">
                    <button type="button" onclick="window.history.back()" class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-2 small">
                        <i class="bi bi-arrow-left"></i>
                        <span>Regresar</span>
                    </button>
                </div>
                <div class="col-6">
                    <a href="<?= function_exists('url') ? url('/activities') : '/activities' ?>" class="btn btn-outline-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2 small">
                        <i class="bi bi-kanban"></i>
                        <span>Producción</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-top pt-3 d-flex align-items-center justify-content-between text-muted" style="font-size: 0.72rem;">
            <span><i class="bi bi-shield-check text-success me-1"></i> Nexus Operations Suite</span>
            <span>Miami Luxury Media Ops &copy; <?= date('Y') ?></span>
        </div>
    </div>
</div>

<!-- Bootstrap 5.3.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
