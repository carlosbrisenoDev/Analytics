-- db_updates.sql
-- Archivo de actualización incremental (sentencias ALTER y CREATE)
-- Diseñado para ejecutarse sobre una base de datos en producción o existente para migrarla a la versión actual sin pérdida de datos ni inserts destructivos.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. Creación de la tabla `allowed_domains` (si no existía en la versión anterior)
CREATE TABLE IF NOT EXISTS `allowed_domains` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `allowed_domains_domain_unique` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Creación de la tabla `system_configs` para la gestión de precios, monedas y llaves de la plataforma de cobros
CREATE TABLE IF NOT EXISTS `system_configs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_configs_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2.1. Incorporación de coordenadas en `events` para mapas de calor (Heatmaps)
-- Ejecutar en caso de que la tabla `events` aún no cuente con las columnas x_coord y y_coord
ALTER TABLE `events`
  ADD COLUMN IF NOT EXISTS `x_coord` int(11) DEFAULT NULL AFTER `duration`,
  ADD COLUMN IF NOT EXISTS `y_coord` int(11) DEFAULT NULL AFTER `x_coord`;

-- 3. Alteraciones sobre la tabla `users` para incorporar los parámetros y estatus de suscripción recurrente
ALTER TABLE `users`
  ADD COLUMN `stripe_customer_id` varchar(255) DEFAULT NULL AFTER `site_name`,
  ADD COLUMN `stripe_subscription_id` varchar(255) DEFAULT NULL AFTER `stripe_customer_id`,
  ADD COLUMN `subscription_status` varchar(50) NOT NULL DEFAULT 'inactive' AFTER `stripe_subscription_id`,
  ADD COLUMN `subscription_ends_at` timestamp NULL DEFAULT NULL AFTER `subscription_status`,
  ADD INDEX `users_stripe_customer_id_index` (`stripe_customer_id`);

-- 4. Creación de la tabla `page_analyses` para auditoría e inspección técnica de integraciones
CREATE TABLE IF NOT EXISTS `page_analyses` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL,
  `url` varchar(2048) NOT NULL,
  `path` varchar(255) NOT NULL,
  `http_status` smallint UNSIGNED DEFAULT NULL,
  `has_ga4` tinyint(1) NOT NULL DEFAULT 0,
  `has_gtm` tinyint(1) NOT NULL DEFAULT 0,
  `has_meta_pixel` tinyint(1) NOT NULL DEFAULT 0,
  `has_search_console` tinyint(1) NOT NULL DEFAULT 0,
  `has_schema_org` tinyint(1) NOT NULL DEFAULT 0,
  `schema_org_types` text DEFAULT NULL,
  `detection_details` json DEFAULT NULL,
  `fetch_error` varchar(500) DEFAULT NULL,
  `analyzed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `page_analyses_site_name_index` (`site_name`),
  INDEX `page_analyses_analyzed_at_index` (`analyzed_at`),
  UNIQUE KEY `page_analyses_site_path_unique` (`site_name`, `path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Inicialización de la configuración del switch de pagos en `system_configs`
INSERT IGNORE INTO `system_configs` (`key`, `value`, `created_at`, `updated_at`)
VALUES ('payments_enabled', 'true', NOW(), NOW());

COMMIT;
