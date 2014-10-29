<?php

include( ABSPATH . 'wp-content/plugins/'.$emp_dir .'/add-ons/coupons/coupons-admin.php');

/**
 * Due to lack of hooks, we need our own version of EM_Coupons_Admin to get this
 * working in Buddypress (some links are hard coded to wp_admin)
 */
class Zswim_EM_Coupons_Admin extends EM_Coupons_Admin {

	static function init(){
		// Unlike parent, do nothing (as coupon-admin.php already included elsewhere)
	}

	static function admin_page($args = array()){
		global $EM_Coupon, $EM_Notices;
		//load coupon if necessary
		$EM_Coupon = !empty($_REQUEST['coupon_id']) ? new EM_Coupon_Admin($_REQUEST['coupon_id']) : new EM_Coupon_Admin();
		//save coupon if necessary
		if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'coupon_save' && wp_verify_nonce($_REQUEST['_wpnonce'], 'coupon_save') ){
		if ( $EM_Coupon->get_post() && $EM_Coupon->save() ) {
			//Success notice
			$EM_Notices->add_confirm( $EM_Coupon->feedback_message );
		}else{
			$EM_Notices->add_error( $EM_Coupon->get_errors() );
		}
		}
		//Delete if necessary
		if( !empty($_REQUEST['action']) && $_REQUEST['action'] == 'coupon_delete' && wp_verify_nonce($_REQUEST['_wpnonce'], 'coupon_delete_'.$EM_Coupon->coupon_id) ){
		if ( $EM_Coupon->delete() ) {
			$EM_Notices->add_confirm( $EM_Coupon->feedback_message );
		}else{
			$EM_Notices->add_error( $EM_Coupon->get_errors() );
		}
		}
		//Display relevant page
		if( !empty($_GET['action']) && $_GET['action']=='edit' ){
		if( empty($_REQUEST['redirect_to']) ){
			$_REQUEST['redirect_to'] = em_add_get_params($_SERVER['REQUEST_URI'], array('action'=>null, 'coupon_id'=>null));
		}
		self::edit_form();
		}elseif( !empty($_GET['action']) && $_GET['action']=='view' ){
		self::view_page();
		}else{
		self::select_page();
		}
	}


	static function select_page() {
		global $wpdb, $EM_Pro, $EM_Notices;
		$url = empty($url) ? $_SERVER['REQUEST_URI']:$url; //url to this page
		$limit = ( !empty($_REQUEST['limit']) && is_numeric($_REQUEST[ 'limit']) ) ? $_REQUEST['limit'] : 20;//Default limit
		$page = ( !empty($_REQUEST['pno']) ) ? $_REQUEST['pno']:1;
		$offset = ( $page > 1 ) ? ($page-1)*$limit : 0;
		$args = array('limit'=>$limit, 'offset'=>$offset);
		$coupons_mine_count = EM_Coupons::count( array('owner'=>get_current_user_id()) );
		$coupons_all_count = current_user_can('manage_others_bookings') ? EM_Coupons::count():0;
		if( !empty($_REQUEST['view']) && $_REQUEST['view'] == 'others' && current_user_can('manage_others_bookings') ){
		$coupons = EM_Coupons::get( $args );
		$coupons_count = $coupons_all_count;
		}else{
		$coupons = EM_Coupons::get( array_merge($args, array('owner'=>get_current_user_id())) );
		$coupons_count = $coupons_mine_count;
		}
		?>
	<div class='wrap'>
		<div class="icon32" id="icon-bookings"><br></div>
		<h2><?php _e('Edit Coupons','em-pro'); ?></h2>
		<a href="<?php echo add_query_arg(array('action'=>'edit')); ?>" class="em-button button add-new-h2"><?php _e('Add New','dbem'); ?></a>
		<?php echo $EM_Notices; ?>
		<form id='coupons-filter' method='post' action=''>
		<input type='hidden' name='pno' value='<?php echo $page ?>' />
		<div class="tablenav">
			<div class="actions">
			<div class="subsubsub">
				<a href='<?php echo em_add_get_params($_SERVER['REQUEST_URI'], array('view'=>null, 'pno'=>null)); ?>' <?php echo ( empty($_REQUEST['view']) ) ? 'class="current"':''; ?>><?php echo sprintf( __( 'My %s', 'dbem' ), __('Coupons','em-pro')); ?> <span class="count">(<?php echo $coupons_mine_count; ?>)</span></a>
				<?php if( current_user_can('manage_others_bookings') ): ?>
				&nbsp;|&nbsp;
				<a href='<?php echo em_add_get_params($_SERVER['REQUEST_URI'], array('view'=>'others', 'pno'=>null)); ?>' <?php echo ( !empty($_REQUEST['view']) && $_REQUEST['view'] == 'others' ) ? 'class="current"':''; ?>><?php echo sprintf( __( 'All %s', 'dbem' ), __('Coupons','em-pro')); ?> <span class="count">(<?php echo $coupons_all_count; ?>)</span></a>
				<?php endif; ?>
			</div>
			</div>
			<?php
			if ( $coupons_count >= $limit ) {
			$coupons_nav = em_admin_paginate( $coupons_count, $limit, $page );
			echo $coupons_nav;
			}
			?>
		</div>
		<?php if ( $coupons_count > 0 ) : ?>
		<table class='widefat'>
			<thead>
			<tr>
				<th><?php _e('Name', 'em-pro') ?></th>
				<th><?php _e('Code', 'em-pro') ?></th>
				<th><?php _e('Description', 'em-pro') ?></th>
				<th><?php _e('Discount', 'em-pro') ?></th>
				<th><?php _e('Uses', 'em-pro') ?></th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th><?php _e('Name', 'em-pro') ?></th>
				<th><?php _e('Code', 'em-pro') ?></th>
				<th><?php _e('Description', 'em-pro') ?></th>
				<th><?php _e('Discount', 'em-pro') ?></th>
				<th><?php _e('Uses', 'em-pro') ?></th>
			</tr>
			</tfoot>
			<tbody>
			<?php foreach ($coupons as $EM_Coupon) : ?> 
				<tr>
				<td>
					<a href='<?php echo add_query_arg(array('coupon_id'=>$EM_Coupon->coupon_id,'action'=>'edit')) ?>'><?php echo $EM_Coupon->coupon_name ?></a>
					<div class="row-actions">
					<span class="trash"><a class="submitdelete" href="<?php echo add_query_arg(array('coupon_id'=>$EM_Coupon->coupon_id,'action'=>'coupon_delete','_wpnonce'=>wp_create_nonce('coupon_delete_'.$EM_Coupon->coupon_id))) ?>"><?php _e('Delete','em-pro')?></a></span>
					</div>
				</td>
				<td><?php echo esc_html($EM_Coupon->coupon_code); ?></td>
				<td><?php echo esc_html($EM_Coupon->coupon_description); ?></td>
				<td><?php echo $EM_Coupon->get_discount_text(); ?></td>
				<td>
					<a href='?action=view&amp;coupon_id=<?php echo $EM_Coupon->coupon_id ?>'>
					<?php
					if( !empty($EM_Coupon->coupon_max) ){
					echo esc_html($EM_Coupon->get_count() .'/'. $EM_Coupon->coupon_max);
					}else{
					echo esc_html($EM_Coupon->get_count() .'/'. __('Unlimited','em-pro'));
					}
					?>
					</a>
				</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php else: ?>
		<br class="clear" />
		<p><?php _e('No coupons have been inserted yet!', 'dbem') ?></p>
		<?php endif; ?>

		<?php if ( !empty($coupons_nav) ) echo '<div class="tablenav">'. $coupons_nav .'</div>'; ?>
		</form>

	</div> <!-- wrap -->
	<?php
	}
}

Zswim_EM_Coupons_Admin::init();