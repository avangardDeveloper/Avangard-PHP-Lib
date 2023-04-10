<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

Namespace Atol102\Services\Request;

class GetStatusRequest extends BaseServiceRequest
{

    /** @var string */
    protected $groupCode;
    /** @var string */
    protected $uuId;
    /** @var string */
    protected $token;

    /**
     * @inheritdoc
     */
    public function getRequestUrl($test = false)
    {
        return ($test ? self::TEST_REQUEST_URL : self::REQUEST_URL) . $this->groupCode . '/report/' . $this->uuId . '?tokenid=' . $this->token;
    }

    /**
     * @param string $groupCode
     * @param string $uuId
     * @param string $token
     */
    public function __construct($groupCode, $uuId, $token)
    {
        $this->groupCode = $groupCode;
        $this->uuId = $uuId;
        $this->token = $token;
    }

    public function getParameters()
    {
        return [];
    }
}
