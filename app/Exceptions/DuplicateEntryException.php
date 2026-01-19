<?php

namespace App\Exceptions;

use Exception;

class DuplicateEntryException extends Exception
{
    protected $customMessage;
    protected $errorCode;

    public function __construct(string $message = '', string $errorCode = '')
    {
        $this->customMessage = $message;
        $this->errorCode = $errorCode;
        parent::__construct($message);
    }

    public function getCustomMessage(): string
    {
        return $this->customMessage;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->customMessage,
            'error_code' => $this->errorCode
        ], 409); // HTTP 409 Conflict
    }
}