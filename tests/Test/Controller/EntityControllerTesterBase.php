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

namespace Apigee\Edge\Tests\Test\Controller;

/**
 * @internal
 */
abstract class EntityControllerTesterBase
{
    /** @var object */
    protected $decorated;

    /**
     * EntityControllerTesterBase constructor.
     *
     * @param object $controller
     *   Controller object that needs to be decorated.
     */
    public function __construct($controller)
    {
        $this->validateController($controller);
        $this->decorated = $controller;
    }

    public function __call($name, $arguments)
    {
        $object = null;
        if (method_exists($this, $name)) {
            $object = $this;
        } elseif (method_exists($this->decorated, $name)) {
            $object = $this->decorated;
        } else {
            throw new \InvalidArgumentException("Method not found {$name}.");
        }

        return call_user_func_array([$object, $name], $arguments);
    }

    public function instanceOf(string $fqcn): bool
    {
        $ro = new \ReflectionObject($this);
        if ($this->decorated instanceof EntityControllerTesterInterface) {
            return $this->decorated->instanceOf($fqcn);
        }

        return $ro->isSubclassOf($fqcn);
    }

    /**
     * @param object $controller
     *
     * @throws \InvalidArgumentException
     */
    protected function validateController($controller): void
    {
        if (!is_object($controller)) {
            throw new \InvalidArgumentException('Controller must be an object.');
        }
    }
}
