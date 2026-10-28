<?php

namespace App\Exceptions;

use RuntimeException;

class PppoeUserAlreadyExistsException extends RuntimeException
{
    public function __construct(public readonly string $username, string $routerMessage = '')
    {
        parent::__construct(
            "El usuario PPPoE '{$username}' ya existe en el router MikroTik."
            .($routerMessage !== '' ? " ({$routerMessage})" : '')
        );
    }
}
