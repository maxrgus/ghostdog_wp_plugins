<?php
/**
 * Template Name: Employees
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package mrgweb-employees
 */

get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php
				$args = array(
					'post_status' => 'publish',
					'post_type' => 'employee',
					'orderby'		=> 'title',
					'order'			=> 'ASC'

				);
				$query = new WP_Query($args);
				if ( $query->have_posts() ) : ?>
					<div class="employees-wrap">
				<?php
				$options = get_option('employee_settings');
				/* Start the loop */
				while ( $query->have_posts() ) : $query->the_post();
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

					    	if ($options['employee_name_field'] == '1') {
					    		the_title( '<h5>', '</h5>' );
					    	}
					      	/* Get email and phone */
						      $email = get_post_meta( $post->ID, '_employee_email', true );
						      $phone = get_post_meta( $post->ID, '_employee_phone', true );
						      $title = get_post_meta( $post->ID, '_employee_title', true);
						      $department = get_post_meta( $post->ID, '_employee_department', true);

						      $terms = wp_get_post_terms( $post->ID, 'organisations', array("fields" => "all"));
						      $organisation = $terms[0]->name;

						    if ($options['employee_title_field'] == '1') {
						    	if (!empty($title)) {
						    		echo '<p>' . $title . '</p>';
						    	}
						    } 

						    if ($options['employee_email_field'] == '1') {
						    	// Check if email is an email, if it is echo mailto link
						      	if (strpos($email, '@') !== false) {
						        	echo '<a href="mailto:' . $email . '">' . $email . '</a>';
						      	} elseif (!empty($email)) { // echo normal paragraph if not mail and not empty
						        	echo '<p>' . $email . '</p>';
					      		}
						    }
						    if ($options['employee_phone_field'] == '1') {
						    	// Check if phonenumber is a phonenumber
						      	if (strpbrk($phone, '0123456789-+') != 0) {
						        	echo '<a href="tel:' . $phone . '">' . $phone . '</a>';
						      	} elseif (!empty($phone)) {
						        	echo '<p>' . $phone . '</p>';
						      	}
						    }

						    if ($options['employee_department_field'] == '1') {
						    	if (!empty($department)) {
						    		echo '<p>' . $department . '</p>';
						    	}
						    }
						    
						    if ($options['employee_organisation_field'] == '1') {
						    	if (!empty($organisation)) {
						    		echo '<p>' . $organisation . '</p>';
						    	}
						    }

						    			      		
					      	
				    	?>
					</div><!-- .entry-header -->
				</article><!-- #post-## -->
				<?php endwhile; ?>
	 		</div>	
	 		<?php endif; ?>
		</main>
 	</div>
 	<?php get_sidebar(); ?>
</div>	
<?php get_footer(); ?>