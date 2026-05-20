# Contao Redirects Bundle

A simple Contao extension for URL redirects (301, 302) and 410 (Gone) responses, similar to Yoast's redirects in WordPress.

- **Source URL**: path without a leading slash (e.g. `old-page`).
- **Target URL**: absolute URL, relative path, page picker, insert tags (e.g. `{{link_url::4}}`), or wildcard placeholders (`$1`, `$2`, ...).
- **Match type**: `exact` or `wildcard` (regular expression) matching.
- **Status**: 301 (permanent), 302 (temporary), 410 (gone).
- Manage via **System → Redirection** in the backend.

## Install

```bash
composer require respinar/contao-redirects
```

## Usage

Add redirects in the backend with a source URL, target URL, status and active checkbox.

### Wildcard matching

With `wildcard` as the match type, the source URL is treated as a regular expression. Captured groups can be used in the target with `$1`, `$2`, ...:

- Source: `^old/pages/(.*)$` → Target: `new/place/$1` (redirects `/old/pages/anything` to `/new/place/anything`).

### 410 Gone

When the status is set to `410`, no redirect is performed and a `410 Gone` response is returned instead. The target URL is then ignored.

## License

Licensed under the MIT License (LICENSE).
