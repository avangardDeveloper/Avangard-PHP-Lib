<?php

namespace Box\DataObjects;

class ClientEntity extends BaseDataObject
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $phone;
    /**
     * @var string
     */
    private $email;

    public function __construct($name)
    {
        $this->name = $this->convertUTF8($name);
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}