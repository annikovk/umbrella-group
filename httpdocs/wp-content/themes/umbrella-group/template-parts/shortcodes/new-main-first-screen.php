<?php
class new_main_first_screen
{
    public $atts;

    /**
     * Слайды первого экрана (акции). Чтобы добавить ещё одну акцию — допишите
     * элемент массива: разметка, стрелки и переключение подхватятся сами.
     * Порядок элементов = порядок листания слева направо.
     */
    public static function get_slides()
    {
        return [
            /* Слайды «Товарный знак −15%» и «Паспорт безопасности −10 000 ₽» — акции
               закончились, сняты с главной 02.11.2026. Оставлены на случай повтора.
            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Ваш бренд работает<br>на вас.<span class="accent">А кому принадлежат<br>права на него?</span>',
                'descriptions' => [
                    'Название и логотип могут стать узнаваемыми, но без регистрации товарного знака защитить права на них сложнее.',
                    'Проверим обозначение, подготовим и подадим заявку в Роспатент, сопроводим процедуру регистрации до получения свидетельства.',
                ],
                'features' => [
                    [
                        'icon' => 'search',
                        'title' => 'Проверим<br>обозначение',
                        'text' => 'Убедимся, что ваш знак уникален',
                    ],
                    [
                        'icon' => 'document',
                        'title' => 'Подготовим<br>и подадим заявку',
                        'text' => 'Соберём документы и направим в Роспатент',
                    ],
                    [
                        'icon' => 'shield',
                        'title' => 'Сопроводим<br>регистрацию',
                        'text' => 'Контролируем процесс до получения свидетельства',
                    ],
                ],
                'button_text' => 'Зарегистрировать знак со скидкой 15%',
                'button_link' => '/akcii/registraciya-tovarnogo-znaka-skidka-15/',
                'image' => '/wp-content/uploads/registraciya-tz-banner.png',
                'image_webp' => '/wp-content/uploads/registraciya-tz-banner.webp',
                'image_alt' => 'Свидетельство на товарный знак — скидка на регистрацию',
            ],

            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Паспорт<br>безопасности объекта<span class="accent">со&nbsp;скидкой 10&nbsp;000&nbsp;&#8381;</span>',
                'descriptions' => [
                    'Для медицинских центров, торговых объектов, аптек и других организаций действуют требования антитеррористической защищённости. Проверим, распространяются ли они на ваш объект, и оформим необходимые документы.',
                ],
                'features' => [
                    [
                        'icon' => 'medical',
                        'title' => 'Для<br>медицинских<br>центров',
                    ],
                    [
                        'icon' => 'shop',
                        'title' => 'Для торговых<br>центров<br>и магазинов',
                    ],
                    [
                        'icon' => 'pill',
                        'title' => 'Для аптек<br>и прочих объектов',
                    ],
                ],
                'button_text' => 'Проверить объект и получить скидку',
                'button_link' => '/akcii/pasport-bezopasnosti-obekta-skidka-10000/',
                'image' => '/wp-content/uploads/pasport-bezopasnosti-banner-2.png',
                'image_webp' => '/wp-content/uploads/pasport-bezopasnosti-banner-2.webp',
                'image_alt' => 'Паспорт антитеррористической защищённости объекта — скидка 10 000 рублей',
                'modifier' => 'promo-slide-pasport',
            ],
            */

            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'ООО в подарок<br>за оформление лицензии<span class="accent">Регистрация ООО = 0&nbsp;руб.!</span>',
                'descriptions' => [
                    'Нужна лицензия для бизнеса, но предприятие еще не зарегистрировано? Сэкономьте на регистрации компании — при оформлении лицензии регистрация ООО бесплатно!',
                ],
                'features' => [
                    [
                        'icon' => 'document',
                        'title' => 'Подготовим<br>документы',
                        'text' => 'Соберем пакет документов<br>на регистрацию компании',
                    ],
                    [
                        'icon' => 'users',
                        'title' => 'Зарегистрируем<br>ООО',
                        'text' => 'Оформим компанию<br>без лишних хлопот',
                    ],
                    [
                        'icon' => 'shield',
                        'title' => 'Сопроводим<br>под ключ',
                        'text' => 'Сопроводим услугу<br>до результата',
                    ],
                ],
                'button_text' => 'Получить ООО в подарок',
                'button_link' => '/akcii/ooo-v-podarok-za-litsenziyu/',
                'image' => '/wp-content/uploads/ooo-v-podarok-banner-3.png',
                'image_webp' => '/wp-content/uploads/ooo-v-podarok-banner-3.webp',
                'image_alt' => 'Регистрация ООО за 0 рублей при оформлении лицензии',
                'modifier' => 'promo-slide-ooo',
            ],

            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Аудиторские услуги<br>для бизнеса<span class="accent">Проверьте учёт сегодня, чтобы избежать проблем завтра</span>',
                'descriptions' => [
                    'Обязательный и инициативный аудит, проверка налоговых рисков и отдельных участков учёта. Поможем выявить ошибки, оценить их последствия и разобраться, что требует внимания.',
                ],
                'features' => [
                    [
                        'icon' => 'doc-check',
                        'title' => 'Проверим<br>учёт',
                        'text' => 'Оценим достоверность отчётности и корректность учёта',
                    ],
                    [
                        'icon' => 'shield-check',
                        'title' => 'Выявим<br>риски',
                        'text' => 'Найдём ошибки и возможные налоговые последствия',
                    ],
                    [
                        'icon' => 'clipboard',
                        'title' => 'Предложим<br>решения',
                        'text' => 'Дадим рекомендации по устранению нарушений',
                    ],
                ],
                'button_text' => 'Подробнее об аудите',
                'button_link' => '/services/audit/',
                'image' => '/wp-content/uploads/audit-banner.png',
                'image_webp' => '/wp-content/uploads/audit-banner.webp',
                'image_alt' => 'Аудиторское заключение, бухгалтерская отчётность и калькулятор — аудит для бизнеса',
                'modifier' => 'promo-slide-audit',
            ],

            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Бухгалтерские услуги<br>для бизнеса<span class="accent">Ваш учёт — под контролем профессионалов</span>',
                'descriptions' => [
                    'Ведение и восстановление бухгалтерского учёта, подготовка отчётности, расчёт налогов и консультации. Возьмём на себя бухгалтерские задачи, чтобы вы могли сосредоточиться на развитии бизнеса.',
                ],
                'features' => [
                    [
                        'icon' => 'calculator',
                        'title' => 'Ведём<br>учёт',
                        'text' => 'От первичных документов до отчётности',
                    ],
                    [
                        'icon' => 'doc-check',
                        'title' => 'Рассчитываем<br>налоги',
                        'text' => 'Следим за сроками и корректностью расчётов',
                    ],
                    [
                        'icon' => 'chat',
                        'title' => 'Консультируем',
                        'text' => 'Помогаем разобраться в сложных вопросах учёта',
                    ],
                ],
                'button_text' => 'Подробнее об услугах',
                'button_link' => '/services/bukhgalterskie-uslugi/',
                'image' => '/wp-content/uploads/buhgalteriya-banner.png',
                'image_webp' => '/wp-content/uploads/buhgalteriya-banner.webp',
                'image_alt' => 'Папка «Бухгалтерия», отчёты с графиками и калькулятор — бухгалтерские услуги для бизнеса',
                'modifier' => 'promo-slide-buh',
            ],

            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Лицензирование<br>деятельности под ключ<span class="accent">От подготовки документов до получения лицензии</span>',
                'descriptions' => [
                    'Поможем оформить лицензии на медицинскую, образовательную, фармацевтическую и другие виды деятельности, а также разработать паспорта безопасности объектов. Проверим соответствие требованиям и сопроводим процедуру оформления.',
                ],
                'features' => [
                    [
                        'icon' => 'clipboard-check',
                        'title' => 'Проверим<br>готовность',
                        'text' => 'Оценим соответствие установленным требованиям',
                    ],
                    [
                        'icon' => 'copy',
                        'title' => 'Подготовим<br>документы',
                        'text' => 'Оформим лицензии и паспорта безопасности',
                    ],
                    [
                        'icon' => 'shield-check',
                        'title' => 'Сопроводим<br>под ключ',
                        'text' => 'Поможем пройти проверки и согласования',
                    ],
                ],
                'button_text' => 'Подробнее о лицензировании',
                'button_link' => '/services/licensing/',
                'image' => '/wp-content/uploads/licenzirovanie-banner.png',
                'image_webp' => '/wp-content/uploads/licenzirovanie-banner.webp',
                'image_alt' => 'Лицензия, паспорт безопасности и печать — лицензирование деятельности под ключ',
                'modifier' => 'promo-slide-lic',
            ],

            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Защита интеллектуальной<br>собственности<span class="accent">Ваш бренд, идеи и разработки&nbsp;—<br>под правовой защитой</span>',
                'descriptions' => [
                    'Поможем зарегистрировать товарный знак, защитить права на интеллектуальную собственность и разобраться с вопросами её использования. От проверки обозначений до оформления документов и сопровождения регистрации.',
                ],
                'features' => [
                    [
                        'icon' => 'search',
                        'title' => 'Проверим<br>обозначение',
                        'text' => 'Оценим возможность регистрации товарного знака',
                    ],
                    [
                        'icon' => 'doc-check',
                        'title' => 'Зарегистрируем<br>права',
                        'text' => 'Подготовим документы и сопроводим процедуру',
                    ],
                    [
                        'icon' => 'shield-check',
                        'title' => 'Защитим<br>интересы',
                        'text' => 'Поможем разобраться с нарушениями ваших прав',
                    ],
                ],
                'button_text' => 'Подробнее об услугах',
                'button_link' => '/services/intellektualnoe-pravo/',
                'image' => '/wp-content/uploads/intellektualnoe-pravo-banner.png',
                'image_webp' => '/wp-content/uploads/intellektualnoe-pravo-banner.webp',
                'image_alt' => 'Папка «Интеллектуальное право», товарный знак и щит TM — защита интеллектуальной собственности',
                'modifier' => 'promo-slide-ip',
            ],

            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Регистрация и ликвидация<br>компаний<span class="accent">От открытия бизнеса до завершения его деятельности</span>',
                'descriptions' => [
                    'Поможем зарегистрировать ООО или ИП, внести изменения в учредительные документы, провести реорганизацию или ликвидацию компании. Возьмём на себя подготовку документов и юридическое сопровождение всех процедур.',
                ],
                'features' => [
                    [
                        'icon' => 'building',
                        'title' => 'Зарегистрируем<br>бизнес',
                        'text' => 'Подготовим документы для открытия ООО или ИП',
                    ],
                    [
                        'icon' => 'edit',
                        'title' => 'Оформим<br>изменения',
                        'text' => 'Сопроводим изменения в ЕГРЮЛ и учредительных документах',
                    ],
                    [
                        'icon' => 'clipboard-check',
                        'title' => 'Проведём<br>ликвидацию',
                        'text' => 'Поможем завершить деятельность компании с соблюдением требований закона',
                    ],
                ],
                'button_text' => 'Подробнее об услугах',
                'button_link' => '/services/register-elimination/',
                'image' => '/wp-content/uploads/registraciya-likvidaciya-banner.png',
                'image_webp' => '/wp-content/uploads/registraciya-likvidaciya-banner.webp',
                'image_alt' => 'Ноутбук, документы ЕГРЮЛ и печать — регистрация и ликвидация компаний',
                'modifier' => 'promo-slide-reg',
            ],

            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Налоговое право<br>для бизнеса<span class="accent">От налогового планирования до&nbsp;защиты в&nbsp;спорах с&nbsp;ФНС</span>',
                'descriptions' => [
                    'Поможем оценить налоговые риски, оптимизировать налогообложение, подготовиться к проверкам и сопроводить в спорах с ФНС. Обеспечим юридическое сопровождение бизнеса на каждом этапе.',
                ],
                'features' => [
                    [
                        'icon' => 'chart',
                        'title' => 'Налоговое<br>планирование',
                        'text' => 'Поможем с оптимизацией налогообложения',
                    ],
                    [
                        'icon' => 'clipboard-check',
                        'title' => 'Сопровождение<br>проверок',
                        'text' => 'Поможем подготовиться к проверкам, защитим интересы при взаимодействии с ФНС',
                    ],
                    [
                        'icon' => 'scales',
                        'title' => 'Налоговые<br>споры',
                        'text' => 'Сопроводим в спорах с ФНС',
                    ],
                ],
                'button_text' => 'Подробнее об услугах',
                'button_link' => '/services/nalogovoe-pravo/',
                'image' => '/wp-content/uploads/nalogovoe-pravo-banner.png',
                'image_webp' => '/wp-content/uploads/nalogovoe-pravo-banner.webp',
                'image_alt' => 'Книга «Налоговое право», анализ налоговых рисков и калькулятор — налоговое право для бизнеса',
                'modifier' => 'promo-slide-tax',
            ],

            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Юридические услуги<br>для бизнеса<span class="accent">От решения текущих вопросов до&nbsp;защиты интересов в&nbsp;суде</span>',
                'descriptions' => [
                    'Юридическое сопровождение компаний: договорная работа, урегулирование споров и представительство в суде. Поможем защитить интересы бизнеса, снизить правовые риски и найти решение даже в сложных ситуациях.',
                ],
                'features' => [
                    [
                        'icon' => 'edit',
                        'title' => 'Договорная<br>работа',
                        'text' => 'Подготовим и проверим договоры, оценим юридические риски',
                    ],
                    [
                        'icon' => 'building',
                        'title' => 'Корпоративное<br>право',
                        'text' => 'Поможем решить правовые вопросы деятельности компании',
                    ],
                    [
                        'icon' => 'scales',
                        'title' => 'Разрешение<br>споров',
                        'text' => 'Защитим интересы в переговорах и судебных разбирательствах',
                    ],
                ],
                'button_text' => 'Подробнее об услугах',
                'button_link' => '/services/services-le/',
                'image_alt' => 'Юридические услуги для бизнеса',
                'modifier' => 'promo-slide-law',
            ],

            /* Слайд «Сибирский налоговый семинар 2027» — снят с главной 22.09.2026,
               оставлен на случай, если акцию вернут в ротацию.
            [
                'sub_header' => 'Экспертное сопровождение бизнеса в Новосибирске',
                'title' => 'Сибирский налоговый семинар 2027',
                'descriptions' => [
                    '2026 год был только началом. И уже кардинально изменил налоговую реальность российского бизнеса. Но на этом изменения не заканчиваются. На семинаре эксперты разберут, с чем бизнес входит в 2027 год, какие изменения уже известны и как они повлияют на работу компаний.',
                ],
                'list_header' => 'Спикеры:',
                'list_items' => [
                    'Софья Гладкова - председатель Общественного совета России при УФНС по Новосибирской области',
                    'Никита Демиденко - ведущий налоговый юрист по Новосибирской области, директор ООО "Юрсервис" (Входит в ГК Umbrella Group)',
                ],
                'button_text' => 'Подробнее о семинаре',
                'button_link' => '/blog/nalogovi-seminar-2027/',
                'image' => 'https://taxlab.ru/wp-content/uploads/spikery-banner-4.png',
                'image_alt' => 'Спикеры семинара',
            ],
            */
        ];
    }

    /** [ширина, высота] картинки слайда из uploads по её пути или null (внешний URL, нет файла). */
    private static function image_size($src)
    {
        if (!is_string($src) || !str_starts_with($src, '/wp-content/uploads/')) {
            return null;
        }
        $size = wp_getimagesize(ABSPATH . ltrim($src, '/'));
        return $size ? [(int) $size[0], (int) $size[1]] : null;
    }

    /** Иконки блока преимуществ — инлайном, чтобы не зависеть от медиатеки. */
    private function get_icon($name)
    {
        $icons = [
            'search' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="11" cy="11" r="6.25" stroke="currentColor" stroke-width="1.8"/><path d="M15.7 15.7 20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'document' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M14 3.75H7A1.25 1.25 0 0 0 5.75 5v14A1.25 1.25 0 0 0 7 20.25h10A1.25 1.25 0 0 0 18.25 19V8L14 3.75Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M13.75 4v4.25H18" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8.75 12.5h6.5M8.75 16h4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'shield' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 3.5 5 6.2v5.4c0 4.1 2.8 7.4 7 8.9 4.2-1.5 7-4.8 7-8.9V6.2L12 3.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'users' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="9" cy="8.5" r="3.25" stroke="currentColor" stroke-width="1.8"/><path d="M3.75 19.25c0-2.9 2.35-5 5.25-5s5.25 2.1 5.25 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M15.5 5.6a3.25 3.25 0 0 1 0 5.8M17.25 14.6c1.8.6 3 2.4 3 4.65" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'doc-check' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M14 3.75H7A1.25 1.25 0 0 0 5.75 5v14A1.25 1.25 0 0 0 7 20.25h10A1.25 1.25 0 0 0 18.25 19V8L14 3.75Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M13.75 4v4.25H18" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m9 14 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'shield-check' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 3.5 5 6.2v5.4c0 4.1 2.8 7.4 7 8.9 4.2-1.5 7-4.8 7-8.9V6.2L12 3.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'clipboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9 4.75H7A1.25 1.25 0 0 0 5.75 6v13A1.25 1.25 0 0 0 7 20.25h10A1.25 1.25 0 0 0 18.25 19V6A1.25 1.25 0 0 0 17 4.75h-2" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><rect x="9" y="3.25" width="6" height="3" rx="1" stroke="currentColor" stroke-width="1.8"/><path d="M9 11h6M9 14.5h6M9 18h3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'calculator' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="5.75" y="3.75" width="12.5" height="16.5" rx="1.5" stroke="currentColor" stroke-width="1.8"/><path d="M8.75 7.25h6.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M9 11.5h.01M12 11.5h.01M15 11.5h.01M9 14.5h.01M12 14.5h.01M15 14.5h.01M9 17.5h.01M12 17.5h.01M15 17.5h.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>',
            'chat' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 4.75h14A1.25 1.25 0 0 1 20.25 6v9.5A1.25 1.25 0 0 1 19 16.75H10l-4.25 3.5v-3.5H5A1.25 1.25 0 0 1 3.75 15.5V6A1.25 1.25 0 0 1 5 4.75Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 9.25h8M8 12.5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'clipboard-check' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9 4.75H7A1.25 1.25 0 0 0 5.75 6v13A1.25 1.25 0 0 0 7 20.25h10A1.25 1.25 0 0 0 18.25 19V6A1.25 1.25 0 0 0 17 4.75h-2" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><rect x="9" y="3.25" width="6" height="3" rx="1" stroke="currentColor" stroke-width="1.8"/><path d="m9 13.5 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'copy' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="8.75" y="8.75" width="11.5" height="11.5" rx="1.5" stroke="currentColor" stroke-width="1.8"/><path d="M15.25 5.25v-.5A1.25 1.25 0 0 0 14 3.5H5A1.25 1.25 0 0 0 3.75 4.75V14A1.25 1.25 0 0 0 5 15.25h.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'building' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4.75 20.25V5A1.25 1.25 0 0 1 6 3.75h7A1.25 1.25 0 0 1 14.25 5v15.25M14.25 9.75H18A1.25 1.25 0 0 1 19.25 11v9.25M3 20.25h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 7.75h3M8 11h3M8 14.25h3M17 13.5v.01M17 16.75v.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'edit' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M11 4.75H6A1.25 1.25 0 0 0 4.75 6v12A1.25 1.25 0 0 0 6 19.25h12A1.25 1.25 0 0 0 19.25 18v-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M17.5 3.9a1.9 1.9 0 0 1 2.6 2.6L12 14.6l-3.25.65L9.4 12l8.1-8.1Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'chart' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3.75 20.25h16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M6.5 17v-3M10.5 17v-5.5M14.5 17v-4M18.5 17V9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="m5 10.5 4.5-4 4 2.5 5.5-5M15.5 4h3.5v3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'scales' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 3.75v16.5M8 20.25h8M5 6.75h14M12 6.75V5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="m5.5 7-2.75 6.25a2.9 2.9 0 0 0 5.5 0L5.5 7ZM18.5 7l-2.75 6.25a2.9 2.9 0 0 0 5.5 0L18.5 7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'medical' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9.5 3.75h5V9.5h5.75v5H14.5v5.75h-5V14.5H3.75v-5H9.5V3.75Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'shop' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4.75 8.4h14.5v9.85a2.5 2.5 0 0 1-2.5 2.5h-9.5a2.5 2.5 0 0 1-2.5-2.5V8.4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8.5 8.4V6.9a3.5 3.5 0 0 1 7 0v1.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'pill' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><g transform="rotate(-45 12 12)"><rect x="2.4" y="8.4" width="19.2" height="7.2" rx="3.6" stroke="currentColor" stroke-width="1.8"/><path d="M12 8.4v7.2" stroke="currentColor" stroke-width="1.8"/></g></svg>',
        ];
        return isset($icons[$name]) ? $icons[$name] : '';
    }

    private function render_button($slide, $extra_class)
    {
        $text = $slide['button_text'];
        $link = $slide['button_link'];
        $arrow = '<svg class="banner-button-arrow" width="24" height="12" viewBox="0 0 24 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M1 6h21M17 1l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        return <<<EOT
            <a href="$link" class="$extra_class button primary lowercase banner-button">$text $arrow</a>
            EOT;
    }

    private function render_slide($slide, $index)
    {
        // h1 на странице должен быть один: он у первого слайда, у остальных — абзац.
        $header_tag = $index === 0 ? 'h1' : 'p';
        $active = $index === 0 ? ' is-active' : '';
        // Класс акции — на случай, когда картинка или заголовок просят своих размеров.
        $modifier = empty($slide['modifier']) ? '' : ' ' . $slide['modifier'];
        $sub_header = $slide['sub_header'];
        $title = $slide['title'];

        $descriptions = '';
        foreach ($slide['descriptions'] as $description) {
            $descriptions .= '<p class="description">' . $description . '</p>';
        }

        $list = '';
        if (!empty($slide['list_items'])) {
            $list_header = isset($slide['list_header']) ? '<p>' . $slide['list_header'] . '</p>' : '';
            $list = $list_header . '<ul class="description">';
            foreach ($slide['list_items'] as $list_item) {
                $list .= '<li>' . $list_item . '</li>';
            }
            $list .= '</ul>';
        }

        $features = '';
        if (!empty($slide['features'])) {
            $features = '<div class="promo-features">';
            foreach ($slide['features'] as $feature) {
                $icon = $this->get_icon($feature['icon']);
                // Пояснение под заголовком есть не у всех акций.
                $text = empty($feature['text']) ? '' : '<div class="promo-feature-text">' . $feature['text'] . '</div>';
                $features .= <<<EOT
                    <div class="promo-feature">
                        <div class="promo-feature-icon">$icon</div>
                        <div class="promo-feature-title">{$feature['title']}</div>
                        $text
                    </div>
                    EOT;
            }
            $features .= '</div>';
        }

        $button_desktop = $this->render_button($slide, 'hide-for-small');
        $button_mobile = $this->render_button($slide, 'show-for-small');
        $image = isset($slide['image']) ? $slide['image'] : '';
        $image_alt = isset($slide['image_alt']) ? $slide['image_alt'] : '';
        // Первый слайд — LCP главной: грузится сразу и первым (мимо lazy-load
        // Flatsome, см. ug_eager_img), остальные скрыты до клика — в конец очереди.
        $loading = $index === 0 ? ' fetchpriority="high"' : ' loading="lazy" fetchpriority="low"';
        // width/height — пропорции до загрузки картинки (CSS всё равно width:100%; height:auto).
        $size = self::image_size($image);
        $dims = $size ? ' width="' . $size[0] . '" height="' . $size[1] . '"' : '';
        // webp весит в семь раз меньше png, но png оставляем запасным вариантом.
        $picture = '<img src="' . $image . '" alt="' . $image_alt . '"' . $dims . $loading . '>';
        if ($index === 0) {
            $picture = ug_eager_img($picture);
        }
        if (!empty($slide['image_webp'])) {
            $picture = '<picture><source srcset="' . $slide['image_webp'] . '" type="image/webp">' . $picture . '</picture>';
        }
        // Слайд без картинки — без правой колонки.
        $right = $image === '' ? '' : '<div class="right">' . $picture . '</div>';

        return <<<EOT
            <div class="promo-slide$active$modifier" data-promo-slide="$index">
                <div class="main-banner-content">
                    <div class="left">
                        <$header_tag class="sub-header">$sub_header</$header_tag>
                        <p class="text-like-h1">$title</p>
                        $descriptions
                        $list
                        $features
                        $button_desktop
                    </div>
                    $right
                    $button_mobile
                </div>
            </div>
            EOT;
    }

    private function render_slider()
    {
        $slides = self::get_slides();
        $slides_html = '';
        foreach ($slides as $index => $slide) {
            $slides_html .= $this->render_slide($slide, $index);
        }

        // Стрелки — только когда есть что листать.
        $arrow_left = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 4l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $arrow_right = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8 4l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $arrows = <<<EOT
            <button type="button" class="promo-slider-arrow promo-slider-prev" data-promo-prev aria-label="Предыдущая акция">$arrow_left</button>
            <button type="button" class="promo-slider-arrow promo-slider-next" data-promo-next aria-label="Следующая акция">$arrow_right</button>
            EOT;

        if (count($slides) < 2) {
            $arrows = '';
        }

        return <<<EOT
            <div class="promo-slider" data-promo-slider>
                <div class="promo-slider-viewport">
                    $slides_html
                </div>
                $arrows
            </div>
            EOT;
    }

    public function generate_shortcode()
    {
        $slider = $this->render_slider();
        $html = <<<EOT
         [section id='new_main_first_screen' class='new_main_first_screen'  padding="0px"]
            <div class="first-screen-services-tabs show-for-small">
                <ul class="tabs">
                    <li><a href="/services/audit/">Аудит</a></li>
                    <li><a href="/services/bukhgalterskie-uslugi/">Бухгалтерия</a></li>
                    <li><a href="/services/licensing/">Лицензирование</a></li>
                    <li><a href="/services/register-elimination/">Регистрация</a></li>
                    <li><a href="/services/register-elimination/likvidatsiya-ooo/">Ликвидация</a></li>
                    <li><a href="/services/intellektualnoe-pravo/registracija-tovarnogo-znaka/">Товарный знак</a></li>
                    <li><a href="/services/services-le/">Юридические услуги</a></li>
                </ul>
            </div>
            <div class="first-screen">
                <div class="main-banner">
                    <div class="row">
                        <div class="col">
                            $slider
                        </div>
                    </div>
                </div>
                <div class="ratings">
                    [row style="collapse" width="full-width"]
                    [col span__sm="12"]
                    [row_inner_1 style="collapse" v_align="middle"]
                    [col_inner_1 span="4" span__sm="12" padding="0px 0px 0px 0px" margin="0px 0px 0px 0px"]
                    [featured_box img="13631" inline_svg="0" img_width="95" pos="left"]
                    <p class="laurel-place">ТОП-5</p>
                    <p class="laurel-icon-header">юридических компаний Новосибирска<sup style="font-size: 6pt;"> 3</sup></p>
                    <p class="laurels-icons-year">июнь, 2025</p>
                    [/featured_box]
                    [/col_inner_1]
                    [col_inner_1 span="4" span__sm="12"]
                    [featured_box img="13809" inline_svg="0" img_width="95" pos="left" ]
                    <p class="laurel-place">1-ое место</p>
                    <p class="laurel-icon-header">среди бухгалтерских компаний Новосибирска<sup style="font-size: 6pt;"> 2</sup></p>
                    <p class="laurels-icons-year">2023</p>
                    [/featured_box]
                    [/col_inner_1]
                    [col_inner_1 span="4" span__sm="12"]
                    [featured_box img="13809" inline_svg="0" img_width="95" pos="left"]
                    <p class="laurel-place">1-ое место</p>
                     <p class="laurel-icon-header">в номинации <br>«Корпоративное право»<sup style="font-size: 6pt;"> 1</sup></p>
                    <p class="laurels-icons-year">июнь, 2025</p>
                    [/featured_box]
                    [/col_inner_1]
            <div class="laurel-footnote">1 — по версии рейтинга журнала «Деловой квартал» 2 — по итогам рейтинга «Планка» (planka.ru) 3 — по версии журнала «Деловой квартал»;</div>
                    [/row_inner_1]

                    [/col]
                    [/row]
                </div>
            </div>
            [lightbox width="400px" padding="0" id="main-banner-contact-form-lightbox"]
            [contact-form-7 id="13619" title="Форма на главной (узнать стоимость услуг)"]
            [/lightbox]

            [lightbox width="400px" padding="0" id="main-banner-contact-form-lightbox-seminar"]
            [contact-form-7 id="080e72d" title="Форма для семинара"]
            [/lightbox]

            [lightbox width="400px" padding="0" id="main-banner-contact-form-lightboxx"]
            [contact-form-7 id="4af4748" title="Форма на главной (узнать стоимость услуг)_copy"]
            [/lightbox]




         [/section]
        EOT;
        ug_enqueue_style('assets/css/blocks/new_main_first_screen.css');
        ug_enqueue_script('assets/js/blocks/main-first-screen-slider.js', ['jquery'], ['in_footer' => true, 'strategy' => 'defer']);
        return $html;
    }

}

