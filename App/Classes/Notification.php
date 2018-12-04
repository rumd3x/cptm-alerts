<?php
namespace CptmAlerts\Classes;

use Carbon\Carbon;

class Notification {
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
    public function __construct(string $text) {
        $this->data = [
            'color' => self::COLOR_DEFAULT,
            'text' => $text,
            'ts' => Carbon::now()->timestamp,
            'footer' => 'CPTM Alert Service',
            'fields' => [],
        ];
    }

    /**
     * @param string $color
     * @return self
     */
    public function setColor(string $color) {
        $this->data['color'] = $color;
        return $this;
    }

    /**
     * @param string $title
     * @param string $text
     * @param boolean $short
     * @return self
     */
    public function addField(string $title, string $text, bool $short) {
        $this->data['fields'][] = [
            'title' => $title,
            'text' => $text,
            'short' => $short
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function toAttachments() {
        return [
            'attachments' => json_encode([$this->data])
        ];
    }
}