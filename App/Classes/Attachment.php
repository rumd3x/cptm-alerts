<?php
namespace CptmAlerts\Classes;

use Carbon\Carbon;

class Attachment
{
    const COLOR_DEFAULT = "#867d7d";
    const COLOR_SUCCESS = "#15d51b";
    const COLOR_DANGER = "#ce1a1a";
    const COLOR_WARNING = "#f4ae2d";
    const COLOR_INFO = "#00b0dd";

    /**
     * @param string $text
     * @var array
     */
    private $data;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->data = [
            'color' => self::COLOR_DEFAULT,
            'text' => $text,
            'fields' => [],
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return self
     */
    public function withFooter()
    {
        $this->data['ts'] = Carbon::now()->timestamp;
        $this->data['footer'] = 'CPTM Alert Service';
        return $this;
    }

    /**
     * @param string $color
     * @return self
     */
    public function setColor(string $color)
    {
        $this->data['color'] = $color;
        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @param string $title
     * @param string $text
     * @param boolean $short
     * @return self
     */
    public function addField(string $title, string $text, bool $short = false)
    {
        $this->data['fields'][] = [
            'title' => $title,
            'value' => $text,
            'short' => $short,
        ];
        return $this;
    }

    /**
     * @return string
     */
    public function toArray()
    {
        return $this->data;
    }
}
