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

use DuoClock\DuoClock;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;

use function microtime;
use function time;

#[CoversClass(DuoClock::class)]
class DuoClockTest extends TestCase
{
    public function testNowReturnsDateTimeImmutable(): void
    {
        $clock = new DuoClock();

        $before = new DateTimeImmutable();
        $now = $clock->now();
        $after = new DateTimeImmutable();

        $this->assertInstanceOf(DateTimeImmutable::class, $now);
        $this->assertGreaterThanOrEqual($before, $now);
        $this->assertLessThanOrEqual($after, $now);
    }

    public function testUnixTimeReturnsCurrentTimestamp(): void
    {
        $clock = new DuoClock();

        $before = time();
        $timestamp = $clock->time();
        $after = time();

        $this->assertIsInt($timestamp);
        $this->assertGreaterThanOrEqual($before, $timestamp);
        $this->assertLessThanOrEqual($after, $timestamp);
    }

    public function testTimeReturnsFloatMicrotime(): void
    {
        $clock = new DuoClock();

        $before = microtime(true);
        $time = $clock->microtime();
        $after = microtime(true);

        $this->assertIsFloat($time);
        $this->assertGreaterThanOrEqual($before, $time);
        $this->assertLessThanOrEqual($after, $time);
    }

    public function testSleepActuallySleeps(): void
    {
        $clock = new DuoClock();

        $timer = -microtime(true);
        $retval = $clock->sleep(1);
        $timer += microtime(true);

        $this->assertGreaterThanOrEqual(0.9, $timer);
        $this->assertLessThan(1.5, $timer);
        $this->assertSame(0, $retval, 'sleep should return 0 on success');
    }

    public function testUsleepActuallyMicrosleeps(): void
    {
        $clock = new DuoClock();

        $timer = -microtime(true);
        $clock->usleep(100);
        $timer += microtime(true);

        $this->assertGreaterThanOrEqual(0.0001, $timer);
        $this->assertLessThan(0.001, $timer);
    }

    public function testTimeNanosleepActuallySleeps(): void
    {
        $clock = new DuoClock();

        $timer = -microtime(true);
        $result = $clock->time_nanosleep(0, 1_000_000); // 1ms
        $timer += microtime(true);

        $this->assertTrue($result);
        $this->assertGreaterThanOrEqual(0.001, $timer);
        $this->assertLessThan(0.01, $timer);
    }

    public function testNanosleepActuallySleeps(): void
    {
        $clock = new DuoClock();

        $timer = -microtime(true);
        $result = $clock->nanosleep(1_000_000); // 1ms
        $timer += microtime(true);

        $this->assertTrue($result);
        $this->assertGreaterThanOrEqual(0.001, $timer);
        $this->assertLessThan(0.01, $timer);
    }
}
