<?php
/**
 * Controller for the location views in BP (using mvc terms here)
 */
function bp_em_my_coupons() {
	global $bp, $EM_Coupon;
	if( !is_object($EM_Coupon) && !empty($_REQUEST['coupon_id']) ){
		$EM_Coupon = new EM_Coupon($_REQUEST['coupon_id']);
	}

	do_action( 'bp_em_my_coupons' );

	$template_title = 'bp_em_my_coupons_title';
	$template_content = 'bp_em_my_coupons_content';

	if( !empty($_GET['action']) ){
		switch($_GET['action']){
			case 'edit':
				$template_title = 'bp_em_my_coupons_editor_title';
				break;
		}
	}

	add_action( 'bp_template_title', $template_title );
	add_action( 'bp_template_content', $template_content );

	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_em_my_coupons_title() {
	_e( 'My Coupons', 'dbem' );
}

/**
 * Determines whether to show location page or coupons page, and saves any updates to the location or coupons
 * @return null
 */
function bp_em_my_coupons_content() {
	em_locate_template('buddypress/my-coupons.php', true);
}

function bp_em_my_coupons_editor_title() {
	global $EM_Coupon;
	if( empty($EM_Coupon) || !is_object($EM_Coupon) ){
		$title = __('Add Coupon', 'dbem');
	}else{
		$title = __('Edit Coupon', 'dbem');
	}
}
?>