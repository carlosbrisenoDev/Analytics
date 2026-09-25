# Documentación del Sistema de Analíticas (Unlock Brands)

Bienvenido a la documentación oficial del sistema de analíticas simplificado. Esta documentación explica cómo funciona la ingestión de datos, la estructura de la base de datos y la arquitectura técnica general del sistema.

## Estructura de la Documentación

La documentación está dividida en las siguientes secciones:

- [1. Arquitectura y Flujo de Datos](architecture.md)
  *Describe la arquitectura general y el ciclo de vida de una petición de evento.*
- [2. Referencia de la API](api-reference.md)
  *Detalles del endpoint público de ingestión.*
- [3. Estructura de Base de Datos](database.md)
  *Esquema y relaciones de la base de datos MySQL.*
- [4. Autenticación y Roles](authentication.md)
  *Detalle sobre los roles de usuario y sus permisos de acceso y borrado.*

## Acerca del Sistema

Este sistema está diseñado con el principio de **máxima simplicidad y velocidad** para el registro de eventos web.

Se deshizo de la arquitectura compleja ("mixta") y la complejidad de múltiples inquilinos con API Keys en favor de un enfoque ultra-plano, ideal para soportar alto volumen de tráfico y simplificar la integración por parte de los clientes front-end, delegando la gestión de permisos al panel administrativo.
