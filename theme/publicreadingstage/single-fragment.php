<?php
// just redirect to homepage (getting permalink structure to work for this CPT has been tricky...)

$fraginfo = get_page_by_path(get_query_var('fragment'),OBJECT,'fragment');
wp_redirect('/?f=' .$fraginfo->ID . "#fragment-" . $fraginfo->ID,'302');
?>

<?php get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<div class="row">
			<div class="eightcol" id='content'>
				<h1><?php the_title(); ?></h1>

				<?php the_content(); ?>

				<p class='postmeta'>Tagged: <?php echo get_the_tag_list(); ?></p>
							
				<?php comments_template( '', true ); ?>

		</div>
		
		<div class="fourcol last" id='sidebar'>
		
			<?php get_sidebar(); ?>
		
		</div>

<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>