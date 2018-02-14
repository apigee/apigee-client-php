<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
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
     * @return null|string
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
