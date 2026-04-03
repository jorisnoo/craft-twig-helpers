# Craft Twig Helpers

A [Craft CMS](https://craftcms.com/) module that provides shared Twig helpers with config-based extensibility. Ships with a set of built-in functions and filters, and allows you to register your own via a config file.

## Features

- Built-in Twig functions: `placeholderImage`
- Built-in Twig filters: `hasTransparency`, `trimEmptyParagraphs`
- Built-in Twig tag: `{% minify %}` for HTML minification
- Register custom filters, functions, globals, and tests via config
- Supports Craft 4 and Craft 5

## Requirements

- Craft CMS ^4.0.0 or ^5.0.0
- PHP ^8.2

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

Generates a data URI for a placeholder SVG image.

```twig
<img src="{{ placeholderImage({ width: 800, height: 600 }) }}">
<img src="{{ placeholderImage({ width: 800, height: 600, color: '#eee' }) }}">
```

### Filters

#### `hasTransparency`

Checks whether an Asset image has transparency.

```twig
{% if asset|hasTransparency %}
    {# handle transparent image #}
{% endif %}
```

#### `trimEmptyParagraphs`

Strips empty `<p>` tags from the beginning and end of HTML content. Useful for cleaning up CKEditor output that adds filler paragraphs.

Handles `<p></p>`, `<p><br></p>`, `<p>&nbsp;</p>`, non-breaking space characters, and `<br>` tags with attributes (e.g. `data-cke-filler`).

```twig
{{ entry.text|trimEmptyParagraphs }}
```

### Tags

#### `{% minify %}`

Minifies the wrapped HTML by stripping comments, collapsing whitespace, and removing unnecessary spaces around block-level elements. Content inside `<pre>`, `<textarea>`, `<script>`, and `<style>` tags is preserved.

Minification is automatically disabled when `devMode` is `true`.

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
