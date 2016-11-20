<?php foreach ($this->bookings as $customer_id => $product_bookings): ?>
	<?php $customer = $this->customers[$customer_id]; ?>
	<?php $persons = []; ?>
	<?php foreach($product_bookings as $product_id => $days): ?>
		<?php for ($ii = $iFirstDay; $ii < $iFirstDay + 7; $ii ++) : ?>
			<?php foreach($days[$ii] as $booking): ?>
				<?php $persons[$booking->status] += count($booking->get_persons()); ?>
			<?php endforeach; ?>
		<?php endfor; ?>
	<?php endforeach; ?>
	<?php $firstRow = TRUE; ?>
	<?php foreach($product_bookings as $product_id => $days): ?>
		<?php
		$duration_type = get_post_meta($product_id, '_wc_booking_duration_type', true);
		$duration = max(absint(get_post_meta($product_id, '_wc_booking_duration', true)), 0);
		$duration_unit = get_post_meta($product_id, '_wc_booking_duration_unit', true);
		?>
		<tr>
			<?php if ($firstRow): ?>
				<?php $firstRow = FALSE; ?>
				<td rowspan="<?php echo count($product_bookings); ?>">
					<p><b><a class="customer" title="<?php _e('Edit Customer', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?> <?php echo $customer_id; ?>"
						href="<?php echo admin_url(sprintf('edit-user.php?user_id=%d', $customer_id)); ?>"><?php echo $customer->name; ?></a></b></p>
						<p><a class="customer" title="<?php _e('Email Customer', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?> <?php echo $customer_id; ?>"
							href="mailto:<?php echo $customer->email; ?>"><?php echo $customer->email; ?></a></p>
					<?php if ($persons): ?>
						<ul>
							<?php foreach ($persons as $status => $count): ?>
								<li><tt><?php echo $count; ?></tt> persons <b><?php echo $status; ?></b>.</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</td>
			<?php endif; ?>
			<?php for ($ii = $iFirstDay; $ii < $iFirstDay + 7; $ii ++) : ?>
				<?php $day = $days[$ii]; ?>
				<?php if ($day && $duration_type == 'fixed' && $duration_unit == 'day'): ?>
					<td colspan="<?php echo min($duration, $iFirstDay + 7 - $ii); ?>" class="booked">
					<?php $ii += $duration -1; ?>
				<?php else: ?>
					<td>
				<?php endif; ?>
					<?php if ($day): ?>
						<p><a class="product" title="<?php _e('Edit Product', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?> <?php echo $product_id; ?>"
							href="<?php echo admin_url(sprintf('post.php?post=%d&action=edit', $product_id)); ?>"><?php echo $this->products[$product_id]->post_title; ?></a></p>
					<?php endif; ?>
					<ul>
						<?php foreach($day as $booking): ?>
							<?php require("booking.php"); ?>
						<?php endforeach; ?>
					</ul>
				</td>
			<?php endfor; ?>
		</tr>
		<?php unset($duration_type, $duration, $duration_unit); ?>
	<?php endforeach; ?>
<?php endforeach; ?>
