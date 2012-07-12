<?php
/* Template name: No sidebar */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div class="row">
					<div class="twelvecol last" id='content'>
					<?php if ( is_front_page() ) { ?>
						<h2><?php the_title(); ?></h2>
					<?php } else { ?>	
						<h1><?php the_title(); ?></h1>
					<?php } ?>				

						<?php the_content(); ?>

					<?php comments_template( '', true ); ?>

					</div>
				</div>

<?php endwhile; ?>

<?php get_footer(); ?>