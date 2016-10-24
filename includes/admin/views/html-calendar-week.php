<div class="wrap woocommerce">
	<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
	<h2><?php _e('Bookings by week', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?></h2>

	<form method="get" id="mainform" enctype="multipart/form-data" class="wc_bookings_calendar_form">
		<input type="hidden" name="post_type" value="wc_booking" />
		<input type="hidden" name="page" value="booking_calendar_weekly" />
		<input type="hidden" name="tab" value="calendar" />
		<input type="hidden" name="calendar_week" id="calendar_week" value="<?php echo date('Y-m-d', $this->time); ?>" />
		<div class="tablenav">
			<div class="filters">
				<select id="calendar-bookings-filter" name="filter_bookings" class="wc-enhanced-select" style="width:200px">
					<option value=""><?php _e('Filter Bookings', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?></option>
					<?php if ($product_filters = $this->product_filters()) : ?>
						<optgroup label="<?php _e('By bookable product', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?>">
							<?php foreach ($product_filters as $filter_id => $filter_name) : ?>
								<option value="<?php echo $filter_id; ?>" <?php selected($product_filter, $filter_id); ?>><?php echo $filter_name; ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endif; ?>
					<?php if ($resources_filters = $this->resources_filters()) : ?>
						<optgroup label="<?php _e('By resource', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?>">
							<?php foreach ($resources_filters as $filter_id => $filter_name) : ?>
								<option value="<?php echo $filter_id; ?>" <?php selected($product_filter, $filter_id); ?>><?php echo $filter_name; ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endif; ?>
				</select>
			</div>
			<div class="date_selector">
				<div>
					<label>Week <b><?php echo date('W', $this->time); ?></b> starting:&nbsp;
						<input class="week-picker"
							data-datepicker.first-day="<?php echo get_option('start_of_week', 1); ?>"
							data-datepicker.date-format="D, d M yy"
							data-datepicker.alt-field="#calendar_week"
					        data-datepicker.alt-format="yy-mm-dd"
							value="<?php echo date('D, j M Y', $this->time); ?> "/>
					</label>
					<button>Go</button>
				</div>
			</div>
			<div class="views">
				<a class="new-booking" href="<?php echo admin_url('edit.php?post_type=wc_booking&page=create_booking'); ?>"><?php _e('New Booking', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?></a>
				<a class="day" href="<?php echo esc_url(add_query_arg(array('view' => 'day', 'page' => 'booking_calendar'))); ?>"><?php _e( 'Day View', 'woocommerce-bookings' ); ?></a>
				<a class="month" href="<?php echo esc_url(add_query_arg(array('view' => 'month', 'page' => 'booking_calendar'))); ?>"><?php _e( 'Month View', 'woocommerce-bookings' ); ?></a>
			</div>
		</div>

		<table class="wc_bookings_calendar wc_bookings_calendar_weekly widefat">
			<caption>Week <b><?php echo date('W', $this->time); ?></b> starting <b><?php echo date('D, j M Y', $this->time); ?></b></caption>
			<thead>
				<tr>
					<th width="20%"><?php _e('Product', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?></th>
					<?php for ($ii = get_option('start_of_week', 1); $ii < get_option('start_of_week', 1) + 7; $ii ++) : ?>
						<th width="10%"><?php echo date_i18n(_x('l', 'date format', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'), strtotime("next sunday +{$ii} day")); ?></th>
					<?php endfor; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->bookings as $product_id => $days): ?>
					<?php $product = $this->lookup_product($product_id); ?>
					<tr>
						<td>
							<p><a class="product" title="<?php _e('Edit Product', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?>"
								href="<?php echo admin_url(sprintf('post.php?post=%d&action=edit', $product_id)); ?>"><?php echo $product_id; ?></a>
								<?php echo $product->post_title; ?></p>
						</td>
						<?php for ($ii = get_option('start_of_week', 1); $ii < get_option('start_of_week', 1) + 7; $ii ++) : ?>
							<td>
								<ul>
									<?php $persons = []; ?>
									<?php foreach($days[$ii] as $booking): ?>
										<?php $persons[$booking->status] += count($booking->get_persons()); ?>
										<li>
											<a class="booking" title="<?php _e('Edit Booking', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?>"
												href="<?php echo admin_url(sprintf('post.php?post=%d&action=edit', $booking->id)); ?>"><?php echo $booking->id; ?></a>
											/ <a class="order" title="<?php _e('Edit Order', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?>"
												href="<?php echo admin_url(sprintf('post.php?post=%d&action=edit', $booking->order_id)); ?>"><?php echo $booking->order_id; ?></a>
											for <tt><?php echo count($booking->get_persons()); ?></tt>, <b><?php echo $booking->status; ?></b>.
										</li>
									<?php endforeach; ?>
								</ul>
								<?php if ($persons): ?>
									<hr/>
									<ul>
										<?php foreach ($persons as $status => $count): ?>
											<li>Total <b><?php echo $status; ?></b>: <tt><?php echo $count; ?></tt> persons.</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</td>
						<?php endfor; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</form>
</div>
