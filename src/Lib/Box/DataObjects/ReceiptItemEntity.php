<?php

namespace Box\DataObjects;

class ReceiptItemEntity extends BaseDataObject
{
    const DELIVERY_PAYMENT_OBJECT = 'service';

    /**
     * @var string
     */
    private $name;
    /**
     * @var float
     */
    private $quantity;
    /**
     * @var float
     */
    private $price;
    /**
     * @var float
     */
    private $sum;
    /**
     * @var string
     */
    private $payment_object;
    /**
     * @var string|int
     */
    private $measure;

    public function __construct($name, $quantity, $price, $sum)
    {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->sum = $sum;
    }

    public static function delivery($name, $quantity, $price, $sum)
    {
        $delivery = new ReceiptItemEntity(
            $name,
            $quantity,
            $price,
            $sum
        );

        $delivery->setPaymentObject(self::DELIVERY_PAYMENT_OBJECT);

        return $delivery;
    }

    /**
     * @param string $payment_object
     */
    public function setPaymentObject($payment_object)
    {
        $this->payment_object = $payment_object;
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
     * @return string
     */
    public function getPaymentObject()
    {
        return $this->payment_object;
    }
}