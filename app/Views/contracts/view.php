<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Documento Legal de Contrato') ?> | Miami Luxury Agency</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-navy: #0f172a;
            --accent-gold: #d97706;
            --emerald-accent: #059669;
            --border-light: #e2e8f0;
            --bg-canvas: #f1f5f9;
        }

        body {
            background-color: var(--bg-canvas);
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Barra Superior Flotante para Navegación y Exportación */
        .top-action-bar {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1050;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .action-bar-brand {
            font-family: 'Cinzel', serif;
            letter-spacing: 1px;
            font-size: 0.95rem;
            color: #f8fafc;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Contenedor Hoja de Documento Legal */
        .document-wrapper {
            max-width: 920px;
            margin: 36px auto;
            background: #ffffff;
            box-shadow: 0 15px 40px rgba(15, 23, 42, 0.08), 0 2px 6px rgba(15, 23, 42, 0.04);
            border-radius: 12px;
            padding: 60px 70px;
            position: relative;
            border: 1px solid #e2e8f0;
        }

        /* Banda superior dorada y esmeralda */
        .document-wrapper::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #10b981 0%, #0284c7 50%, #f59e0b 100%);
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        /* Membrete Oficial */
        .letterhead-title {
            font-family: 'Cinzel', serif;
            font-size: 1.55rem;
            letter-spacing: 1.5px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .letterhead-subtitle {
            font-size: 0.82rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
        }

        .letterhead-registry {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* Sello Notarial y de Validación */
        .official-seal-badge {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1.5px solid #d97706;
            color: #92400e;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-align: right;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.12);
            display: inline-block;
        }

        /* Tarjeta Resumen de Comparecientes */
        .contract-meta-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px 24px;
            margin-bottom: 30px;
        }

        .meta-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.8px;
            margin-bottom: 3px;
        }

        .meta-value {
            font-weight: 600;
            color: #0f172a;
            font-size: 0.92rem;
        }

        .financial-pill-box {
            background: #ffffff;
            border: 1.5px solid #10b981;
            border-radius: 8px;
            padding: 10px 16px;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.08);
        }

        /* Cuerpo Legal y Cláusulas */
        .contract-body {
            font-size: 0.95rem;
            line-height: 1.8;
            color: #334155;
            margin-top: 25px;
        }

        .contract-body h3, .contract-body h4 {
            font-family: 'Cinzel', serif;
            color: #0f172a;
            font-weight: 700;
            margin-top: 24px;
            margin-bottom: 12px;
            border-left: 3px solid #0f172a;
            padding-left: 10px;
        }

        .contract-body p {
            margin-bottom: 16px;
            text-align: justify;
        }

        .contract-body ul, .contract-body ol {
            padding-left: 24px;
            margin-bottom: 18px;
        }

        .contract-body li {
            margin-bottom: 8px;
        }

        /* Bloque de Firmas */
        .signature-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px dashed #cbd5e1;
            page-break-inside: avoid;
        }

        .signature-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 24px;
            min-height: 170px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .signature-line {
            width: 80%;
            height: 1.5px;
            background: #0f172a;
            margin: 0 auto 10px auto;
        }

        .signature-stamp-agency {
            position: absolute;
            top: 20px;
            right: 20px;
            border: 2px solid #059669;
            color: #059669;
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            padding: 4px 8px;
            border-radius: 6px;
            transform: rotate(-4deg);
            opacity: 0.85;
            background: rgba(16, 185, 129, 0.05);
        }

        .security-hash {
            font-family: monospace;
            font-size: 0.68rem;
            color: #94a3b8;
            word-break: break-all;
        }

        /* Historial de Auditoría */
        .audit-box {
            background: #fdfdfd;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px 24px;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        /* Reglas Específicas de Impresión y Exportación a PDF */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .no-print {
                display: none !important;
            }
            .document-wrapper {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 15mm 15mm !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .document-wrapper::before {
                display: none !important;
            }
            @page {
                size: letter;
                margin: 12mm 12mm 15mm 12mm;
            }
            .contract-meta-card {
                background: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
            }
            .signature-section, .audit-box {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<!-- Barra Superior Ejecutiva de Controles (Oculta en Impresión) -->
<div class="no-print top-action-bar">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= url('/contracts/final') ?>" class="btn btn-outline-light btn-sm fw-semibold" title="Volver al Repositorio de Contratos">
                <i class="bi bi-arrow-left me-1"></i> Volver a Contratos Finales
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="action-bar-brand d-none d-md-flex">
                    <i class="bi bi-shield-lock-fill text-warning"></i> MIAMI LUXURY MEDIA
                </span>
                <span class="text-white-50 d-none d-md-inline">&bull;</span>
                <span class="badge bg-secondary font-monospace px-2 py-1 fs-6"><?= htmlspecialchars($contract['contract_code']) ?></span>
                <?php
                    $badgeClass = match($contract['status']) {
                        'APROBADO' => 'bg-success',
                        'EN_REVISION' => 'bg-warning text-dark',
                        'RECHAZADO' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                ?>
                <span class="badge <?= $badgeClass ?> px-2 py-1 fw-bold"><?= htmlspecialchars($contract['status']) ?></span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="copyContractLink()" class="btn btn-outline-light btn-sm" title="Copiar enlace directo">
                <i class="bi bi-link-45deg me-1"></i> <span class="d-none d-sm-inline">Copiar Enlace</span>
            </button>
            <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold px-3 shadow-sm text-dark">
                <i class="bi bi-printer-fill me-1"></i> Imprimir / Guardar en PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-sm" title="Cerrar pestaña">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
</div>

<!-- Contenedor Hoja Legal / Expediente -->
<div class="document-wrapper">

    <!-- Membrete Corporativo de Lujo -->
    <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="bg-dark text-white rounded p-1 px-2 fw-bold font-monospace" style="font-size: 0.8rem; letter-spacing: 1px;">
                    MIA &bull; OPS
                </div>
                <div class="letterhead-subtitle">Servicios Audiovisuales & Marketing de Alto Perfil</div>
            </div>
            <h1 class="letterhead-title">MIAMI LUXURY MEDIA AGENCY LLC</h1>
            <div class="letterhead-registry">
                1001 Brickell Bay Drive, Suite 2700, Miami, FL 33131 &bull; Div. of Corporations Registry: #L21000392819
            </div>
            <div class="mt-2 text-muted small">
                <strong>INSTRUMENTO LEGAL:</strong> Expediente de Prestación de Servicios Profesionales & Exclusividad
            </div>
        </div>

        <div class="text-end flex-shrink-0">
            <div class="official-seal-badge mb-2">
                <div class="text-uppercase"><i class="bi bi-patch-check-fill text-warning me-1"></i>DOCUMENTO REGISTRADO</div>
                <div class="small fw-normal text-muted">Condado de Miami-Dade &bull; Florida</div>
            </div>
            <div class="font-monospace fw-bold text-dark fs-6"><?= htmlspecialchars($contract['contract_code']) ?></div>
            <div class="small text-muted">Fecha Emisión: <?= htmlspecialchars($contract['start_date']) ?></div>
        </div>
    </div>

    <!-- Resumen Ejecutivo de Comparecientes y Condiciones Económicas -->
    <div class="contract-meta-card">
        <div class="row g-3">
            <div class="col-md-5 border-end-md">
                <div class="meta-label"><i class="bi bi-building me-1"></i>Cliente / Brokerage Inmobiliario</div>
                <div class="meta-value fs-5"><?= htmlspecialchars($contract['company_name']) ?></div>
                <div class="text-secondary small mt-1">
                    <i class="bi bi-person-badge me-1"></i>Representante: <strong><?= htmlspecialchars($contract['contact_person']) ?></strong>
                </div>
                <?php if (!empty($contract['realtor_phone']) || !empty($contract['realtor_email'])): ?>
                    <div class="text-muted small mt-1">
                        <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($contract['realtor_email'] ?? 'contacto@cliente.com') ?> &bull; 
                        <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($contract['realtor_phone'] ?? 'N/A') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-3 border-end-md">
                <div class="meta-label"><i class="bi bi-geo-alt-fill me-1"></i>Zona Territorial Exclusiva</div>
                <div class="meta-value text-primary"><?= htmlspecialchars($contract['miami_zone']) ?></div>
                <div class="text-muted small mt-1">Cobertura Miami-Dade</div>
                <div class="meta-label mt-2"><i class="bi bi-calendar3 me-1"></i>Vigencia Acordada</div>
                <div class="meta-value small"><?= htmlspecialchars($contract['start_date']) ?> &rarr; <?= htmlspecialchars($contract['end_date']) ?></div>
            </div>

            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="meta-label text-dark"><i class="bi bi-shield-check me-1 text-primary"></i>Acuerdo Operativo Audiovisual</div>
                    <div class="fs-5 fw-bold text-dark">Plan de Producción Mensual</div>
                    <div class="d-flex justify-content-between align-items-center mt-1 pt-1 border-top small text-muted">
                        <span>Modalidad:</span>
                        <strong class="text-dark">Producción Recurrente Miami-Dade</strong>
                    </div>
                </div>
                <div class="small text-muted mt-2">
                    <i class="bi bi-person-check me-1"></i>Redactor Autorizado: <?= htmlspecialchars($contract['generator_employee_name']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Compilado del Contrato Oficial (Cláusulas) -->
    <div class="contract-body">
        <?= $contract['compiled_body_html'] ?>
    </div>

    <!-- Bloque de Firmas y Validación Jurídica Notarial -->
    <div class="signature-section">
        <div class="text-center mb-4">
            <span class="text-uppercase small fw-bold text-secondary letter-spacing-1">
                Formalización y Ejecución Legal de las Partes Comparecientes
            </span>
        </div>

        <div class="row g-4">
            <!-- Firma Prestador -->
            <div class="col-md-6">
                <div class="signature-box text-center">
                    <div class="signature-stamp-agency">
                        <i class="bi bi-shield-check me-1"></i>CERTIFICADO DIGITAL
                    </div>
                    <div class="text-start mb-4">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Por la Agencia Prestadora:</small>
                        <div class="fw-bold text-dark">MIAMI LUXURY MEDIA AGENCY LLC</div>
                    </div>

                    <div class="mt-4">
                        <div class="signature-line"></div>
                        <div class="fw-bold text-dark">Dirección General & Operaciones</div>
                        <div class="text-muted small">Miami Luxury Media Agency LLC</div>
                        <div class="security-hash mt-2">
                            Aprobado digitalmente &bull; Hash: SHA256: <?= substr(hash('sha256', $contract['contract_code'] . $contract['created_at']), 0, 24) ?>...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Firma Cliente -->
            <div class="col-md-6">
                <div class="signature-box text-center">
                    <div class="text-start mb-4">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Por el Cliente Contratante:</small>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($contract['company_name']) ?></div>
                    </div>

                    <div class="mt-4">
                        <div class="signature-line"></div>
                        <div class="fw-bold text-dark"><?= htmlspecialchars($contract['contact_person']) ?></div>
                        <div class="text-muted small">Representante Legal / Broker Autorizado</div>
                        <div class="security-hash mt-2">
                            Conformidad plena con los términos, plazos y anexos estipulados
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3 text-muted small" style="font-size: 0.72rem;">
            <i class="bi bi-info-circle me-1"></i> Este documento goza de plena validez jurídica conforme al Florida Electronic Signature Act (FESA), Florida Statutes Chapter 668.
        </div>
    </div>

    <!-- Historial de Trazabilidad y Aprobaciones del Jefe -->
    <?php if (!empty($contract['reviews'])): ?>
        <div class="audit-box">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                <h6 class="fw-bold text-dark text-uppercase small mb-0">
                    <i class="bi bi-file-earmark-check-fill text-success me-1"></i> Registro Notarial & Trazabilidad de Aprobación
                </h6>
                <span class="badge bg-light text-muted border small"><?= count($contract['reviews']) ?> revisiones registradas</span>
            </div>

            <div class="list-group list-group-flush">
                <?php foreach ($contract['reviews'] as $rev): ?>
                    <div class="list-group-item bg-transparent px-0 py-2 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge <?= $rev['status_assigned'] === 'APROBADO' ? 'bg-success' : 'bg-danger' ?> me-2">
                                    <?= htmlspecialchars($rev['status_assigned']) ?>
                                </span>
                                <strong class="text-dark small">Revisado por Dirección: <?= htmlspecialchars($rev['reviewer_name'] ?? $rev['reviewer_email']) ?></strong>
                            </div>
                            <span class="text-muted small"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($rev['created_at']) ?></span>
                        </div>
                        <?php if (!empty($rev['feedback_notes'])): ?>
                            <div class="text-secondary small mt-1 bg-white p-2 rounded border ms-3">
                                <i class="bi bi-chat-left-quote text-muted me-1"></i>"<?= htmlspecialchars($rev['feedback_notes']) ?>"
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Script para utilidades -->
<script>
function copyContractLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        alert("Enlace del contrato copiado al portapapeles.");
    }).catch(() => {
        prompt("Copie el enlace:", window.location.href);
    });
}
</script>

<?php if (isset($_GET['print'])): ?>
<script>
window.addEventListener('DOMContentLoaded', () => {
    window.print();
});
</script>
<?php endif; ?>

</body>
</html>
