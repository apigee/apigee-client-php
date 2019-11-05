<?php

/*
 * Copyright 2019 Google LLC
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

namespace Apigee\Edge\Tests\PropertyAccess;

use Apigee\Edge\Utility\DeprecatedPropertyTrait;
use PHPUnit\Framework\TestCase;

class DeprecatedPropertyTraitTest extends TestCase
{
    /**
     * Test that a class that uses this trait has declared it's "deprecatedProperties" property.
     */
    public function testUndeclaredDeprecatedProperties(): void
    {
        $a = new class() {
            use DeprecatedPropertyTrait;
        };

        try {
            $c = $a->b;
        } catch (\Exception $exception) {
            $this->assertInstanceOf(\LogicException::class, $exception);
        }
    }

    /**
     * Test accessing the properties and it's exceptions.
     */
    public function testdeprecatedProperties(): void
    {
        $a = new class() {
            use DeprecatedPropertyTrait;
            public $new = 'ABC';
            protected $deprecatedProperties = [
                'old' => 'new',
                'removed' => '',
                'invalid' => 'undeclared',
            ];
        };

        // Try accessing "old" property.
        $this->assertEquals($a->new, $a->old);

        // Try accessing "removed" property.
        try {
            $c = $a->removed;
        } catch (\Exception $exception1) {
            $this->assertInstanceOf(\LogicException::class, $exception1);
        }

        // Try accessing "invalid" property, where it's replacement "undeclared" is invalid.
        try {
            $c = $a->invalid;
        } catch (\Exception $exception2) {
            $this->assertInstanceOf(\LogicException::class, $exception2);
        } finally {
            if (!isset($exception2)) {
                $this->fail('An exception should have been thrown.');
            }
        }
    }
}
