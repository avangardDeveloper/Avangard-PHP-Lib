<?php

namespace Box\DataObjects;

class ReceiptItemEntity extends BaseDataObject
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var float
     */
    private $price;
    /**
     * @var float
     */
    private $quantity;
    /**
     * @var string|int
     */
    private $measure;
    /**
     * @var float
     */
    private $sum;
    /**
     * @var bool
     */
    private $isDelivery;

    private function __construct($name, $price, $quantity, $sum)
    {
        $this->name = $this->convertUTF8($name);
        $this->price = $price;
        $this->quantity = $quantity;
        $this->sum = $sum;
    }

    public static function product($name, $price, $quantity, $sum)
    {
        $product = new ReceiptItemEntity(
            $name,
            $price,
            $quantity,
            $sum
        );

        $product->isDelivery = false;

        return $product;
    }

    public static function delivery($name, $price, $sum)
    {
        $delivery = new ReceiptItemEntity(
            $name,
            $price,
            1,
            $sum
        );

        $delivery->isDelivery = true;

        return $delivery;
    }

    /**
     * @param int|string $measure
     */
    public function setMeasure($measure)
    {
        $this->measure = $measure;
    }

    /**
     * @return int|string
     */
    public function getMeasure()
    {
        return $this->measure;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @return bool
     */
    public function isDelivery()
    {
        return $this->isDelivery;
    }
}