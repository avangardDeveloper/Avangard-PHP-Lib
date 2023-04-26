<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace AtolV4\Services;

use stdClass;

class GetTokenResponse extends BaseServiceResponse
{
    /** @var string */
    public $token;

    public function __construct(stdClass $response)
    {
        if (!empty($response->error)) {
            $this->errorCode = $response->error->code;
            $this->errorDescription = $response->error->text;
        }

        parent::__construct($response);
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->token;
    }
}
