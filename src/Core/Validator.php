<?php

namespace Dileep\Mvc\Core;

abstract class Validator
{
    protected array $errors = [];

    abstract public function validate(array $data): bool;

    public function getErrors(): array
    {
        return $this->errors;
    }

    protected function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    public function getFirstError(): ?string
    {
        return $this->errors[0] ?? null;
    }
}