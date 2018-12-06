<?php
namespace CptmAlerts\Classes;

class Field
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $value;

    /**
     * @var bool
     */
    public $short;

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @param string $type
     * @param string $title
     * @param string $value
     */
    public function __construct(string $type, string $title, string $value = '')
    {
        $this->short = strtolower($type) === 'short';
        $this->title = $title;
        $this->value = $value;
    }
}
