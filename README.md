# Contao Redirects Bundle

A simple Contao extension for URL redirects (301, 302) and 410 (Gone) responses, similar to Yoast's redirects in WordPress.

- **Source URL**: path without a leading slash (e.g. `old-page`).
- **Target URL**: absolute URL, relative path, page picker, insert tags (e.g. `{{link_url::4}}`), or `*` placeholders that mirror the matched source characters.
- **Match type**: `exact` or `wildcard` (using `*` as a placeholder for any characters) matching.
- **Status**: 301 (permanent), 302 (temporary), 410 (gone).
- Manage via **System → Redirects** in the backend.

## Install

```bash
composer require respinar/contao-redirects
```

## Usage

Add redirects in the backend with a source URL, target URL, status and active checkbox.

### Wildcard matching

With `wildcard` as the match type, the source URL may contain `*` as a placeholder that matches any characters. Both the source and the target may use wildcards:

- Source: `old/pages/*` → Target: `new/place/*` (redirects `/old/pages/anything` to `/new/place/anything`).

Each `*` in the target mirrors the characters matched at the same position in the source. For convenience you may also use numbered placeholders (`$1`, `$2`, ...) referencing the wildcard positions in the same order.

### 410 Gone

When the status is set to `410`, no redirect is performed and a `410 Gone` response is returned instead. The target URL is then ignored.

The optional **410 Gone** page (page type `410 Gone`) renders the response. Just like Contao's built-in 401, 403 and 404 pages, only **one** 410 page is allowed per site root — as soon as a root has a 410 page, the `410 Gone` option is removed from the page type selection for that root, and it becomes available again once the page is deleted.

## License

Licensed under the MIT License (LICENSE).
