<?php
/*
 * Plugin Name: Our Projects
 * Description: Showcase projects as posttypes.
 * Version: 0.1
 * Author: Maximilian Gustafsson
 * License: GPL2
 */
 /*
  Copyright 2016 Maximilian Gustafsson

  Projects is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 2 of the License, or
  any later version.

  Projects is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Projects. If not, see {License URI}.
 */
function projects_enqueue_script() {
      wp_enqueue_media();

      // Registers and enqueues the required javascript.
      wp_register_script( 'projects-functions', plugin_dir_url( __FILE__ ) . 'js/functions.js', array( 'jquery' ) );
      wp_localize_script( 'projects-functions', 'meta_image',
            array(
                'title' => __( 'Choose or Upload an Image', 'prfx-textdomain' ),
                'button' => __( 'Use this image', 'prfx-textdomain' ),
            )
        );
      wp_enqueue_script( 'projects-functions' );
}
add_action( 'admin_enqueue_scripts', 'projects_enqueue_script' );
function add_custom_posttypes() {
  $labels = array(
    'name'                =>  'Projekt',
    'singular_name'       =>  'Projekt',
    'menu_name'           =>  'Projekt',
    'name_admin_bar'      =>  'Projekt',
    'add_new'             =>  'Nytt projekt',
    'add_new_item'        =>  'Lägg till nytt projekt',
    'new_item'            =>  'Nytt projekt',
    'edit_item'           =>  'Redigera projekt',
    'view_item'           =>  'Visa projekt',
    'all_items'           =>  'Alla projekt',
    'search_items'        =>  'Sök projekt',
    'parent_item_colon'   =>  'Huvudprojekt',
    'not_found'           =>  'Inga projekt',
    'not_found_in_trash'  =>  'Inga projekt i papperskorgen',
  );
  $args = array(
    'labels'              =>  $labels,
    'public'              =>  true,
    'publicly_queryable'  =>  true,
    'show_ui'             =>  true,
    'show_in_menu'        =>  true,
    'menu_position'       =>  5,
    'menu_icon'           =>  'dashicons-admin-multisite',
    'query_var'           =>  true,
    'rewrite'             =>  array( 'slug' => 'projekt' ),
    'capability_type'     =>  'post',
    'has_archive'         =>  true,
    'hierarchical'        =>  false,
    'supports'            =>  array( 'title', 'editor', 'thumbnail' )
  );
  register_post_type( 'projects', $args );
}

add_action( 'init', 'add_custom_posttypes' );


function my_rewrite_flush() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry,
    // when you add a post of this CPT.
    add_custom_posttypes();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'my_rewrite_flush' );

/* Custom Taxonomies */

function add_custom_taxonomies() {
  /* Type of Project */
  $labels = array(
    'name'                =>  'Projektkategorier',
    'singular_name'       =>  'Projektkategori',
    'search_items'        =>  'Sök Projektkategorier',
    'all_items'           =>  'Alla Projektkategorier',
    'parent_item'         =>  'Huvudkategori',
    'parent_item_colon'   =>  'Huvudkategori',
    'edit_item'           =>  'Redigera Projektkategori',
    'update_item'         =>  'Updatera Projektkategori',
    'add_new_item'        =>  'Lägg till Projektkategori',
    'new_item_name'       =>  'Ny Projektkategori',
    'menu_name'           =>  'Projektkategorier',
  );
  $args = array(
    'hierarchical'        =>  true,
    'labels'              =>  $labels,
    'show_ui'             =>  true,
    'show_admin_column'   =>  true,
    'query_var'           =>  true,
    'rewrite'             =>  array( 'slug' => 'projektkategorier'),
  );
  register_taxonomy( 'project-category', array( 'projects' ), $args );
}
add_action( 'init', 'add_custom_taxonomies' );

/** Custom meta boxes **/

function projects_add_meta_boxes( $post ) {
  add_meta_box( 'projects_meta_box', __( 'Projektinformation', 'projects_meta_plugin' ), 'projects_build_meta_box', 'projects', 'normal', 'high' );
}
add_action( 'add_meta_boxes_projects', 'projects_add_meta_boxes' );
function projects_add_images_boxes ( $post ) {
  add_meta_box( 'projects_images_box', __( 'Projektbilder', 'projects_meta_plugin' ), 'projects_build_images_meta_box', 'projects', 'side', 'low');
}
add_action( 'add_meta_boxes_projects', 'projects_add_images_boxes' );
function projects_build_meta_box( $post ) {
  // add nonce
  wp_nonce_field( basename( __FILE__ ), 'projects_meta_box_nonce' );

  // retrieve the _projects_city current value
  $current_city = get_post_meta( $post->ID, '_projects_city', true);

  // retrive the _projects_year current value
  $current_year = get_post_meta( $post->ID, '_projects_year', true);
  ?>
  <div class="inside">
    <h4><?php _e( 'Stad', 'projects_meta_plugin' ); ?></h4>
    <p>
        <input type="text" name="city" value="<?php echo $current_city; ?>" />
    </p>
    <h4><?php _e( 'Byggår', 'projects_meta_plugin' ); ?></h4>
    <p>
        <input type="text" name="year" value="<?php echo $current_year; ?>" />
    </p>
  </div>
  <?php
}

