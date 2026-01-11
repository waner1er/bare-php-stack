<?php

/**
 * Définition des chemins principaux de l'application
 */

// Racine du projet
define('ROOT_PATH', dirname(__DIR__));

// Fichiers de configuration
define('ENV_FILE', ROOT_PATH . '/.env');

// Dossiers principaux
define('SRC_PATH', ROOT_PATH . '/src');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VENDOR_PATH', ROOT_PATH . '/vendor');

// Dossiers de stockage
define('CACHE_PATH', STORAGE_PATH . '/cache');
define('LOGS_PATH', STORAGE_PATH . '/logs');

// Dossiers src
define('DOMAIN_PATH', SRC_PATH . '/Domain');
define('APPLICATION_PATH', SRC_PATH . '/Application');
define('INFRASTRUCTURE_PATH', SRC_PATH . '/Infrastructure');
define('INTERFACE_PATH', SRC_PATH . '/Interface');

// Chemins spécifiques
define('VIEW_PATH', INTERFACE_PATH . '/View');
define('CONTROLLER_PATH', INTERFACE_PATH . '/Controller');
// Base de données et migrations
define('MIGRATIONS_PATH', ROOT_PATH . '/migrations');
define('SEEDERS_PATH', MIGRATIONS_PATH . '/seeders');
