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

namespace DuoClock;

use DuoClock\Interfaces\DuoClockInterface;
use DateTimeImmutable;
use DuoClock\Interfaces\SleeperInterface;
use Override;
use Psr\Clock\ClockInterface;

use function microtime;
use function sleep;
use function time;
use function usleep;

class DuoClock implements SleeperInterface, ClockInterface, DuoClockInterface
{
    #[Override]
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    #[Override]
    public function time(): int
    {
        return time();
    }

    #[Override]
    public function microtime(): float
    {
        return microtime(true);
    }

    #[Override]
    public function sleep(int $seconds): int
    {
        // @infection-ignore-all
        return sleep($seconds);
    }

    #[Override]
    public function usleep(int $microseconds): void
    {
        // @infection-ignore-all
        usleep($microseconds);
    }
}
