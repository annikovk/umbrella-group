<?php
/**
 * Plugin Name: TaxLab local dev
 * Description: Делает прод-копию пригодной для локального просмотра: гасит плагины, которые лезут наружу или пишут в зеркало, и переписывает прод-домен в выводе.
 * Version: 1.0
 *
 * Лежит вне httpdocs (docker/overlay/mu-plugins) и монтируется в контейнер, так
 * что зеркало остаётся чистой копией сервера, а tools/sync-from-server.sh с
 * --delete этот файл не снесёт.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Домен прода, который встречается в постах и настройках абсолютными ссылками. */
const TAXLAB_LOCAL_PROD_HOST = 'taxlab.ru';

/**
 * Плагины, которые на стенде только мешают. active_plugins в базе не меняем —
 * фильтруем на чтении, поэтому база остаётся точной копией прода.
 *
 * Правится руками: если нужно посмотреть, как ведёт себя, например, WP Rocket,
 * просто закомментируй строку и перезагрузи страницу.
 */
const TAXLAB_LOCAL_DISABLED_PLUGINS = array(
	'wp-rocket/wp-rocket.php',                                       // кэш и минификация: пишет в зеркало, прячет реальную вёрстку
	'webp-express/webp-express.php',                                 // подменяет картинки через .htaccess, которого здесь нет
	'wp-mail-smtp/wp_mail_smtp.php',                                 // почта наружу
	'duplicator/duplicator.php',                                     // бэкапы всего сайта
	'yandex-metrica/yandex-metrica.php',                             // внешний счётчик
	'utm-tracker-for-contact-form-7/utm-tracker-for-contact-form-7.php', // трекинг
);

add_filter(
	'option_active_plugins',
	static function ( $plugins ) {
		if ( ! is_array( $plugins ) ) {
			return $plugins;
		}

		return array_values( array_diff( $plugins, TAXLAB_LOCAL_DISABLED_PLUGINS ) );
	}
);

// Ни одного письма со стенда — в базе живые адреса из заявок.
add_filter( 'pre_wp_mail', '__return_true', 1 );

/**
 * В контенте постов ссылки, картинки и фоны прописаны абсолютно
 * (https://taxlab.ru/wp-content/uploads/...). Без подмены половина страницы
 * тянется с прода, а клик уводит на живой сайт. Меняем на выходе — в базу не
 * лезем, чтобы она осталась копией прода и её можно было перезалить дампом.
 */
add_action(
	'template_redirect',
	static function () {
		if ( is_admin() ) {
			return;
		}

		ob_start( 'taxlab_local_rewrite_prod_host' );
	},
	0
);

/**
 * @param string $buffer Ответ страницы.
 * @return string
 */
function taxlab_local_rewrite_prod_host( $buffer ) {
	$local = untrailingslashit( home_url() );          // http://localhost:8088
	$host  = TAXLAB_LOCAL_PROD_HOST;

	// Схему у локального адреса иногда надо убрать: в разметке попадается
	// протокол-относительный вид //taxlab.ru/...
	$local_schemeless = (string) preg_replace( '#^https?:#', '', $local );

	$replacements = array(
		'https://' . $host => $local,
		'http://' . $host  => $local,
		'//' . $host       => $local_schemeless,
	);

	// Те же варианты в JSON внутри страницы, где слэши экранированы:
	// "https:\/\/taxlab.ru\/..." (инлайн-конфиги ACF, CF7, Flatsome).
	foreach ( $replacements as $from => $to ) {
		$replacements[ str_replace( '/', '\/', $from ) ] = str_replace( '/', '\/', $to );
	}

	return str_replace( array_keys( $replacements ), array_values( $replacements ), $buffer );
}
