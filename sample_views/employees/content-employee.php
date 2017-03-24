<?php
/**
 * Template part for displaying employees.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Spooky_Dog
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="employee">
    <?php

      if ( has_post_thumbnail() ) { ?>
      <figure class="featured-image">
          <?php the_post_thumbnail('employee'); ?>
      </figure>
      <?php } ?>

    <?php
      the_title( '<h6 class="employee-title">', '</h6>' );
      /* Get email and phone */
      $email = get_post_meta( $post->ID, '_employees_email', true );
      $phone = get_post_meta( $post->ID, '_employees_phone', true );
      // Check if email is an email, if it is echo mailto link
      if (strpos($email, '@') !== false) {
        echo '<a class="contact-link"href="mailto:' . $email . '">' . $email . '</a>';
      } elseif (!empty($email)) { // echo normal paragraph if not mail and not empty
        echo '<p>' . $email . '</p>';
      }
      // Check if phonenumber is a phonenumber
      if (strpbrk($phone, '0123456789-+') != 0) {
        echo '<a class="contact-link" href="tel:' . $phone . '">' . $phone . '</a>';
      } elseif (!empty($phone)) {
        echo '<p>' . $phone . '</p>';
      }
    ?>
    </div><!-- .entry-header -->
</article><!-- #post-## -->
