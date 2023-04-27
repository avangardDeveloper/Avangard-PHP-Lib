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
     * @var string
     */
    private $payment_object;

    public function __construct($name, $price, $quantity, $sum)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->sum = $sum;
    }

    public static function delivery($name, $price, $quantity, $sum)
    {
        $delivery = new ReceiptItemEntity(
            $name,
            $price,
            $quantity,
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