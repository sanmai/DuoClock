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

namespace Tests\DuoClock\Fixtures;

use DuoClock\Interfaces\SleeperInterface;

class ExampleUsingSleep
{
    private const POLL_TIME = 1000;

    public function __construct(
        public readonly SleeperInterface $clock,
    ) {}

    public function waitDuringPolling(): void
    {
        $this->clock->usleep(self::POLL_TIME);
    }
}
