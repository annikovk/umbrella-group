<?php
/**
 * Шапка всех страниц. Вариант 'business' (page-bussnise.php, страница 14148)
 * отличается счётчиками (template-parts/analytics.php) и без CSS
 * assets/css/header.css (inc/layout.php); сама шапка у всех одна —
 * template-parts/header/header-site.php (header-wrapper.php).
 */
$ug_variant = ug_header_variant();

$ug_meta_description = '';

if ( is_post_type_archive( 'akcii' ) ) {
    $ug_meta_description = 'Акции и скидки на юридические, бухгалтерские и аудиторские услуги от Umbrella Group в Новосибирске. Актуальные предложения — выгодные условия на регистрацию, сопровождение бизнеса и другие услуги.';
} elseif ( is_post_type_archive( 'faq' ) ) {
    $ug_meta_description = 'Ответы на частые вопросы клиентов Umbrella Group о юридических, бухгалтерских и аудиторских услугах для бизнеса в Новосибирске. Разберём ваш случай на бесплатной консультации.';
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php flatsome_html_classes(); ?>">
<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="cmsmagazine" content="6436b2df9477c3bdde6dd89690ae98b9"/>
    <?php if ( $ug_meta_description ) : ?>
    <meta name="description" content="<?php echo esc_attr( $ug_meta_description ); ?>"/>
    <?php endif; ?>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="profile" href="http://gmpg.org/xfn/11"/>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>

    <?php wp_head(); ?>

<?php get_template_part('template-parts/analytics', null, ['part' => 'head', 'variant' => $ug_variant]); ?>
</head>

<body <?php body_class(); // Body classes is added from inc/helpers-frontend.php ?>>
<?php get_template_part('template-parts/analytics', null, ['part' => 'body', 'variant' => $ug_variant]); ?>
<?php do_action('flatsome_after_body_open'); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'flatsome'); ?></a>

<div id="wrapper">

    <?php do_action('flatsome_before_header'); ?>

    <header id="header" class="header <?php flatsome_header_classes(); ?>">
        <div class="header-wrapper">
            <?php get_template_part('template-parts/header/header', 'wrapper', ['variant' => $ug_variant]); ?>
        </div><!-- header-wrapper-->
    </header>

    <?php do_action('flatsome_after_header'); ?>

    <main id="main" class="<?php flatsome_main_classes(); ?>">
