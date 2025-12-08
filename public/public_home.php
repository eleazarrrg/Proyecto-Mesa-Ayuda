<?php
require_once __DIR__ . '/../app/core/Database.php';

$db = Database::getInstance()->getConnection();

$tipos = $db->query("SELECT nombre, categoria FROM tipos_ticket ORDER BY nombre")->fetchAll();

$tecnicos = [];
$academicos = [];
foreach ($tipos as $t) {
    if ($t['categoria'] === 'TECNICO') {
        $tecnicos[] = $t['nombre'];
    } else {
        $academicos[] = $t['nombre'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mesa de Ayuda - Página pública</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Estilos adicionales SOLO para esta página pública -->
    <style>
        .public-main {
            max-width: 1100px;
            margin: 24px auto 40px auto;
            padding: 0 16px;
        }

        .public-hero-actions {
            margin-top: 10px;
        }

        .public-hero-actions .btn {
            padding: 7px 16px;
            font-size: 0.9rem;
        }

        .public-subtitle {
            margin-top: 6px;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .public-intro.card {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(0, 1.3fr);
            gap: 22px;
            align-items: flex-start;
        }

        .public-kpi {
            background: #eff6ff;
            border-radius: 14px;
            padding: 10px 14px;
            font-size: 0.86rem;
            color: #1f2937;
        }

        .public-kpi strong {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }

        .public-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }

        .public-chip {
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.78rem;
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid rgba(37, 99, 235, 0.25);
        }

        .public-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 10px;
        }

        .public-card-tickets ul {
            margin-top: 6px;
            padding-left: 20px;
            font-size: 0.9rem;
        }

        .public-card-tickets li {
            margin-bottom: 3px;
        }

        .public-callout {
            margin-top: 26px;
            font-size: 0.9rem;
            background: #ecfdf3;
            border-radius: 14px;
            padding: 12px 16px;
            border: 1px solid #bbf7d0;
            color: #065f46;
        }

        .public-callout strong {
            color: #047857;
        }

        @media (max-width: 768px) {
            .public-intro.card {
                grid-template-columns: 1fr;
            }

            .public-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Hero / cabecera vistosa -->
    <div class="header-public">
        <h1>Mesa de Ayuda</h1>
        <p class="public-subtitle">
            Tu canal único para reportar incidentes de TI y gestionar solicitudes académicas.
        </p>
        <div class="public-hero-actions">
            <a href="index.php" class="btn">Iniciar sesión</a>
        </div>
    </div>

    <div class="public-main">
        <!-- Bloque introductorio corto -->
        <section class="card public-intro">
            <div>
                <h2>¿Qué es la Mesa de Ayuda?</h2>
                <p>
                    Es una plataforma donde los estudiantes pueden registrar solicitudes y reportar
                    problemas relacionados con tecnología y procesos académicos, todo en un solo lugar.
                </p>
                <ul>
                    <li>Centraliza las solicitudes y el historial de atención.</li>
                    <li>Mejora los tiempos de respuesta del equipo de soporte.</li>
                    <li>Deja evidencia clara de lo que se ha atendido.</li>
                </ul>
            </div>

            <div class="public-kpi">
                <strong>Beneficios para la institución</strong>
                <p>✔ Control y trazabilidad de cada solicitud.</p>
                <p>✔ Estadísticas de tickets atendidos por categoría.</p>
                <p>✔ Encuestas de satisfacción para medir la calidad del servicio.</p>
            </div>
        </section>

        <!-- Tipos de tickets en tarjetas -->
        <section class="card">
            <h2>Tipos de Tickets</h2>
            <p>Los tickets se agrupan en dos grandes categorías, según el tipo de necesidad del estudiante:</p>

            <div class="public-grid">
                <!-- Soporte técnico -->
                <div class="public-card-tickets">
                    <h3>Soporte Técnico</h3>
                    <p style="margin-top:4px;">
                        Para incidentes informáticos y servicios de TI.
                    </p>
                    <div class="public-chip-list">
                        <span class="public-chip">Correo institucional</span>
                        <span class="public-chip">Acceso a internet</span>
                        <span class="public-chip">Equipos de laboratorio</span>
                        <span class="public-chip">Plataformas en línea</span>
                    </div>
                    <?php if (!empty($tecnicos)): ?>
                        <ul>
                            <?php foreach ($tecnicos as $n): ?>
                                <li><?= htmlspecialchars($n); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Académicos -->
                <div class="public-card-tickets">
                    <h3>Tickets Académicos</h3>
                    <p style="margin-top:4px;">
                        Para trámites y gestiones relacionadas con tu vida académica.
                    </p>
                    <div class="public-chip-list">
                        <span class="public-chip">Créditos oficiales</span>
                        <span class="public-chip">Reclamo de notas</span>
                        <span class="public-chip">Certificados</span>
                        <span class="public-chip">Otros trámites</span>
                    </div>
                    <?php if (!empty($academicos)): ?>
                        <ul>
                            <?php foreach ($academicos as $n): ?>
                                <li><?= htmlspecialchars($n); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Bloque final tipo marketing simple -->
        <section class="public-callout">
            <strong>¿Eres parte del Departamento de TI o administración?</strong>
            <p style="margin:6px 0 0;">
                Usa la Mesa de Ayuda para asignar tickets, registrar soluciones y generar
                reportes en Excel sobre los servicios atendidos.  
                Cuando estés listo, puedes ingresar al sistema desde
                <a href="index.php">Iniciar sesión</a>.
            </p>
        </section>
    </div>
</body>
</html>