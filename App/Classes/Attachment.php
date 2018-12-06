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
        $this->data = collect();
        $this->data->put('color', self::COLOR_DEFAULT);
        $this->data->put('text', $text);
        $this->data->put('fields', collect());
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
     * @param Field $field
     * @return self
     */
    public function addField(Field $field)
    {
        $this->data->get('fields')->push($field);
        return $this;
    }

    /**
     * @return string
     */
    public function toArray()
    {
        return $this->data->all();
    }
}
