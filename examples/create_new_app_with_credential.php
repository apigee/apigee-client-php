<?php

/**
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

use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController;
use Apigee\Edge\Api\Management\Entity\DeveloperApp;
use Apigee\Edge\Exception\ApiException;
use Apigee\Edge\Exception\ApiRequestException;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Exception\ServerErrorException;
use Apigee\Edge\Structure\AttributesProperty;

require_once 'authentication.inc';

$developerMail = 'developer1@example.com';

$dac = new DeveloperAppController($clientFactory->getOrganization(), $developerMail, $clientFactory->getClient());

try {
    // Create a new developer app.
    /** @var \Apigee\Edge\Api\Management\Entity\DeveloperApp $developerApp */
    $developerApp = new DeveloperApp(['name' => 'test_app_1']);
    $developerApp->setDisplayName('My first app');
    $dac->create($developerApp);

    $dacc = new DeveloperAppCredentialController($organization, $developerMail, $developerApp->id(), $client);
    $attributes = new AttributesProperty(['foo' => 'bar']);
    $apiProducts = ['product_1', 'product_2'];
    $scopes = ['scope 1', 'scope 2'];

    // Add products, attributes, and scopes to the auto-generated credential that was created along with the app.
    $credentials = $developerApp->getCredentials();
    /** @var \Apigee\Edge\Api\Management\Entity\AppCredential $credential */
    $credential = reset($credentials);
    $dacc->addProducts($credential->id(), $apiProducts);
    $dacc->overrideAttributes($credential->id(), $attributes);
    $dacc->overrideScopes($credential->id(), $scopes);

    // Create a new, auto-generated credential that expires after 1 week.
    $dacc->generate($apiProducts, $attributes, $scopes, 604800000);

    // Create a credential with a specific key and secret and add the same products, attributes and scopes to it.
    $credential = $dacc->create('MY_CONSUMER_KEY', 'MY_CONSUMER_SECRET');
    $dacc->addProducts($credential->id(), $apiProducts);
    $dacc->overrideAttributes($credential->id(), $attributes);
    $dacc->overrideScopes($credential->id(), $scopes);
} catch (ClientErrorException $e) {
    // HTTP code >= 400 and < 500. Ex.: 401 Unauthorised.
    if ($e->getEdgeErrorCode()) {
        echo $e->getEdgeErrorCode();
    } else {
        echo $e;
    }
} catch (ServerErrorException $e) {
    // HTTP code >= 500 and < 600. Ex.: 500 Server error.
} catch (ApiRequestException $e) {
    // The request has failed, ex.: networking issues.
} catch (ApiException $e) {
    // Anything else, because this is the parent class of all the above.
}
