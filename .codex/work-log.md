# Work Log

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
