# Unlock Brands Analytics Dashboard

Un sistema de analíticas simplificado, robusto y ultra rápido construido sobre Laravel. Diseñado para recibir grandes volúmenes de eventos sin sacrificar el rendimiento, eliminando la necesidad de tokens o autenticación en el origen de los datos.

## 📚 Documentación Oficial

Toda la documentación técnica y arquitectónica del sistema ha sido reescrita y estructurada. Por favor revisa la carpeta `docs/` para entender el funcionamiento interno.

- [Inicio / Índice de Documentación](docs/README.md)
- [Arquitectura y Flujo de Datos](docs/architecture.md)
- [Referencia de la API (Ingestión)](docs/api-reference.md)
- [Estructura de la Base de Datos](docs/database.md)
- [Autenticación y Roles (Master, Editor, Viewer)](docs/authentication.md)

## 🚀 Despliegue Rápido (Quickstart)

1. Sube los archivos al servidor.
2. Configura el `.env` (asegúrate de que `DB_CONNECTION=mysql`).
3. Importa el archivo `db_structure.sql` (en la raíz) directamente en tu gestor de base de datos (ej. phpMyAdmin).
4. Inicia sesión en `/login` usando:
   - **Email:** `admin@admin.com`
   - **Password:** `password`

## Licencia

Propiedad de Unlock Brands.
