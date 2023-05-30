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

namespace Apigee\Edge\Tests\Test;

use Apigee\Edge\Client;
use Apigee\Edge\Exception\ApiResponseException;
use Apigee\Edge\HttpClient\Utility\Builder;
use Http\Client\Exception;
use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class OnlineClientBase extends Client implements OnlineClientInterface
{
    /**
     * OnlineClientBase constructor.
     *
     * @param \Http\Message\Authentication $authentication
     * @param string|null $endpoint
     * @param array $options
     */
    public function __construct(Authentication $authentication, ?string $endpoint = null, array $options = [])
    {
        $options += [
            Client::CONFIG_USER_AGENT_PREFIX => static::USER_AGENT_PREFIX,
            Client::CONFIG_RETRY_PLUGIN_CONFIG => [
                'retries' => 5,
                'exception_decider' => function (RequestInterface $request, Exception $e) {
                    // Only retry API calls that failed with this specific error.
                    if ($e instanceof ApiResponseException && 'messaging.adaptors.http.flow.ApplicationNotFound' === $e->getEdgeErrorCode()) {
                        return true;
                    }

                    return false;
                },
                'exception_delay' => function (RequestInterface $request, Exception $e, $retries): int {
                    return $retries * 15000000;
                },
            ],
            Client::CONFIG_HTTP_CLIENT_BUILDER => new Builder($options[static::CONFIG_HTTP_CLIENT]),
        ];
        parent::__construct($authentication, $endpoint, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault(static::CONFIG_HTTP_CLIENT, null);
        $resolver->setAllowedTypes(static::CONFIG_HTTP_CLIENT, [
            '\Psr\Http\Client\ClientInterface',
            '\Http\Client\HttpAsyncClient',
        ]);
        parent::configureOptions($resolver);
    }
}
