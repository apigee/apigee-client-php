<?php

/*
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Tests\Test\Utility;

/**
 * Base definition of a random generator.
 */
interface RandomGeneratorInterface
{
    public function displayName(): string;

    public function email(): string;

    public function machineName(): string;

    public function number(int $min = 1, int $max = 1000): string;

    public function string(): string;

    public function text(int $mintLength = 1, int $maxLength = 10): string;

    public function url(): string;
}
