# WP Mock Stand-ins

Minimal WordPress core class stand-ins for [`10up/wp_mock`](https://github.com/10up/wp_mock)-based PHPUnit tests.

WP_Mock mocks WordPress *functions* (via Patchwork) but ships no stand-ins
for WordPress *classes* — `WP_Error`, `WP_Query`, `WP_REST_Request`, and so
on. This package provides minimal, deterministic re-implementations of just
enough real behavior for a unit test to construct one, call a handful of
methods, and assert on the result. It is not a full reimplementation of
WordPress core, and it is not a substitute for integration testing against a
real WordPress install.

## Classes provided

`WP_Error`, `WP_REST_Request`, `WP_REST_Response`, `WP_REST_Server`,
`WP_Query`, `WP_Post`, `WP_Post_Type`, `WP_Block`, `WP_Block_Type`,
`WP_Block_Type_Registry`, `WP_Block_Patterns_Registry`.

Every class is guarded with `class_exists($class, false)` so it never
shadows a real WordPress core class already loaded in the same process
(e.g. an integration test running against a real WordPress install).

## Usage

Add as a `require-dev` dependency, then require the file from your test
bootstrap **after** resolving your own package's autoloader — the file
lives alongside whichever `vendor/` you actually loaded (a package's own
local `vendor/`, or a monorepo's shared root `vendor/`):

```php
require_once dirname($autoloader) . '/builtnorth/wp-mock-stand-ins/inc/stand-ins.php';
```

```json
{
  "require-dev": {
    "builtnorth/wp-mock-stand-ins": "^1.0"
  }
}
```

## Scope

This package intentionally does **not** include:

- Stand-ins for anything other than WordPress core classes (e.g. a
  package's own first-party framework classes, or third-party library
  stand-ins like Action Scheduler) — those belong in the consuming
  package's own test bootstrap.
- WordPress core *functions* — use `10up/wp_mock` for those.
- A full reimplementation of any WordPress core class — only the subset of
  behavior real test suites actually exercise.
