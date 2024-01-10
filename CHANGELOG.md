# CHANGELOG
## [3.0.4](https://github.com/apigee/apigee-client-php/milestone/28?closed=1)
* [#343] Fix Invalid payload issue while editing/updating AppGroup/Teams in Apigee X.
* [#342] Fix for test failing for symfony 6.4.
* [#340] Fix for attribute values lost in PUT call while creating AppGroup.
* [#337] Fix for Team app credentials listings sorted in ascending order.
* [#334] Fix for \Apigee\Edge\HttpClient\Plugin\Authentication\GceServiceAccount::isAvailable() throws exception when called on non-GCE context.

## [3.0.3](https://github.com/apigee/apigee-client-php/milestone/26?closed=1)
* [#323] Add union type to suppress deprecation warning.
* [#325] Fix error when AppGroup list is empty or do not returns appgroups array.

## [3.0.2](https://github.com/apigee/apigee-client-php/milestone/24?closed=1)
* [#314] Added method getSupportedTypes which is added in symfony 6.3.
* [#316] PHPCS : Added import global namespace.
* [#312] Bumped symfony version to 6.3.
* [#310] Added support for psr/http-message >=2.0.
* Added query parameters in PaginatedEntityListingControllerInterface.

## [3.0.1](https://github.com/apigee/apigee-client-php/milestone/22?closed=1)
* [#286] Support for PHP 8.2 and updated league/period library.
* [#290] Upgraded friendsofphp/php-cs-fixer to ^3.22.
* [#297] Extended AppGroup class from base class AppOwner.
* [#298] Apigee X Teams with pagination.
See [AppGroup-pagination] (https://cloud.google.com/apigee/docs/reference/apis/apigee/rest/v1/organizations.appgroups/list#query-parameters) for more information.
* [#307] Apigee X Teams Apps with pagination.
See [AppGroup-Apps-pagination] (https://cloud.google.com/apigee/docs/reference/apis/apigee/rest/v1/organizations.appgroups.apps/list#query-parameters) for more information.

## [3.0.0](https://github.com/apigee/apigee-client-php/milestone/21?closed=1)
* [#262] Support Symfony/Serializer minimum version 6.2 (Added support for Drupal 10)
* [#272] Update dependencies for Symfony/Serializer 6.2 and dropped support for Symfony/Serializer 4.4

## [2.1.0](https://github.com/apigee/apigee-client-php/milestone/19?closed=1)
* [#246] Introduced AppGroup Entity which adds support for Apigee X Teams (New Feature)
API providers can now manage their Teams and Team members in Apigee X for non-monetized organizations. API providers using Apigee X with monetization enabled cannot leverage the Teams features in their Drupal developer portals at this time.
* [#247] Now, supports Team Apps for Apigee X Teams (New Feature)
* [#260] Added channelID and channelURI in AppGroupInterface.
See [appgroup-settings](https://cloud.google.com/apigee/docs/api-platform/publish/organizing-client-app-ownership#appgroup-settings) for more information.
* [#239] Removed support for PHP 7.4
PHP 7.4 is non-compatible because Union Types are not supported in PHP <8.0.
* [#268] Team member information migrated from Apigee Edge to Apigee X are stored in AppGroup API `__apigee_reserved__developer_details` attribute.
* [#238] Fixed error Symfony\Component\Serializer\Normalizer\AbstractNormalizer::setIgnoredAttributes() method is deprecated.

## [2.0.19](https://github.com/apigee/apigee-client-php/milestone/18?closed=1)
* [#248] Support assigning API products to Apps on creation, Introduced setInitialApiProducts only during app creation. This will be helpful for the organization having the features.keymanagement.disable.unbounded.permissions is TRUE

## [2.0.18](https://github.com/apigee/apigee-client-php/milestone/17?closed=1)
* [#243] Add additional organization type for Apigee X

## [2.0.17](https://github.com/apigee/apigee-client-php/milestone/16?closed=1)
* [#230] Bump firebase/php-jwt version to ^6.
* [#232] Deprecation warnings when called serialized classes from the Symfony framework 4.4.x.

## [2.0.16](https://github.com/apigee/apigee-client-php/milestone/15?closed=1)
* [#223] Bump required version of league/period to ^4.12.

## [2.0.15](https://github.com/apigee/apigee-client-php/milestone/14?closed=1)
* [#215] Added support for php 8.1.

## [2.0.14](https://github.com/apigee/apigee-client-php/milestone/13?closed=1)
* [#211] Added support for php 8.0.

## [2.0.13](https://github.com/apigee/apigee-client-php/milestone/12?closed=1)
* [#193] Fixed bug that was throwing error InvalidArgument while analyzing code.
* [#207] Bump php-http/message requirement to 1.13 from 1.9.
* [#208] Upgrade the phpunit and dependent libraries.

## [2.0.12](https://github.com/apigee/apigee-client-php/milestone/11?closed=1)
* [#186] Added support for symfony/serializer minimum version..

## [2.0.11](https://github.com/apigee/apigee-client-php/milestone/10?closed=1)
* [#182] Prepaid feature for Apigee X monetization.
* [#159] Ignore global averages in optimized metrics calculation.
* [#174] Drop support for PHP 7.1/7.2
* [#171] Updated user-agent string with additional information.

## [2.0.10](https://github.com/apigee/apigee-client-php/milestone/9?closed=1)
* [#165] Added support for PHP 7.4

## [2.0.9](https://github.com/apigee/apigee-client-php/milestone/8?closed=1)
* [#150] Fixed bug that prevented purchase of rate plan for PHP 7.3 ApigeeX org.
* [#153] Fix for failing user profile update for ApigeeX org.
* [#157] Fix phpdoc_tag_type and lambda_not_used_import error.
* [#112] Changed ClientInterface constant names and HybridOauth2 class to fit new Apigee naming.

## [2.0.8](https://github.com/apigee/apigee-client-php/milestone/7?closed=1)
* Fixed bug that was throwing error on returning string response (ApiResponseException)
* (Monetization) Added addition fields to Rateplan entity.
* (Monetization) Added runtimeType and monetizationConfig property to check monetization status for Apigee X org.

## [2.0.7](https://github.com/apigee/apigee-client-php/milestone/6?closed=1)
* Added support for Apigee X connections (New Feature)
API providers can now connect their Drupal 8/9 developer portals to Apigee X organizations.  API providers using Apigee X with Monetization enabled can now leverage monetization features in their Drupal developer portals.  For more information, see [Overview of Apigee monetization](https://cloud.google.com/apigee/docs/api-platform/monetization/overview) and the [Apigee Monetization Drupal module documentation](https://www.drupal.org/docs/contributed-modules/apigee-monetization).

* [#105](https://github.com/apigee/apigee-client-php/pull/123) Unable to retrieve analytics data on developer app analytics page for hybrid orgs.
* [#108](https://github.com/apigee/apigee-client-php/pull/125) Notice in recent message logs if description is empty after creating app in Apigee hybrid.

## [2.0.6](https://github.com/apigee/apigee-client-php/milestone/5?closed=1)
* Dropped support of HTTPlug 1.x libraries (HTTPlug, Client Common and Guzzle 6 adapter).
* Added support for GCE Service Account Authentication.
* Added support for `phpdocumentor/reflection-docblock:^5.0`.

## [2.0.5](https://github.com/apigee/apigee-client-php/milestone/3?closed=1) - May 26 2020
* GA support for Apigee hybrid Management API.
* Remove Alpha note about Monetization in README.
* [#101](https://github.com/apigee/apigee-client-php/pull/101) Use toDate instead of endDate for revenue report criteria.

## [2.0.4](https://github.com/apigee/apigee-client-php/milestone/2?closed=1) - December 5 2019
* Updated php-cs-fixer version and code analysis tools rules/validations.
#### Apigee Hybrid Management API
* Alpha support for Apigee hybrid API.
* Support for OAuth 2.0 for server to server applications using email and a private key.
#### Management API
* [#65](https://github.com/apigee/apigee-client-php/pull/74) Deprecated `createdBy` and `lastModifiedBy` properties in
all entities.
* [#82](https://github.com/apigee/apigee-client-php/pull/82),
 [#299-apigee-edge-drupal](https://github.com/apigee/apigee-client-php/pull/93) Introduced an organization features
 utility class (`OrganizationFeatures`) with methods `isCpsEnabled()`, `isPaginationAvailable()`,
 `isCompaniesFeatureAvailable()`, `isHybridEnabled()`, `isMonetizationEnabled()`, and `isFeatureEnabled()`.
#### Monetization API
* [#61](https://github.com/apigee/apigee-client-php/pull/61) Fix PHP notices produced when the developer is not set.

## [2.0.3](https://github.com/apigee/apigee-client-php/milestone/1?closed=1) - June 24 2019
#### Management API
* [#54](https://github.com/apigee/apigee-client-php/pull/54) Add forked AddPathPlugin and removed required patch
#### Monetization API
* [#58](https://github.com/apigee/apigee-client-php/pull/58) Added the end unit property to `RatePlanRate`

## 2.0.2 - 2019-05-09
#### Monetization API
* Added support for [Reports API](https://docs.apigee.com/api-platform/monetization/create-reports). [#51](https://github.com/apigee/apigee-client-php/pull/51)
* Fixed: Keep original start date property can be null [#49](https://github.com/apigee/apigee-client-php/pull/49)

## 2.0.1 - 2019-02-06
#### Management API
* **Fixed Edge for Private Cloud support.** Core Persistent Services (CPS) is not available in Private Cloud installations and because of that earlier versions of this library threw a CpsNotEnabledException exception when someone tried to construct an API request by adding pagination.
You can find more information about this in the [related pull request](https://github.com/apigee/apigee-client-php/pull/43) and in the ["Edge for Private Cloud" section of the README.md](README.md#edge-for-private-cloud).
#### Monetization API
* [#36](https://github.com/apigee/apigee-client-php/issues/36): Send developer email address instead of developer UUID to the [Accept rate plan](https://apidocs.apigee.com/monetize/apis/post/organizations/%7Borg_name%7D/developers/%7Bdeveloper_or_company_id%7D/developer-rateplans) endpoint.
#### Other
* Updated minimum required version to [php-http/client-common >= 1.9](https://github.com/php-http/client-common/blob/master/CHANGELOG.md#190---2019-01-03).

## 2.0.0 - 2018-11-27
* **First stable release!** :tada: :tada: :tada:
* Added Monetization API support with [alpha stability](https://github.com/apigee/apigee-client-php/blob/3856d585878628eb6c44541cf5529055c8a480b6/README.md#monetization-api-alpha-release). (Management API support is stable.)
* Added missing organization controller parameter to the Company controller's constructor.
* Improved documentation of the CompanyMembersControllerInterface's `setMembers()` methods.

## 2.0.0-alpha6 - 2018-11-09
* idProperty() on entity objects is a static method from now.
* Setters on entities now accepts variable lengths arguments instead of an array. This way we can leverage PHP's built-in type check on these methods as well.
* Developer and company entities extends and implements one new parent class and interface: AppOwner and AppOwnerInterface.
* Refactored management API tests.
* New environment variable: APIGEE_EDGE_PHP_CLIENT_API_CLIENT
* Bumped minimum required versions from php-client/httplug and php-client/client-common packages.
* Travis CI: Removed PHP nightly builds from the test matrix, fixed failed tests caused by Composer process timeout.

## 2.0.0-alpha5 - 2018-10-08
* Added missing constructor to the Company entity.
* Added "JSON_PRESERVE_ZERO_FRACTION" to the serializer to ensure float values are always encoded as a float value.
* Simplified and improved serialization. The EntityNormalizer now called as ObjectNormalizer and the EntityDenormalizer called as ObjectDenormalizer.
* Blocked installation php-http/client-common>=1.8.0 until this issue does not get solved: https://github.com/php-http/client-common/issues/109.
* Updated vimeo/psalm dev dependency to the latest 2.x version.

## 2.0.0-alpha4 - 2018-08-14
* Dropped paginated entity listing support from developer- and company apps controllers. Pagination only supported on developer- and company apps endpoints when entity ids listed. These endpoints only return maximum 100 entities (ids and objects), if a developer/company has more apps (it should not be) use the /apps endpoint to load all apps one by one in a loop.
* Decoupled entity serialization logic from controller classes.
* Introduced new traits that allows us to keep contract about methods use in traits and classes.
* Now EntityTransformer handles updating entity properties from API responses.
* Introducing a new BaseObject as a parent class for entities and (data) structures.

## 2.0.0-alpha3 - 2018-07-26

* As CPS pagination got supported in the Management API for listing API products and Companies we also added it to API client.
* `getEntities()` and `getEntityIds()` load all entities and entity ids
from Apigee Edge by default, even if it requires multiple API calls
because of CPS pagination.
* Classes and interfaces related to CPS and non CPS entity listing got renamed:
  * CpsLimitEntityController => PaginatedEntityController
  * CpsLimitEntityControllerInterface => PaginatedEntityControllerInterface
  * CpsLimitEntityController => PaginatedEntityController
  * CpsListingEntityControllerInterface => PaginatedEntityListingControllerInterface
  * CpsListingEntityControllerTrait => PaginatedEntityListingControllerTrait
  * NonCpsListingEntityControllerTrait => NoPaginationEntityListingControllerTrait
  * NonCpsListingEntityControllerInterface => NonPaginatedEntityListingControllerInterface
  * CpsListLimitInterface => PagerInterface
* OAuth: Fixed re-authentication with client credentials if refresh token has expired.
* [Changed return types](https://github.com/apigee/apigee-client-php/commit/a3b51721a5a9d937d978490ae7e7ee6601b8a3b8) in AppCredentialController as it does not inherit from EntityCrudOperationsControllerTrait anymore.
* Constants that represents constant state of the Management API now defined in interfaces instead of classes.
* Also the visibility of some constants changed to public from private or protected.
* Added support to add [Retry plugin](http://docs.php-http.org/en/latest/plugins/retry.html) configuration to the client.
* Renamed environment variables used in tests for authentication from APIGEE_EDGE_PHP_SDK_* to APIGEE_EDGE_PHP_CLIENT_*.
* Improved test coverage.
* Better configuration in .gitattributes to exclude more unnecessary files from prefer-dist install.

## 2.0.0-alpha2 - 2018-05-24

* Added CHANGELOG.md.
* Fixed: Failed API calls caused issues if OAuth authentication were in use.
* Two new required parameters added to AppCredentialControllerInterface::generate().
(These required by the API.)
* AppCredentialControllerInterface::overrideAttributes() removed because
interface now implements the AttributesAwareEntityControllerInterface interface.
All previous usage of overrideAttributes() must be replaced with
updateAttributes().
* Extended test coverage.

## 2.0.0-alpha1 - 2018-05-09

* First alpha release.
