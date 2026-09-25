# Arquitectura y Flujo de Datos

El sistema sigue una arquitectura cliente-servidor ultra simple, sin middleware de autenticación en la capa de ingestión.

## Diagrama de Flujo (Ingestión y Consumo)

A continuación, un diagrama que explica cómo un sitio de terceros se comunica con nuestro Dashboard, y cómo los usuarios consumen la información.

```mermaid
sequenceDiagram
    participant JS as Script de Terceros (Browser)
    participant Nginx as Servidor (Rate Limiter IP)
    participant Laravel as Controlador (Ingestion)
    participant DB as MySQL (Events)
    participant Admin as Usuario del Dashboard (Master/Editor/Viewer)

    %% Flujo de Ingestión
    JS->>Nginx: POST /api/track { site_name, type }
    Note right of JS: La petición no lleva API Key ni Autenticación.
    
    Nginx->>Laravel: Reenvía Petición
    Laravel->>Laravel: Valida Regla Anti-DDoS (Max 60 req/min por IP)
    
    alt Límite Excedido
        Laravel-->>JS: 429 Too Many Requests
    else IP Permitida
        Laravel->>DB: INSERT INTO events
        Laravel-->>JS: 200 OK
    end

    %% Flujo de Dashboard
    Admin->>Laravel: GET /dashboard (Cookie Auth)
    Laravel->>DB: SELECT * FROM events WHERE site_name = ?
    DB-->>Laravel: Retorna eventos
    Laravel-->>Admin: Renderiza Interfaz HTML
```

## Arquitectura Lógica

```mermaid
graph TD
    subgraph "Clientes Externos"
        A[Sitio Cliente A] -->|HTTP POST| E
        B[Sitio Cliente B] -->|HTTP POST| E
        C[Sitio Cliente C] -->|HTTP POST| E
    end

    subgraph "API / Ingestión"
        E[Endpoint: /api/track] --> F{Limitador por IP}
        F -->|Bloquea| G[Drop]
        F -->|Permite| H[App\Models\Event]
    end

    subgraph "Almacenamiento"
        H --> I[(Base de Datos MySQL)]
    end

    subgraph "Dashboard (UI Privada)"
        I --> J[App\Models\Event]
        K[App\Models\User] --> J
        J --> L[Blade Views]
        L --> M[Usuario Master]
        L --> N[Usuario Viewer]
    end
```

## Anti-DDoS y Limitación de Tasa (Rate Limiting)

Para evitar que se llene la base de datos de basura (ya que el endpoint es público), implementamos una estrategia de **Limitación por IP**.
Si una misma IP de usuario (proveniente del navegador del cliente, o inyectada por el servidor del cliente) envía más de **60 eventos en 1 minuto**, el servidor de analíticas denegará las peticiones subsiguientes hasta que pase el período de gracia.