function projects_build_images_meta_box( $post ) {
  // add nonce
  wp_nonce_field( basename( __FILE__ ), 'projects_meta_box_nonce' );
  // retrieve current value _projects_images_url as an array
  $images_url = get_post_meta( $post->ID, '_projects_images_url', true);
  ?>
  <div class="inside">
    <h4><?php _e( 'Bild 1', 'projects_meta_plugin' ); ?></h4>
    <p>
      <?php if (is_array($images_url) && $images_url['image1'] !== '') { ?>
        <input id="url_to_image1" type="hidden" name="url1" value="<?php echo $images_url['image1']; ?>" />
        <a href="#" id="image1" class="set_image" style="display:block;">
          <img id="image1_url" src="<?php echo $images_url['image1']; ?>" alt="Ange bild" style="max-width:100%;"/>
        </a>
        <a href="#" id="1remove_image" class="remove_image">Ta bort bild</a>
    <?php }
      else { ?>
        <input id="url_to_image1" type="hidden" name="url1"  />
        <a href="#" id="image1" class="set_image" style="display:block;">
          <img id="image1_url" src="" alt="Ange bild" style="max-width:100%;"/>
        </a>
    <?php } ?>
    </p>
    <h4><?php _e( 'Bild 2', 'projects_meta_plugin' ); ?></h4>
    <p>
      <?php if (is_array($images_url) && $images_url['image2'] !== '') { ?>
        <input id="url_to_image2" type="hidden" name="url2" value="<?php echo $images_url['image2']; ?>" />
        <a href="#" id="image2" class="set_image" style="display:block;">
          <img id="image2_url" src="<?php echo $images_url['image2']; ?>" alt="Ange bild" style="max-width:100%;"/>
        </a>
        <a href="#" id="2remove_image" class="remove_image">Ta bort bild</a>
    <?php }
      else { ?>
        <input id="url_to_image2" type="hidden" name="url2" />
        <a href="#" id="image2" class="set_image" style="display:block;">
          <img id="image2_url" src="" alt="Ange bild" style="max-width:100%;"/>
        </a>
    <?php } ?>
    </p>
    <h4><?php _e( 'Bild 3', 'projects_meta_plugin' ); ?></h4>
    <p>
      <?php if (is_array($images_url) && $images_url['image3'] !== '') { ?>
        <input id="url_to_image3" type="hidden" name="url3" value="<?php echo $images_url['image3']; ?>" />
        <a href="#" id="image3" class="set_image" style="display:block;">
          <img id="image3_url" src="<?php echo $images_url['image3']; ?>" alt="Ange bild" style="max-width:100%;"/>
        </a>
        <a href="#" id="3remove_image" class="remove_image">Ta bort bild</a>
    <?php }
      else { ?>
        <input id="url_to_image3" type="hidden" name="url3" />
        <a href="#" id="image3" class="set_image" style="display:block;">
          <img id="image3_url" src="" alt="Ange bild" style="max-width:100%;"/>
        </a>
    <?php } ?>

    </p>
    <h4><?php _e( 'Bild 4', 'projects_meta_plugin' ); ?></h4>
    <p>
      <?php if (is_array($images_url) && $images_url['image4'] !== '') { ?>
        <input id="url_to_image4" type="hidden" name="url4" value="<?php echo $images_url['image4']; ?>" />
        <a href="#" id="image4" class="set_image" style="display:block;">
          <img id="image4_url" src="<?php echo $images_url['image4']; ?>" alt="Ange bild" style="max-width:100%;"/>
        </a>
        <a href="#" id="4remove_image" class="remove_image">Ta bort bild</a>
    <?php }
      else { ?>
        <input id="url_to_image4" type="hidden" name="url4" />
        <a href="#" id="image4" class="set_image" style="display:block;">
          <img id="image4_url" src="" alt="Ange bild" style="max-width:100%;"/>
        </a>
    <?php } ?>
    </p>
    <h4><?php _e( 'Bild 5', 'projects_meta_plugin' ); ?></h4>
    <p>
      <?php if (is_array($images_url) && $images_url['image5'] !== '') { ?>
        <input id="url_to_image5" type="hidden" name="url5" value="<?php echo $images_url['image5']; ?>" />
        <a href="#" id="image5" class="set_image" style="display:block;">
          <img id="image5_url" src="<?php echo $images_url['image5']; ?>" alt="Ange bild" style="max-width:100%;"/>
        </a>
        <a href="#" id="5remove_image" class="remove_image">Ta bort bild</a>
    <?php }
      else { ?>
        <input id="url_to_image5" type="hidden" name="url5"  />
        <a href="#" id="image5" class="set_image" style="display:block;">
          <img id="image5_url" src="" alt="Ange bild" style="max-width:100%;"/>
        </a>
    <?php } ?>
    </p>
  </div>
  <?php
}

function projects_save_meta_boxes_data ( $post_id ) {
  // verify meta box nonce
  if ( !isset( $_POST['projects_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['projects_meta_box_nonce'], basename( __FILE__ ) ) ) {
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
  // city string
  if ( isset( $_REQUEST['city'] ) ) {
    update_post_meta( $post_id, '_projects_city', sanitize_text_field( $_POST['city'] ) );
  }
  // year string
  if ( isset( $_REQUEST['year'] ) ) {
    update_post_meta( $post_id, '_projects_year', sanitize_text_field( $_POST['year'] ) );
  }
  $updated_images_url = array(
    'image1' => sanitize_text_field( $_POST['url1'] ),
    'image2' => sanitize_text_field( $_POST['url2'] ),
    'image3' => sanitize_text_field( $_POST['url3'] ),
    'image4' => sanitize_text_field( $_POST['url4'] ),
    'image5' => sanitize_text_field( $_POST['url5'] ),
  );
  // Check if update_images_url is filled with urls
  // images
  if ( !$arraysAreEqual = ($images_url === $updated_images_url) ) {
    update_post_meta( $post_id, '_projects_images_url', $updated_images_url );
  }
}
add_action( 'save_post_projects', 'projects_save_meta_boxes_data', 10, 2 );
