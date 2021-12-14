-- application_remote_credentials: table
CREATE TABLE `application_remote_credentials` (
                                                  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                                  `application_id` bigint unsigned NOT NULL,
                                                  `os` tinyint NOT NULL COMMENT '1 => google, 2 => ios',
                                                  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                  `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                  `created_at` timestamp NULL DEFAULT NULL,
                                                  `updated_at` timestamp NULL DEFAULT NULL,
                                                  PRIMARY KEY (`id`),
                                                  UNIQUE KEY `uidx_application_id_os` (`application_id`,`os`),
                                                  KEY `idx_username_password` (`username`,`password`),
                                                  CONSTRAINT `fk_arc_application_id` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- No native definition for element: idx_username_password (index)

-- applications: table
CREATE TABLE `applications` (
                                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `hook_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `created_at` timestamp NULL DEFAULT NULL,
                                `updated_at` timestamp NULL DEFAULT NULL,
                                PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- devices: table
CREATE TABLE `devices` (
                           `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                           `unique_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `os` tinyint NOT NULL COMMENT '1 => google, 2 => ios',
                           `created_at` timestamp NULL DEFAULT NULL,
                           `updated_at` timestamp NULL DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           UNIQUE KEY `uidx_unique_id` (`unique_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- failed_jobs: table
CREATE TABLE `failed_jobs` (
                               `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                               `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
                               `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
                               `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                               `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                               `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                               PRIMARY KEY (`id`),
                               UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- migrations: table
CREATE TABLE `migrations` (
                              `id` int unsigned NOT NULL AUTO_INCREMENT,
                              `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `batch` int NOT NULL,
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- personal_access_tokens: table
CREATE TABLE `personal_access_tokens` (
                                          `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                          `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                          `tokenable_id` bigint unsigned NOT NULL,
                                          `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                          `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
                                          `abilities` text COLLATE utf8mb4_unicode_ci,
                                          `last_used_at` timestamp NULL DEFAULT NULL,
                                          `created_at` timestamp NULL DEFAULT NULL,
                                          `updated_at` timestamp NULL DEFAULT NULL,
                                          PRIMARY KEY (`id`),
                                          UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
                                          KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- No native definition for element: personal_access_tokens_tokenable_type_tokenable_id_index (index)

-- reports: table
CREATE TABLE `reports` (
                           `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                           `application_id` bigint unsigned NOT NULL,
                           `date` date NOT NULL,
                           `event` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `os` tinyint NOT NULL COMMENT '1 => google, 2 => ios',
                           `count` int NOT NULL DEFAULT '0',
                           `created_at` timestamp NULL DEFAULT NULL,
                           `updated_at` timestamp NULL DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           UNIQUE KEY `uidx_application_id_date_os_event` (`application_id`,`date`,`os`,`event`),
                           CONSTRAINT `fk_r_application_id` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- subscriptions: table
CREATE TABLE `subscriptions` (
                                 `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                 `device_id` bigint unsigned NOT NULL,
                                 `application_id` bigint unsigned NOT NULL,
                                 `worker_group` smallint NOT NULL,
                                 `receipt` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                                 `status` tinyint NOT NULL,
                                 `expired_at` datetime DEFAULT NULL,
                                 `created_at` timestamp NULL DEFAULT NULL,
                                 `updated_at` timestamp NULL DEFAULT NULL,
                                 PRIMARY KEY (`id`),
                                 UNIQUE KEY `uidx_s_device_id_application_id` (`device_id`,`application_id`),
                                 KEY `idx_status_expired_at` (`status`,`expired_at`),
                                 KEY `fk_s_application_id` (`application_id`),
                                 CONSTRAINT `fk_s_application_id` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
                                 CONSTRAINT `fk_s_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- No native definition for element: fk_s_application_id (index)

-- No native definition for element: idx_status_expired_at (index)