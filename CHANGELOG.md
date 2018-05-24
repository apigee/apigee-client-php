# CHANGELOG

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
