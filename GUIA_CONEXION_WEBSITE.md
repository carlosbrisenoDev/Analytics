# 🌐 Guía de Integración Técnica: Conexión de Nuevo Website

Esta guía reúne todas las cabeceras HTTP, metaetiquetas HTML, estructuración semántica y el script de analíticas necesario para dar de alta y conectar un nuevo sitio web al **Analytics Dashboard**.

---

## 📋 Índice Rápido
1. [Requisitos Previos en el Dashboard](#1-requisitos-previos-en-el-dashboard)
2. [Cabeceras del Servidor y Seguridad (HTTP Headers)](#2-cabeceras-del-servidor-y-seguridad-http-headers)
3. [Etiquetas HTML para el `<head>` (SEO, Meta & Redes)](#3-etiquetas-html-para-el-head-seo-meta--redes)
4. [Script de Conexión de Analíticas (Client-Side)](#4-script-de-conexión-de-analíticas-client-side)
5. [Marcaje de Eventos en Botones y Formularios](#5-marcaje-de-eventos-en-botones-y-formularios)
6. [Integración en Frameworks (React, Next.js, Vite, Vue)](#6-integración-en-frameworks-react-nextjs-vite-vue)
7. [Checklist de Verificación](#7-checklist-de-verificación)

---

## 1. Requisitos Previos en el Dashboard

Antes de enviar tráfico:
1. Inicia sesión en el Dashboard con rol de **Master / Administrador**.
2. Ve al módulo de **Configuración / Dominios CORS**.
3. Da de alta el dominio del nuevo sitio (ej: `https://mi-nuevo-sitio.com` y `http://localhost:5173` si estás en desarrollo local).

---

## 2. Cabeceras del Servidor y Seguridad (HTTP Headers)

Si tu servidor o hosting utiliza políticas estrictas de seguridad (Content Security Policy), añade el dominio de tu dashboard a la directiva `connect-src`:

### Directiva CSP recomendada:
```http
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; connect-src 'self' https://TU_DOMINIO_DASHBOARD.com;
```

### Cabeceras recomendadas en `.htaccess` (Apache) o Nginx:
```apache
# Habilitar envío de referrer completo para análisis de procedencia
Header set Referrer-Policy "strict-origin-when-cross-origin"

# Seguridad estándar
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
```

---

## 3. Etiquetas HTML para el `<head>` (SEO, Meta & Redes)

Pega esta plantilla base en el `<head>` de tu página principal:

```html
<!DOCTYPE html>
<html lang="es">
<head>
  <!-- 1. Configuración Básica y Viewport -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  <!-- 2. SEO Principal -->
  <title>Nombre de tu Marca | Propuesta de Valor Principal</title>
  <meta name="description" content="Descripción concisa de tu producto o servicio en 150-160 caracteres." />
  <meta name="robots" content="index, follow" />
  <link rel="canonical" href="https://mi-nuevo-sitio.com/" />

  <!-- 3. Open Graph (Facebook, LinkedIn, WhatsApp) -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://mi-nuevo-sitio.com/" />
  <meta property="og:title" content="Nombre de tu Marca | Propuesta de Valor" />
  <meta property="og:description" content="Descripción concisa de tu producto o servicio." />
  <meta property="og:image" content="https://mi-nuevo-sitio.com/assets/og-cover.jpg" />
  <meta property="og:locale" content="es_MX" />

  <!-- 4. Twitter Cards -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Nombre de tu Marca | Propuesta de Valor" />
  <meta name="twitter:description" content="Descripción concisa de tu producto o servicio." />
  <meta name="twitter:image" content="https://mi-nuevo-sitio.com/assets/og-cover.jpg" />

  <!-- 5. Favicon e Iconografía -->
  <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
  <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
  <meta name="theme-color" content="#0f172a" />

  <!-- 6. Schema.org (Datos Estructurados JSON-LD) -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Nombre de tu Marca",
    "url": "https://mi-nuevo-sitio.com",
    "logo": "https://mi-nuevo-sitio.com/assets/logo.png",
    "sameAs": [
      "https://www.instagram.com/tumarca",
      "https://www.linkedin.com/company/tumarca"
    ]
  }
  </script>

  <!-- 7. Script de Rastreo Analytics (Ver Sección 4) -->
</head>
```

---

## 4. Script de Conexión de Analíticas (Client-Side)

Inserta este bloque dentro del `<head>` o justo antes de cerrar el `</body>`.

> **Recuerda:** Cambia `API_URL` por la URL de tu Dashboard y `SITE_ID` por el identificador único del sitio (ej: `mi-nuevo-sitio.com`).

```html
<script>
  (function() {
    // 1. Configuración de sesión y proyecto
    const API_URL = 'https://TU_DOMINIO_DASHBOARD.com/api/track'; // URL de tu servidor Analytics
    const SITE_ID = 'mi-nuevo-sitio.com';                         // Identificador del sitio en el Dashboard

    // 2. Gestión de sesión persistente por usuario
    let ub_session = localStorage.getItem('ub_session');
    if (!ub_session) {
      ub_session = Math.random().toString(36).substring(2, 15) + Date.now().toString(36);
      localStorage.setItem('ub_session', ub_session);
    }
    const ub_region = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
    const ub_startTime = Date.now();

    // 3. Función global de despacho de eventos
    window.trackUnlockEvent = function(eventType, content = window.location.pathname, x = null, y = null) {
      const duration = Math.floor((Date.now() - ub_startTime) / 1000);
      const payloadObj = {
        site_name: SITE_ID,
        type: eventType,
        session_id: ub_session,
        region: ub_region,
        duration: duration,
        content: content
      };

      if (x !== null && x !== undefined && !isNaN(x)) payloadObj.x_coord = Math.round(x);
      if (y !== null && y !== undefined && !isNaN(y)) payloadObj.y_coord = Math.round(y);

      const payload = JSON.stringify(payloadObj);

      // Envío ultraligero
      if (typeof fetch !== 'undefined') {
        fetch(API_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: payload,
          keepalive: true
        }).catch(err => console.warn('[Analytics Error]', err));
      } else if (navigator.sendBeacon) {
        const blob = new Blob([payload], { type: 'application/json' });
        navigator.sendBeacon(API_URL, blob);
      }
    };

    // 4. Rastreo de visita inicial
    if (document.readyState === 'loading') {
      window.addEventListener('DOMContentLoaded', () => trackUnlockEvent('view', window.location.pathname));
    } else {
      trackUnlockEvent('view', window.location.pathname);
    }

    // 5. Heatmap Tracking (Coordenadas de calor de cursor y pantalla táctil)
    let mousePositions = [];
    let currentX = null;
    let currentY = null;
    let hasActivity = false;

    document.addEventListener('mousemove', (e) => {
      currentX = e.pageX;
      currentY = e.pageY;
      hasActivity = true;
    }, { passive: true });

    document.addEventListener('touchmove', (e) => {
      if (e.touches && e.touches[0]) {
        currentX = e.touches[0].pageX;
        currentY = e.touches[0].pageY;
        hasActivity = true;
      }
    }, { passive: true });

    // Guardar muestra cada 500ms si el usuario interactuó
    setInterval(() => {
      if (document.visibilityState !== 'hidden' && hasActivity && currentX !== null && currentY !== null) {
        mousePositions.push({ x: currentX, y: currentY });
        hasActivity = false;
        if (mousePositions.length > 20) mousePositions = mousePositions.slice(-20);
      }
    }, 500);

    // Enviar ráfaga de coordenadas heatmap_trace cada 5 segundos
    setInterval(() => {
      if (document.visibilityState !== 'hidden' && mousePositions.length > 0) {
        const positionsToSend = [...mousePositions];
        mousePositions = [];
        positionsToSend.forEach((pos, index) => {
          setTimeout(() => {
            trackUnlockEvent('click', 'heatmap_trace', pos.x, pos.y);
          }, index * 50);
        });
      }
    }, 5000);

    // 6. Heartbeat al salir o cambiar de pestaña (mide retención real)
    window.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'hidden') {
        trackUnlockEvent('view', window.location.pathname);
      }
    });
  })();
</script>
```

---

## 5. Marcaje de Eventos en Botones y Formularios

Para que el embudo y las tarjetas del Dashboard se alimenten correctamente, añade llamadas a `trackUnlockEvent(tipo, etiqueta)`:

| Tipo (`type`) | Cuándo Usarlo | Impacto en Dashboard |
| :--- | :--- | :--- |
| `view` | Vista de página (se lanza automático). | Tráfico total y paso 1 del embudo. |
| `cta_click` | Clics en botones principales ("Comprar", "Cotizar", "Comenzar"). | Tasa de conversión CTA y paso 3. |
| `click` | Clics secundarios (acordeones, pestañas, filtros). | Interacción y zonas de atención. |
| `form_start` | Cuando el usuario empieza a rellenar un formulario (`onfocus`). | Intención y paso 4 del embudo. |
| `form_submit` | Cuando el usuario completa y envía un formulario de lead. | Total de Leads y conversión final. |
| `external_link` | Clics hacia WhatsApp, redes sociales o enlaces salientes. | Fugas de tráfico y canales externos. |
| `download` | Clics en descargas de PDF, catálogo o brochure. | Interacciones de alto valor. |

### Ejemplos en HTML:

```html
<!-- Botón Llamado a la Acción (CTA) -->
<button onclick="trackUnlockEvent('cta_click', 'btn_hero_comenzar')">
  Comenzar Ahora
</button>

<!-- Formulario con seguimiento de embudo (Inicio y Envío) -->
<form onsubmit="trackUnlockEvent('form_submit', 'form_contacto')">
  <input 
    type="email" 
    placeholder="Tu correo" 
    onfocus="trackUnlockEvent('form_start', 'form_contacto')" 
    required 
  />
  <button type="submit">Enviar Mensaje</button>
</form>

<!-- Enlace a WhatsApp -->
<a 
  href="https://wa.me/521234567890" 
  target="_blank" 
  onclick="trackUnlockEvent('external_link', 'btn_whatsapp_flotante')"
>
  Chatear con un asesor
</a>

<!-- Descarga de Documento / Lead Magnet -->
<a 
  href="/docs/brochure-2026.pdf" 
  download 
  onclick="trackUnlockEvent('download', 'brochure_corporativo_pdf')"
>
  Descargar Brochure
</a>
```

---

## 6. Integración en Frameworks (React, Next.js, Vite, Vue)

### En React / Vite (`src/analytics.ts`):
```typescript
import { useEffect, useRef } from 'react';
import { useLocation } from 'react-router-dom';

const API_URL = import.meta.env.VITE_ANALYTICS_API_URL || 'https://TU_DOMINIO_DASHBOARD.com/api/track';
const SITE_ID = import.meta.env.VITE_ANALYTICS_SITE_ID || 'mi-nuevo-sitio.com';

const ub_startTime = Date.now();
let mousePositions: Array<{ x: number; y: number }> = [];
let currentX: number | null = null;
let currentY: number | null = null;
let hasActivity = false;
let isHeatmapInitialized = false;

export const trackEvent = (
  type: 'view' | 'click' | 'cta_click' | 'form_start' | 'form_submit' | 'external_link' | 'download',
  content: string = window.location.pathname,
  xCoord?: number | null,
  yCoord?: number | null
) => {
  let sessionId = localStorage.getItem('ub_session');
  if (!sessionId) {
    sessionId = Math.random().toString(36).substring(2, 15) + Date.now().toString(36);
    localStorage.setItem('ub_session', sessionId);
  }

  const duration = Math.floor((Date.now() - ub_startTime) / 1000);
  const payloadObj: Record<string, unknown> = {
    site_name: SITE_ID,
    type,
    session_id: sessionId,
    region: Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC',
    duration,
    content
  };

  const resX = xCoord !== undefined && xCoord !== null ? xCoord : (type === 'click' ? currentX : null);
  const resY = yCoord !== undefined && yCoord !== null ? yCoord : (type === 'click' ? currentY : null);
  if (resX !== null && !isNaN(resX)) payloadObj.x_coord = Math.round(resX);
  if (resY !== null && !isNaN(resY)) payloadObj.y_coord = Math.round(resY);

  fetch(API_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payloadObj),
    keepalive: true
  }).catch(() => {});
};

export const initHeatmapTracking = () => {
  if (typeof window === 'undefined' || isHeatmapInitialized) return;
  isHeatmapInitialized = true;

  document.addEventListener('mousemove', (e) => {
    currentX = e.pageX;
    currentY = e.pageY;
    hasActivity = true;
  }, { passive: true });

  document.addEventListener('touchmove', (e) => {
    if (e.touches && e.touches[0]) {
      currentX = e.touches[0].pageX;
      currentY = e.touches[0].pageY;
      hasActivity = true;
    }
  }, { passive: true });

  setInterval(() => {
    if (document.visibilityState !== 'hidden' && hasActivity && currentX !== null && currentY !== null) {
      mousePositions.push({ x: currentX, y: currentY });
      hasActivity = false;
      if (mousePositions.length > 20) mousePositions = mousePositions.slice(-20);
    }
  }, 500);

  setInterval(() => {
    if (document.visibilityState !== 'hidden' && mousePositions.length > 0) {
      const positionsToSend = [...mousePositions];
      mousePositions = [];
      positionsToSend.forEach((pos, idx) => {
        setTimeout(() => {
          trackEvent('click', 'heatmap_trace', pos.x, pos.y);
        }, idx * 50);
      });
    }
  }, 5000);
};

if (typeof window !== 'undefined') {
  initHeatmapTracking();
}

### Hook para cambios de ruta (React Router):
```tsx
export const usePageTracking = () => {
  const location = useLocation();

  useEffect(() => {
    initHeatmapTracking();
    trackEvent('view', location.pathname + location.search);
  }, [location.pathname, location.search]);
};
```
};
```

---

## 7. Checklist de Verificación

- [ ] Dominio registrado en la sección CORS / Sitios del Dashboard.
- [ ] Variables `API_URL` y `SITE_ID` actualizadas con los valores correspondientes.
- [ ] Etiquetas `<title>`, `<meta description>` y Open Graph configuradas.
- [ ] Schema.org JSON-LD presente para la validación de Sistema Técnico.
- [ ] Botones clave marcados con `cta_click` y formularios con `form_start`/`form_submit`.
- [ ] Petición `POST /api/track` visible con estado `200 OK` en la pestaña *Network / Red* de las DevTools del navegador.
