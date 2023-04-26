<?php

namespace Box\DataObjects;

abstract class BaseCompany extends BaseDataObject
{
    /**
     * Система налогообложения
     *
     * @var string
     */
    private $sno;

    /**
     * Налоговая ставка
     *
     * @var
     */
    private $vat;

    public function __construct($sno, $vat)
    {
        $this->sno = $sno;
        $this->vat = $vat;
    }

    /**
     * @return string
     */
    public function getSno()
    {
        return $this->sno;
    }

    /**
     * @return mixed
     */
    public function getVat()
    {
        return $this->vat;
    }
}