<?php

trait Debug
{
    private $_debug         = false;
    private $_debugMT       = [];
    private $_debugT        = false;

    /**
     * стартуем дебаг
     */
    protected function _debugStart()
    {
        if ($this->_debug) {
            $this->_debugT = microtime(true);
            $this->_debugMT = [['start', 0, 0]];
            ob_start();
        }
    }

    /**
     * выводим дебаг
     *
     * @param $string
     */
    protected function _debug($string)
    {
        if ($this->_debug) {
            $mt = microtime(true) - $this->_debugT;

            $this->_debugMT[] = [
                $string,
                $mt,
                $mt - $this->_debugMT[count($this->_debugMT) - 1][1]
            ];

            $cnt = count($this->_debugMT);

            echo $cnt . ': ' . print_r($this->_debugMT[$cnt - 1], true) . "\n";
            ob_flush();
        }
    }
}