<?php
/*
Plugin Name: Events Manager BP Event Coupons
Version: 1.0
Plugin URI: http://www.andyplace.co.uk
Description: Additional Buddypress option to allow users to add/edit coupons for events
Author: Andy Place
Author URI: http://wp-events-plugin.com
*/


// @TODO: Disable if Events Manager Pro plugin not active. Add flash notice.


function bp_em_coupons_includes() {

  $includes = array(
    'buddypress/screens/my-coupons.php',
  );

  $path = plugin_dir_path( __FILE__ );

  foreach( $includes as $file ) {
    require( $path . $file );
  }

}
add_action( 'bp_events_includes', 'bp_em_coupons_includes');


/**
 * Hook into EM Buddy Press Component creation and add extra sub menu item for coupons
 */
function bp_em_coupons_sub_nav() {

  $em_link = trailingslashit( bp_displayed_user_domain() . em_bp_get_slug() );

  $sub_nav = array(
    'name' => __( 'My Coupons', 'dbem' ),
    'slug' => 'my-coupons',
    'parent_slug' => em_bp_get_slug(),
    'parent_url' => $em_link,
    'screen_function' => 'bp_em_my_coupons',
    'position' => 45,
    'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
  );

  bp_core_new_subnav_item( $sub_nav );
}
add_action( 'bp_events_setup_nav', 'bp_em_coupons_sub_nav' );


/**
 * We want to mimic EM behavior, so hook into em_locate_template function, and extend search into
 * this plugins template dir, if template not found.
 */
function bp_em_coupons_locate_template($located, $template_name, $load, $args) {
  if( !$located ){
    if ( file_exists(plugin_dir_path( __FILE__ ).'/templates/'.$template_name) ) {
      $located = plugin_dir_path( __FILE__ ).'/templates/'.$template_name;
    }
  }
  return $located;
}
add_filter('em_locate_template', 'bp_em_coupons_locate_template', 10, 4);