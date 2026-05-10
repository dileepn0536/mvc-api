<?php

namespace Dileep\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Dileep\Mvc\Core\ExceptionHandler;
use Dileep\Mvc\Exceptions\NotFoundException;
use Dileep\Mvc\Exceptions\ValidationException;
use Dileep\Mvc\Exceptions\UnauthorizedException;

class ExceptionHandlerTest extends TestCase
{
    private ExceptionHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new ExceptionHandler();
    }

    public function test_handles_not_found_exception(): void
    {
        $exception = new NotFoundException("User not found");
        $result    = $this->handler->handle($exception);

        $this->assertFalse($result['status']);
        $this->assertEquals("User not found", $result['message']);
        $this->assertEquals(404, $result['code']);
    }

    public function test_handles_validation_exception(): void
    {
        $exception = new ValidationException("Email is invalid");
        $result    = $this->handler->handle($exception);

        $this->assertFalse($result['status']);
        $this->assertEquals("Email is invalid", $result['message']);
        $this->assertEquals(422, $result['code']);
    }

    public function test_handles_unauthorized_exception(): void
    {
        $exception = new UnauthorizedException("Unauthorized");
        $result    = $this->handler->handle($exception);

        $this->assertFalse($result['status']);
        $this->assertEquals("Unauthorized", $result['message']);
        $this->assertEquals(401, $result['code']);
    }

    public function test_handles_generic_exception(): void
    {
        $exception = new \Exception("Something went wrong");
        $result    = $this->handler->handle($exception);

        $this->assertFalse($result['status']);
        $this->assertEquals("Internal server error", $result['message']);
        $this->assertEquals(500, $result['code']);
    }
}