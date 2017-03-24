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

  employees is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 2 of the License, or
  any later version.

  employees is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with employees. If not, see {License URI}.
 */

function employees_custom_posttypes() {
  $labels = array(
    'name'                =>  'Anställda',
    'singular_name'       =>  'Anställd',
    'menu_name'           =>  'Anställda',
    'name_admin_bar'      =>  'Anställda',
    'add_new'             =>  'Ny anställd',
    'add_new_item'        =>  'Lägg till ny anställd',
    'new_item'            =>  'Ny anställd',
    'edit_item'           =>  'Redigera anställd',
    'view_item'           =>  'Visa anställd',
    'all_items'           =>  'Alla anställda',
    'search_items'        =>  'Sök anställd',
    'parent_item_colon'   =>  'Huvudanställd',
    'not_found'           =>  'Inga anställda',
    'not_found_in_trash'  =>  'Inga anställda i papperskorgen',
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
    'rewrite'             =>  array( 'slug' => 'anstallda' ),
    'capability_type'     =>  'post',
    'has_archive'         =>  true,
    'hierarchical'        =>  false,
    'supports'            =>  array( 'title', 'thumbnail' )
  );
  register_post_type( 'employees', $args );
}

add_action( 'init', 'employees_custom_posttypes' );

add_filter( 'enter_title_here', 'custom_enter_title' );

// Translate title to name of employee
function custom_enter_title( $input ) {
  global $post_type;

  if ( 'employees' === $post_type ) {
    return __( 'Ange namn här', 'se-lat6' );
  }
  return $input;
}

function employees_rewrite_flush() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry,
    // when you add a post of this CPT.
    employees_custom_posttypes();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'employees_rewrite_flush' );

/* Custom Taxonomies */

function employee_custom_taxonomies() {
  /* Type of Project */
  $labels = array(
    'name'                =>  'Roller',
    'singular_name'       =>  'Roll',
    'search_items'        =>  'Sök roller',
    'all_items'           =>  'Alla roller',
    'parent_item'         =>  'Huvudroll',
    'parent_item_colon'   =>  'Huvudroll',
    'edit_item'           =>  'Redigera roll',
    'update_item'         =>  'Updatera roll',
    'add_new_item'        =>  'Lägg till roll',
    'new_item_name'       =>  'Ny roll',
    'menu_name'           =>  'Roller',
  );
  $args = array(
    'hierarchical'        =>  true,
    'labels'              =>  $labels,
    'show_ui'             =>  true,
    'show_admin_column'   =>  true,
    'query_var'           =>  true,
    'rewrite'             =>  array( 'slug' => 'roller'),
  );
  register_taxonomy( 'roles', array( 'employees' ), $args );
}
add_action( 'init', 'employee_custom_taxonomies' );

/** Custom meta boxes **/

function employees_add_meta_boxes( $post ) {
  add_meta_box( 'employees_meta_box', __( 'Roll', 'employees_meta_plugin' ), 'employees_build_meta_box', 'employees', 'normal', 'high' );

}
add_action( 'add_meta_boxes_employees', 'employees_add_meta_boxes' );

function employees_build_meta_box( $post ) {
  // add nonce
  wp_nonce_field( basename( __FILE__ ), 'employees_meta_box_nonce' );

  // retrieve the _employees_city current value
  $current_email = get_post_meta( $post->ID, '_employees_email', true);

  // retrive the _employees_year current value
  $current_phone = get_post_meta( $post->ID, '_employees_phone', true);
  ?>
  <div class="inside">
    <h4><?php _e( 'Telefon', 'employees_meta_plugin' ); ?></h4>
    <p>
        <input type="text" name="phone" value="<?php echo $current_phone; ?>" />
    </p>
    <h4><?php _e( 'E-mail', 'employees_meta_plugin' ); ?></h4>
    <p>
        <input type="email" name="email" value="<?php echo $current_email; ?>" />
    </p>
  </div>
  <?php
}

function employees_save_meta_boxes_data ( $post_id ) {
  // verify meta box nonce
  if ( !isset( $_POST['employees_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['employees_meta_box_nonce'], basename( __FILE__ ) ) ) {
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
    update_post_meta( $post_id, '_employees_phone', sanitize_text_field( $_POST['phone'] ) );
  }
  // email string
  if ( isset( $_REQUEST['email'] ) ) {
    update_post_meta( $post_id, '_employees_email', sanitize_text_field( $_POST['email'] ) );
  }
}
add_action( 'save_post_employees', 'employees_save_meta_boxes_data', 10, 2 );
