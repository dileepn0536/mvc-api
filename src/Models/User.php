<?php

namespace Dileep\Mvc\Models;

class User
{
    public ?int $id;
    public string $name;
    public string $email;

    public function __construct(string $name, string $email, ?int $id=null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->id = $id;
    }
}