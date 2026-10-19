-- db_structure.sql
-- Estructura de base de datos completa y limpia (súmario de tablas y esquema principal)
-- Sin instrucciones ALTER ni INSERT.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Estructura de tabla para la tabla `events`
--
CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `content` varchar(255) DEFAULT NULL,
  `session_id` varchar(64) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `x_coord` int(11) DEFAULT NULL,
  `y_coord` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `events_site_name_index` (`site_name`),
  INDEX `events_type_index` (`type`),
  INDEX `events_session_id_index` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de tabla para la tabla `users`
--
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('master','editor','viewer') NOT NULL DEFAULT 'viewer',
  `site_name` varchar(255) DEFAULT NULL,
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `stripe_subscription_id` varchar(255) DEFAULT NULL,
  `subscription_status` varchar(50) NOT NULL DEFAULT 'inactive',
  `subscription_ends_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  INDEX `users_stripe_customer_id_index` (`stripe_customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de tabla para la tabla `allowed_domains`
--
CREATE TABLE `allowed_domains` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `allowed_domains_domain_unique` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de tabla para la tabla `system_configs`
--
CREATE TABLE `system_configs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_configs_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `system_configs` (`key`, `value`, `created_at`, `updated_at`)
VALUES ('payments_enabled', 'true', NOW(), NOW());

--
-- Estructura de tabla para la tabla `page_analyses`
--
CREATE TABLE `page_analyses` (
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

COMMIT;
