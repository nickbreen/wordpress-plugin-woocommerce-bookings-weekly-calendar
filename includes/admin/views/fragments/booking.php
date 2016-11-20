<li class="booking <?php echo $booking->status; ?>" id="booking-<?php echo $booking->id; ?>"
        data-product-id="<?php echo $booking->product_id; ?>"
        data-booking-id="<?php echo $booking->id; ?>">
    <a class="order"
        title="<?php _e('Edit Order', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?>"
        href="<?php echo admin_url(sprintf('post.php?post=%d&action=edit', $booking->order_id)); ?>"><?php echo $booking->order_id; ?></a>/<a
        class="booking"
        title="<?php _e('Edit Booking', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?>"
        href="<?php echo admin_url(sprintf('post.php?post=%d&action=edit', $booking->id)); ?>"><?php echo $booking->id; ?></a>
    <tt><?php echo count($booking->get_persons()); ?></tt> persons <b><?php echo $booking->status; ?></b>.
</li>
