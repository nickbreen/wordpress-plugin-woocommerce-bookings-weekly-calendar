<?php
/*
Plugin Name: WooCommerce Bookings Weekly Calendar
Version: 0.2.0
Description: Provides a weekly calendar view for WooCommerce Bookings.
Author: Nick Breen
Author URI: http://foobar.net.nz
Plugin URI: https://github.com/nickbreen/wordpress-plugin-woocommerce-bookings-weekly-calendar
Text Domain: wordpress-plugin-woocommerce-bookings-weekly-calendar
Domain Path: /languages
*/

// See /wp-content/plugins/woocommerce-bookings/
// See includes/admin/class-wc-bookings-menus.php:109
add_action('admin_menu', function () {
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

/**
 * Add the driver bookings end points
 */

add_action('init', function () {
    // TODO option
    add_rewrite_endpoint('driver-bookings', EP_ROOT|EP_PAGES);
});

add_filter('woocommerce_account_menu_items', function ($items) {
    if (current_user_can('driver'))
        // TODO option
        $items['driver-bookings'] = __('Driver Bookings', 'wordpress-plugin-woocommerce-bookings-weekly-calendar');
    return $items;
});

add_filter('woocommerce_endpoint_driver-bookings_title', function ($items) {
    return __('Driver Bookings', 'wordpress-plugin-woocommerce-bookings-weekly-calendar');
});

add_filter('pods_api_get_table_info_default_post_status', function ($stati, $post_type, $info, $object_type, $object, $name, $pod, $field) {
    return $stati = $field['options']['pick_post_status'] ?? $stati;
}, 10, 8);

add_action('woocommerce_account_driver-bookings_endpoint', function ($value) {
    // TODO option
    $driver = pods('driver', array(
        'where' => sprintf('user.ID = %d', get_current_user_id())
    ));

    while ($driver->fetch()) {

        printf('<h1>%s&apos;s Bookings</h1>', $driver->display('post_title'));

        echo '<code style="color: initial">';
        echo '</code>';
    }
});
