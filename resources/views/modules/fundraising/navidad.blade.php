@extends('layouts.app')

@section('title', 'Navidad - Fundraising')

@section('styles')
<style>
    .hero {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a78bfa 100%);
        padding: 5rem 1.5rem;
        text-align: center;
        color: white;
        margin: -2.5rem -1.5rem 0;
    }

    .hero h1 {
        font-size: 2.75rem;
        font-weight: 700;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }

    .hero p {
        font-size: 1.15rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .content-container { padding: 3rem 0; }

    .section-title { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; color: #1e293b; }
    .section-subtitle { color: #64748b; margin-bottom: 2.5rem; font-size: 1rem; }

    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .info-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        border: 1px solid #e2e8f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    .card-icon {
        width: 48px; height: 48px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; margin-bottom: 1.25rem;
    }

    .card-icon.purple { background: #ede9fe; }
    .card-icon.blue { background: #dbeafe; }
    .card-icon.green { background: #dcfce7; }

    .info-card h3 { font-size: 1.15rem; font-weight: 600; margin-bottom: 0.5rem; }
    .info-card p { color: #64748b; font-size: 0.9rem; line-height: 1.6; }

    .stats-section {
        background: white; border-radius: 12px;
        border: 1px solid #e2e8f0; padding: 2.5rem; margin-top: 3rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem; text-align: center;
    }

    .stat-number { font-size: 2.5rem; font-weight: 700; color: #6366f1; line-height: 1; margin-bottom: 0.5rem; }
    .stat-label-info { color: #64748b; font-size: 0.9rem; font-weight: 500; }

    .timeline { margin-top: 3rem; }

    .timeline-item {
        display: flex; gap: 1.5rem; padding-bottom: 2rem; position: relative;
    }

    .timeline-item:not(:last-child)::after {
        content: ''; position: absolute; left: 19px; top: 44px;
        width: 2px; bottom: 0; background: #e2e8f0;
    }

    .timeline-dot {
        width: 40px; height: 40px; border-radius: 50%;
        background: #6366f1; display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 600; font-size: 0.85rem; flex-shrink: 0;
    }

    .timeline-content h4 { font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; }
    .timeline-content p { color: #64748b; font-size: 0.9rem; }

    @media (max-width: 640px) {
        .hero h1 { font-size: 1.75rem; }
        .hero p { font-size: 1rem; }
    }
</style>
@endsection

@section('content')
    <section class="hero">
        <h1>Navidad 2025</h1>
        <p>Este a&ntilde;o celebramos juntos con nuestro intercambio de amigo secreto y actividades especiales para compartir en equipo.</p>
    </section>

    <div class="content-container">
        <h2 class="section-title">Actividades</h2>
        <p class="section-subtitle">Todo lo que tenemos planeado para esta temporada navide&ntilde;a.</p>

        <div class="cards-grid">
            <div class="info-card">
                <div class="card-icon purple">&#127873;</div>
                <h3>Amigo Secreto</h3>
                <p>Intercambio de regalos entre todos los participantes. Cada persona recibe una URL secreta para descubrir a qui&eacute;n le toca regalar.</p>
            </div>
            <div class="info-card">
                <div class="card-icon blue">&#127876;</div>
                <h3>Cena Navide&ntilde;a</h3>
                <p>Reuni&oacute;n de fin de a&ntilde;o para compartir, entregar regalos y celebrar juntos los logros del a&ntilde;o.</p>
            </div>
            <div class="info-card">
                <div class="card-icon green">&#128176;</div>
                <h3>Fondo Com&uacute;n</h3>
                <p>Recaudaci&oacute;n colectiva para cubrir los gastos de decoraci&oacute;n, comida y actividades de la celebraci&oacute;n.</p>
            </div>
        </div>

        <div class="stats-section">
            <div class="stats-grid">
                <div>
                    <div class="stat-number">12</div>
                    <div class="stat-label-info">Participantes</div>
                </div>
                <div>
                    <div class="stat-number">$120</div>
                    <div class="stat-label-info">Meta de recaudaci&oacute;n</div>
                </div>
                <div>
                    <div class="stat-number">$15</div>
                    <div class="stat-label-info">L&iacute;mite por regalo</div>
                </div>
                <div>
                    <div class="stat-number">Dec 20</div>
                    <div class="stat-label-info">Fecha del evento</div>
                </div>
            </div>
        </div>

        <div class="timeline">
            <h2 class="section-title">Cronograma</h2>
            <p class="section-subtitle">Fechas importantes para la organizaci&oacute;n.</p>

            <div class="timeline-item">
                <div class="timeline-dot">1</div>
                <div class="timeline-content">
                    <h4>Registro de participantes</h4>
                    <p>Confirmar qui&eacute;nes participar&aacute;n en el intercambio y las actividades.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot">2</div>
                <div class="timeline-content">
                    <h4>Sorteo de amigo secreto</h4>
                    <p>Asignaci&oacute;n aleatoria y env&iacute;o de URLs secretas a cada participante.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot">3</div>
                <div class="timeline-content">
                    <h4>Recaudaci&oacute;n del fondo</h4>
                    <p>Cada participante aporta su cuota para cubrir los gastos del evento.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot">4</div>
                <div class="timeline-content">
                    <h4>Celebraci&oacute;n</h4>
                    <p>Cena navide&ntilde;a, entrega de regalos y cierre del a&ntilde;o.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
