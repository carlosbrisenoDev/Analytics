<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Unlock Brands | Sistema de Presencia Digital Activa</title>
  <style>
    :root{
      --bg:#080808;
      --panel:#101010;
      --panel-2:#151515;
      --line:#242424;
      --text:#f3f3f0;
      --muted:#9b9b94;
      --acid:#00ff9c;
      --red:#ff4d4d;
      --amber:#f6c85f;
      --blue:#70a7ff;
      --shadow:0 20px 60px rgba(0,0,0,.45);
      --radius:24px;
    }

    *{box-sizing:border-box}
    body{
      margin:0;
      background: radial-gradient(circle at 20% 0%, rgba(0,255,156,.09), transparent 28%),
                  radial-gradient(circle at 90% 10%, rgba(112,167,255,.08), transparent 24%),
                  var(--bg);
      color:var(--text);
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      letter-spacing:-.02em;
    }

    .app{
      display:grid;
      grid-template-columns:290px 1fr;
      min-height:100vh;
    }

    aside{
      position:sticky;
      top:0;
      height:100vh;
      padding:28px 22px;
      border-right:1px solid var(--line);
      background:rgba(8,8,8,.82);
      backdrop-filter:blur(18px);
    }

    .brand{
      display:flex;
      align-items:center;
      gap:12px;
      margin-bottom:34px;
    }

    .mark{
      width:42px;height:42px;
      border:1px solid rgba(0,255,156,.6);
      border-radius:14px;
      display:grid;place-items:center;
      box-shadow:0 0 26px rgba(0,255,156,.18) inset;
      color:var(--acid);
      font-weight:800;
    }

    .brand h1{
      font-size:15px;
      margin:0;
      text-transform:uppercase;
      letter-spacing:.08em;
    }

    .brand p{margin:3px 0 0;color:var(--muted);font-size:12px;letter-spacing:0}

    nav{display:flex;flex-direction:column;gap:8px}
    nav a{
      text-decoration:none;
      color:var(--muted);
      padding:13px 14px;
      border-radius:16px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      font-size:14px;
      transition:.2s ease;
    }
    nav a:hover, nav a.active{
      color:var(--text);
      background:linear-gradient(90deg, rgba(0,255,156,.14), rgba(255,255,255,.035));
    }
    nav span{font-family:ui-monospace, SFMono-Regular, Menlo, monospace;font-size:11px;color:#666}

    .aside-card{
      margin-top:28px;
      padding:18px;
      border:1px solid var(--line);
      border-radius:22px;
      background:linear-gradient(180deg, rgba(255,255,255,.035), rgba(255,255,255,.015));
    }
    .aside-card b{display:block;font-size:13px;margin-bottom:8px}
    .aside-card p{margin:0;color:var(--muted);font-size:12px;line-height:1.5;letter-spacing:0}

    main{padding:34px;overflow:hidden}
    header{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      margin-bottom:28px;
    }
    .eyebrow{color:var(--acid);font-family:ui-monospace, Menlo, monospace;font-size:12px;text-transform:uppercase;letter-spacing:.14em}
    h2{font-size:42px;line-height:1;margin:8px 0 8px;letter-spacing:-.06em}
    .sub{color:var(--muted);max-width:720px;margin:0;line-height:1.5;letter-spacing:0}

    .client-pill{
      border:1px solid var(--line);
      background:rgba(255,255,255,.04);
      padding:12px 16px;
      border-radius:999px;
      color:var(--muted);
      font-size:13px;
    }
    .client-pill strong{color:var(--text)}

    section{margin-bottom:26px}
    .grid{display:grid;gap:18px}
    .grid-3{grid-template-columns:1.1fr .95fr .95fr}
    .grid-4{grid-template-columns:repeat(4,1fr)}
    .grid-2{grid-template-columns:1fr 1fr}

    .card{
      background:linear-gradient(180deg, rgba(255,255,255,.055), rgba(255,255,255,.022));
      border:1px solid var(--line);
      border-radius:var(--radius);
      padding:22px;
      box-shadow:var(--shadow);
      position:relative;
      overflow:hidden;
    }
    .card:before{
      content:"";
      position:absolute;inset:0;
      background:linear-gradient(120deg, rgba(0,255,156,.07), transparent 28%);
      pointer-events:none;
      opacity:.7;
    }
    .card > *{position:relative;z-index:1}
    .label{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.12em;margin-bottom:12px}
    .metric{font-family:ui-monospace, SFMono-Regular, Menlo, monospace;font-size:40px;font-weight:700;letter-spacing:-.06em}
    .small{color:var(--muted);font-size:13px;line-height:1.45;letter-spacing:0}
    .trend{margin-top:14px;font-size:13px;color:var(--acid);font-family:ui-monospace, Menlo, monospace}
    .bad{color:var(--red)} .warn{color:var(--amber)} .blue{color:var(--blue)}

    .score-wrap{display:grid;grid-template-columns:190px 1fr;gap:24px;align-items:center}
    .score{
      width:174px;height:174px;border-radius:50%;
      background:conic-gradient(var(--acid) 0 78%, #252525 78% 100%);
      display:grid;place-items:center;
      position:relative;
      box-shadow:0 0 42px rgba(0,255,156,.12);
    }
    .score:after{content:"";position:absolute;inset:13px;background:var(--panel);border-radius:50%;border:1px solid var(--line)}
    .score div{position:relative;z-index:2;text-align:center}
    .score strong{display:block;font-family:ui-monospace, Menlo, monospace;font-size:42px;letter-spacing:-.08em}
    .score span{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.12em}
    .reading{font-size:25px;line-height:1.18;letter-spacing:-.05em;margin:0 0 14px}

    .bar{height:10px;border-radius:999px;background:#262626;overflow:hidden;margin-top:16px}
    .bar i{display:block;height:100%;background:var(--acid);border-radius:999px}

    .fake-chart{height:185px;display:flex;align-items:end;gap:10px;padding-top:20px}
    .fake-chart i{flex:1;background:linear-gradient(180deg, rgba(0,255,156,.95), rgba(0,255,156,.14));border-radius:10px 10px 0 0;min-height:22px}

    .heatmap{
      height:290px;
      border-radius:20px;
      background:#0d0d0d;
      border:1px solid var(--line);
      position:relative;
      overflow:hidden;
    }
    .heatmap:before{content:"";position:absolute;inset:24px;border:1px dashed #333;border-radius:16px}
    .hot{position:absolute;border-radius:50%;filter:blur(7px);opacity:.82}
    .h1{width:120px;height:120px;background:rgba(0,255,156,.45);top:35px;left:80px}
    .h2{width:90px;height:90px;background:rgba(255,77,77,.46);top:150px;right:90px}
    .h3{width:68px;height:68px;background:rgba(246,200,95,.55);bottom:45px;left:230px}

    .funnel{display:grid;gap:12px;margin-top:8px}
    .step{padding:14px 16px;border:1px solid var(--line);border-radius:16px;background:rgba(255,255,255,.03);display:flex;justify-content:space-between;color:var(--muted);font-size:14px}
    .step strong{color:var(--text);font-family:ui-monospace, Menlo, monospace}

    .alert{display:flex;gap:14px;align-items:flex-start;padding:16px;border:1px solid var(--line);border-radius:18px;background:rgba(255,255,255,.03);margin-top:12px}
    .dot{width:10px;height:10px;border-radius:50%;background:var(--acid);margin-top:5px;box-shadow:0 0 18px var(--acid)}
    .dot.red{background:var(--red);box-shadow:0 0 18px var(--red)}
    .dot.amber{background:var(--amber);box-shadow:0 0 18px var(--amber)}

    .module-title{font-size:24px;margin:0 0 14px;letter-spacing:-.05em}
    .table{width:100%;border-collapse:collapse;font-size:14px;color:var(--muted)}
    .table td{border-bottom:1px solid var(--line);padding:14px 6px}
    .table td:last-child{text-align:right;font-family:ui-monospace, Menlo, monospace;color:var(--text)}

    footer{padding:18px 0 40px;color:#666;font-size:12px;letter-spacing:0}

    .coming-soon-banner {
      position: absolute;
      inset: 0;
      background: rgba(8, 8, 8, 0.85);
      backdrop-filter: blur(4px);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 10;
      border-radius: var(--radius);
    }
    .coming-soon-banner span {
      background: var(--acid);
      color: #000;
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      margin-bottom: 8px;
    }
    .coming-soon-banner p {
      color: var(--text);
      font-size: 14px;
      margin: 0;
    }
    .site-select {
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid var(--line);
      color: var(--text);
      padding: 10px 16px;
      border-radius: 12px;
      font-family: inherit;
      font-size: 14px;
      outline: none;
      cursor: pointer;
    }

    .add-site-btn {
      background: var(--acid);
      color: #000;
      border: none;
      border-radius: 8px;
      padding: 0 12px;
      font-weight: bold;
      cursor: pointer;
      font-family: inherit;
      height: 38px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .modal {
      border: 1px solid var(--line);
      border-radius: 12px;
      background: var(--bg);
      padding: 24px;
      color: var(--text);
    }
    .modal::backdrop {
      background: rgba(0,0,0,0.7);
      backdrop-filter: blur(4px);
    }
    .modal-content { display: flex; flex-direction: column; gap: 16px; min-width: 300px; }
    .modal-content input {
      background: transparent;
      border: 1px solid var(--line);
      padding: 10px;
      border-radius: 6px;
      color: var(--text);
      outline: none;
    }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
    .modal-actions button { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: bold; }
    .btn-cancel { background: transparent; color: var(--text); border: 1px solid var(--line); }
    .btn-submit { background: var(--acid); color: #000; }

    @media(max-width:980px){
      .app{grid-template-columns:1fr}
      aside{position:relative;height:auto}
      .grid-3,.grid-4,.grid-2,.score-wrap{grid-template-columns:1fr}
      h2{font-size:34px}
      header{flex-direction:column;gap:16px}
    }
  </style>
</head>
<body>
  <div class="app">
    <aside>
      <div class="brand">
        <div class="mark">UB</div>
        <div>
          <h1>Unlock Brands</h1>
          <p>Sistema de Presencia Digital Activa</p>
        </div>
      </div>
      <nav>
        <a href="#estado" class="active">Estado de la marca <span>01</span></a>
        <a href="#comportamiento">Comportamiento <span>02</span></a>
        <a href="#friccion">Fricción <span>03</span></a>
        <a href="#visibilidad">Visibilidad <span>04</span></a>
        <a href="#captura">Captura de valor <span>05</span></a>
        <a href="#sistema">Sistema técnico <span>06</span></a>
      </nav>
      <div class="aside-card">
        <b>Lectura activa</b>
        <p>Este dashboard no muestra datos aislados. Traduce tráfico, comportamiento, SEO, campañas y rendimiento técnico en una lectura ejecutiva de la marca.</p>
      </div>
    </aside>

    <main>
      <header>
        <div>
          <div class="eyebrow">Dashboard ejecutivo / cliente</div>
          <h2>Interfaz de lectura de la marca</h2>
          <p class="sub">Mockup conceptual para entregar a clientes de Cetia Media: una plataforma donde el sitio deja de ser una página estática y se convierte en un sistema medible, interpretable y optimizable.</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
          <select id="site-selector" class="site-select">
            @foreach($sites as $site)
              <option value="{{ $site->api_key }}" data-name="{{ $site->name }}">{{ $site->name }}</option>
            @endforeach
            @if($sites->isEmpty())
              <option value="">No hay sitios registrados</option>
            @endif
          </select>
          <button id="btn-open-modal" class="add-site-btn">+</button>
          <div class="client-pill">Cliente: <strong id="client-name">Selecciona un sitio</strong></div>
        </div>
      </header>

      <section id="estado" class="grid grid-3">
        <div class="card" style="grid-column:span 2">
          <div class="label">Estado de la marca</div>
          <div class="score-wrap">
            <div class="score"><div><strong id="health-score-metric">78</strong><span>salud digital</span></div></div>
            <div>
              <p class="reading">La marca está siendo vista, pero todavía no convierte toda esa atención en decisión.</p>
              <p class="small">El tráfico crece, la interacción mejora y el posicionamiento comienza a estabilizarse. La oportunidad está en reducir fricción en formularios y reforzar los CTA de mayor intención.</p>
              <div class="trend" id="health-score-trend">--</div>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="label">Lectura semanal</div>
          <p class="reading" style="font-size:22px" id="week-title">Semana --</p>
          <p class="small">La marca aparece con más fuerza, pero el usuario aún necesita un argumento más claro para avanzar.</p>
          <div class="bar"><i style="width:68%"></i></div>
        </div>
      </section>

      <section class="grid grid-4">
        <div class="card"><div class="label">Tráfico</div><div class="metric" id="metric-traffic">--</div><p class="trend" id="trend-traffic">--</p><p class="small">La atención crece.</p></div>
        <div class="card"><div class="label">Conversión</div><div class="metric" id="metric-conversion">--</div><p class="trend bad" id="trend-conversion">--</p><p class="small">La decisión se enfría.</p></div>
        <div class="card"><div class="label">Retención</div><div class="metric" id="metric-retention">--</div><p class="trend warn" id="trend-retention">--</p><p class="small">El relato retiene parcialmente.</p></div>
        <div class="card"><div class="label">Interacción</div><div class="metric" id="metric-interaction">--</div><p class="trend" id="trend-interaction">--</p><p class="small">El usuario explora más.</p></div>
      </section>

      <section id="comportamiento" class="grid grid-2">
        <div class="card">
          <div class="label">Comportamiento del usuario</div>
          <h3 class="module-title">Zonas de atención</h3>
          <div class="heatmap"><span class="hot h1"></span><span class="hot h2"></span><span class="hot h3"></span></div>
          <p class="small">El usuario concentra atención en el bloque de promesa inicial y abandona parcialmente antes del formulario.</p>
        </div>
        <div class="card">
          <div class="label">Recorrido</div>
          <h3 class="module-title">Del interés a la acción</h3>
          <div class="funnel" id="funnel-container">
            <div class="step">Visita página principal <strong>100%</strong></div>
            <div class="step">Lee oferta central <strong>64%</strong></div>
            <div class="step">Hace clic en CTA <strong>28%</strong></div>
            <div class="step">Inicia formulario <strong>12%</strong></div>
            <div class="step">Envía datos <strong>3.2%</strong></div>
          </div>
          <div class="alert"><span class="dot amber"></span><p class="small"><b>Insight:</b> La caída fuerte sucede entre CTA y formulario. Hay intención, pero el costo percibido de avanzar parece alto.</p></div>
        </div>
      </section>

      <section id="friccion" class="grid grid-3">
        <div class="card">
          <div class="label">Fricción</div>
          <h3 class="module-title">Lugares donde la marca falla</h3>
          <div id="friction-alerts">
             <div class="alert"><span class="dot grey"></span><p class="small">Cargando datos de Express...</p></div>
          </div>
        </div>
        <div class="card" style="grid-column:span 2">
          <div class="label">Rendimiento de intención</div>
          <h3 class="module-title">Páginas con mayor tensión</h3>
          <table class="table" id="pages-table">
            <tr><td>/servicios</td><td>rebote 68%</td></tr>
            <tr><td>/contacto</td><td>abandono 72%</td></tr>
            <tr><td>/blog/guia</td><td>scroll 81%</td></tr>
            <tr><td>/oferta</td><td>clic CTA 31%</td></tr>
          </table>
        </div>
      </section>

      <section id="visibilidad" class="grid grid-2">
        <div class="card">
          <div class="label">Visibilidad</div>
          <h3 class="module-title">Qué entiende Google de la marca</h3>
          <div class="fake-chart">
            <i style="height:35%"></i><i style="height:52%"></i><i style="height:47%"></i><i style="height:74%"></i><i style="height:62%"></i><i style="height:88%"></i><i style="height:79%"></i>
          </div>
          <p class="small">La indexación crece y algunas páginas comienzan a responder preguntas útiles. Falta ampliar FAQ y schema para consultas de intención alta.</p>
        </div>
        <div class="card">
          <div class="label">IA-ready / LLM</div>
          <h3 class="module-title">Preguntas que la marca ya puede responder</h3>
          <table class="table">
            <tr><td>¿Qué ofrece la marca?</td><td class="blue">claro</td></tr>
            <tr><td>¿Por qué elegirla?</td><td class="warn">medio</td></tr>
            <tr><td>¿Cuánto cuesta?</td><td class="bad">débil</td></tr>
            <tr><td>¿Cómo iniciar?</td><td class="blue">claro</td></tr>
          </table>
        </div>
      </section>

      <section id="captura" class="grid grid-3">
        <div class="card" style="grid-column:span 2">
          <div class="label">Captura de valor</div>
          <h3 class="module-title">Dónde la marca captura deseo</h3>
          <div class="fake-chart">
            <i style="height:28%"></i><i style="height:38%"></i><i style="height:46%"></i><i style="height:61%"></i><i style="height:58%"></i><i style="height:77%"></i><i style="height:69%"></i><i style="height:92%"></i>
          </div>
        </div>
        <div class="card">
          <div class="label">Leads</div>
          <div class="metric">184</div>
          <p class="trend">▲ 21.5%</p>
          <p class="small">La captación mejora cuando la promesa se formula como transformación, no como catálogo de servicios.</p>
        </div>
      </section>

      <section id="sistema" class="grid grid-2">
        <div class="card">
          <div class="label">Sistema técnico</div>
          <h3 class="module-title">Estado operativo</h3>
          <table class="table">
            <tr><td>GA4 / Tag Manager</td><td>activo</td></tr>
            <tr><td>Meta Pixel</td><td>activo</td></tr>
            <tr><td>Search Console</td><td>verificado</td></tr>
            <tr><td>Schema.org</td><td>parcial</td></tr>
            <tr><td>PageSpeed móvil</td><td>82/100</td></tr>
          </table>
        </div>
        <div class="card">
          <div class="label">Próxima decisión</div>
          <h3 class="module-title">Acción recomendada</h3>
          <p class="reading" style="font-size:22px">Reducir fricción antes de aumentar inversión publicitaria.</p>
          <p class="small">La marca ya atrae atención. Antes de escalar presupuesto, conviene mejorar formularios, jerarquía de CTA y claridad de promesa en páginas de conversión.</p>
          <div class="alert"><span class="dot"></span><p class="small"><b>Prioridad Unlock:</b> convertir lectura en intervención.</p></div>
        </div>
      </section>

      <footer>
        Mockup conceptual desarrollado para Cetia Media / Unlock Brands. Datos simulados para presentación interna y comercial.
      </footer>
    </main>
  </div>

  <dialog id="site-modal" class="modal">
    <div class="modal-content">
      <h3 style="margin: 0; font-size: 18px;">Añadir Nuevo Sitio</h3>
      <input type="text" id="site-name" placeholder="Nombre del sitio (ej. Revista)" />
      <input type="text" id="site-domain" placeholder="Dominio (ej. revista.com)" />
      
      <div id="modal-success" style="display:none; color: var(--acid); font-size: 12px; background: rgba(220, 255, 122, 0.1); padding: 8px; border-radius: 4px;">
        Sitio creado. API KEY generada: <br><strong id="modal-api-key" style="user-select: all;"></strong>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn-cancel" id="btn-close-modal">Cerrar</button>
        <button type="button" class="btn-submit" id="btn-save-site">Guardar</button>
      </div>
    </div>
  </dialog>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const modal = document.getElementById('site-modal');
      const btnOpen = document.getElementById('btn-open-modal');
      const btnClose = document.getElementById('btn-close-modal');
      const btnSave = document.getElementById('btn-save-site');
      
      btnOpen.addEventListener('click', () => modal.showModal());
      
      btnClose.addEventListener('click', () => {
        modal.close();
        document.getElementById('site-name').value = '';
        document.getElementById('site-domain').value = '';
        document.getElementById('modal-success').style.display = 'none';
        btnSave.style.display = 'block';
      });

      btnSave.addEventListener('click', async () => {
        const name = document.getElementById('site-name').value;
        const domain = document.getElementById('site-domain').value;
        if(!name || !domain) return alert('Llena ambos campos');

        btnSave.innerText = 'Guardando...';
        
        try {
          const res = await fetch('/sites', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, domain })
          });
          if(res.ok) {
            const data = await res.json();
            document.getElementById('modal-api-key').innerText = data.site.api_key;
            document.getElementById('modal-success').style.display = 'block';
            btnSave.style.display = 'none';
            btnSave.innerText = 'Guardar';
            
            // Append to select
            const select = document.getElementById('site-selector');
            const option = document.createElement('option');
            option.value = data.site.api_key;
            option.dataset.name = data.site.name;
            option.innerText = data.site.name;
            select.appendChild(option);
            select.value = option.value;
            select.dispatchEvent(new Event('change'));
          }
        } catch(e) {
          console.error(e);
          alert('Error creando sitio');
          btnSave.innerText = 'Guardar';
        }
      });
    });
  </script>

  @vite(['resources/js/dashboard.ts'])
</body>
</html>