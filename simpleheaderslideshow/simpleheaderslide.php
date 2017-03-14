<?php
/*
 * Plugin Name: Ghostdog: Header Slideshow
 * Description: Adds a simple slideshow that loops through all uploaded headers.
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

function initiate_slideshow() {
  $header_images = get_uploaded_header_images();
  echo '<div id="header-slideshow">';
  foreach ($header_images as $image) {

    echo '<div style="background-image: url(' . $image['url'] .')">';
    echo '<div class="site-logo site-logo-mobile">';
    if ( function_exists( 'jetpack_the_site_logo' ) ) jetpack_the_site_logo();
    echo '</div>';
    echo '</div>';

  }
  echo '</div>';

}
function slideshow_enqueue_script() {
  

  wp_enqueue_script('header-slide-functions', plugin_dir_url( __FILE__ ) . 'js/functions.js', array( 'jquery' ) );
  wp_enqueue_style('header-slide-styles', plugin_dir_url( __FILE__ ) . 'css/style.css');
}
add_action( 'wp_enqueue_scripts', 'slideshow_enqueue_script' );
