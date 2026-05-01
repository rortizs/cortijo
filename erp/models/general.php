<?php

/**
 * POS / Class General
 * @author Richard Sasvin
 * @version 2.1 20260430
 */
class General {

    /** CONSTRUCTORES
     *
     */
    public $timeZone;
    public $resultado;
    public $date;
    public $dateViews;
    public $time;
    public $timestamp;
    public $timestampViews;

    /** INICIALIZACION DE CONSTRUCTORES
     *
     */
    public function __construct() {
        $this->timeZone = date_default_timezone_set("America/Guatemala");
        $this->resultado = array();
        $this->date = date("Y-m-d");
        $this->dateViews = date("d-m-Y");
        $this->time = date("H:i:s");
        $this->timestamp = date("Y-m-d H:i:s");
        $this->timestampViews = date("d-m-Y H:i:s");
    }

}
