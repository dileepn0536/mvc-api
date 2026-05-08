<?php

namespace Dileep\Mvc\Validators;

use Dileep\Mvc\Core\Validator;

class UserValidator extends Validator
{
    public function validate(array $data): bool
    {
        $this->errors = []; // Reset errors before validation

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;

        if (empty($name)) {
            $this->addError('Name is required.');
        }

        if (empty($email)) {
            $this->addError('Email is required.');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError('Invalid email format.');
        }

        return empty($this->errors);
    }
}