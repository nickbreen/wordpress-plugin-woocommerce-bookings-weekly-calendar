<?php
// See /wp-content/plugins/woocommerce-bookings/
// See includes/admin/class-wc-bookings-calendar.php
// See includes/admin/views/html-calendar-day.php
// See includes/admin/views/html-calendar-month.php

class WC_Bookings_Calendar_Weekly //extends WC_Bookings_Calendar
{

    const FILTER_PARAM = 'filter_bookings';
    const WEEK_PARAM = 'calendar_week';
    const VIEW_PARAM = 'view';

    const VCUST = 'week-customer';
    const VPROD = 'week-product';

    private $bookings = [];
    private $products = [];
    private $customers = [];
    private $time;

    /**
     * Output the calendar view
     */
    public function output()
    {
        wp_enqueue_script('calendar-weekly', plugins_url( '../../assets/js/calendar-weekly.js', __FILE__ ), array('jquery-ui-datepicker'), '0.0.0', true);
        wp_enqueue_style('calendar-weekly', plugins_url( '../../assets/css/calendar-weekly.css', __FILE__ ));

        $product_filter = filter_input(INPUT_GET, self::FILTER_PARAM, FILTER_CALLBACK, ['options' => function ($value) {
            return absint($value) ?? '';
        }]);

        $view = filter_input(INPUT_GET, self::VIEW_PARAM, FILTER_CALLBACK, ['options' => function ($value) {
            return in_array($value, [self::VCUST, self::VPROD]) ? $value : NULL;
        }]) ?: self::VCUST;

        // Work out the first day of the week
        $iFirstDay = get_option('start_of_week', 1);
        // Clamp the date to the start of the week
        $this->time = filter_input(INPUT_GET, self::WEEK_PARAM, FILTER_CALLBACK, ['options' => function ($value) use ($iFirstDay) {
            return strtotime("last sunday +{$iFirstDay} days", strtotime($value));
        }]) ?: strtotime("this sunday +{$iFirstDay} days", time());

        $bookings = WC_Bookings_Controller::get_bookings_in_date_range(
            strtotime("midnight last sunday +{$iFirstDay} days", $this->time),
            strtotime("midnight next sunday +{$iFirstDay} days", $this->time),
            $product_filter,
            FALSE
        );

        $products = WC_Bookings_Admin::get_booking_products();
        foreach ($products as $product) {
            $this->products[$product->ID] = $product;
        }

        foreach ($bookings as $booking) {
            $customer = $booking->get_customer();
            $this->customers[$customer->user_id] = $customer;
        }

        foreach ($bookings as $booking) {
            $this->bookings
                    [$view == self::VCUST ? $booking->get_customer()->user_id : $booking->get_product_id()]
                    [$view == self::VPROD ? $booking->get_customer()->user_id : $booking->get_product_id()]
                    [$booking->get_start_date('N')]
                    [] = $booking;
        }

        require('views/html-calendar-week.php');
    }

    /**
     * Filters products for narrowing search
     */
    public function product_filters()
    {
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
    public function resources_filters()
    {
        $filters = array();

        $resources = WC_Bookings_Admin::get_booking_resources();

        foreach ( $resources as $resource ) {
            $filters[ $resource->ID ] = $resource->post_title;
        }

        return $filters;
    }

}
