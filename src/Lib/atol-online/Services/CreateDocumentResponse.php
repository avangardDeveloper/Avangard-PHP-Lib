<?php
/**
 * Copyright Bykovskiy Maxim. Avangard (c) 2019.
 */

namespace AtolV4\Services;

use stdClass;

class CreateDocumentResponse extends BaseServiceResponse
{

    /** @var string Уникальный идентификатор */
    public $uuid;

    /** @var string */
    public $status;

    public function __construct(stdClass $response)
    {
        if (!empty($response->error)) {
            $this->errorCode = $response->error->code;
            $this->errorDescription = $response->error->text;
        }

        parent::__construct($response);
    }
}
