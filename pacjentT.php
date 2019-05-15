<?php

/**
 * Class pacjentT
 */
class pacjentT
{
    /**
     * @var int
     */
    public $idPacjenta = '';

    /**
     * @var string
     */
    public $pesel = '';

    /**
     * @var string
     */
    public $nazwisko = '';

    /**
     * @var string
     */
    public $imie = '';

    /**
     * @var dateTime
     */
    public $dataUrodzenia = '';

    /**
     * @var string
     */
    public $plec = '';

    /**
     * @var string
     * ___FOR_ZEND_minOccurs=0
     */
    public $kodPocztowy;

    /**
     * @var string
     * ___FOR_ZEND_minOccurs=0
     */
    public $miejscowosc;

    /**
     * @var string
     * ___FOR_ZEND_minOccurs=0
     */
    public $teryt;

    /**
     * @var string
     * ___FOR_ZEND_minOccurs=0
     */
    public $ulica;

    /**
     * @var string
     * ___FOR_ZEND_minOccurs=0
     */
    public $nrDomu;

    /**
     * @var string
     * ___FOR_ZEND_minOccurs=0
     */
    public $nrLokalu;

    /**
     * @var string
     * ___FOR_ZEND_minOccurs=0
     */
    public $email;

    /**
     * @var string
     * ___FOR_ZEND_minOccurs=0
     */
    public $telefon;

    /**
     * @var kaoz
     * ___FOR_ZEND_minOccurs=0
     * ___FOR_ZEND_maxOccurs=unbounded
     */
    public $kaoz;

}
