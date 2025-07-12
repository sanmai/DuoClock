<?php

/**
 * Copyright 2025 Alexey Kopytko <alexey@kopytko.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace Tests\DuoClock;

use DuoClock\FrozenClock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use Tests\DuoClock\Fixtures\ExampleUsingSleep;
use Tests\DuoClock\Fixtures\ExampleUsingTime;

#[CoversClass(FrozenClock::class)]
class FrozenClockTest extends TestCase
{
    public function testSetTimeUpdatesTime(): void
    {
        $clock = new FrozenClock();
        $clock->setTime(1234567890);
        $this->assertSame(1234567890, $clock->time());
        $this->assertSame(1234567890.0, $clock->microtime());
    }

    public function testConstructorDefaultsToZero(): void
    {
        $clock = new FrozenClock();
        $this->assertSame(0, $clock->time());
        $this->assertSame(0.0, $clock->microtime());
    }

    public function testConstructorWithExplicitTime(): void
    {
        $clock = new FrozenClock(1234567890.5);
        $this->assertSame(1234567890, $clock->time());
        $this->assertSame(1234567890.5, $clock->microtime());
    }

    public function testMicrotimeReturnsCurrentUnixTimestampWithMicroseconds(): void
    {
        $clock = new FrozenClock();
        $clock->setTime(1234567890.123456);
        $this->assertSame(1234567890.123456, $clock->microtime());
    }

    public function testNowReturnsDateTimeImmutable(): void
    {
        $clock = new FrozenClock(1234567890);
        $now = $clock->now();
        $this->assertInstanceOf(DateTimeImmutable::class, $now);
        $this->assertSame(1234567890, $now->getTimestamp());
    }

    public function testNowDoesNotPreserveSubseconds(): void
    {
        $clock = new FrozenClock(1234567890.123456);
        $now = $clock->now();
        $this->assertSame(1234567890, $now->getTimestamp());
        $this->assertSame('1234567890.000000', $now->format('U.u'));
    }

    public function testSleepAdvancesTime(): void
    {
        $clock = new FrozenClock(100);
        $result = $clock->sleep(10);
        $this->assertSame(0, $result);
        $this->assertSame(110, $clock->time());
        $this->assertSame(110.0, $clock->microtime());
    }

    public function testUsleepAdvancesTimeByMicroseconds(): void
    {
        $clock = new FrozenClock(100.5);
        $clock->usleep(500000); // 0.5 seconds
        $this->assertSame(101, $clock->time());
        $this->assertSame(101.0, $clock->microtime());
    }

    public function testUsleepWithSmallMicroseconds(): void
    {
        $clock = new FrozenClock(100.123);
        $clock->usleep(1000); // 0.001 seconds
        $this->assertSame(100, $clock->time());
        $this->assertEqualsWithDelta(100.124, $clock->microtime(), 0.000001);
    }

    public function testExampleUsingTime(): void
    {
        $mock = $this->createMock(FrozenClock::class);

        $mock->expects($this->exactly(2))
            ->method('time')
            ->willReturnOnConsecutiveCalls(20000000 - 1, 20000000 + 1);

        $example = new ExampleUsingTime($mock);

        $this->assertFalse($example->launchTime());
        $this->assertTrue($example->launchTime());
    }

    public function testExampleUsingSleep(): void
    {
        $mock = $this->createMock(FrozenClock::class);

        $mock->expects($this->exactly(1))
            ->method('usleep')
            ->with(1000);

        $example = new ExampleUsingSleep($mock);

        $example->waitDuringPolling();
    }
}
