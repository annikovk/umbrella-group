<?php
/**
 * The template for displaying the footer.
 *
 * Override вендорного flatsome/footer.php (Flatsome 3.13.4): тот правлен на
 * сервере руками и не в git — кнопки Telegram/Max и их <style> слетели бы
 * при обновлении Flatsome. Здесь — вендорный вид; кнопки выводит
 * inc/layout.php на хуке flatsome_footer, CSS — в assets/css/main.css.
 *
 * @package flatsome
 */

global $flatsome_opt;
?>

</main>

<footer id="footer" class="footer-wrapper">

	<?php do_action('flatsome_footer'); ?>

</footer>

</div>

<?php wp_footer(); ?>

</body>
</html>
