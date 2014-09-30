<?php

  // We need to load EM_Coupons_Admin, as this is only normally available for admins
  $pathinfo = pathinfo( EMP_SLUG );
  $emp_dir = $pathinfo['dirname'];
  include( ABSPATH . 'wp-content/plugins/'.$emp_dir .'/add-ons/coupons/coupons-admin.php');

  EM_Coupons_Admin::admin_page();