function new_main_first_screen_shortcode($atts)
{
    $shortcode = new new_main_first_screen();
    $shortcode->atts = $atts;
    return $shortcode->generate_shortcode();
}

add_shortcode('new_main_first_screen', 'new_main_first_screen_shortcode');

/**
 * Preload картинки первого слайда: шорткод рендерится в body после
 * блокирующих CSS в <head> — без preload браузер узнаёт о LCP-картинке поздно.
 * Браузер берёт из <picture> webp, его и грузим.
 *
 * Только на ширине от 850px (граница hide-for-medium Flatsome): там LCP —
 * эта картинка. На телефоне LCP — текст первого экрана, а картинка 280px
 * и так грузится тегом <img fetchpriority="high"> из разметки; ранний
 * preload там только отнимает канал у CSS и шрифтов до первой отрисовки.
 */
add_action('wp_head', function () {
    if (!is_singular() || !has_shortcode((string) get_post_field('post_content', get_queried_object_id()), 'new_main_first_screen')) {
        return;
    }
    $slide = new_main_first_screen::get_slides()[0] ?? null;
    if (!$slide) {
        return;
    }
    $webp = !empty($slide['image_webp']);
    printf(
        '<link rel="preload" as="image" href="%s"%s media="(min-width: 850px)" fetchpriority="high">' . "\n",
        esc_url($webp ? $slide['image_webp'] : $slide['image']),
        $webp ? ' type="image/webp"' : ''
    );
}, 2);
