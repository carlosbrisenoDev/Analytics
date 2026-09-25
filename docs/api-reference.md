# Referencia de la API y Guía de Integración para Nuevos Proyectos

El ecosistema de **Unlock Brands Analytics (Cetia Media)** está diseñado con una arquitectura de máxima velocidad y simplicidad. Permite conectar cualquier aplicación web, e-commerce o landing page con un script ligero (cero dependencias externas) y alimentar las métricas en tiempo real del Dashboard Ejecutivo.

---

## 🚀 1. Ingestión Principal de Eventos (`POST /api/track`)

Este es el endpoint central para capturar tráfico, interacciones, clics en botones principales (CTA) y conversiones reales (Leads).

### Endpoint y Headers
- **URL:** `POST https://TU_DOMINIO.com/api/track` *(También disponible en el alias `/api/activity`)*
- **Headers:** `Content-Type: application/json`
- **Rate Limit:** Máximo 60 peticiones por minuto por `session_id` (Respuestas `429 Too Many Requests` tras superar el límite).

### Estructura del Payload (JSON)
```json
{
  "site_name": "mi-nuevo-proyecto.com",
  "type": "view",
  "session_id": "8x7a6b9c1d2e",
  "region": "America/Mexico_City",
  "duration": 0,
  "content": "/landing-ventas"
}
```

### Tipos de Eventos Soportados (`type`)
| Tipo (`type`) | Uso Recomendado en Nuevos Proyectos | Impacto en el Dashboard |
| :--- | :--- | :--- |
| **`view`** | Se dispara automáticamente al abrir la página. | Alimenta la card de **Tráfico (Vistas)** y el embudo inicial (100%). |
| **`click`** | Clics exploratorios, tabs o acordeones de FAQ. | Alimenta la card de **Interacción** y **Zonas de Atención**. |
| **`cta_click`** | Clic en botones estratégicos (*"Comprar"*, *"Agendar"*). | Alimenta la **Tasa de Conversión (CTA)** y paso 3 del embudo. |
| **`form_start`** | Al hacer focus en el primer campo de un formulario. | Mide la intención real en el paso 4 del embudo. |
| **`form_submit`** | Al enviar exitosamente el formulario o checkout. | Alimenta la card principal de **Leads** y conversión final. |
| **`external_link`** | Clic en enlaces a WhatsApp o socios comerciales. | Mide fugas de tráfico hacia plataformas externas. |
| **`reload`** | Recarga activa de la página por el usuario. | Diagnostica problemas de legibilidad o fallas en botones. |
| **`download`** | Descargas de brochures PDF o recursos lead magnets. | Contabiliza interacciones de alto valor comercial. |

---

## 💻 2. Script Oficial de Integración Rápida (Client-Side)

Pega el siguiente bloque en el `<head>` o antes del cierre del `<body>` en cualquier nuevo sitio HTML, WordPress, Vite, React o Next.js:

```html
<script>
  (function() {
    // 1. Inicializar sesión persistente
    let ub_session = localStorage.getItem('ub_session');
    if (!ub_session) {
      ub_session = Math.random().toString(36).substring(2, 15) + Date.now().toString(36);
      localStorage.setItem('ub_session', ub_session);
    }
    const ub_region = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
    const ub_startTime = Date.now();
    const API_URL = 'https://TU_DOMINIO_DASHBOARD.com/api/track'; // Reemplazar con URL de tu dashboard
    const SITE_ID = 'mi-sitio-cliente.com'; // <- CAMBIAR POR EL IDENTIFICADOR DEL PROYECTO

    // 2. Motor universal de despacho
    window.trackUnlockEvent = function(eventType, content = window.location.pathname) {
      const duration = Math.floor((Date.now() - ub_startTime) / 1000);
      const payload = JSON.stringify({
        site_name: SITE_ID,
        type: eventType,
        session_id: ub_session,
        region: ub_region,
        duration: duration,
        content: content
      });

      if (navigator.sendBeacon) {
        const blob = new Blob([payload], { type: 'application/json' });
        navigator.sendBeacon(API_URL, blob);
      } else {
        fetch(API_URL, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: payload })
          .catch(e => console.error('UB Error:', e));
      }
    };

    // 3. Rastreo de visita inicial
    window.addEventListener('DOMContentLoaded', () => trackUnlockEvent('view', window.location.pathname));

    // 4. Heartbeat de Retención Real (al abandonar o cambiar de pestaña)
    window.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'hidden') {
        trackUnlockEvent('view', window.location.pathname); // Actualiza la duración final en BD
      }
    });
  })();
</script>
```

