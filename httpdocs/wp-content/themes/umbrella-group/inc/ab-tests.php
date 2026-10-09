<?php
/**
 * A/B-теги <umbrella_ab_testN_variantM> в контенте.
 *
 * Перенесено из functions.php:243-250 дословно (WS-0). WS-7 разворачивает теги
 * в контенте и удаляет этот файл.
 */

// Hook : to get content with s3elected AB tests variants only
require get_theme_file_path() . '/umbrella_filter_ab_tests.php';


add_action('init', 'umbrella_get_ab_test_tags');

add_filter('the_content', 'umbrella_filter_ab_tests');
add_filter('wp_footer', 'umbrella_filter_ab_tests');
