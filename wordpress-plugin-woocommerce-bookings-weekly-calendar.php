<?php
/*
Plugin Name: WooCommerce Bookings Weekly Calendar
Version: 0.1.0
Description: Provides a weekly calendar view for WooCommerce Bookings.
Author: Nick Breen
Author URI: http://foobar.net.nz
Plugin URI: https://github.com/nickbreen/wordpress-plugin-woocommerce-bookings-weekly-calendar
Text Domain: wordpress-plugin-woocommerce-bookings-weekly-calendar
Domain Path: /languages
*/

// Menu Item under 'WooCommerce Bookings' for 'Weekly Calendar'

// See /wp-content/plugins/woocommerce-bookings/
// See includes/admin/class-wc-bookings-menus.php:109
add_action( 'admin_menu', function () {
    $calendar_page = add_submenu_page(
        'edit.php?post_type=wc_booking',
        __( 'Weekly Calendar', 'wordpress-plugin-woocommerce-bookings-weekly-calendar' ),
        __( 'Weekly Calendar', 'wordpress-plugin-woocommerce-bookings-weekly-calendar' ),
        'manage_bookings',
        'booking_calendar_weekly',
        function () {
            require_once( __DIR__ . '/includes/admin/class-wc-bookings-calendar-weekly.php' );
    		$page = new WC_Bookings_Calendar_Weekly();
    		$page->output();
        }
    );
}, 49 );

add_filter('woocommerce_screen_ids', function ($ids) {
    return array_merge( $ids, array(
        'wc_booking_page_booking_calendar_weekly',
    ) );
});
