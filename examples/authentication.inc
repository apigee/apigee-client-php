<?php
/**
 * Basic authentication provider used by examples.
 *
 * Requires the following environment variables to be set on your system:
 * - APIGEE_EDGE_PHP_SDK_BASIC_AUTH_USER
 * - APIGEE_EDGE_PHP_SDK_BASIC_AUTH_PASSWORD
 * - APIGEE_EDGE_PHP_SDK_ORGANIZATION
 *
 * Optional:
 * - APIGEE_EDGE_PHP_SDK_ENDPOINT
 * - APIGEE_EDGE_PHP_SDK_ENVIRONMENT
 */

use Apigee\Edge\HttpClient\Client;
use Http\Message\Authentication\BasicAuth;

require_once __DIR__ . '/../vendor/autoload.php';

$username = getenv('APIGEE_EDGE_PHP_SDK_BASIC_AUTH_USER') ? : 'my-email-address@example.com';
$password = getenv('APIGEE_EDGE_PHP_SDK_BASIC_AUTH_PASSWORD') ? : 'my-secure-password';
$organization = getenv('APIGEE_EDGE_PHP_SDK_ORGANIZATION') ?: 'my-organization';
$endpoint = getenv('APIGEE_EDGE_PHP_SDK_ENDPOINT') ?: null;
$environment = getenv('APIGEE_EDGE_PHP_SDK_ENVIRONMENT') ?: 'test';

$auth = new BasicAuth($username, $password);
$client = new Client($auth, null, $endpoint);
