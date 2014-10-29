<?php

  // We need to load EM_Coupons_Admin, as this is only normally available for admins
  $pathinfo = pathinfo( EMP_SLUG );
  $emp_dir = $pathinfo['dirname'];
  include( ABSPATH . 'wp-content/plugins/events-manager-buddypress-event-coupons/add-ons/coupons/coupons-admin.php');

  Zswim_EM_Coupons_Admin::admin_page();