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

namespace Apigee\Edge\Controller;

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Utility\JsonDecoderAwareTrait;
use Apigee\Edge\Utility\ResponseToArrayHelper;
use Symfony\Component\Serializer\Encoder\JsonDecode;

/**
 * Class AbstractController.
 *
 * Base controller for communicating with Apigee Edge.
 */
abstract class AbstractController
{
    use ClientAwareControllerTrait;
    use BaseEndpointAwareControllerTrait;
    use JsonDecoderAwareTrait;
    use ResponseToArrayHelper;

    /**
     * Client interface that should be used for communication.
     *
     * @var \Apigee\Edge\ClientInterface
     */
    protected $client;

    /** @var \Symfony\Component\Serializer\Encoder\JsonDecode */
    protected $jsonDecoder;

    /**
     * AbstractController constructor.
     *
     * @param \Apigee\Edge\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        // Keep the same structure that we get from Edge, do not transforms objects to arrays.
        $this->jsonDecoder = new JsonDecode();
    }

    /**
     * @inheritDoc
     */
    protected function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @inheritdoc
     */
    protected function jsonDecoder(): JsonDecode
    {
        return $this->jsonDecoder;
    }
}
