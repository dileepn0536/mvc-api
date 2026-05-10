<?php

namespace Dileep\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Dileep\Mvc\Services\CachedUserService;
use Dileep\Mvc\Interfaces\UserServiceInterface;
use Dileep\Mvc\Core\Cache;

class CachedUserServiceTest extends TestCase
{
    private $mockService;
    private $mockCache;
    private CachedUserService $cachedService;

    protected function setUp(): void
    {
        $this->mockService = $this->createMock(UserServiceInterface::class);
        $this->mockCache   = $this->createMock(Cache::class);

        $this->cachedService = new CachedUserService(
            $this->mockService,
            $this->mockCache
        );
    }

    public function test_get_users_returns_cached_data(): void
    {
        $cachedUsers = [['id' => 1, 'name' => 'Dileep']];

        $this->mockCache
            ->expects($this->once())
            ->method('get')
            ->willReturn($cachedUsers);

        $this->mockService
            ->expects($this->never())
            ->method('getUsers');

        $result = $this->cachedService->getUsers(20, 0);
        $this->assertEquals($cachedUsers, $result);
    }

    public function test_get_users_hits_db_on_cache_miss(): void
    {
        $dbUsers = [['id' => 1, 'name' => 'Dileep']];

        $this->mockCache
            ->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $this->mockService
            ->expects($this->once())
            ->method('getUsers')
            ->willReturn($dbUsers);

        $this->mockCache
            ->expects($this->once())
            ->method('set');

        $result = $this->cachedService->getUsers(20, 0);
        $this->assertEquals($dbUsers, $result);
    }
}