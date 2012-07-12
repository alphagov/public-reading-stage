<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */
?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if ( $wp_query->max_num_pages > 1 ) : ?>

		<?php 
		
			if (is_category('case-study')) {
				$itemtype = "case studies";
			} elseif (is_category('latest-updates')) {
				$itemtype = "updates";
			} else {
				$itemtype = "items";
			}	
		
		?>

		<?php next_posts_link( __( '&larr; Previous '.$itemtype, 'twentyten' ) ); ?>
		<?php previous_posts_link( __( 'Next '.$itemtype.' &rarr;', 'twentyten' ) ); ?>

<?php endif; ?>

<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>
		<h1><?php _e( 'Not Found', 'twentyten' ); ?></h1>
		<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyten' ); ?></p>
		<?php get_search_form(); ?>

<?php endif; ?>

<?php while ( have_posts() ) : the_post(); ?>


	<div class='metabox clearfix'>

	<h2><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<p class='postmeta'><?php if (is_category('latest-updates') || in_category('latest-updates')) twentyten_posted_on(); ?></p>

	<?php if ( is_archive() || is_search() ) : // Only display excerpts for archives and search. ?>
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_post_thumbnail('thumbnail','class=postthumb'); ?></a>
			<?php the_excerpt(); ?>
	<?php else : ?>
			<?php the_content( __( 'Continue reading &rarr;', 'twentyten' ) ); ?>
			<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'twentyten' ), 'after' => '' ) ); ?>
	<?php endif; ?>

		<?php if ( count( get_the_category() ) ) : ?>
			<?php //printf( __( 'Posted in %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?>
			
		<?php endif; ?>
		<?php
			//$tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list ):
		?>
			<?php printf( __( 'Tagged %2$s', 'twentyten' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?>
			
		<?php endif; ?>

	</div>

	<?php comments_template( '', true ); ?>

<?php endwhile; // End the loop. Whew. ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>

		<?php next_posts_link( __( '&larr; Previous '.$itemtype, 'twentyten' ) ); ?>
		<?php previous_posts_link( __( 'Next '.$itemtype.' &rarr;', 'twentyten' ) ); ?>

<?php endif; ?>