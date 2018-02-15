<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
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
 * In the SDK anything that implements \Apigee\Edge\Controller\CpsListingEntityControllerInterface.
 */

// The startKey is the entity id of an entity. (In case of a developer this either the email address or the developer
// id (uuid).
foreach ($dc->getEntities($dc->createCpsLimit('john.doe@example.com', 5)) as $developer) {
    echo $developer->getFirstName() . ' ' . $developer->getLastName() . "\n";
}

/**
 * But API products do not.
 *
 * In the SDK anything that implements \Apigee\Edge\Controller\NonCpsListingEntityControllerInterface.
 */
$ac = new ApiProductController($organization, $client);
/** @var \Apigee\Edge\Api\Management\Entity\ApiProductInterface $product */
foreach ($ac->getEntities() as $product) {
    echo $product->getDisplayName() . "\n";
}

foreach ($ac->getEntityIds() as $productName) {
    // Lazy load API products here with load.
    $product = $ac->load($productName);
    echo $product->getDisplayName() . "\n";
}
