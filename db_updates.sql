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

-- 3. Alteraciones sobre la tabla `users` para incorporar los parámetros y estatus de suscripción recurrente
ALTER TABLE `users`
  ADD COLUMN `stripe_customer_id` varchar(255) DEFAULT NULL AFTER `site_name`,
  ADD COLUMN `stripe_subscription_id` varchar(255) DEFAULT NULL AFTER `stripe_customer_id`,
  ADD COLUMN `subscription_status` varchar(50) NOT NULL DEFAULT 'inactive' AFTER `stripe_subscription_id`,
  ADD COLUMN `subscription_ends_at` timestamp NULL DEFAULT NULL AFTER `subscription_status`,
  ADD INDEX `users_stripe_customer_id_index` (`stripe_customer_id`);

COMMIT;
