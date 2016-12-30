<?php
/*
Plugin Name: Polylang - Get all translations
Plugin URI:  https://github.com/k1sul1/polylang-get-all-translations
Description: Basic plugin that defines function pll_get_all_translations() if it doesn't exist. Useful in client side operations.
Version:     0.1
Author:      k1sul1
Author URI:  https://github.com/k1sul1
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

namespace k1sul1\Polylang\GAA {

  function get_options() {
    $initial = ["string_count" => NULL];
    return get_option('k1sul1_pll_all_translations', $initial);
  }

  function save_options($options) {
    return update_option('k1sul1_pll_all_translations', $options);
  }

  function save_keypairs($keypairs) {
    set_transient(
      'k1sul1_pll_all_translations_keypairs',
      $keypairs,
      apply_filters('k1sul1_pll_all_translations_transient', HOUR_IN_SECONDS)
    );

    $options = get_options();
    $options['string_count'] = count($keypairs);
    save_options($options);
  }

  function get_all_entries() {
    return apply_filters(
      'k1sul1_pll_all_translations_get_all_entries',
      $GLOBALS['l10n']['pll_string']->entries
    );
  }

  function get_cached_keypairs() {
    return get_transient('k1sul1_pll_all_translations_keypairs');
  }

  function get_fresh_keypairs() {
    $entries = get_all_entries();
    $keypairs = [];

    foreach ($entries as $key => $entry) {
      $keypairs[$key] = $entry->translations[0];
    }

    save_keypairs($keypairs);
    return $keypairs;
  }

  function get_keypairs() {
    $cached_keypairs = get_cached_keypairs();
    $entry_count = count($cached_keypairs);
    $options = get_options();

    if ($options['string_count'] === $entry_count && $cached_keypairs) {
      // Assume cache to be fresh. Stricter checking is likely too resource intensive.
      return $cached_keypairs;
    }

    return get_fresh_keypairs();
  }
}

namespace {
  if (!function_exists('pl_get_all_translations')) {
    // Single 'l' to minimize conflict.
    function pl_get_all_translations() {
      return k1sul1\Polylang\GAA\get_keypairs();
    }
  }
}
