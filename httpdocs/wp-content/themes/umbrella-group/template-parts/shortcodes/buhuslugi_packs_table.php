<?php

class buhuslugi_packs_table
{
    private $css_file = ['/assets/css/blocks/buhuslugi-packs-block.css'];
    private $js_file = ['/assets/js/blocks/buhuslugi-packs-block.js'];
    private $link;
    private $header;
    private $fio;
    private $photo;

    private $text;
    public $atts;
    public $err;

    public function fill_attributes()
    {
        if (isset($this->atts['link']) && isset($this->atts['header']) && isset($this->atts['fio']) && isset($this->atts['photo']) && isset($this->atts['text'])) {
            $this->link = $this->atts['link'];
            $this->header = $this->atts['header'];
            $this->fio = $this->atts['fio'];
            $this->photo = $this->atts['photo'];
            $this->text = $this->atts['text'];
            return true;
        } else {
            $this->err = 'Not enough attributes. Here is the correct shortcode: [video_block link="https://www.youtube.com/embed/pJikk8r9Aj8" header="Заголовок" fio="<b>Тарасова Юлия Сергеевна</b>, руководитель отдела бухгалтерского обслуживания ГК Umbrella Group" photo="123"  text="test text"]';
            return false;
        }
    }

    public function generate_shortcode()
    {
        $html =
            <<<EOHTML
                <div id="buh-table">
                  <div class="leftBlock">
                    <div class="head"></div>
                    <div class="price">
                      <div class="checkBoxWrapper">
                        <input
                          type="radio"
                          name="typeCompany"
                          id="company"
                          class="buh-table-radio"
                          value="company"
                          checked
                        />
                        <label for="company"><span>для юридического лица</span></label>
                      </div>
                      <div class="checkBoxWrapper">
                        <input
                          type="radio"
                          name="typeCompany"
                          id="ip"
                          class="buh-table-radio"
                          value="ip"
                        />
                        <label for="ip">для ИП</label>
                      </div>
                    </div>
                    <div class="content">
                      <div class="item">Бухгалтерский учет</div>
                      <div class="item">Подготовка, сдача отчётности
                       <span class="hint" data-tooltip="#service1">? </span>
                        </div>
                      <div class="item">Расчёт заработной платы
                      <span class="hint" data-tooltip="#service2">?</span>
                        </div>
                      <div class="item">Расчёт налогов
                      <span class="hint" data-tooltip="#service3">?</span>
                        </div>
                      <div class="item">Консультация бухгалтера
                      <span class="hint" data-tooltip="#service4">?</span>
                      </div>
                      <div class="item">Кадровый учёт</div>
                      <div class="item">Юридическое сопровождение</div>
                      <div class="item service">
                        <span class="line"></span>Устное и письменное консультирование
                       
                      </div>
                      <div class="item service">
                        <span class="line"></span>Сопровождение и экспертиза сделок
                        
                      </div>
                      <div class="item service">
                        <span class="line"></span>Разработка юридических документов
                        
                      </div>
                      <div class="item service">
                        <span class="line"></span>Претензионная работа
                        </span>
                      </div>
                      <div class="item accent">
                        <img src="/wp-content/uploads/securityIcon.png" />
                        Ответственность
                        <span class="hint" data-tooltip="#tooltipResponsibility"
                          >?
                        </span>
                      </div>

                    </div>
                  </div>
                  <div class="rightBlock">
                    <div class="head">
                      <div class="item" data-option="1">
                        <div class="name">
                          Главный бухгалтер
                          <span class="hint" data-tooltip="#ttEmployee1"
                            >?
                            <div class="tooltip" id="ttEmployee1">
                              Подойдёт, если у вас: <br/>
                                • есть свой бухгалтер, но нужен контроль и страховка от ошибок; <br/>
                                • своя 1С.
                            </div>
                          </span>
                        </div>
                      </div>
                      <div class="item" data-option="2">
                        <div class="name">
                          Бухгалтерский аутсорсинг
                          <span class="hint" data-tooltip="#ttEmployee2"
                            >?
                            <div class="tooltip" id="ttEmployee2">
                              Подойдёт, если у вас: <br/>
                                • нет штатного бухгалтера, либо он перегружен и не справляется; <br/>
                                • нет 1С; <br/>
                                • небольшой штат (1-3 сотрудника).
                            </div>
                          </span>
                        </div>
                        <div class="sale-flag"  data-tooltip="#tooltipHeaderSale">
                            <div class="tooltip" id="tooltipHeaderSale">
                                СКИДКА 10 % на первый месяц ведения новым клиентам.
                            </div>
                        </div>
                      </div>
                      <div class="item" data-option="3">
                        <div class="name">
                          Бухгалтерия + кадры
                          <span class="hint" data-tooltip="#ttEmployee3"
                            >?
                            <div class="tooltip" id="ttEmployee3">
                              Подойдёт, если у вас:<br/>
                                • нет штатного бухгалтера, либо он перегружен и не справляется;<br/>
                                • нет 1С;<br/>
                                • штат больше 3 человек;<br/>
                                • частые приёмы на работу и увольнения, отпуски, командировки.
                            </div>
                          </span>
                        </div>
                        <div class="hit-flag"  data-tooltip="#tooltipHeaderHit">
                            <div class="tooltip" id="tooltipHeaderHit">
                                Самый популярный тариф среди наших клиентов.
                            </div>
                        </div>
                      </div>
                      <div class="item" data-option="4">
                        <div class="name">
                          Бухгалтерия + кадры + юристы
                          <span class="hint" data-tooltip="#ttEmployee4"
                            >?
                            <div class="tooltip" id="ttEmployee4">
                              Подойдёт, если у вас:<br/>
                                • нет штатного бухгалтера, либо он перегружен и не справляется;<br/>
                                • нет 1С;<br/>
                                • частые согласования договоров;<br/>
                                • бывают претензии и нужна помощь юриста в грамотной работе с ними;<br/>
                                • разрабатываются локальные нормативные акты;<br/>
                                • частые перемещения персонала.
                            </div>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="price">
                      <div class="item" data-option="1">
                        <div class="priceWrapper">
                          <div class="value" data-ip="8 000" data-company="10 000">
                            10 000
                          </div>
                          <div class="subtitle">
                            руб. в месяц
                            <span class="hint" data-tooltip="#ttPrice1"
                              >?
                              <div class="tooltip" id="ttPrice1">
                                Указана нижняя граница тарифа: до 100 операций в месяц и
                                до 3 работников.
                              </div>
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="item sale" data-option="2">
                        <div class="priceWrapper">
                          <div class="value prevPrice" data-ip="3 400" data-company="5 600">
                            5 600
                          </div>
                          <div class="value" data-ip="3 000" data-company="5 000">
                            5 000
                          </div>
                          <div class="subtitle">
                            руб. в месяц
                            <span class="hint" data-tooltip="#ttPrice2"
                              >?
                              <div class="tooltip" id="ttPrice2">
                                Указана нижняя граница тарифа: до 20 операций в месяц и до 3 работников. СКИДКА 10 % - подарок новым клиентам на первый месяц ведения.
                              </div>
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="item" data-option="3">
                        <div class="priceWrapper">
                          <div class="value" data-ip="8 000" data-company="10 000">
                            10 000
                          </div>
                          <div class="subtitle">
                            руб. в месяц
                            <span class="hint" data-tooltip="#ttPrice3"
                              >?
                              <div class="tooltip" id="ttPrice3">
                                Указана нижняя граница тарифа: до 20 операций в месяц и до 5 работников
                              </div>
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="item" data-option="4">
                        <div class="priceWrapper">
                         <div class="value" data-ip="18 000" data-company="20 000">
                            20 000
                          </div>
                          <div class="subtitle">
                            руб. в месяц
                            <span class="hint" data-tooltip="#ttPrice4"
                              >?
                              <div class="tooltip" id="ttPrice4">
                                Указана нижняя граница тарифа: до 20 операций в месяц, до 5 работников, до 5 часов работы юриста.
                              </div>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="content">
                      <div class="buh-table-row">
                        <div class="item text" data-option="1">
                          Только контроль
                          <span class="hint" data-tooltip="#ttBuh1"
                            >?
                            <div class="tooltip" id="ttBuh1">
                              Ваши сотрудники ведут вашу 1С — мы проверяем. Подходит для иногородних: можем контролировать вашу отчётность даже из других городов!
                            </div>
                          </span>
                        </div>
                        <div class="item text" data-option="2">
                          Ведение + 1С
                          <span class="hint" data-tooltip="#ttBuh2"
                            >?
                            <div class="tooltip" id="ttBuh2">
                              Вы предоставляете нам первичные документы, а мы ведём 1С в нашем ПО. Вы экономите: на 1С, рабочих местах, налогах с зарплаты штатных бухгалтеров. 
                            </div>
                          </span>
                        </div>
                        <div class="item text" data-option="3">
                          Ведение + 1С
                          <span class="hint" data-tooltip="#ttBuh3"
                            >?
                            <div class="tooltip" id="ttBuh3">
                              Вы предоставляете нам первичные документы, а мы ведём 1С в нашем ПО. Вы экономите на 1С, рабочих местах, налогах с зарплаты штатных бухгалтеров и кадровиков.
                            </div>
                          </span>
                        </div>
                        <div class="item text" data-option="4">
                          Ведение + 1С
                          <span class="hint" data-tooltip="#ttBuh4"
                            >?
                            <div class="tooltip" id="ttBuh4">
                             Вы предоставляете нам первичные документы, а мы ведём 1С в нашем ПО. Вы экономите на 1С, рабочих местах, налогах с зарплаты штатных бухгалтеров, кадровиков и юристов.
                            </div>
                          </span>
                        </div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item plus" data-option="1"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="2"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="3"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="4"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item plus" data-option="1"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="2"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="3"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="4"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item plus" data-option="1"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""> 
                          <span class="hint" data-tooltip="#ttContent"
                            >?
                            <div class="tooltip" id="ttContent">
                              + Найдём способы законно и безопасно снизить налоги. Для
                              81 % наших клиентов мы находим способы сократить налоги в
                              среднем до 22 %.
                            </div>
                          </span>
                        </div>
                        <div class="item plus" data-option="2"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="3"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="4"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item plus" data-option="1"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="2"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="3"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="4"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item" data-option="1"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item" data-option="2"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="3"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height="">
                            <span class="hint" data-tooltip="#ttBuh5"
                            >?
                            <div class="tooltip" id="ttBuh5">
                             Составим трудовые договоры, штатное расписание, кадровые приказы. Оформим кадровые локально-нормативные акты.
                            </div>
                          </span>
                        </div>
                        <div class="item plus" data-option="4"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""><span class="hint" data-tooltip="#ttBuh5"
                            >?</span></div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item" data-option="1"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item" data-option="2"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item" data-option="3"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="4"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item" data-option="1"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item" data-option="2"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item" data-option="3"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="4"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item" data-option="1"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item" data-option="2"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item" data-option="3"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="4"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item" data-option="1"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item" data-option="2"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item" data-option="3"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="4"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item" data-option="1"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item" data-option="2"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item" data-option="3"><img src="/wp-content/uploads/buh_table_minus.png" alt="plus icon" width="" height=""></div>
                        <div class="item plus" data-option="4"><img src="/wp-content/uploads/buh_table_plus.png" alt="plus icon" width="" height=""></div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item text" data-option="1">100%</div>
                        <div class="item text" data-option="2">100%</div>
                        <div class="item text" data-option="3">100%</div>
                        <div class="item text" data-option="4">100%</div>
                        <div class="item for-mobile" ></div>
                      </div>
                      <div class="buh-table-row">
                        <div class="item" data-option="1">
                          <a href="#buh-table-form"><button>Расчет стоимости</button></a>
                        </div>
                        <div class="item" data-option="2">
                          <a href="#buh-table-form"><button>Расчет стоимости</button></a>
                        </div>
                        <div class="item" data-option="3">
                          <a href="#buh-table-form"><button>Расчет стоимости</button></a>
                        </div>
                        <div class="item" data-option="4">
                          <a href="#buh-table-form"><button>Расчет стоимости</button></a>
                        </div>
                      </div>
                    </div>
                  </div>

                      <div class="tooltip" id="tooltipResponsibility">
                           Штрафы и пени по нашей вине — берём на себя.
                      </div>
                      <div class="tooltip" id="service1">
                            Составляем бухгалтерскую и налоговую отчетность с двойным контролем. Отвечаем на требования налоговой. Сопровождаем при проверке. Сдаём всё точно в срок.
                      </div>
                      <div class="tooltip" id="service2">
                            Рассчитываем зарплату, составляем и сдаём отчетность по сотрудникам
                      </div>
                      <div class="tooltip" id="service3">
                            Рассчитаем все налоги заранее. Без рисков, просрочек и штрафов. Берём всё общение с налоговой на себя.
                      </div>
                      <div class="tooltip" id="service4">
                            В рабочие дни бухгалтер будет всё время на связи по вопросам бухгалтерского учета и налогообложения в рамках текущей деятельности.
                      </div>
                </div>
                [lightbox id="buh-table-form" width="400px" padding="0px"]
                    [contact-form-7 id="11903" title="Форма Расчёт стоимости услуги таблицы бух.услуг"]
                [/lightbox]
        EOHTML;
        umbrella_add_custom_css_files($this->css_file);
        umbrella_add_custom_js_files($this->js_file);

        return ($html);
    }

}

function buhuslugi_packs_table_shortcode($atts)
{
    $shortcode = new buhuslugi_packs_table();
    return $shortcode->generate_shortcode();
}

add_shortcode('buhuslugi_packs_table', 'buhuslugi_packs_table_shortcode');