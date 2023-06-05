<?php
namespace CssSweet\v2\Snippet;

abstract class Snippet
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
        $this->debug = (bool)$this->getOption('debug', 0);
    }

    abstract public function process();

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

        echo "[" . Snippet::class . "]: " . $msg . ' ' . $formattedData . '<br />';
    }
}
