<?php

class video_block
{
    private $css_file = ['/assets/css/blocks/video-block.css'];
    private $link;
    private $duration;
    private $header;
    private $fio;
    private $photo;
    private $text;

    public $atts;
    public $err;

    public function fill_attributes()
    {
        if (isset($this->atts['link']) && isset($this->atts['header']) && isset($this->atts['duration']) && isset($this->atts['fio']) && isset($this->atts['photo']) && isset($this->atts['text'])) {
            $this->link = $this->atts['link'];
            $this->duration = $this->atts['duration'];
            $this->header = $this->atts['header'];
            $this->fio = $this->atts['fio'];
            $this->photo = $this->atts['photo'];
            $this->text = $this->atts['text'];
            return true;
        } else {
            $this->err = 'Not enough attributes. Here is the correct shortcode: [video_block link="https://www.youtube.com/embed/pJikk8r9Aj8" header="Заголовок" duration="5 минут" fio="<b>Тарасова Юлия Сергеевна</b>, руководитель отдела бухгалтерского обслуживания ГК Umbrella Group" photo="123"  text="test text"]';
            return false;
        }
    }

    public function generate_shortcode()
    {
        $image_attributes = wp_get_attachment_image_src($this->photo, 'full');
        $this->photo = '<img src="' . $image_attributes[0] . '"  />';
        $html = <<<EOHTML
        <div class="video-block-outer">
            <h3>$this->header</h3>
            <div class="video-block"> 
                <div class="video-description show-for-small">
                    <div class="video-duration">
                        $this->duration
                    </div>
                    <div class="video-author">
                        <div class="photo">
                            $this->photo
                        </div>
                        <div class="fio">
                            Рассказывает $this->fio
                        </div>
                    </div>
                </div>  
                <div class="video-frame">
                    <iframe width="100%" height="100%" src="$this->link" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>   
                </div>
                <div class="video-description ">
                    <div class="video-duration hide-for-small">
                     $this->duration
                    </div>
                    <div class="video-author hide-for-small">
                        <div class="photo">
                            $this->photo
                        </div>
                        <div class="fio">
                            Рассказывает $this->fio
                        </div>
                    </div>
                    <div class="video-text">
                        $this->text
                    </div>
                </div>
            </div>
        </div>
        EOHTML;

        umbrella_add_custom_css_files($this->css_file);
        return do_shortcode($html);
    }

}

function video_block_shortcode($atts)
{
    $shortcode = new video_block();
    $shortcode->atts = $atts;
    if (!$shortcode->fill_attributes()) {
        return $shortcode->err;
    }
    return $shortcode->generate_shortcode();
}

add_shortcode('video_block', 'video_block_shortcode');