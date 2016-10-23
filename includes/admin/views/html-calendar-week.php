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
			<div class="filters">
				<label>Week <b><?php echo date('W', $this->time); ?></b> starting:&nbsp;
					<input class="week-picker"
						data-datepicker.first-day="<?php echo get_option('start_of_week', 1); ?>"
						data-datepicker.date-format="D, j M yy"
						data-datepicker.alt-field="#calendar_week"
				        data-datepicker.alt-format="yy-mm-dd"
						value="<?php echo date('D, j M Y', $this->time); ?> "/>
				</label>
				<button>Go</button>
			</div>
		</div>

		<table class="wc_bookings_calendar widefat">
			<thead>
				<tr>
					<th><?php _e('Product', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'); ?></th>
					<?php for ($ii = get_option('start_of_week', 1); $ii < get_option('start_of_week', 1) + 7; $ii ++) : ?>
						<th><?php echo date_i18n(_x('l', 'date format', 'wordpress-plugin-woocommerce-bookings-weekly-calendar'), strtotime("next sunday +{$ii} day")); ?></th>
					<?php endfor; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->bookings as $product_id => $bookings): ?>
					<tr>
						<td><?php echo $product_id; ?></td>
						<?php for ($ii = get_option('start_of_week', 1); $ii < get_option('start_of_week', 1) + 7; $ii ++) : ?>
							<td>
								<ul>
									<?php foreach($this->list_bookings($product_id, strtotime("next sunday +{$ii} day")) as $booking): ?>
										<li style="white-space: pre; text-align: initial">
											<?php echo $booking->id; ?>
											<?php print_r($booking); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</td>
						<?php endfor; ?>
					</tr>
				<?php endforeach; ?>
					<?php /*
						$timestamp = $start_timestamp;
						$index     = 0;
						while ($timestamp <= $end_timestamp) :
							?>
							<td width="14.285%" class="<?php
							if (date('n', $timestamp) != absint($month)) {
								echo 'calendar-diff-month';
							}
							?>">
								<a href="<?php echo admin_url('edit.php?post_type=wc_booking&page=booking_calendar&view=day&tab=calendar&calendar_day=' . date('Y-m-d', $timestamp)); ?>">
									<?php echo date('d', $timestamp); ?>
								</a>
								<div class="bookings">
									<ul>
										<?php $this->list_bookings(
											date('d', $timestamp),
											date('m', $timestamp),
											date('Y', $timestamp)
										);  ?>
									</ul>
								</div>
							</td>
							<?php
							$timestamp = strtotime('+1 day', $timestamp);
							$index ++;

							if ($index % 7 === 0) {
								echo '</tr><tr>';
							}
						endwhile;
					*/ ?>
			</tbody>
		</table>
	</form>
</div>
