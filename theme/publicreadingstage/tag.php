<?php
/**
 * The template for displaying Tag Archive pages.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

get_header(); 
query_posts($query_string . "&post_type=fragment");

?>

				<div class="row">
					<div class="eightcol" id="content">
				<h1><?php
					printf( __( 'Parts of this document tagged: %s', 'twentyten' ), '' . single_tag_title( '', false ) . '' );
				?></h1>

<?php
/* Run the loop for the tag archive to output the posts
 * If you want to overload this in a child theme then include a file
 * called loop-tag.php and that will be used instead.
 */
 get_template_part( 'loop', 'tag' );
?>

					</div>

					<div class="fourcol last" id='sidebar'>
						<ul class="xoxo">
							<?php get_sidebar(); ?>
						</ul>
					</div>

				</div>
<?php get_footer(); ?>