<?php
/**
 * Сниппет insert-php #14263 «footer_bottom» → [ug_footer_bottom]: нижняя полоса
 * подвала (меню footer + копирайт) на бизнес-лендинге 14148 — копия
 * flatsome/template-parts/footer/footer-absolute.php.
 *
 * В eval'е сниппета переменных шаблона Flatsome не было: $align,
 * $flatsome_footer_right_text и $flatsome_absolute_footer_secondary давали
 * по warning на рендер, класс выравнивания был пустым, а правый блок не
 * печатался. Здесь то же без warnings: класс пуст, правого блока нет.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

function ug_snippet_footer_bottom(array $a): void
{
    ?><div class="absolute-footer <?php echo flatsome_option('footer_bottom_text'); ?> medium-text-center ">
  <div class="container clearfix">

    <div class="footer-primary pull-left">
      <?php if ( has_nav_menu( 'footer' ) ) : ?>
        <?php
        wp_nav_menu( array(
          'theme_location' => 'footer',
          'menu_class' => 'links footer-nav uppercase',
          'depth' => 1,
          'fallback_cb' => false,
        ) );
        ?>
      <?php endif; ?>
      <div class="copyright-footer">
        <?php echo do_shortcode( get_theme_mod( 'footer_left_text', 'Copyright [ux_current_year] © <strong>UX Themes</strong>' ) ); ?>
      </div>
      <?php do_action( 'flatsome_absolute_footer_primary' ); ?>
    </div>
  </div>
</div>
<?php
}

ug_snippet_shortcode('ug_footer_bottom', 'ug_snippet_footer_bottom');
