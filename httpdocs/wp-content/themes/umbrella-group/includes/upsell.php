<?php
/*
 * Допродажа после заявки: когда клиент отправил любую форму CF7 на странице
 * услуги, вместо плашки «Спасибо за Ваше сообщение» показываем благодарность
 * и несколько других услуг. Разметку подборки печатаем в футер <template>'ом,
 * показывает её assets/js/upsell.js по событию wpcf7mailsent.
 */

// Подборки руками: адрес страницы => адреса услуг, которые ей предлагать.
// Адреса, а не ID: ID в локальной базе и на проде расходятся.
// Своя подпись вместо заголовка страницы: 'адрес' => 'Подпись'.
const UMBRELLA_UPSELL_MAP = array(
    'services/licensing/litsenziya-na-obrazovatelnuyu-deyatelnost' => array(
        'services/licensing/dokumenty-dlya-obrazovatelnoj-organizacii',
        'services/licensing/registracija-tovarnogo-znaka',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/services-le/sostavlenie-dogovorov',
    ),
    'services/licensing/dokumenty-dlya-obrazovatelnoj-organizacii' => array(
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/licensing/registracija-tovarnogo-znaka',
        'services/services-le/sostavlenie-dogovorov',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/licensing/litsenziya-na-meditsinskuyu-deyatelnost' => array(
        'services/licensing/paket-dokumentov-dlya-medicinskoj-organizacii',
        'services/licensing/registracija-tovarnogo-znaka',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/bukhgalterskie-uslugi/vedenie-kadrovogo-ucheta',
    ),
    'services/licensing/paket-dokumentov-dlya-medicinskoj-organizacii' => array(
        'services/licensing/registracija-tovarnogo-znaka',
        'services/licensing/pasport-antiterroristicheskoy-zaschischennosti',
        'services/bukhgalterskie-uslugi/konsultatsii-po-buhgalterskomu-uchetu-i-nalogooblozheniyu-2',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/licensing/litsenziya-mchs' => array(
        'services/services-le/sostavlenie-dogovorov',
        'services/licensing/registracija-tovarnogo-znaka',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
        'services/bukhgalterskie-uslugi/vedenie-kadrovogo-ucheta',
    ),
    'services/licensing/litsenziya-na-obrazovatelnuyu-deyatelnost-onlayn' => array(
        'services/licensing/dokumenty-dlya-obrazovatelnoj-organizacii',
        'services/licensing/registracija-tovarnogo-znaka',
        'services/services-le/sostavlenie-dogovorov',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
    ),
    'services/licensing/zaklyuchenie-roszdravnadzora' => array(
        'services/licensing/paket-dokumentov-dlya-medicinskoj-organizacii',
        'services/licensing/zaklyuchenie-rospotrebnadzora',
        'services/licensing/pasport-antiterroristicheskoy-zaschischennosti',
        'services/licensing/registracija-tovarnogo-znaka',
    ),
    'services/licensing/licenziya-na-tabachnuyu-produkciyu' => array(
        'services/register-elimination/registracija-kommercheskoj-organizacii/registracija-ooo',
        'services/licensing/registracija-tovarnogo-znaka',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/register-elimination/uvedomlenie-rkn',
    ),
    'services/licensing/pasport-bezopasnosti-potencialno-opasnogo-objecta-v-novosibirske' => array(
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy' => 'Абонентское юридическое обслуживание',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/licensing/litsenziya-mchs',
        'services/services-le/predstavitelstvo-v-arbitrazhnom-sude',
    ),
    'services/licensing/litsenziya-na-telemeditsiny' => array(
        'services/licensing/paket-dokumentov-dlya-medicinskoj-organizacii',
        'services/licensing/registracija-tovarnogo-znaka',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
    ),
    'services/licensing/litsenziya-na-dezinfektsiyu-dezinsektciyu-deratizatsiyu' => array(
        'services/register-elimination/registracija-kommercheskoj-organizacii/registracija-ooo',
        'services/licensing/registracija-tovarnogo-znaka',
        'services/services-le/sostavlenie-dogovorov',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
    ),
    'services/licensing/pasport-antiterroristicheskoy-zaschischennosti' => array(
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
        'services/register-elimination/uvedomlenie-rkn',
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/services-le/soprovozhdenie-nalogovoy-proverki',
    ),
    'services/licensing/farmatsevticheskaya-litsenziya' => array(
        'services/register-elimination/registracija-kommercheskoj-organizacii/registracija-ooo',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty/smena-generalnogo-direktora-ooo' => 'Смена директора ООО',
        'services/register-elimination/uvedomlenie-rkn',
        'services/licensing/registracija-tovarnogo-znaka',
    ),
    'services/licensing/licenziya-na-pererabotku-othodov' => array(
        'services/services-le/sostavlenie-dogovorov',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/register-elimination/uvedomlenie-rkn',
        'services/services-le/yuridicheskaya-konsultatsiya',
    ),
    'services/licensing/roznichnaya-torgovlya-alkogolem' => array(
        'services/licensing/registracija-tovarnogo-znaka',
        'services/services-le/sostavlenie-dogovorov',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
    ),
    'services/licensing/predreysoviy-meditsinskiy-osmotr-voditeley' => array(
        'services/licensing/paket-dokumentov-dlya-medicinskoj-organizacii',
        'services/licensing/registracija-tovarnogo-znaka',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/bukhgalterskie-uslugi/vedenie-kadrovogo-ucheta',
    ),
    'services/licensing/registratsiya-elektrolaboratorii' => array(
        'services/register-elimination/registracija-kommercheskoj-organizacii/registracija-ooo',
        'services/services-le/yuridicheskaya-konsultatsiya',
        'services/services-le/sostavlenie-dogovorov',
        'services/licensing/pasport-antiterroristicheskoy-zaschischennosti',
    ),
    'services/licensing/zaklyuchenie-rospotrebnadzora' => array(
        'services/licensing/pereoformlenie-licenzii',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
        'services/register-elimination/uvedomlenie-rkn',
        'services/bukhgalterskie-uslugi/konsultatsii-po-buhgalterskomu-uchetu-i-nalogooblozheniyu-2',
    ),
    'services/licensing/pereoformlenie-licenzii' => array(
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
        'services/services-le/yuridicheskaya-konsultatsiya',
        'services/bukhgalterskie-uslugi/express-proverka-bukhgalterskogo-uchyeta',
        'services/licensing/registracija-tovarnogo-znaka',
    ),
);

