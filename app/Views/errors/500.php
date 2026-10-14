<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Incidencia en el Servidor | Nexus Operations Suite</title>
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
            background: linear-gradient(135deg, #0b1329 0%, #1e1b4b 45%, #0f172a 100%);
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
            max-width: 680px;
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
            background: linear-gradient(180deg, #f87171 0%, #b91c1c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 12px 0 6px;
        }
        .tech-box {
            background: #0b1329;
            color: #94a3b8;
            border-radius: 10px;
            padding: 16px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.78rem;
            max-height: 240px;
            overflow-y: auto;
            border: 1px solid #1e293b;
        }
    </style>
</head>
<body>

<div class="error-card">
    <div class="error-card-header">
        <div class="error-badge-pill mb-2">
            <i class="bi bi-shield-exclamation"></i> Incidencia de Servidor
        </div>
        <div class="error-code-watermark">500</div>
        <h4 class="fw-bold mb-1 text-white">Incidencia Técnica en el Sistema</h4>
        <p class="text-white-50 small mb-0">
            El servidor ha detectado un comportamiento imprevisto al procesar la solicitud
        </p>
    </div>

    <div class="p-4 p-md-5">
        <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-dark d-flex align-items-start gap-3 p-3 rounded-3 mb-4">
            <i class="bi bi-exclamation-triangle-fill text-warning fs-4 flex-shrink-0 mt-1"></i>
            <div class="small">
                <strong>Integridad Protegida:</strong> Si estabas guardando información, la transacción de base de datos fue revertida automáticamente para proteger la integridad relacional de los registros de Realtors, Tareas y Contratos.
            </div>
        </div>

        <?php if (!empty($errorMessage) && (defined('APP_ENV') && APP_ENV === 'development')): ?>
            <div class="card border border-slate-200 mb-4 shadow-none">
                <div class="card-header bg-light py-2 px-3 d-flex align-items-center justify-content-between">
                    <span class="small fw-semibold text-secondary font-monospace">
                        <i class="bi bi-code-square me-1"></i> Diagnóstico Técnico (Modo Desarrollo)
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 small" onclick="copyDiagnostic()" id="btnCopyDiag">
                        <i class="bi bi-clipboard me-1"></i> Copiar
                    </button>
                </div>
                <div class="card-body p-3">
                    <div class="text-danger fw-semibold small mb-1" id="diagMessage">
                        <?= htmlspecialchars($errorMessage) ?>
                    </div>
                    <?php if (!empty($errorFile)): ?>
                        <div class="text-muted small mb-2 font-monospace" style="font-size: 0.75rem;">
                            <?= htmlspecialchars($errorFile) ?> : línea <?= htmlspecialchars((string)($errorLine ?? '')) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($errorTrace)): ?>
                        <div class="tech-box text-light" id="diagTrace">
                            <?= htmlspecialchars($errorTrace) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-grid gap-2 mb-3">
            <button type="button" onclick="location.reload()" class="btn btn-dark py-2 d-flex align-items-center justify-content-center gap-2 fw-semibold">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Reintentar Operación</span>
            </button>
            <div class="row g-2">
                <div class="col-6">
                    <button type="button" onclick="window.history.back()" class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-2 small">
                        <i class="bi bi-arrow-left"></i>
                        <span>Regresar</span>
                    </button>
                </div>
                <div class="col-6">
                    <a href="<?= function_exists('url') ? url('/dashboard') : '/dashboard' ?>" class="btn btn-outline-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2 small">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
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

<script>
function copyDiagnostic() {
    const msg = document.getElementById('diagMessage')?.innerText || '';
    const trace = document.getElementById('diagTrace')?.innerText || '';
    const fullText = "Mensaje: " + msg + "\n\nTrace:\n" + trace;
    navigator.clipboard.writeText(fullText).then(() => {
        const btn = document.getElementById('btnCopyDiag');
        if (btn) {
            btn.innerHTML = '<i class="bi bi-check2 text-success me-1"></i> Copiado';
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-clipboard me-1"></i> Copiar';
            }, 2000);
        }
    });
}
</script>

<!-- Bootstrap 5.3.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
