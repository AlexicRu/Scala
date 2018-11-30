<?php

trait Log
{
    protected $_logErrorExecute     = -1; //операция завершена неуспешно
    protected $_logErrorArguments   = -2; //ошибка в аргументах запуска
    protected $_logErrorAuth        = -3; //невозможно залогиниться

    protected $_agentId;
    protected $_tubeId;
    protected $_logFile;

    protected $_cardId      = null;
    protected $_customerId  = null;

    /**
     * логируем
     *
     * @param $logString
     * @param null $exitCodeMsg
     * @return bool
     */
    protected function _log($logString, $exitCodeMsg = null)
    {
        if (empty($this->_logFile)) {
            return false;
        }

        while (!($handle = fopen($this->_logFile, "a")) or !flock($handle, LOCK_EX | LOCK_NB)) {
            sleep(1);
            $this->_log("waiting for logfile exclusive lock");
        }

        if ($handle) {
            $logDate = date("Y-m-d H:i:s");
            $mypId = getmypid();

            $string =
                $logDate . "|" .
                $this->_agentId . "_" . $this->_tubeId . "|" .
                $mypId . "|" .
                ($this->_customerId !== null ? $this->_customerId . "|" : "") .
                ($this->_cardId !== null ? $this->_cardId . "|" : "") .
                trim($logString) .
                ($exitCodeMsg !== null ? "|" . $exitCodeMsg : "") .
                "\n"
            ;

            if (fwrite($handle, $string)) {
                fclose($handle);

                if ($exitCodeMsg !== null) {
                    die($exitCodeMsg . "\n");
                }
                return true;
            }
        }

        fclose($handle);
        flock($handle, LOCK_UN);

        if ($exitCodeMsg !== null) {
            die($exitCodeMsg . "\n");
        }
        return false;
    }
}