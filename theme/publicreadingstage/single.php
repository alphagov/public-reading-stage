<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<?php if (in_category('case-study')) : ?>

				<div class="row">
					<div class="twelvecol last" id='content'>
						<h1><?php the_title(); ?></h1>

						<p class='postmeta'><?php if (!in_category('case-study') || is_category('case-study')) twentyten_posted_on(); ?></p>

						<?php the_content(); ?>

						<?php comments_template( '', true ); ?>

				</div>

	<?php else : ?>

				<div class="row">
					<div class="eightcol" id='content'>
						<h1><?php the_title(); ?></h1>

						<p class='postmeta'><?php if (!in_category('case-study') || is_category('case-study')) twentyten_posted_on(); ?></p>

						<?php the_content(); ?>

						<?php comments_template( '', true ); ?>

				</div>
				
				<div class="fourcol last" id='sidebar'>
				
					<?php get_sidebar(); ?>
				
				</div>

	<?php endif; ?>


<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>