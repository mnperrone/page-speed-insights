<?php

namespace App\Exceptions;

use Exception;

class PageSpeedException extends Exception
{
    protected $apiErrorCode;

    public function __construct($message = "", $code = 0, Throwable $previous = null, $apiErrorCode = null) {
        parent::__construct($message, $code, $previous);
        $this->apiErrorCode = $apiErrorCode;
    }

    public function getApiErrorCode() {
        return $this->apiErrorCode;
    }
}