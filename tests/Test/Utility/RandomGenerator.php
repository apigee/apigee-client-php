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

use Faker\Factory;

/**
 * Generates random data.
 */
class RandomGenerator implements RandomGeneratorInterface
{
    /** @var \Faker\Generator */
    protected $faker;

    /**
     * RandomGenerator constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * @inheritdoc
     */
    public function displayName(): string
    {
        return '(Test)' . $this->faker->unique()->words($this->faker->numberBetween(1, 3), true);
    }

    /**
     * @inheritdoc
     */
    public function machineName(): string
    {
        return 'test_' . $this->faker->unique()->userName;
    }

    /**
     * @inheritdoc
     */
    public function number(int $min = 1, int $max = 1000): string
    {
        return $this->faker->unique()->numberBetween($min, $max);
    }

    /**
     * @inheritdoc
     */
    public function string(): string
    {
        return $this->faker->unique()->word;
    }

    public function text(int $mintLength = 1, int $maxLength = 10): string
    {
        return $this->faker->unique()->words($this->faker->numberBetween($mintLength, $maxLength), true);
    }

    public function email(): string
    {
        return 'test_' . $this->faker->unique()->email;
    }
}
