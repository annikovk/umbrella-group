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
// Общая подборка для страниц ведения бухучёта ООО, ИП, НКО и АО.
const UMBRELLA_UPSELL_BOOKKEEPING = array(
    'services/services-le/ocenka-nalogovyh-riskov',
    'services/services-le/strukturirovanie-biznesa',
    'services/licensing' => 'Оформление лицензий',
    'services/register-elimination' => 'Корпоративные услуги',
);

// Общая подборка для «Банкротства» и его страниц для кредиторов и должников.
const UMBRELLA_UPSELL_BANKRUPTCY = array(
    'services/services-le/vzyskanie-dolgov',
    'services/services-le/predstavitelstvo-v-arbitrazhnom-sude',
    'services/register-elimination/likvidatsiya-ooo',
    'services/bukhgalterskie-uslugi/vosstanovleniya-ucheta',
);

const UMBRELLA_UPSELL_MAP = array(
    'services/licensing/litsenziya-na-obrazovatelnuyu-deyatelnost' => array(
        'services/licensing/pasport-antiterroristicheskoy-zaschischennosti',
        'services/licensing/dokumenty-dlya-obrazovatelnoj-organizacii',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
    ),
    'services/licensing/dokumenty-dlya-obrazovatelnoj-organizacii' => array(
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/services-le/sostavlenie-dogovorov',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/licensing/litsenziya-na-meditsinskuyu-deyatelnost' => array(
        'services/licensing/pasport-antiterroristicheskoy-zaschischennosti',
        'services/licensing/paket-dokumentov-dlya-medicinskoj-organizacii',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/bukhgalterskie-uslugi/vedenie-kadrovogo-ucheta',
    ),
    'services/licensing/paket-dokumentov-dlya-medicinskoj-organizacii' => array(
        'services/register-elimination/uvedomlenie-rkn',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/licensing/pasport-antiterroristicheskoy-zaschischennosti',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/licensing/litsenziya-mchs' => array(
        'services/register-elimination/uvedomlenie-rkn',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/services-le/sostavlenie-dogovorov',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/licensing/litsenziya-na-obrazovatelnuyu-deyatelnost-onlayn' => array(
        'services/register-elimination/uvedomlenie-rkn',
        'services/licensing/dokumenty-dlya-obrazovatelnoj-organizacii',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
    ),
    'services/licensing/zaklyuchenie-roszdravnadzora' => array(
        'services/licensing/dokumenty-dlya-obrazovatelnoj-organizacii',
        'services/licensing/zaklyuchenie-rospotrebnadzora',
        'services/licensing/pasport-antiterroristicheskoy-zaschischennosti',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
    ),
    'services/licensing/licenziya-na-tabachnuyu-produkciyu' => array(
        'services/register-elimination/registracija-kommercheskoj-organizacii',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/register-elimination/uvedomlenie-rkn',
    ),
    'services/licensing/pasport-bezopasnosti-potencialno-opasnogo-objecta-v-novosibirske' => array(
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/licensing/litsenziya-mchs',
        'services/services-le/predstavitelstvo-v-arbitrazhnom-sude',
    ),
    'services/licensing/litsenziya-na-telemeditsiny' => array(
        'services/licensing/paket-dokumentov-dlya-medicinskoj-organizacii',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
    ),
    'services/licensing/litsenziya-na-dezinfektsiyu-dezinsektciyu-deratizatsiyu' => array(
        'services/register-elimination/uvedomlenie-rkn',
        'services/register-elimination/registracija-kommercheskoj-organizacii',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/services-le/sostavlenie-dogovorov',
    ),
    'services/licensing/pasport-antiterroristicheskoy-zaschischennosti' => array(
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
        'services/register-elimination/uvedomlenie-rkn',
        'services/services-le/soprovozhdenie-nalogovoy-proverki',
    ),
    'services/licensing/farmatsevticheskaya-litsenziya' => array(
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/register-elimination/registracija-kommercheskoj-organizacii/registracija-ooo',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty/smena-generalnogo-direktora-ooo',
    ),
    'services/licensing/licenziya-na-pererabotku-othodov' => array(
        'services/register-elimination/registracija-kommercheskoj-organizacii',
        'services/register-elimination/uvedomlenie-rkn',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/services-le/sostavlenie-dogovorov',
    ),
    'services/licensing/roznichnaya-torgovlya-alkogolem' => array(
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/services-le/sostavlenie-dogovorov',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
    ),
    'services/licensing/predreysoviy-meditsinskiy-osmotr-voditeley' => array(
        'services/licensing/pasport-antiterroristicheskoy-zaschischennosti',
        'services/licensing/paket-dokumentov-dlya-medicinskoj-organizacii',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/bukhgalterskie-uslugi/vedenie-kadrovogo-ucheta',
    ),
    'services/licensing/registratsiya-elektrolaboratorii' => array(
        'services/register-elimination/registracija-kommercheskoj-organizacii/registracija-ooo',
        'services/services-le/yuridicheskaya-konsultatsiya',
        'services/services-le/sostavlenie-dogovorov',
        'services/bukhgalterskie-uslugi/express-proverka-bukhgalterskogo-uchyeta',
    ),
    'services/licensing/zaklyuchenie-rospotrebnadzora' => array(
        'services/licensing/pereoformlenie-licenzii',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
        'services/register-elimination/uvedomlenie-rkn',
        'services/bukhgalterskie-uslugi/konsultatsii-po-buhgalterskomu-uchetu-i-nalogooblozheniyu-2',
    ),
    'services/licensing/pereoformlenie-licenzii' => array(
        'services/licensing/zaklyuchenie-rospotrebnadzora',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/services-le/yuridicheskaya-konsultatsiya',
    ),
    'services/audit/obyazatelnyiy' => array(
        'services/audit/audit-kadrovyih-dokumentov' => 'Кадровый аудит',
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/services-le/strukturirovanie-biznesa',
        'services/services-le/yuridicheskaya-konsultatsiya' => 'Консультация юриста',
    ),
    'services/audit/initsiativnyiy' => array(
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/audit/audit-kadrovyih-dokumentov' => 'Кадровый аудит',
        'services/services-le/strukturirovanie-biznesa',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/audit/audit-kadrovyih-dokumentov' => array(
        'services/audit/initsiativnyiy',
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
    ),
    'services/audit/konsultatsiya' => array(
        'services/bukhgalterskie-uslugi/konsultatsii-po-buhgalterskomu-uchetu-i-nalogooblozheniyu-2',
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/services-le/strukturirovanie-biznesa',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/audit/bukhgalterskiy' => array(
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/audit/audit-kadrovyih-dokumentov' => 'Кадровый аудит',
        'services/services-le/strukturirovanie-biznesa',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/audit/finansoviy' => array(
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/audit/audit-kadrovyih-dokumentov' => 'Кадровый аудит',
        'services/services-le/strukturirovanie-biznesa',
        'services/bukhgalterskie-uslugi/konsultatsii-po-buhgalterskomu-uchetu-i-nalogooblozheniyu-2',
    ),
    'services/audit/due-diligence' => array(
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/audit/nalogovyiy' => array(
        'services/audit/konsultatsiya',
        'services/services-le/strukturirovanie-biznesa',
        'services/services-le/soprovozhdenie-nalogovoy-proverki',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
    ),
    'services/bukhgalterskie-uslugi/obsluzhivanie' => array(
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/licensing' => 'Оформление лицензий',
        'services/services-le/sostavlenie-dogovorov',
        'services/register-elimination/uvedomlenie-rkn',
    ),
    'services/bukhgalterskie-uslugi/konsultatsii-po-buhgalterskomu-uchetu-i-nalogooblozheniyu-2' => array(
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy' => 'Юридическое обслуживание бизнеса',
        'services/licensing' => 'Оформление лицензий',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
    ),
    'services/bukhgalterskie-uslugi/vedenie-kadrovogo-ucheta' => array(
        'services/audit/audit-kadrovyih-dokumentov' => 'Кадровый аудит',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
    ),
    'services/bukhgalterskie-uslugi/express-proverka-bukhgalterskogo-uchyeta' => array(
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/audit/audit-kadrovyih-dokumentov' => 'Кадровый аудит',
        'services/licensing' => 'Оформление лицензий',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
    ),
    'services/bukhgalterskie-uslugi/prof-ocenka-na-dolzhnost-buh' => array(
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/audit/audit-kadrovyih-dokumentov' => 'Кадровый аудит',
        'services/bukhgalterskie-uslugi/express-proverka-bukhgalterskogo-uchyeta',
        'services/services-le/ocenka-nalogovyh-riskov',
    ),
    'services/bukhgalterskie-uslugi/vosstanovleniya-ucheta' => array(
        'services/bukhgalterskie-uslugi/konsultatsii-po-buhgalterskomu-uchetu-i-nalogooblozheniyu-2',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
        'services/register-elimination/uvedomlenie-rkn',
        'services/services-le/ocenka-nalogovyh-riskov',
    ),
    'services/bukhgalterskie-uslugi/razrabotka-uchetnoy-politiki-kompanii' => array(
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/services-le/strukturirovanie-biznesa',
        'services/audit/initsiativnyiy',
        'services/services-le/yuridicheskaya-konsultatsiya',
    ),
    'services/bukhgalterskie-uslugi/sostavlenie-i-peredacha-otchetnosti' => array(
        'services/bukhgalterskie-uslugi/konsultatsii-po-buhgalterskomu-uchetu-i-nalogooblozheniyu-2',
        'services/bukhgalterskie-uslugi/express-proverka-bukhgalterskogo-uchyeta',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
        'services/audit/konsultatsiya' => 'Аудиторские консультации',
    ),
    'services/bukhgalterskie-uslugi/obsluzhivanie/vedenie-buhgalterskogo-ucheta-ooo' => UMBRELLA_UPSELL_BOOKKEEPING,
    'services/bukhgalterskie-uslugi/obsluzhivanie/vedenie-buhgalterskogo-ucheta-ip' => UMBRELLA_UPSELL_BOOKKEEPING,
    'services/bukhgalterskie-uslugi/obsluzhivanie/vedenie-buhgalterskogo-ucheta-nko' => UMBRELLA_UPSELL_BOOKKEEPING,
    'services/bukhgalterskie-uslugi/obsluzhivanie/vedenie-buhgalterskogo-ucheta-oa' => UMBRELLA_UPSELL_BOOKKEEPING,
    'services/intellektualnoe-pravo/obsluzhivane-tovarnyh-znakov' => array(
        'services/services-le/sporyi-v-sfere-intellektualnyih-prav',
        'services/intellektualnoe-pravo/registratsiya-litsenzionnogo-dogovora',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
        'services/licensing' => 'Оформление лицензий',
    ),
    'services/intellektualnoe-pravo/registracija-tovarnogo-znaka' => array(
        'services/intellektualnoe-pravo/obsluzhivane-tovarnyh-znakov',
        'services/licensing' => 'Оформление лицензий',
        'services/register-elimination/uvedomlenie-rkn',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/intellektualnoe-pravo/registratsiya-litsenzionnogo-dogovora' => array(
        'services/intellektualnoe-pravo/obsluzhivane-tovarnyh-znakov',
        'services/licensing' => 'Оформление лицензий',
        'services/licensing/pasport-antiterroristicheskoy-zaschischennosti',
        'services/register-elimination/uvedomlenie-rkn',
    ),
    'services/services-le/sporyi-v-sfere-intellektualnyih-prav' => array(
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/intellektualnoe-pravo/obsluzhivane-tovarnyh-znakov',
        'services/intellektualnoe-pravo/registratsiya-litsenzionnogo-dogovora',
        'services/services-le/predstavitelstvo-v-arbitrazhnom-sude',
    ),
    'services/services-le/ocenka-nalogovyh-riskov' => array(
        'services/audit/initsiativnyiy',
        'services/register-elimination/reorganizacija' => 'Реорганизация',
        'services/bukhgalterskie-uslugi/express-proverka-bukhgalterskogo-uchyeta',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
    ),
    'services/services-le/razreshenie-nalogovyih-sporov' => array(
        'services/services-le/soprovozhdenie-nalogovoy-proverki',
        'services/audit/nalogovyiy',
        'services/bukhgalterskie-uslugi/express-proverka-bukhgalterskogo-uchyeta',
        'services/audit/konsultatsiya',
    ),
    'services/services-le/strukturirovanie-biznesa' => array(
        'services/audit/initsiativnyiy',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
        'services/register-elimination/reorganizacija',
    ),
    'services/services-le/soprovozhdenie-nalogovoy-proverki' => array(
        'services/services-le/razreshenie-nalogovyih-sporov',
        'services/bukhgalterskie-uslugi/konsultatsii-po-buhgalterskomu-uchetu-i-nalogooblozheniyu-2',
        'services/audit/konsultatsiya',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/services-le/lichnyj-fond' => array(
        'services/services-le/strukturirovanie-biznesa',
        'services/services-le/yuridicheskaya-konsultatsiya' => 'Консультация налогового юриста',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/services-le/oformlenie-prikrytogo-skrytogo-vladeniya-biznesom',
    ),
    'services/register-elimination/registracija-kommercheskoj-organizacii' => array(
        'services/register-elimination/uvedomlenie-rkn',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/licensing' => 'Оформление лицензий',
    ),
    'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty' => array(
        'services/register-elimination/uvedomlenie-rkn',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/register-elimination/reorganizacija' => 'Реструктуризация бизнеса (группы компаний) путем реорганизации',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
    ),
    'services/register-elimination/registraciya-nekommercheskoj-organizatsii' => array(
        'services/licensing' => 'Оформление лицензий',
        'services/bukhgalterskie-uslugi/obsluzhivanie/vedenie-buhgalterskogo-ucheta-nko',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/register-elimination/uvedomlenie-rkn',
    ),
    'services/register-elimination/reorganizacija' => array(
        'services/register-elimination/likvidatsiya-ooo',
        'services/services-le/strukturirovanie-biznesa',
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/bukhgalterskie-uslugi/express-proverka-bukhgalterskogo-uchyeta',
    ),
    'services/register-elimination/likvidatsiya-ooo' => array(
        'services/register-elimination/reorganizacija' => 'Реструктуризация бизнеса (группы компаний) путем реорганизации',
        'services/bukhgalterskie-uslugi/express-proverka-bukhgalterskogo-uchyeta',
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/bukhgalterskie-uslugi/vosstanovleniya-ucheta',
    ),
    'services/register-elimination/uvedomlenie-rkn' => array(
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/services-le/sostavlenie-dogovorov',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
    ),
    'services/register-elimination/otkrytie-biznesa-pod-klyuch' => array(
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/licensing' => 'Оформление лицензий',
        'services/bukhgalterskie-uslugi/vedenie-kadrovogo-ucheta',
    ),
    'services/register-elimination/konsultaciya-po-korporativnomu-pravu' => array(
        'services/services-le/strukturirovanie-biznesa',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty/vkhod-uchastnikov-ooo',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
        'services/services-le/korporativniy-yurist',
    ),
    'services/register-elimination/registracija-kommercheskoj-organizacii/registracija-ooo' => array(
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/register-elimination/uvedomlenie-rkn',
        'services/services-le/sostavlenie-dogovorov' => 'Составление корпоративного договора',
    ),
    'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty/smena-generalnogo-direktora-ooo' => array(
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty/isklyuchenie-nedostovernykh-svedeniy-v-egryul',
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
    ),
    'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty/isklyuchenie-nedostovernykh-svedeniy-v-egryul' => array(
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty/smena-yuridicheskogo-adresa-ooo',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty/smena-generalnogo-direktora-ooo',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy' => array(
        'services/bukhgalterskie-uslugi/obsluzhivanie',
        'services/services-le/sostavlenie-dogovorov',
        'services/services-le/predstavitelstvo-v-arbitrazhnom-sude',
        'services/services-le/pravovoj-audit',
    ),
    'services/services-le/yuridicheskaya-konsultatsiya' => array(
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
        'services/services-le/sostavlenie-dogovorov',
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
    ),
    'services/services-le/sostavlenie-dogovorov' => array(
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
        'services/intellektualnoe-pravo/registracija-tovarnogo-znaka',
        'services/services-le/vzyskanie-dolgov',
        'services/services-le/predstavitelstvo-v-arbitrazhnom-sude',
    ),
    'services/services-le/predstavitelstvo-v-arbitrazhnom-sude' => array(
        'services/services-le/vzyskanie-dolgov',
        'services/services-le/korporativniy-yurist',
        'services/services-le/razreshenie-nalogovyih-sporov',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
    ),
    'services/services-le/korporativniy-yurist' => array(
        'services/services-le/predstavitelstvo-v-arbitrazhnom-sude',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
        'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty/vkhod-uchastnikov-ooo',
        'services/services-le/strukturirovanie-biznesa',
    ),
    'services/services-le/oformlenie-prikrytogo-skrytogo-vladeniya-biznesom' => array(
        'services/services-le/strukturirovanie-biznesa',
        'services/services-le/lichnyj-fond',
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
        'services/services-le/ocenka-nalogovyh-riskov',
    ),
    'services/services-le/vzyskanie-dolgov' => array(
        'services/services-le/predstavitelstvo-v-arbitrazhnom-sude',
        'services/services-le/soprovozhdenie-procedury-bankrotstva',
        'services/services-le/sostavlenie-dogovorov',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
    ),
    'services/services-le/pravovoj-audit' => array(
        'services/audit/due-diligence',
        'services/services-le/ocenka-nalogovyh-riskov',
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
        'services/services-le/sostavlenie-dogovorov',
    ),
    'services/services-le/obuchenie-menedgerov-pravu' => array(
        'services/services-le/yuridicheskoe-obsluzhivanie-organizatsiy',
        'services/services-le/sostavlenie-dogovorov',
        'services/audit/audit-kadrovyih-dokumentov',
        'services/services-le/yuridicheskaya-konsultatsiya',
    ),
    'services/register-elimination/vnesenie-izmenenij-v-uchreditelnye-dokumenty/smena-yuridicheskogo-adresa-ooo' => array(
        'services/register-elimination/konsultaciya-po-korporativnomu-pravu',
        'services/bukhgalterskie-uslugi/express-proverka-bukhgalterskogo-uchyeta',
        'services/register-elimination/uvedomlenie-rkn',
        'services/register-elimination/reorganizacija',
    ),
    'services/services-le/soprovozhdenie-procedury-bankrotstva' => UMBRELLA_UPSELL_BANKRUPTCY,
    'services/services-le/soprovozhdenie-procedury-bankrotstva/uslugi-kreditoram' => UMBRELLA_UPSELL_BANKRUPTCY,
    'services/services-le/soprovozhdenie-procedury-bankrotstva/uslugi-dolzhnikam' => UMBRELLA_UPSELL_BANKRUPTCY,
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
            <p class="umbrella-upsell__text">Перезвоним в ближайшее рабочее время: с&nbsp;09:00 до&nbsp;18:00 по&nbsp;Новосибирску.</p>
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
