<?php
/**
 * Template Name: Employees
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Spooky_Dog
 */

get_header(); ?>

    <div id="primary" class="content-area-full">
        <main id="main" class="site-main-full" role="main">
            <div class="header-image secondary-pages-header" style="background-image: url(<?php the_post_thumbnail_url( 'full' );?>)">
            </div>

            <?php
            $args = array(
                'post_status'   => 'publish',     // Only employees that are published.
                'post_type'     => 'employees',  // Only get employees.
                'orderby'       => 'title',
                'order'         => 'ASC'

            );
            $query = new WP_Query($args);

            if ( $query->have_posts() ) :
              /* Start the loop */
                  while ( $query->have_posts() ) : $query->the_post();
                  /*
                   * Include the Post-Format-specific template for the content.
                   * If you want to override this in a child theme, then include a file
                   * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                   */
                   get_template_part( 'template-parts/content', 'contact' );

                   endwhile; ?>
                 </div>
             </div>
               <?php
                 else :
                        get_template_part( 'template-parts/content', 'none' );
                 endif; ?>
        </main><!-- #main -->
    </div><!-- #primary -->
<?php
  get_footer();
