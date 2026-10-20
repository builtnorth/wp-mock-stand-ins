# Built North WP Mock Stand-ins

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
`WP_Block_Type_Registry`, `WP_Block_Patterns_Registry`, `WP_Theme_JSON_Resolver`.

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

## License

GPL-3.0-or-later — see `LICENSE`.

## Disclaimer

This software is provided "as is", without warranty of any kind, express or
implied, including the warranties of merchantability and fitness for a
particular purpose. To the extent permitted by applicable law, Built North LLC
(Kitaris) and its contributors are not liable for any direct, indirect,
incidental, or consequential damages arising from its use or inability to use
it, including loss of data, revenue, or site availability.

The software does not guarantee that your site is secure, ranks in search
results, or complies with privacy or other laws; that remains the site owner's
responsibility. AI-generated content can be inaccurate and should be reviewed
before publishing. Third-party services the software connects to are governed
by their own terms.

Keep a current backup and test changes on a staging site before applying them
to a live site. See sections 15 and 16 of the GNU General Public License,
version 3, for the full terms.
