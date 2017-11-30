<?php
/**
 * An example how to create new developer app with credential.
 *
 * All SDK classes tries to fulfil only one task (Single Responsibility design pattern).
 *
 * This is the reason why the DeveloperAppController only handles developer app related C.R.U.D operations and
 * does not allow to add or modify credentials for an app. These operations are handled by the
 * DeveloperAppCredentialController.
 */

use Apigee\Edge\Api\Management\Controller\DeveloperAppController;
use Apigee\Edge\Api\Management\Controller\DeveloperAppCredentialController;
use Apigee\Edge\Exception\ApiException;
use Apigee\Edge\Exception\ClientErrorException;
use Apigee\Edge\Exception\ServerErrorException;
use Apigee\Edge\HttpClient\Client;
use Apigee\Edge\Structure\AttributesProperty;
use Http\Message\Authentication\BasicAuth;

include_once '../vendor/autoload.php';

$username = 'my-email-address@example.com';
$password = 'my-secure-password';
$organization = 'my-organization';
$developerMail = 'developer1@example.com';

$auth = new BasicAuth($username, $password);
$client = new Client($auth);

$dac = new DeveloperAppController($organization, $developerMail, $client);

try {
    /** @var \Apigee\Edge\Api\Management\Entity\DeveloperApp $developerApp */
    $developerApp = new \Apigee\Edge\Api\Management\Entity\DeveloperApp(['name' => 'test_app_1']);
    $developerApp->setDisplayName('My first app');
    $dac->create($developerApp);
} catch (ClientErrorException $e) {
    // HTTP code >= 400 and < 500. Ex.: 401 Unauthorised.
    if ($e->getEdgeErrorCode()) {
        print $e->getEdgeErrorCode();
    } else {
        print $e;
    }
} catch (ServerErrorException $e) {
    // HTTP code >= 500 and < 600. Ex.: 500 Server error.
} catch (ApiException $e) {
    // Anything else, because this is the parent class of all the above.
}

$dacc = new DeveloperAppCredentialController($organization, $developerMail, $developerApp->id(), $client);
$apiProducts = ['product_1', 'product_2'];
$scopes = ['scope 1', 'scope 2'];

try {
    // Create a new, auto generated key that expires after 1 week.
    $attributes = new AttributesProperty(['foo' => 'bar']);
    $dacc->generate($apiProducts, $attributes, $scopes, 604800000);

    // Create a credential with a specific key and secret and add the same products, attributes and scopes to it.
    $credential = $dacc->create('MY_CONSUMER_KEY', 'MY_CONSUMER_SECRET');
    $dacc->addProducts($credential->id(), $apiProducts);
    $dacc->overrideAttributes($credential->id(), $attributes);
    $dacc->overrideScopes($credential->id(), $scopes);
} catch (ClientErrorException $e) {
    // HTTP code >= 400 and < 500. Ex.: 401 Unauthorised.
    if ($e->getEdgeErrorCode()) {
        print $e->getEdgeErrorCode();
    } else {
        print $e;
    }
} catch (ServerErrorException $e) {
    // HTTP code >= 500 and < 600. Ex.: 500 Server error.
} catch (ApiException $e) {
    // Anything else, because this is the parent class of all the above.
}
