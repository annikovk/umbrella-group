<?php
/**
 * wp-config.php локального стенда. Монтируется в контейнер поверх
 * httpdocs/wp-config.php — файл зеркала при этом не меняется, зеркало остаётся
 * побайтовой копией сервера.
 *
 * Отличия от прод-конфига ровно четыре:
 *   1. Реквизиты БД — контейнерные, из переменных compose.
 *   2. WP_HOME/WP_SITEURL перебивают https://taxlab.ru из базы, иначе локальный
 *      сайт редиректит на прод.
 *   3. Не подключается protect918/code/include.php (антибот antibot.cloud) — на
 *      стенде он не нужен.
 *   4. WP_CACHE=false, чтобы не включался advanced-cache.php от WP Rocket.
 *
 * Всё остальное (префикс таблиц, DB_CHARSET, DISALLOW_FILE_*) — как на проде.
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/**
 * Переменная окружения с дефолтом: пустую строку считаем отсутствием значения.
 */
function taxlab_local_env( $name, $default = '' ) {
	$value = getenv( $name );

	return ( false === $value || '' === $value ) ? $default : $value;
}

// ** База ** //
define( 'DB_NAME', taxlab_local_env( 'WORDPRESS_DB_NAME', 'wp_taxlab' ) );
define( 'DB_USER', taxlab_local_env( 'WORDPRESS_DB_USER', 'root' ) );
define( 'DB_PASSWORD', taxlab_local_env( 'WORDPRESS_DB_PASSWORD', 'local' ) );
define( 'DB_HOST', taxlab_local_env( 'WORDPRESS_DB_HOST', 'db:3306' ) );
// На проде база в utf8mb3, а DB_CHARSET='utf8' — повторяем один в один, иначе
// кириллица в дампе прочитается не так, как её пишет живой сайт.
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$table_prefix = 'r17Y1_';

// ** Адрес стенда ** //
$taxlab_local_url = taxlab_local_env( 'LOCAL_SITE_URL', 'http://localhost:8088' );
define( 'WP_HOME', $taxlab_local_url );
define( 'WP_SITEURL', $taxlab_local_url );

// ** Поведение стенда ** //
define( 'WP_CACHE', false );              // без advanced-cache.php (WP Rocket)
define( 'DISABLE_WP_CRON', true );        // не гонять фоновые задачи прода
define( 'WP_HTTP_BLOCK_EXTERNAL', true ); // не стучаться наружу — иначе админка висит
define( 'WP_ACCESSIBLE_HOSTS', 'localhost,127.0.0.1,db' );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_ENVIRONMENT_TYPE', 'local' );
define( 'FS_METHOD', 'direct' );
define( 'CONCATENATE_SCRIPTS', false );   // на проде true, локально мешает отладке

// Как на проде: править темы и плагины можно только файлами.
define( 'DISALLOW_FILE_MODS', true );
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_ALLOW_MULTISITE', true );

$taxlab_local_debug = '1' === taxlab_local_env( 'WP_DEBUG', '0' );
define( 'WP_DEBUG', $taxlab_local_debug );
define( 'SCRIPT_DEBUG', $taxlab_local_debug );
define( 'WP_DEBUG_DISPLAY', false );      // ошибки в лог контейнера, не в вёрстку
define( 'WP_DEBUG_LOG', false );          // и уж точно не файлом в зеркало

/**
 * Соли. Одинаковые у всех и заведомо не секретные: это одноразовый стенд,
 * прод-соли сюда не переносим (репозиторий публичный).
 */
foreach ( array( 'AUTH', 'SECURE_AUTH', 'LOGGED_IN', 'NONCE' ) as $taxlab_local_key ) {
	define( $taxlab_local_key . '_KEY', 'taxlab-local-' . $taxlab_local_key . '-key' );
	define( $taxlab_local_key . '_SALT', 'taxlab-local-' . $taxlab_local_key . '-salt' );
}
unset( $taxlab_local_key );

require_once ABSPATH . 'wp-settings.php';
