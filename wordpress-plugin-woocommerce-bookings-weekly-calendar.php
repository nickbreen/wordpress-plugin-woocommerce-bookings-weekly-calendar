<?php
/*
Plugin Name: WooCommerce Bookings Weekly Calendar
Version: 1.0
Description: Provides a weekly calendar view for WooCommerce Bookings.
Author: Nick Breen
Author URI: http://foobar.net.nz
Plugin URI: https://github.com/nickbreen/wordpress-plugin-woocommerce-bookings-weekly-calendar
Text Domain: vary
Domain Path: /languages
*/

add_filter('wp_headers', function ($headers) {
  // Don't bother unless there are cookies set
  if (!isset($headers['Set-Cookie'])) return $headers;

  // Use a grotty regex to parse out all the cookies
  $re = '/(?P<name>[^\\s=]+)=(?P<value>[^\\s;]*);/x';
  preg_match_all($re, $headers['Set-Cookie'], $cookies_matches, PREG_SET_ORDER);
  // Build a list of cookies
  foreach ($cookies_matches as $i => $cookie_matches)
    foreach ($cookie_matches as $cookie_match)
      $matched_cookies[$i][$cookie_match['pname']] = $cookie_match['pvalue'];
  // If there are none, something's wrong.
  trigger_error("Cookies set, matched: $cookies_matches");
  if (!isset($matched_cookies)) return $headers;

  // TODO make this a setting!
  $cookies_to_vary = get_option('cookies_to_vary', array(
    'woocommerce_cart_hash' => 'X-Woocommerce-Cart-Hash',
  ));

  // Filter all matched cookies for the one(s) we want to vary on.
  foreach ($matched_cookies as $matched_cookie)
    foreach ($cookies_to_vary as $cookie => $header)
      if ($cookie == $matched_cookie['name'])
        $vary_headers[$header] = $matched_cookie['value'];
  // If there are no cookies we want to vary one, give up.
  if (!isset($vary_headers)) return;
  // For each cookie to vary on, issue an X- header for that cookie.
  foreach ($vary_headers as $header => $value)
    $headers[$header] = $value;
  // Vary on all the issued headers
  $headers['Vary'] = implode(', ', $vary_headers);

  return $headers;
}, 30);
