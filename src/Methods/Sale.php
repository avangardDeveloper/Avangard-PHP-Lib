<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace Avangard\Methods;

use Avangard\ApiClient;

/**
 * Trait Sale
 * @package Avangard\Methods
 */
trait Sale
{
    /**
     * Validate response from PS
     *
     * @param array $params
     * @return bool
     */
    public function isCorrectHash($params = array())
    {
        if (empty($params['order_number']) ||
            empty($params['amount']) ||
            empty($params['signature'])) {
            return false;
        }

        $signature = strtoupper(
            MD5(
                strtoupper(
                    MD5(
                        $this->server_sign
                    ) .
                    MD5(
                        $this->shop_id .
                        $params['order_number'] .
                        $params['amount']
                    )
                )
            )
        );

        return $signature == $params['signature'];
    }

    /**
     * Send sale receipt into box
     *
     * @param array $data
     * @return mixed
     */
    public function sendBill($data = array())
    {
        if(!$this->isBox()) {
            throw new \InvalidArgumentException(
                "sendBill: box is't connected"
            );
        }

        if(empty($data)) {
            throw new \InvalidArgumentException(
                "sendBill: empty params"
            );
        }

        $result = $this->getBox()->saveBill($data);

        return $result;
    }

    /**
     * Send refund receipt into box
     *
     * @param array $data
     * @return mixed
     */
    public function refundBill($data = array())
    {
        if(!$this->isBox()) {
            throw new \InvalidArgumentException(
                "refundBill: box is't connected"
            );
        }

        if(empty($data)) {
            throw new \InvalidArgumentException(
                "refundBill: empty params"
            );
        }

        $result = $this->getBox()->refundBill($data);

        return $result;
    }

    /**
     * Get boxes payment methods
     *
     * @return array
     */
    public function getPaymentMethod()
    {
        if(!$this->isBox()) {
            throw new \InvalidArgumentException(
                "refundBill: box is't connected"
            );
        }

        $result = $this->getBox()->getPaymentMethods();

        return $result;
    }

    /**
     * Get boxes payment objects
     *
     * @return array
     */
    public function getPaymentObject()
    {
        if(!$this->isBox()) {
            throw new \InvalidArgumentException(
                "refundBill: box is't connected"
            );
        }

        $result = $this->getBox()->getPaymentObjects();

        return $result;
    }

    /**
     * Get boxes taxation systems
     *
     * @return array
     */
    public function getTaxationSystem()
    {
        if(!$this->isBox()) {
            throw new \InvalidArgumentException(
                "refundBill: box is't connected"
            );
        }

        $result = $this->getBox()->getTaxationSystemы();

        return $result;
    }

    /**
     * Get boxes vats
     *
     * @return array
     */
    public function getVats()
    {
        if(!$this->isBox()) {
            throw new \InvalidArgumentException(
                "refundBill: box is't connected"
            );
        }

        $result = $this->getBox()->getVats();

        return $result;
    }
}