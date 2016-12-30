# Polylang - Get all translations

Get all translations for the current language as a single key-value object. Calls are cached for performance.
You can use the object in `wp_localize_script()` to print all string translations to the client as a JavaScript object.

`pl_get_all_translations()` gives you a cached array of keypairs.

There's a few filters that you can use to configure the plugin.

## Installation

Install the plugin via [Composer](https://getcomposer.org/)
```
composer require k1sul1/polylang-get-all-translations
```

Activate the plugin
```
wp plugin activate polylang-get-all-translations
```
