<?php

namespace Dileep\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Dileep\Mvc\Validators\UserValidator;

class UserValidatorTest extends TestCase
{
    private UserValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new UserValidator();
    }

    public function test_valid_user_passes(): void
    {
        $result = $this->validator->validate([
            'name'  => 'Dileep',
            'email' => 'dileep@gmail.com'
        ]);
        $this->assertTrue($result);
    }

    public function test_empty_name_fails(): void
    {
        $result = $this->validator->validate([
            'name'  => '',
            'email' => 'dileep@gmail.com'
        ]);
        $this->assertFalse($result);
    }

    public function test_invalid_email_fails(): void
    {
        $result = $this->validator->validate([
            'name'  => 'Dileep',
            'email' => 'invalid-email'
        ]);
        $this->assertFalse($result);
    }

    public function test_empty_email_fails(): void
    {
        $result = $this->validator->validate([
            'name'  => 'Dileep',
            'email' => ''
        ]);
        $this->assertFalse($result);
    }
}