function umbrella_upsell_current_paths()
{
    if (!is_page()) {
        return null;
    }
    $path = get_page_uri(get_queried_object_id());
    return UMBRELLA_UPSELL_MAP[$path] ?? null;
}

function umbrella_upsell_pages($paths)
{
    $pages = array();
    foreach ($paths as $key => $value) {
        $path = is_int($key) ? $value : $key;
        $page = get_page_by_path($path);
        if ($page && $page->post_status === 'publish') {
            $pages[] = array(
                'url'   => get_permalink($page),
                'title' => is_int($key) ? get_the_title($page) : $value,
            );
        }
    }
    return $pages;
}

add_action('wp_enqueue_scripts', 'umbrella_upsell_assets');

function umbrella_upsell_assets()
{
    if (!umbrella_upsell_current_paths()) {
        return;
    }
    // CSS — обычной очередью в <head> (не late styles блоков): его место в
    // каскаде то же, что было. Версия — mtime файла вместо ручной «1.2».
    // JS только слушает wpcf7mailsent — defer.
    $css = 'assets/css/blocks/upsell.css';
    $js = 'assets/js/blocks/upsell.js';
    wp_enqueue_style('umbrella-upsell', get_theme_file_uri($css), array(), ug_asset_ver($css));
    wp_enqueue_script('umbrella-upsell', get_theme_file_uri($js), array(), ug_asset_ver($js), array('in_footer' => true, 'strategy' => 'defer'));
}

add_action('wp_footer', 'umbrella_upsell_template');

function umbrella_upsell_template()
{
    $paths = umbrella_upsell_current_paths();
    $pages = $paths ? umbrella_upsell_pages($paths) : array();
    if (!$pages) {
        return;
    }
    ?>
    <template id="umbrella-upsell">
        <div class="umbrella-upsell">
            <div class="umbrella-upsell__check">✓</div>
            <p class="umbrella-upsell__title">Спасибо, заявка принята!</p>
            <p class="umbrella-upsell__text">Перезвоним в ближайшее рабочее время: с 09:00 до 18:00 по Новосибирску.</p>
            <p class="umbrella-upsell__subtitle">Вам может пригодиться</p>
            <ul class="umbrella-upsell__list">
                <?php foreach ($pages as $page) : ?>
                    <li>
                        <a class="umbrella-upsell__item" href="<?= esc_url($page['url']) ?>">
                            <span class="umbrella-upsell__name"><?= esc_html($page['title']) ?></span>
                            <span class="umbrella-upsell__arrow">→</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </template>
    <?php
}
