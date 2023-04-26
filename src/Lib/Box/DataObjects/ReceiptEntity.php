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

    /*
     * $receipt = [
            'id' => (string)$order['order_id'],
            'time' => date("Y-m-d H:i:s"),
            'client' => [
                'name' => $order['payment_firstname'] . ' ' . $order['payment_lastname'],
                'phone' => $order['telephone'],
                'email' => $order['email']
            ],
            'items' => [],
            'total' => $order['total']
        ];

        $this->load->model('checkout/order');

        $order_products = $this->model_checkout_order->getOrderProducts($_POST['order_number']);

        foreach ($order_products as $product) {
            $receipt['items'][] = [
                'name' => $product['name'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'sum' => $product['total'],
                'payment_object' => $this->payment_object,
                'payment_method' => $this->payment_method,
                'vat' =>  $this->nds
            ];
        }

        $orderTotals = $this->model_checkout_order->getOrderTotals($_POST['order_number']);

        $deliveryPrice = 0;
        foreach($orderTotals as $total){
            if($total['code'] == 'shipping'){
                $deliveryPrice = $total['value'];
                $deliveryFullName = $total['title'];
            }
        }

        if ($deliveryPrice > 0) {
            $receipt['items'][] = [
                "name" => $deliveryFullName,
                "price" => round($deliveryPrice),
                "quantity" => 1,
                "sum" => round($deliveryPrice),
                "payment_object" => 'service',
                "payment_method" => $this->payment_method,
                "vat" => $this->nds,
            ];
        }
     * */
}