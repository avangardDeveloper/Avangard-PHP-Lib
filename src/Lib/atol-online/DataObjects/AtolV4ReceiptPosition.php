<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace AtolV4\DataObjects;

use AtolV4\SdkException;
use Box\DataObjects\BaseDataObject;

class AtolV4ReceiptPosition extends BaseDataObject
{

    const
        TAX_NONE = 'none',
        TAX_VAT0 = 'vat0',
        TAX_VAT10 = 'vat10',
        TAX_VAT20 = 'vat20',
        TAX_VAT110 = 'vat110',
        TAX_VAT120 = 'vat120';

    /** @var float */
    protected $sum;
    /** @var array */
    protected $vat;
    /** @var string */
    protected $name;
    /** @var float */
    protected $price;
    /** @var int */
    protected $quantity;
    /** @var string */
    protected $payment_method = '';
    /** @var string */
    protected $payment_object = '';

    /**
     * @param string $name Описание товара
     * @param float $price Цена единицы товара
     * @param int $quantity Количество товара
     * @param string $vat Налоговая ставка из констант
     * @param float $sum Сумма количества товаров. Передается если количество * цену товара не равно sum
     * @throws SdkException
     */
    public function __construct($name, $price, $quantity, $vat, $sum = null, $payment_method = null, $payment_object = null)
    {
        if (!in_array($vat, $this->getVates())) {
            throw new SdkException('Wrong vat');
        }

        $this->name = $name;
        $this->price = round($price, 2);
        $this->quantity = round($quantity, 0);
        if (!$sum) {
            $this->sum = round($this->quantity * $this->price, 2);
        } else {
            $this->sum = round($sum, 2);
        }
        $this->vat = ['type' => $vat, 'sum' => round($this->getVatAmount($this->sum, $vat), 2)];

        $this->payment_method = $payment_method;

        $this->payment_object = $payment_object;
    }

    /**
     * Получить сумму позиции
     * @return float
     */
    public function getPositionSum()
    {
        return $this->sum;
    }

    /**
     * Получить все возможные налоговые ставки
     */
    protected function getVates()
    {
        return [
            self::TAX_NONE,
            self::TAX_VAT0,
            self::TAX_VAT10,
            self::TAX_VAT110,
            self::TAX_VAT120,
            self::TAX_VAT20,
        ];
    }

    /**
     * Получить сумму налога
     * @param float $amount
     */
    protected function getVatAmount($amount, $vat)
    {
        switch ($vat) {
            case self::TAX_NONE:
            case self::TAX_VAT0:
                return round(0, 2);
            case self::TAX_VAT10:
            case self::TAX_VAT110:
                return round($amount * 10 / 110, 2);
            case self::TAX_VAT20:
            case self::TAX_VAT120:
                return round($amount * 20 / 120, 2);
            default :
                throw new SdkException('Unknown vat');
        }
    }
}
