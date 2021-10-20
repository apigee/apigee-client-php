# How to Contribute

We'd love to accept your patches and contributions to this project. There are
just a few small guidelines you need to follow.

## Contributor License Agreement

Contributions to this project must be accompanied by a Contributor License
Agreement. You (or your employer) retain the copyright to your contribution;
this simply gives us permission to use and redistribute your contributions as
part of the project. Head over to <https://cla.developers.google.com/> to see
your current agreements on file or to sign a new one.

You generally only need to submit a CLA once, so if you've already submitted one
(even if it was for a different project), you probably don't need to do it
again.

## Code reviews

All submissions, including submissions by project members, require review. We
use GitHub pull requests for this purpose. Consult
[GitHub Help](https://help.github.com/articles/about-pull-requests/) for more
information on using pull requests.

Before you would submit your code changes for review please always run the
following commands on your machine to ensure those are syntactically and semantically
correct and do not break any tests.

```sh
composer fix-style # Automatically fix code style issues.
composer check-style # Report code style issues that can not be fixed automatically.
composer analyze # Check for errors with the static code-analysis tool.
composer test # Run PHPUnit tests. (Please always use only tests with real Apigee Edge.) 
```

For new functionality or a bug fix please always provide new test cases.
