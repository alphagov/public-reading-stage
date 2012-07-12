<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query. 
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
 

get_header(); ?>

				<div class="row">
					<div class="eightcol">
						<?php
						 get_template_part( 'loop', 'index' );
						?>
					</div>

					<div class="fourcol last" id='sidebar'>
						<ul class="xoxo">
							<?php dynamic_sidebar( 'inside-sidebar-widget-area' ); ?>
						</ul>
					</div>

				</div>
						

<?php get_footer(); ?>