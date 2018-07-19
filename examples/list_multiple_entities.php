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

/**
 * This example demonstrates how you can list entities (developers, api products, developer apps, etc.) from a type.
 */
use Apigee\Edge\Api\Management\Controller\ApiProductController;
use Apigee\Edge\Api\Management\Controller\DeveloperController;

require_once 'authentication.inc';

/**
 * List all developers on the organization.
 */
$dc = new DeveloperController($clientFactory->getOrganization(), $clientFactory->getClient());
/** @var \Apigee\Edge\Api\Management\Entity\DeveloperInterface $developer */
foreach ($dc->getEntities() as $developer) {
    echo $developer->getFirstName() . ' ' . $developer->getLastName() . "\n";
}

/*
 * List all developer ids (in this case the email addresses).
 */
foreach ($dc->getEntityIds() as $devId) {
    // Lazy load developers by ids.
    $developer = $dc->load($devId);
    echo $developer->getFirstName() . ' ' . $developer->getLastName() . "\n";
}

/*
 * In case your organization supports CPS you can add a pager to those entity's listing methods that supports paging.
 *
 * https://docs.apigee.com/api-services/content/api-reference-getting-started#cps
 */

/*
 * Developers support paging: https://docs.apigee.com/management/apis/get/organizations/%7Borg_name%7D/developers
 *
 * In the API client everything that implements \Apigee\Edge\Controller\CpsListingEntityControllerInterface has this
 * feature.
 */

/*
 * List the first 1000 developer ids (in this case the email addresses).
 */
foreach ($dc->getEntityIds($dc->createCpsLimit()) as $devId) {
    // Lazy load developers by ids.
    $developer = $dc->load($devId);
    echo $developer->getFirstName() . ' ' . $developer->getLastName() . "\n";
}

// The startKey is the entity id of an entity. (In case of a developer this either the email address or the developer
// id (uuid).
foreach ($dc->getEntities($dc->createCpsLimit(5, 'john.doe@example.com')) as $developer) {
    echo $developer->getFirstName() . ' ' . $developer->getLastName() . "\n";
}

/**
 * But API products do not.
 *
 * In the SDK anything that implements \Apigee\Edge\Controller\NonCpsListingEntityControllerInterface.
 */
$ac = new ApiProductController($clientFactory->getOrganization(), $clientFactory->getClient());
/** @var \Apigee\Edge\Api\Management\Entity\ApiProductInterface $product */
foreach ($ac->getEntities() as $product) {
    echo $product->getDisplayName() . "\n";
}

foreach ($ac->getEntityIds() as $productName) {
    // Lazy load API products here with load.
    $product = $ac->load($productName);
    echo $product->getDisplayName() . "\n";
}
