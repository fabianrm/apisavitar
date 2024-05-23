<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ModelNotFoundException extends Exception
{
    protected $message;

    public function __construct($message = "Model not found")
    {
        $this->message = $message;
        parent::__construct($this->message);
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => $this->message
        ], 404);
    }
}
