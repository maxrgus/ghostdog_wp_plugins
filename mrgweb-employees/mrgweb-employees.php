<?php
/*
 * Plugin Name: Our Employees
 * Description: Employee contact information for easy contact
 * Version: 0.1
 * Author: Maximilian Gustafsson
 * License: GPL2
 */
 /*
  Copyright 2016 Maximilian Gustafsson

  Employee is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 2 of the License, or
  any later version.

  Employee is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with employee. If not, see {License URI}.
 */

include ( plugin_dir_path( __FILE__ ) . 'page-templater.php');

/** Set up translations **/

add_action( 'plugins_loaded', 'employee_load_textdomain' );
function employee_load_textdomain() {
  load_plugin_textdomain( 'mrgweb-employees', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

/** Enqueue styles **/
add_action( 'wp_enqueue_scripts', 'employee_styles' );
function employee_styles() {
  wp_enqueue_style( 'employee-styles', plugin_dir_url( __FILE__ ) . 'css/employees.css' );
}

function employee_custom_posttypes() {
  $labels = array(
    'name'                =>  __( 'Employees', 'mrgweb-employees'),
    'singular_name'       =>  __( 'Employee', 'mrgweb-employees'),
    'menu_name'           =>  __( 'Employees', 'mrgweb-employees'),
    'name_admin_bar'      =>  __( 'Employees', 'mrgweb-employees'),
    'add_new'             =>  __( 'New Employee', 'mrgweb-employees'),
    'add_new_item'        =>  __( 'Add new Employee', 'mrgweb-employees'),
    'new_item'            =>  __( 'New Employee', 'mrgweb-employees'),
    'edit_item'           =>  __( 'Edit Employee', 'mrgweb-employees'),
    'view_item'           =>  __( 'View Employee', 'mrgweb-employees'),
    'all_items'           =>  __( 'All Employees', 'mrgweb-employees'),
    'search_items'        =>  __( 'Search Employee', 'mrgweb-employees'),
    'parent_item_colon'   =>  __( 'Parent Employee', 'mrgweb-employees'),
    'not_found'           =>  __( 'No Employees', 'mrgweb-employees'),
    'not_found_in_trash'  =>  __( 'No Employees in trash', 'mrgweb-employees'),
  );
  $args = array(
    'labels'              =>  $labels,
    'public'              =>  true,
    'publicly_queryable'  =>  true,
    'show_ui'             =>  true,
    'show_in_menu'        =>  true,
    'menu_position'       =>  8,
    'menu_icon'           =>  'dashicons-groups',
    'query_var'           =>  true,
    'rewrite'             =>  array( 'slug' => 'employees' ),
    'capability_type'     =>  'post',
    'has_archive'         =>  true,
    'hierarchical'        =>  false,
    'supports'            =>  array( 'title', 'thumbnail' )
  );
  register_post_type( 'employee', $args );
}

add_action( 'init', 'employee_custom_posttypes' );

add_filter( 'enter_title_here', 'custom_enter_title' );

// Translate title to name of person
function custom_enter_title( $input ) {
  global $post_type;

  if ( 'employee' === $post_type ) {
    return __( 'Enter name here', 'mrgweb-employees' );
  }
  return $input;
}

function employee_rewrite_flush() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry,
    // when you add a post of this CPT.
    employee_custom_posttypes();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}

function employee_run_at_activation() {
  do_action( 'employee_settings_default_options' );
  do_action( 'employee_rewrite_flush' );
}

register_activation_hook( __FILE__, 'employee_run_at_activation' );

/* Custom Taxonomies */

function employee_custom_taxonomies() {
  /* employee organisation */
  $labels = array(
    'name'                =>  __( 'Organisations', 'mrgweb-employees'),
    'singular_name'       =>  __( 'Organisation', 'mrgweb-employees'),
    'search_items'        =>  __( 'Search Organisations', 'mrgweb-employees'),
    'all_items'           =>  __( 'All Organisations', 'mrgweb-employees'),
    'parent_item'         =>  __( 'Parent Organisation', 'mrgweb-employees'),
    'parent_item_colon'   =>  __( 'Parent Organisation', 'mrgweb-employees'),
    'edit_item'           =>  __( 'Edit Organisation', 'mrgweb-employees'),
    'update_item'         =>  __( 'Update Organisation', 'mrgweb-employees'),
    'add_new_item'        =>  __( 'Add new Organisation', 'mrgweb-employees'),
    'new_item_name'       =>  __( 'New Organisation', 'mrgweb-employees'),
    'menu_name'           =>  __( 'Organisations', 'mrgweb-employees'),
  );
  $args = array(
    'hierarchical'        =>  true,
    'labels'              =>  $labels,
    'show_ui'             =>  true,
    'show_admin_column'   =>  true,
    'query_var'           =>  true,
    'rewrite'             =>  array( 'slug' => 'organisations'),
  );
  register_taxonomy( 'organisations', array( 'employee' ), $args );
}
add_action( 'init', 'employee_custom_taxonomies' );

/** Custom meta boxes **/

function employee_add_meta_boxes( $post ) {
  add_meta_box( 'employee_meta_box', __( 'Information', 'mrgweb-employees' ), 'employee_build_meta_box', 'employee', 'normal', 'high' );

}
add_action( 'add_meta_boxes_employee', 'employee_add_meta_boxes' );

function employee_build_meta_box( $post ) {
  // add nonce
  wp_nonce_field( basename( __FILE__ ), 'employee_meta_box_nonce' );

  // retrieve the _employee_email current value
  $current_email = get_post_meta( $post->ID, '_employee_email', true);

  // retrive the _employee_email current value
  $current_phone = get_post_meta( $post->ID, '_employee_phone', true);

  // retrieve the _employee_title current value
  $current_title = get_post_meta( $post->ID, '_employee_title', true);

  // retrieve the hyperlink to the employees section
  $current_department = get_post_meta( $post->ID, '_employee_department', true);


  ?>
  <div class="inside">
    <h4><?php _e( 'Phone', 'mrgweb-employees' ); ?></h4>
    <p>
        <input type="text" name="phone" value="<?php echo $current_phone; ?>" />
    </p>
    <h4><?php _e( 'Email', 'mrgweb-employees' ); ?></h4>
    <p>
        <input type="email" name="email" value="<?php echo $current_email; ?>" />
    </p>
    <h4><?php _e( 'Title', 'mrgweb-employees' ); ?></h4>
    <p>
        <input type="text" name="title" value="<?php echo $current_title; ?>" />
    </p>
    <h4><?php _e( 'Department', 'mrgweb-employees' ); ?></h4>
    <p>
        <input type="text" name="department" value="<?php echo $current_department; ?>" />
    </p>
  </div>
  <?php
}

function employee_save_meta_boxes_data ( $post_id ) {
  // verify meta box nonce
  if ( !isset( $_POST['employee_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['employee_meta_box_nonce'], basename( __FILE__ ) ) ) {
    return;
  }
  // return if autosave
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }
  // check user permissions
  if ( ! current_user_can( 'edit_post', $post_id ) ) {
    return;
  }

  // store custom fields values
  // phone string
  if ( isset( $_REQUEST['phone'] ) ) {
    update_post_meta( $post_id, '_employee_phone', sanitize_text_field( $_POST['phone'] ) );
  }
  // email string
  if ( isset( $_REQUEST['email'] ) ) {
    update_post_meta( $post_id, '_employee_email', sanitize_text_field( $_POST['email'] ) );
  }
  if ( isset( $_REQUEST['title'] ) ) {
    update_post_meta( $post_id, '_employee_title', sanitize_text_field( $_POST['title'] ) );
  }
  if ( isset( $_REQUEST['department'] ) ) {
    update_post_meta( $post_id, '_employee_department', sanitize_text_field( $_POST['department'] ) );
  }
}
add_action( 'save_post_employee', 'employee_save_meta_boxes_data', 10, 2 );


/**  Admin settings menu **/

add_action( 'admin_menu', 'employee_add_admin_menu' );
add_action( 'admin_init', 'employee_settings_init' );

function employee_add_admin_menu() {
  add_options_page( __('Our Employees', 'mrgweb-employees'), __('Our Employees', 'mrgweb-employees'), 'manage_options', 'ouremployees', 'employee_options_page' );
}

function employee_settings_init() {
  register_setting( 'employee_settings_group', 'employee_settings' );

  add_settings_section( 'employee_settings_group_section',
      __( 'Choose which fields to display', 'mrgweb-employees' ),
      'employee_settings_section_callback',
      'employee_settings_group' );

  add_settings_field( 'employee_name_field',
      __( 'Display Name: ', 'mrgweb-employees'),
      'employee_name_field_render',
      'employee_settings_group',
      'employee_settings_group_section' );

  add_settings_field( 'employee_email_field',
      __( 'Display Email: ', 'mrgweb-employees'),
      'employee_email_field_render',
      'employee_settings_group',
      'employee_settings_group_section' );

  add_settings_field( 'employee_phone_field',
      __( 'Display Phone: ', 'mrgweb-employees'),
      'employee_phone_field_render',
      'employee_settings_group',
      'employee_settings_group_section' );

  add_settings_field( 'employee_organisation_field',
      __( 'Display Organisation: ', 'mrgweb-employees'),
      'employee_organisation_field_render',
      'employee_settings_group',
      'employee_settings_group_section' );

  add_settings_field( 'employee_department_field',
      __( 'Display Department: ', 'mrgweb-employees'),
      'employee_department_field_render',
      'employee_settings_group',
      'employee_settings_group_section' );

  add_settings_field( 'employee_title_field',
      __( 'Display Title: ', 'mrgweb-employees'),
      'employee_title_field_render',
      'employee_settings_group',
      'employee_settings_group_section' );
}

function employee_name_field_render() {
  $options = get_option( 'employee_settings');
  ?> <input type='checkbox' name='employee_settings[employee_name_field]' <?php checked( $options['employee_name_field'], 1 ); ?> value='1'> <?php 
}
function employee_email_field_render() {
  $options = get_option( 'employee_settings');
  ?> <input type='checkbox' name='employee_settings[employee_email_field]' <?php checked( $options['employee_email_field'], 1 ); ?> value='1'> <?php 
}
function employee_phone_field_render() {
  $options = get_option( 'employee_settings');
  ?> <input type='checkbox' name='employee_settings[employee_phone_field]' <?php checked( $options['employee_phone_field'], 1 ); ?> value='1'> <?php 
}
function employee_organisation_field_render() {
  $options = get_option( 'employee_settings');
  ?> <input type='checkbox' name='employee_settings[employee_organisation_field]' <?php checked( $options['employee_organisation_field'], 1 ); ?> value='1'> <?php 
}
function employee_department_field_render() {
  $options = get_option( 'employee_settings');
  ?> <input type='checkbox' name='employee_settings[employee_department_field]' <?php checked( $options['employee_department_field'], 1 ); ?> value='1'> <?php 
}
function employee_title_field_render() {
  $options = get_option( 'employee_settings');
  ?> <input type='checkbox' name='employee_settings[employee_title_field]' <?php checked( $options['employee_title_field'], 1 ); ?> value='1'> <?php 
}

function employee_settings_section_callback() {
  echo __( 'Check the ones you wish to display', 'mrgweb-employees' );
}

function employee_options_page() {
  $settings_title = __( 'Our Employees', 'mrgweb-employees' );
  $settings_body = __( 'Configure which fields that are displayed', 'mrgweb-employees' );


  echo '<form action="options.php" method="post">';

  settings_fields( 'employee_settings_group' );
  do_settings_sections( 'employee_settings_group' );
  submit_button();

  echo '</form>';
}

/** Set default options on activation **/
function employee_settings_default_options() {
  $default = array(
    'employee_name_field'         =>  '1',
    'employee_email_field'        =>  '1',
    'employee_phone_field'        =>  '1',
    'employee_department_field'   =>  '1',
    'employee_title_field'        =>  '1',
    'employee_organisation_field' =>  '0',
    );
  update_option( 'employee_settings', $default );
}