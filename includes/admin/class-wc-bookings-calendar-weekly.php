<?php
// See /wp-content/plugins/woocommerce-bookings/
// See includes/admin/class-wc-bookings-calendar.php
// See includes/admin/views/html-calendar-day.php
// See includes/admin/views/html-calendar-month.php

class WC_Bookings_Calendar_Weekly //extends WC_Bookings_Calendar
{

    const FILTER_PARAM = 'filter_bookings';
    const WEEK_PARAM = 'calendar_week';

    private $bookings;
    private $time;

    /**
     * Output the calendar view
     */
    public function output() {
        wp_enqueue_script('week-picker', plugins_url( '../../assets/js/calendar-weekly.js', __FILE__ ), array('jquery-ui-datepicker'), '0.0.3', true);

        $product_filter = isset($_REQUEST[self::FILTER_PARAM]) ? absint($_REQUEST[self::FILTER_PARAM]) : '';
        // Work out the first day of the week
        $iFirstDay = get_option('start_of_week', 1);
        $firstday = date('l', strtotime("this sunday +{$iFirstDay} days"));
        // Clamp the date to the start of the week
        $this->time = strtotime("last {$firstday}", strtotime($_REQUEST[self::WEEK_PARAM]) ?: time());

        $this->bookings = WC_Bookings_Controller::get_bookings_in_date_range(
            strtotime("midnight last {$firstday}", $this->time),
            strtotime("midnight next {$firstday}", $this->time),
            $product_filter,
            false
        );

        require('views/html-calendar-week.php');
    }

    /**
     * Filters products for narrowing search
     */
    public function product_filters() {
        $filters = array();

        $products = WC_Bookings_Admin::get_booking_products();

        foreach ( $products as $product ) {
            $filters[ $product->ID ] = $product->post_title;

            $resources = wc_booking_get_product_resources( $product->ID );

            foreach ( $resources as $resource ) {
                $filters[ $resource->ID ] = $resource->post_title;
            }
        }

        return $filters;
    }

    /**
     * Filters resources for narrowing search
     */
    public function resources_filters() {
        $filters = array();

        $resources = WC_Bookings_Admin::get_booking_resources();

        foreach ( $resources as $resource ) {
            $filters[ $resource->ID ] = $resource->post_title;
        }

        return $filters;
    }

}
