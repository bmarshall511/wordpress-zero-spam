<?php
/**
 * Spammer Log Template
 *
 * Content for the plugin spammer log page.
 *
 * @since 1.5.0
 */

/**
 * Security Note: Blocks direct access to the plugin PHP files.
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$total_spam = count( $spam['raw'] );
$unique_spammers = count( $spam['unique_spammers'] );
$per_day = $this->num_days( end( $spam['raw'] )->date ) ? number_format( ( count( $spam['raw'] ) / $this->num_days( end( $spam['raw'] )->date ) ), 2 ) : 0;
$num_days = $this->num_days( end( $spam['raw'] )->date );
$starting_date = end( $spam['raw'] )->date;

$stats_summary = ucfirst( $this->num_to_word( $spam['registration_spam'] ) ) . ' (';
$stats_summary .= '<b>' . ( $spam['registration_spam'] / count( $spam['raw'] ) * 100 ) . '%) were from registrations</b>';
if ( $this->plugins['cf7'] ):
	$stats_summary .= ', ';
else:
	$stats_summary .= ' and ';
endif;
$stats_summary .= '<b>' . $this->num_to_word( $spam['comment_spam'] )  . ' (';
$stats_summary .= ( $spam['comment_spam'] / count( $spam['raw'] ) * 100 ) . '%) from comments</b>';
if ( $this->plugins['cf7'] ):
	$stats_summary .= ' and ';
else:
	$stats_summary .= '.';
endif;
if ( $this->plugins['cf7'] ):
	$stats_summary .= '<b>' . $this->num_to_word( $spam['cf7_spam'] )  . ' (';
	$stats_summary .= ( $spam['cf7_spam'] / count( $spam['raw'] ) * 100 ) . '%) from Contact Form 7 submissions</b>.';
endif;

?><div class="zero-spam__row">
	<div class="zero-spam__cell">
		<div class="zero-spam__widget zero-spam__bg--secondary">
			<div class="zero-spam__inner">
				<h3><?php echo __( 'Summary', 'zerospam' ); ?></h3>
				<div class="zero-spam__row">
					<div class="zero-spam__stat">
						<?php echo __( 'Protected', 'zerospam' ); ?>
						<b><?php echo number_format( $num_days, 0 ); ?> <?php echo __( 'days', 'zerospam' ); ?></b>
					</div>
					<div class="zero-spam__stat">
						<?php echo __( 'Total Spam', 'zerospam' ); ?>
						<b><?php echo number_format( $total_spam, 0 ); ?></b>
					</div>
					<div class="zero-spam__stat">
						<?php echo __( 'Per day', 'zerospam' ); ?>
						<b><?php echo number_format( $per_day, 0 ); ?></b>
					</div>
					<div class="zero-spam__stat">
						<?php echo __( 'Unique Spammers', 'zerospam' ); ?>
						<b><?php echo number_format( $unique_spammers, 0 ); ?></b>
					</div>
				</div>
				<p><?php
				echo sprintf( _n(
					'WordPress Zero Spam has protected your site from <b>%s</b> spammer ',
					'WordPress Zero Spam has protected your site from <b>%s</b> spammers ',
				$total_spam, 'zerospam'), $this->num_to_word( $total_spam ) );

				echo sprintf( __( '<b>since %s</b> for a total of %s days. ', 'zerospam'), date( 'l, F j, Y', strtotime( $starting_date ) ), $this->num_to_word( $total_spam ) );

				echo sprintf( __( 'That\'s approximately <b>%s per day</b>. ', 'zerospam'), $per_day );
				?></p>
			</div>
		</div>
	</div>
	<div class="zero-spam__cell">
		<div class="zero-spam__widget zero-spam__bg--primary">
			<div class="zero-spam__inner">
				<div id="zero-spam__donut" class="zero-spam__donut"></div>
				<h3><?php echo __( 'Stats', 'zerospam' ); ?></h3>
				<div class="zero-spam__row">
					<div class="zero-spam__stat">
						<?php echo __( 'Comments', 'zerospam' ); ?>
						<b><?php echo number_format( $spam['comment_spam'], 0 ); ?></b>
					</div>
					<div class="zero-spam__stat">
						<?php echo __( 'Registrations', 'zerospam' ); ?>
						<b><?php echo number_format( $spam['registration_spam'], 0 ); ?></b>
					</div>
					<?php if ( $this->plugins['cf7'] ): ?>
						<div class="zero-spam__stat">
							<?php echo __( 'Contact Form 7', 'zerospam' ); ?>
							<b><?php echo number_format( $spam['cf7_spam'], 0 ); ?></b>
						</div>
					<?php endif; ?>
				</div>
				<p><?php echo $stats_summary; ?></p>
			</div>
		</div>
	</div>
</div>

<div class="zero-spam__widget">
    <div class="zero-spam__inner">
        <?php if ( count( $spam['by_date'] ) ): ?>
		<h3><?php echo __( 'All Time', 'zerospam' ); ?></h3>
		<div id="graph"></div>
		<script>
		jQuery(function() {
			// Use Morris.Area instead of Morris.Line
			Morris.Area({
				element: 'graph',
				behaveLikeLine: true,
				data: [
					<?php foreach( $spam['by_date'] as $date => $ary ): ?>
					{
					    'date': '<?php echo $date; ?>',
					    'spam_comments': <?php echo $ary['comment_spam']; ?>,
					    'spam_registrations': <?php echo $ary['registration_spam']; ?>,
					    <?php if ( $this->plugins['cf7'] ): ?>'spam_cf7': <?php echo $ary['cf7_spam']; ?><?php endif; ?>
					},
					<?php endforeach; ?>
				],
				xkey: 'date',
				ykeys: [
					'spam_comments',
					'spam_registrations',
					<?php if ( $this->plugins['cf7'] ): ?>'spam_cf7',<?php endif; ?>
				],
				labels: [
					'<?php echo __( 'Spam Comments', 'zerospam' ); ?>',
					'<?php echo __( 'Spam Registrations', 'zerospam' ); ?>',
					<?php if ( $this->plugins['cf7'] ): ?>'<?php echo __( 'Contact Form 7', 'zerospam' ); ?>',<?php endif; ?>
				],
				xLabels: 'day',
				lineColors: [
					'#00639e',
					'#ff183a',
					'#1b1e24'
				],
		  	});

		  	Morris.Donut({
				element: 'zero-spam__donut',
				data: [
					{value: <?php echo ( $spam['comment_spam'] / count( $spam['raw'] ) * 100 ); ?>, label: 'Comments'},
					{value: <?php echo ( $spam['registration_spam'] / count( $spam['raw'] ) * 100 ); ?>, label: 'Registrations'},
					<?php if ( $this->plugins['cf7'] ): ?>{value: <?php echo ( $spam['cf7_spam'] / count( $spam['raw'] ) * 100 ); ?>, label: 'Contact Form 7'}<?php endif; ?>
				],
				backgroundColor: '#ff183a',
				labelColor: '#ffffff',
				colors: [
					'#fd687e',
					'#fbacb8',
					'#fbc3cb'
				],
				formatter: function (x) { return x + "%"}
			});
		});
		</script>
		<table class="zero-spam__table">
            <thead>
                <tr>
                    <th><?php echo __( 'ID', 'zerospam' ); ?></th>
                    <th><?php echo __( 'Date', 'zerospam' ); ?></th>
                    <th><?php echo __( 'Type', 'zerospam' ); ?></th>
                    <th><?php echo __( 'IP', 'zerospam' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( $spam['raw'] as $key => $obj ):
                    switch ( $obj->type ) {
                        case 1:
                            $type = __( 'Registration', 'zerospam' );
                        break;
                        case 2:
                            $type = __( 'Comment', 'zerospam' );
                        break;
                        case 3:
                            $type = __( 'Contact Form 7', 'zerospam' );
                        break;
                    }
                ?>
                <tr>
                    <td><?php echo $obj->zerospam_id; ?></td>
                    <td><?php echo date( 'l, F j, Y  g:i:sa', strtotime( $obj->date ) ); ?></td>
                    <td><?php echo $type; ?></td>
                    <td><?php echo long2ip( $obj->ip ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
		<?php else: ?>
			<?php echo __( 'No spammers detected yet!', 'zerospam'); ?>
        <?php endif; ?>
    </div>
</div>

<div class="zero-spam__widget zero-spam__bg--trinary">
	<div class="zero-spam__inner">
		<h3><?php echo __( 'Reset Spam Logs', 'zerospam' ); ?></h3>
		<p><?php echo __( 'WARNING: THIS WILL PERMANENTLY DELETE ALL SPAM LOG DATA.', 'zerospam'  ); ?></p>
		<form method="post" action="options.php">
            <?php wp_nonce_field( 'zerospam-options' ); ?>

            <?php submit_button( __( 'Reset Log' ,'zerospam' ), 'primary', 'submit', false ); ?>
        </form>
	</div>
</div>