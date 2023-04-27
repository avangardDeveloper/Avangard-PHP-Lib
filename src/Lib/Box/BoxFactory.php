<?php

namespace Box;

use Box\DataObjects\BaseAuth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class BoxFactory
{
    /**
     * @param BaseAuth|null $boxAuth
     * @param Client $client
     * @return AtolonlineV4|AtolonlineV5|Orangedata|null
     * @throws GuzzleException
     */
    public static function createBox($boxAuth, $client) {
        if (empty($boxAuth)) {
            return null;
        }

        switch ($boxAuth->getBoxType()) {
            case BaseBox::ATOL_V4:
                return new AtolonlineV4($boxAuth, $client);
            case BaseBox::ATOL_V5:
                return new AtolonlineV5($boxAuth, $client);
            case BaseBox::ORANGEDATA:
                return new Orangedata($boxAuth, $client);
            case BaseBox::NONEBOX:
            default:
                return null;
        }
    }
}