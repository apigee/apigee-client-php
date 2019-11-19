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

namespace Apigee\Edge\Tests\Utility;

use Apigee\Edge\Tests\Test\Utility\ResponseToArrayHelper;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseToArrayHelperTest.
 *
 * @small
 */
class ResponseToArrayHelperTest extends TestCase
{
    private $responseToArrayHelper;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->responseToArrayHelper = new ResponseToArrayHelper();
    }

    /**
     * Tests the expand parameter compatibility.
     *
     * @dataProvider expandCompatibilityDataProvider
     */
    public function testExpandCompatibility($edgeResponse, $hybridResponse): void
    {
        /** @var \Psr\Http\Message\ResponseInterface $response1 */
        $response1 = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response1->method('getHeaderLine')->willReturn('application/json');
        $response1->method('getBody')->willReturn($edgeResponse);
        $decodedEdgeStyle = $this->responseToArrayHelper->convertResponseToArray($response1, false);

        /** @var \Psr\Http\Message\ResponseInterface $response2 */
        $response2 = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response2->method('getHeaderLine')->willReturn('application/json');
        $response2->method('getBody')->willReturn($hybridResponse);
        $decodedWithCompatibility = $this->responseToArrayHelper->convertResponseToArray($response2, true);

        $this->assertEquals($decodedEdgeStyle, $decodedWithCompatibility);
    }

    /**
     * DataProvider for testExpandCompatibility.
     *
     * @return array
     *   The array of values to run tests on.
     */
    public function expandCompatibilityDataProvider()
    {
        $edgeStyleNonEmpty = [
            'helloworld',
            'weather',
        ];
        $hybridStyleNonEmpty = (object) [
            'proxies' => [
                (object) [
                    'name' => 'helloworld',
                ],
                (object) [
                    'name' => 'weather',
                ],
            ],
        ];
        return [
            // Test a response.
            [json_encode($edgeStyleNonEmpty), json_encode($hybridStyleNonEmpty)],
            // Test an empty response.
            ['[]', '{}'],
        ];
    }
}
