<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

Namespace AtolV5\DataObjects;

use AtolV5\SdkException;
use Box\DataObjects\BaseDataObject;

class AtolV5ReceiptPosition extends BaseDataObject
{
    const
        TAX_NONE = 'none',
        TAX_VAT0 = 'vat0',
        TAX_VAT10 = 'vat10',
        TAX_VAT110 = 'vat110',
        TAX_VAT20 = 'vat20',
        TAX_VAT120 = 'vat120';

    const
        MEASURE_ITEMS = 0, // Применяется для предметов расчета, которые могут быть реализованы поштучно или единицами
        MEASURE_GRAMS = 10, // Грамм
        MEASURE_KILOS = 11, // Килограмм
        MEASURE_TONS = 12, // Тонна
        MEASURE_CENTIMETERS = 20, // Сантиметр
        MEASURE_DECIMETERS = 21, // Дециметр
        MEASURE_METERS = 22, // Метр
        MEASURE_SQUARE_CENTIMETERS = 30, // Квадратный сантиметр
        MEASURE_SQUARE_DECIMETERS = 31, // Квадратный дециметр
        MEASURE_SQUARE_METERS = 32, // Квадратный метр
        MEASURE_MILLILITERS = 40, // Миллилитр
        MEASURE_LITERS = 41, // Литр
        MEASURE_CUBIC_METERS = 42, // Кубический метр
        MEASURE_KILOWATT_HOURS = 50, // Киловатт час
        MEASURE_GIGACALORIES = 51, // Гигакалория
        MEASURE_DAYS = 70, // Сутки (день)
        MEASURE_HOURS = 71, // Час
        MEASURE_MINUTES = 72, // Минута
        MEASURE_SECONDS = 73, // Секунда
        MEASURE_KILOBYTES = 80, // Килобайт
        MEASURE_MEGABYTES = 81, // Мегабайт
        MEASURE_GIGABYTES = 82, // Гигабайт
        MEASURE_TERABYTES = 83, // Терабайт
        MEASURE_OTHER = 255; // Применяется при использовании иных единиц измерения

    /** @var string */
    protected $name;
    /** @var float */
    protected $price;
    /** @var int */
    protected $quantity;
    /** @var int */
    protected $measure;
    /** @var float */
    protected $sum;
    /** @var string */
    protected $payment_method;
    /** @var string */
    protected $payment_object;
    /** @var array */
    protected $vat;

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
        if (!in_array($vat, $this->getVats())) {
            throw new SdkException('Wrong vat');
        }

        $this->name = substr($name, 0, 128);
        $this->price = round($price, 2);
        $this->quantity = round($quantity, 0);
        if (!$sum) {
            $this->sum = round($this->quantity * $this->price, 2);
        } else {
            $this->sum = round($sum, 2);
        }
        // todo возможно нужно будет реализовать прокидывание единиц измерения с сайта
        $this->measure = self::MEASURE_ITEMS;
        $this->vat = ['type' => $vat, 'sum' => round($this->getVatAmount($this->sum, $vat), 2)];
        $this->payment_method = $payment_method;
        $this->payment_object = (int)$payment_object;
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
    protected function getVats()
    {
        return [
            self::TAX_NONE,
            self::TAX_VAT0,
            self::TAX_VAT10,
            self::TAX_VAT110,
            self::TAX_VAT20,
            self::TAX_VAT120,
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

    protected function getMeasure($measure)
    {
        return in_array($measure, $this->getMeasures()) ? $measure : self::MEASURE_OTHER;
    }

    /**
     * Получить все единицы измерения
     *
     * @return array
     */
    protected function getMeasures()
    {
        return [
            self::MEASURE_ITEMS, // Применяется для предметов расчета, которые могут быть реализованы поштучно или единицами
            self::MEASURE_GRAMS, // Грамм
            self::MEASURE_KILOS, // Килограмм
            self::MEASURE_TONS, // Тонна
            self::MEASURE_CENTIMETERS, // Сантиметр
            self::MEASURE_DECIMETERS, // Дециметр
            self::MEASURE_METERS, // Метр
            self::MEASURE_SQUARE_CENTIMETERS, // Квадратный сантиметр
            self::MEASURE_SQUARE_DECIMETERS, // Квадратный дециметр
            self::MEASURE_SQUARE_METERS, // Квадратный метр
            self::MEASURE_MILLILITERS, // Миллилитр
            self::MEASURE_LITERS, // Литр
            self::MEASURE_CUBIC_METERS, // Кубический метр
            self::MEASURE_KILOWATT_HOURS, // Киловатт час
            self::MEASURE_GIGACALORIES, // Гигакалория
            self::MEASURE_DAYS, // Сутки (день)
            self::MEASURE_HOURS, // Час
            self::MEASURE_MINUTES, // Минута
            self::MEASURE_SECONDS, // Секунда
            self::MEASURE_KILOBYTES, // Килобайт
            self::MEASURE_MEGABYTES, // Мегабайт
            self::MEASURE_GIGABYTES, // Гигабайт
            self::MEASURE_TERABYTES, // Терабайт
            self::MEASURE_OTHER // Применяется при использовании иных единиц измерения
        ];
    }
}
