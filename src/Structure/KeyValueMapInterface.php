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
 * Interface KeyValueMapInterface.
 */
interface KeyValueMapInterface extends \IteratorAggregate
{
    /**
     * Returns values from the store.
     *
     * @return array
     */
    public function values(): array;

    /**
     * Returns value of a key from the store.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getValue(string $key): ?string;

    /**
     * Adds a value with a key to the store.
     *
     * @param string $key
     * @param mixed $value
     */
    public function add(string $key, $value): void;

    /**
     * Set the values in the store.
     *
     * @param array $values
     */
    public function set(array $values): void;

    /**
     * Removes a value from the store.
     *
     * @param string $key
     */
    public function delete(string $key): void;

    /**
     * Checks whether a key exists in the store.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool;
}
