<?php

class User
{
    public $id;
    public $name;
    public $email;

    public function __construct(string $name, string $email, ?int $id=null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->id = $id;
    }
}