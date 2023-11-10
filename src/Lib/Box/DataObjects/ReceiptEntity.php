<?php

namespace Box\DataObjects;

class ReceiptEntity extends BaseDataObject
{
    /**
     * @var string
     */
    private $id;
    /**
     * Timestamp
     *
     * @var int
     */
    private $time;
    /**
     * @var ClientEntity
     */
    private $client;
    /**
     * @var ReceiptItemEntity[]
     */
    private $items;
    /**
     * @var float
     */
    private $total;

    public function __construct($id, $time)
    {
        $this->id = $id;
        $this->time = $time;
    }

    public function addClient(ClientEntity $client)
    {
        $this->client = $client;
    }

    public function addReceiptItem(ReceiptItemEntity $item)
    {
        $this->items[] = $item;
    }

    public function addTotal($total)
    {
        $this->total = $total;
    }

    public function getClientEmail()
    {
        return $this->client->getEmail();
    }

    public function getClientName()
    {
        return $this->client->getName();
    }

    public function getClientPhone()
    {
        return $this->client->getPhone();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return ReceiptItemEntity[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }
}