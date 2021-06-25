<?php

class SU_welcome_screen_conditions
{

    public $atts;
    private $columns_number=4;
    private $css_files = ['/assets/css/blocks/su-welcome-screen-conditions.css'];
    public $err = "<ul style='padding: 24px;'> Неправильное использование шорткода: ";
    private $headers;
    private $sub_headers;
    private $button_texts;
    private $button_links;

    public function fill_attributes()
    {
        $err_initial_len = strlen($this->err);
        for ($i = 0; $i <= $this->columns_number-1; $i++) {
            $i_frontend = $i+1;
            $this->fill_variable('header_'.$i_frontend, $this->headers[$i], true, "Заголовок ".$i_frontend, "Цена");
            $this->fill_variable('sub_header_'.$i_frontend, $this->sub_headers[$i], true, "Подзаголовок ".$i_frontend, "Цена");
            $this->fill_variable('button_text_'.$i_frontend, $this->button_texts[$i], false, "Текст кнопки ".$i_frontend, "Расчёт стоимости");
            if (isset($this->button_texts[$i])){ $this->fill_variable('button_link_'.$i_frontend, $this->button_links[$i], true, "Ссылка кнопки ".$i_frontend, "'#calculate-price-contact-form-lightbox'");}
        }
        if (strlen($this->err) > $err_initial_len) {
            $this->err .= '</ul>' . umbrella_add_custom_css_files($this->css_files);
            return false;
        } else {
            return true;
        }
    }

    private function fill_variable($attribute, &$variable, $obligatory, $name, $sample_value)
    {
        if (isset($this->atts[$attribute])) {
            $variable = $this->atts[$attribute];
        } elseif ($obligatory) {
            $this->err .= <<<EOHTML
                <li> <strong>$name</strong> не задан. Используйте атрибут <span id='codebox'>$attribute</span>. Например <span id='codebox'>$attribute=$sample_value</span> </li>
                EOHTML;
        }
    }

    public function generate_shortcode()
    {
        $columns = '';
        for ($i = 0; $i <= $this->columns_number-1; $i++) {
            $width =  12/$this->columns_number;
            $mobile_width = 12;
            if (isset($this->button_texts[$i])) {
                $button = '[button style="outline" expand="true" text="'.$this->button_texts[$i].'" link="'.$this->button_links[$i].'"]';
            } else {
                $button = '';
            }
            $header = $this->headers[$i];
            $subheader = $this->sub_headers[$i];
            $column = <<<EOHTML
                [col span="$width" span__sm="$mobile_width" class="su_welcome_screen_conditions_column"]
                    <div class="su_welcome_screen_conditions_header">$header</div>
                    <div class="su_welcome_screen_conditions_subheader">$subheader</div>
                    <div class="su_welcome_screen_conditions_button">$button</div>
                [/col]
            EOHTML;
            $columns .= $column;
        }
        umbrella_add_custom_css_files($this->css_files);
        return <<<EOHTML
               [row col_style="divided" class="su_welcome_screen_conditions"]
                $columns
               [/row]
        EOHTML;

    }
}

function su_welcome_screen_conditions_shortcode($atts)
{
    $shortcode = new SU_welcome_screen_conditions();
    $shortcode->atts = $atts;
    if (!$shortcode->fill_attributes()) {
        return $shortcode->err;
    }
    return $shortcode->generate_shortcode();
}

add_shortcode('su_welcome_screen_conditions', 'su_welcome_screen_conditions_shortcode');