<?php
/*
Plugin Name: Polylang - Get all translations
Plugin URI:  https://github.com/k1sul1/polylang-get-all-translations
Description: Basic plugin that defines function pll_get_all_translations() if it doesn't exist. Useful in client side operations.
Version:     0.1.3
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

  function save_keypairs($keypairs, $lang) {
    set_transient(
      "k1sul1_pll_all_{$lang}_translations_keypairs",
      $keypairs,
      apply_filters("k1sul1_pll_all_{$lang}_translations_transient", HOUR_IN_SECONDS)
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

  function get_cached_keypairs($lang) {
    return get_transient("k1sul1_pll_all_{$lang}_translations_keypairs");
  }

  function get_fresh_keypairs($lang) {
    $entries = get_all_entries();
    $keypairs = [];

    foreach ($entries as $key => $entry) {
      $keypairs[$key] = $entry->translations[0];
    }

    save_keypairs($keypairs, $lang);
    return $keypairs;
  }

  function get_keypairs($lang = NULL) {
    $current_lang = !is_null($lang) ? $lang : pll_current_language();
    $cached_keypairs = get_cached_keypairs($current_lang);
    $entry_count = count(get_all_entries());
    $options = get_options();

    if (($options['string_count'] === $entry_count) && $cached_keypairs) {
      // If there's a valid transient, and the option count matches entry count,
      // cache is assumed fresh.

      // Known edge case: if you remove one pll_register_string() call, and add another,
      // and then clean the string translation database, the cache is still considered valid
      // by this. Clearing transients is a sure way to get fresh strings.

      return $cached_keypairs;
    }

    return get_fresh_keypairs($current_lang);
  }
}

namespace {
  if (!function_exists('pl_get_all_translations')) {
    // Single 'l' to minimize conflict.
    function pl_get_all_translations($lang = NULL) {
      return k1sul1\Polylang\GAA\get_keypairs($lang);
    }
  }
}