### Ejemplos de Marcaje HTML en Botones y Formularios:
```html
<!-- Clic en Llamado a la Acción (CTA) -->
<button onclick="trackUnlockEvent('cta_click', 'boton_header_comenzar')">Comenzar Ahora</button>

<!-- Rastreo de Embudo en Formulario -->
<form onsubmit="trackUnlockEvent('form_submit', 'formulario_contacto')">
  <input type="email" placeholder="Tu correo" onfocus="trackUnlockEvent('form_start', 'formulario_contacto')" required>
  <button type="submit">Enviar Datos</button>
</form>

<!-- Clic a WhatsApp / Externo -->
<a href="https://wa.me/521234567890" onclick="trackUnlockEvent('external_link', 'boton_whatsapp')">Chatear por WhatsApp</a>
```

---

## 🛠️ 3. Especificación de Endpoints Complementarios (Módulos Avanzados)

Para implementar una cobertura al 100% de los apartados técnicos y de inteligencia artificial del dashboard en nuevos proyectos, se especifica el protocolo de datos para los endpoints especializados (implementables vía webhooks de CI/CD o crawlers programados):

### A. Diagnóstico de Salud y Sistema Técnico (`POST /api/technical-status`)
Diseñado para consumirse desde herramientas de CI/CD (GitHub Actions), scripts de Google Lighthouse o comprobaciones periódicas del servidor para alimentar la card de **Sistema Técnico**:
- **URL:** `POST /api/technical-status`
- **Payload Recomendado:**
```json
{
  "site_name": "mi-sitio-cliente.com",
  "ga4_active": true,
  "meta_pixel_active": true,
  "search_console_verified": true,
  "schema_org_status": "completo",
  "pagespeed_mobile": 94
}
```

### B. Evaluación IA-Ready / LLM (`POST /api/ia-ready`)
Diseñado para integrarse con evaluadores semánticos que prueban el posicionamiento y claridad conceptual del sitio frente a LLMs (ChatGPT, Gemini, Claude) y buscadores semánticos:
- **URL:** `POST /api/ia-ready`
- **Payload Recomendado:**
```json
{
  "site_name": "mi-sitio-cliente.com",
  "questions_evaluation": [
    { "question": "¿Qué ofrece la marca?", "status": "claro", "confidence": 0.95 },
    { "question": "¿Por qué elegirla?", "status": "claro", "confidence": 0.88 },
    { "question": "¿Cuánto cuesta?", "status": "medio", "confidence": 0.65 },
    { "question": "¿Cómo iniciar?", "status": "claro", "confidence": 0.92 }
  ]
}
```

### C. Consulta Externa de Métricas / API Headless (`GET /api/metrics/{site_name}`)
Permite que un sitio web de cliente o intranet consulte su propio **Score de Salud Digital**, total de **Leads** y métricas rápidas para imprimirlas localmente sin ingresar a la vista Blade:
- **URL:** `GET /api/metrics/{site_name}`
- **Respuesta Esperada:**
```json
{
  "site_name": "mi-sitio-cliente.com",
  "health_score": 85,
  "reading": "La marca muestra un desempeño sólido convirtiendo atención en decisión.",
  "kpis": {
    "views": 4210,
    "clicks": 1820,
    "conversion_rate": "5.4%",
    "leads_total": 215,
    "retention_average": "02:14"
  },
  "top_clicked_zone": "boton_header_comenzar"
}
```
> [!NOTE]
> Todos los dominios desde los cuales se envíen peticiones desde navegadores web (cliente) deben estar previamente dados de alta desde el módulo de **Seguridad (CORS)** del usuario **Master** dentro de la interfaz del Dashboard.
