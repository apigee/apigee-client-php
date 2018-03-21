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

namespace Apigee\Edge\Structure;

/**
 * Class KeyValueMap.
 */
abstract class KeyValueMap implements KeyValueMapInterface
{
    /** @var array */
    protected $values = [];

    /**
     * KeyValueMapAwareTrait constructor.
     *
     * @param array $values
     *   Associative array. Ex.: ['foo' => 'bar']
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * @inheritdoc
     */
    public function values(): array
    {
        return $this->values;
    }

    /**
     * @inheritdoc
     */
    public function getValue(string $key): ?string
    {
        if (array_key_exists($key, $this->values())) {
            return $this->values[$key];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function add(string $key, $value): void
    {
        $this->values[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function set(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @inheritdoc
     */
    public function delete(string $key): void
    {
        unset($this->values[$key]);
    }

    /**
     * @inheritdoc
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->values());
    }
}
