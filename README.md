# Craft Twig Helpers

A [Craft CMS](https://craftcms.com/) module that provides shared Twig helpers with config-based extensibility. Ships with a set of built-in functions and filters, and allows you to register your own via a config file.

## Features

- Built-in Twig functions: `placeholderImage`
- Built-in Twig filter: `trimEmptyParagraphs`
- Built-in Twig tag: `{% minify %}` for HTML minification
- Register custom filters, functions, globals, and tests via config
- Supports Craft 4 and Craft 5

## Requirements

- Craft CMS ^4.3.5 or ^5.0
- PHP ^8.4

Use a Craft/Twig combination that passes Composer's security checks. Craft 4.18.9 pins Twig 3.19.x, which currently has security advisories; use a supported Craft 5 release for new installs.

## Installation

```bash
composer require jorisnoo/craft-twig-helpers
```

Register the module in your `config/app.php`:

```php
return [
    'modules' => [
        'twig-helpers' => \Noo\CraftTwigHelpers\TwigHelpers::class,
    ],
    'bootstrap' => ['twig-helpers'],
];
```

Use `config/app.php` (not `config/app.web.php`) so the same helpers are available to console commands and queue workers.

## Configuration

Create a `config/twig-helpers.php` file in your Craft project to register custom helpers:

```php
use Noo\CraftTwigHelpers\config\TwigHelpersConfig;

return TwigHelpersConfig::create()
    ->filters([
        'myFilter' => fn (string $value) => strtoupper($value),
    ])
    ->functions([
        'myFunction' => fn () => 'Hello, world!',
    ])
    ->globals([
        'siteName' => 'My Site',
    ])
    ->tests([
        'even' => fn (int $value) => $value % 2 === 0,
    ]);
```

## Built-in Helpers

### Functions

#### `placeholderImage(config)`

Generates a data URI for a placeholder SVG image. `width` and `height` must be finite, positive numbers; `color` is an optional CSS color string (default: `transparent`). Invalid dimensions raise `InvalidArgumentException`.

```twig
<img src="{{ placeholderImage({ width: 800, height: 600 }) }}">
<img src="{{ placeholderImage({ width: 800, height: 600, color: '#eee' }) }}">
```

### Filters

#### `trimEmptyParagraphs`

Strips empty `<p>` tags from the beginning and end of HTML content. Useful for cleaning up CKEditor output that adds filler paragraphs.

Handles `<p></p>`, `<p><br></p>`, `<p>&nbsp;</p>`, non-breaking space characters, and `<br>` tags with attributes (e.g. `data-cke-filler`).

An empty field (`null`) produces an empty string. If PCRE cannot process the input, the filter returns it unchanged, preserving its existing Twig safety status.

```twig
{{ entry.text|trimEmptyParagraphs }}
```

### Tags

#### `{% minify %}`

Minifies the wrapped HTML by stripping ordinary comments, trimming line indentation, and removing whitespace before block-level tags. It does not collapse runs of spaces within a line of text. `<pre>` and `<textarea>` contents are preserved verbatim. `<script>` and `<style>` contents are protected from HTML rewriting; surrounding whitespace and legacy comment/CDATA wrappers in their bodies are normalized. If a regex fails, the original HTML is returned unchanged.

Minification is disabled when Craft's resolved `devMode` general config setting is `true`.

```twig
{% minify %}
<!doctype html>
<html>
    <head>...</head>
    <body>
        ...
    </body>
</html>
{% endminify %}
```

## License

MIT

## Maintainer releases

See [RELEASING.md](RELEASING.md) for versioning, changelog entries and the release command.
