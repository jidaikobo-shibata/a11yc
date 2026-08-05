# Work Log

## 2026-08-05 Guzzle Security Update

- What: Raised the runtime requirement for `guzzlehttp/guzzle` from `^7.15.1` to `^7.15.2` and updated the local ignored `composer.lock`/`vendor/` state to Guzzle 7.15.2.
- Why: Guzzle 7.15.2 fixes `GHSA-v5mv-p594-2x33` (host validation and SSRF-related bypasses) and `GHSA-f7vp-7xgx-4w4r` (noncanonical cookie-domain matching). A11YC accepts target URLs and performs HTTP requests with redirects enabled, so the host-validation fix is relevant to runtime behavior.
- Verification:
  - `composer run phpunit`: passed with 5 tests and 20 assertions.
  - `composer run phpstan`: passed with no errors.
  - `composer run phpcs`: passed.
  - `composer audit --locked --no-interaction`: passed with no security vulnerability advisories.
- Incomplete: The updated standalone release still needs to be incorporated into `jwp-a11y`, verified, and redistributed.
- Next: Release this standalone change as 7.2.3, then update `jwp-a11y` to require A11YC 7.2.3 and Guzzle 7.15.2.

## 2026-04-20 PHPUnit Advisory Update

- What: Raised the development baseline for `jidaikobo/a11yc` from PHP `>=7.4` to PHP `>=8.3` and changed the PHPUnit dev constraint from `^9.6` to `^12.5.22`. Updated the local ignored `composer.lock`/`vendor/` state to PHPUnit 12.5.23 for verification.
- Why: GitHub Dependabot reported `GHSA-qrr6-mg7r-m243` for `phpunit/phpunit`; patched PHPUnit releases start at 12.5.22 for the supported 12.x line.
- Verification:
  - `composer validate --strict`: passed.
  - `composer run phpunit`: passed with PHPUnit 12.5.23, 5 tests and 20 assertions.
  - `composer audit`: passed with no security vulnerability advisories.
  - `composer run phpstan`: passed when rerun outside the local sandbox TCP listen restriction.
  - `composer run phpcs`: failed on pre-existing line-length warnings in `src/Analyzer.php` and `src/Yaml.php`.
- Incomplete: PHPCS still reports existing line-length warnings unrelated to the PHPUnit advisory update.
- Next: Optionally clean up the existing PHPCS warnings in a separate focused change.
