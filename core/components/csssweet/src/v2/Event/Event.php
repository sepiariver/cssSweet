<?php
namespace CssSweet\v2\Event;

abstract class Event
{
    /** @var \CssSweet */
    protected $cs;

    /** @var \modX */
    protected $modx;

    /** @var array */
    protected $sp = [];

    /** @var bool */
    protected bool $debug = false;

    public function __construct(\CssSweet $cs, array $scriptProperties)
    {
        $this->cs =& $cs;
        $this->modx =& $this->cs->modx;
        $this->sp = $scriptProperties;
    }

    abstract public function run();

    protected function getOption($key, $default = null, $skipEmpty = false)
    {
        return $this->modx->getOption($key, $this->sp, $default, $skipEmpty);
    }

    protected function debugMsg(string $msg, mixed $data = null): void
    {
        if (!$this->debug) {
            return;
        }

        switch (gettype($data)) {
            case 'NULL':
                $formattedData = null;
                break;
            case 'array':
            case 'object':
                $formattedData = '<pre>' . print_r($data, true) . '</pre>';
                break;
            default:
                $formattedData = $data;
                break;
        };

        echo "[" . Event::class . "]: " . $msg . ' ' . $formattedData . '<br />';
    }
}