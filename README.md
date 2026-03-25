# Craft Twig Helpers

A [Craft CMS](https://craftcms.com/) module that provides shared Twig helpers with config-based extensibility. Ships with a set of built-in functions and filters, and allows you to register your own via a config file.

## Features

- Built-in Twig functions: `textSnippet`, `staticAsset`, `placeholderImage`
- Built-in Twig filter: `hasTransparency`
- Register custom filters, functions, globals, and tests via config
- Supports Craft 4 and Craft 5

## Requirements

- Craft CMS ^4.0.0 or ^5.0.0
- PHP ^8.2

## Installation

```bash
composer require noo/craft-twig-helpers
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

#### `textSnippet(handle, sectionName)`

Retrieves a text snippet from a Craft entry. Defaults to looking in the `translations` section.

```twig
{{ textSnippet('welcomeMessage') }}
{{ textSnippet('label', 'customSection') }}
```

#### `staticAsset(path)`

Returns a URL for a static asset. Uses the `ASSET_CDN_HOST` environment variable when set, otherwise falls back to `@web/dist/`.

```twig
<img src="{{ staticAsset('images/logo.svg') }}">
```

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

## License

MIT
