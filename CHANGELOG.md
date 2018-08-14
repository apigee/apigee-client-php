# CHANGELOG

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
