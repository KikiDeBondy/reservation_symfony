<?php

namespace App\Exception;

use Exception;

class RegisterValidationException extends Exception
{
    private array $errors;

    // Constructeur avec un tableau d'erreurs
    public function __construct(array $errors, string $message = "Registration validation failed", int $code = 0, Exception $previous = null)
    {
        // Si tu veux un message d'erreur par défaut, tu peux le définir ici.
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    // Méthode pour récupérer les erreurs
    public function getErrors(): array
    {
        return $this->errors;
    }
}