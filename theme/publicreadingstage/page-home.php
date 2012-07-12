<?php
/* Template name: Home page */
?>

<?php get_header(); ?>

				<div class="row">

					<div class="twelvecol last">
			
						<div id='maincontent' class='clearfix'>

						<ul id='jumplinks'>
							<li><a href='#explanatorynotes'>Jump to Explanatory Notes</a></li>
							<li><a href='#billtext'>Jump to Bill text</a></li>
							<li><a href='#discussion'>Jump to Discussion</a></li>
						</ul>

						<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	
							<?php the_content(); // the content for the homepage itself ?>
	
						<?php endwhile; // end of the loop. ?>
						
						</div>					

					</div>

				</div>						

				<div class="row">

					<div class="threecol" id='explanatorynotes'>
						
						<?php if (is_active_sidebar('toc-box')) : ?>
						
						<div id='tocinner' class='innerwrapper'>
							<h2>Contents</h2>
							<div id='toccontent'>
								<?php dynamic_sidebar('toc-box'); ?>
							</div>
						</div>
						
						<?php endif; ?>
						
						<div id='searchinner' class='innerwrapper'>
							<h2>Search Bill text</h2>
							
							<?php echo get_search_form(); ?>
						</div>
						
						<div id='explanatorynotesinner' class='innerwrapper'>
							<h2>Explanatory Notes</h2>
							
							<?php

								// Find connected posts via Posts to Posts plugin
								$connected = new WP_Query( array(
								  'connected_type' => 'notes_to_fragments',
								  'connected_items' => get_post($_REQUEST['f']),
								  'nopaging' => true,
								) );
								
								//print_r($connected);
								
								// Display connected posts
								if ( $connected->have_posts() ) {
									
									while ( $connected->have_posts() ) {
										$connected->the_post();
										$linkednotes .= "<li><a class='lightboxnotes' data-url='".get_permalink()."' href='".get_permalink()."'>".get_the_title()."</a></li>\n";
									}								
								}
								
								if ($linkednotes) {
									$linkednotes = "<ul class='linkednotes'>" . $linkednotes . "</ul><div id='lightbox-wrapper'><p><a id='closebutton' class='closelightbox' href='#'><img src='".get_stylesheet_directory_uri()."/images/lightbox-close.png' alt='close' /></a></p><div id='lightbox-content'></div><p style='text-align: center; margin-top: 2em !important;'><a class='closelightbox' id='close-panel' href='#'>Close</a></p></div><div id='lightbox-background'></div>";
								}
								
								wp_reset_postdata();
								
								if ($_REQUEST['f']) {					
									$fragnotes = (get_post_meta($_REQUEST['f'],'_notes',true)) ? get_post_meta($_REQUEST['f'],'_notes',true) : null;
									echo "<div class='fragment_notes'>" . $fragnotes . $linkednotes . "</div>";
								} else {
									echo "<div class='fragment_notes'><span>Choose a part of the document to view accompanying notes</span></div>";
								}
							
							?>
							
						</div>
						
						<?php
							$ftags = get_the_tags($_REQUEST['f']); 
							
							foreach((array)$ftags as $ft) {
							  $tagslist .= "<a href='/tag/".$ft->slug."'>".$ft->name."</a>, ";
							}
							if ($ftags) {
								echo "<div id='metadatainner'>\r\n<p>Tagged: " . substr($tagslist,0,-2) . "</p>\r\n</div>";
							}
						?>
						
					</div>

					<div class="fivecol" id='billtext'>
						<div class='innerwrapper'>
							<h2>Bill text</h2>
							
							<?php
							
								wp_reset_query();
								
								$fragments = new WP_Query('post_type=fragment&orderby=menu_order&order=ASC&posts_per_page=-1');
						
								if ( $fragments->have_posts() ) {
									while ( $fragments->have_posts() ) {
										$fragments->the_post();
										
										$activefragment = ($_REQUEST['f'] == $post->ID) ? "activefragment" : null;
										
										$ancestors = get_post_ancestors($post->ID);
										$fragmentdepth = "fragdepth-" . count($ancestors);
										
										// rewrite references in parentheses format (which WP doesn't handle well as page titles)
										
										$pattern[0] = '/(\w+)\.(\w+)\.(\w+)/';
										$pattern[1] = '/(\w+)\.(\w+)/';

										$replacement[0] = '$1 ($2) ($3)';
										$replacement[1] = '$1 ($2)';
										
										$fragtitle = preg_replace($pattern,$replacement,get_the_title());
										
										if (is_user_logged_in() && $activefragment == 'activefragment') {
											$adminlink = "<a href='" . get_bloginfo('wpurl') . "/wp-admin/post.php?action=edit&post=" . $post->ID . "'>[edit]</a>";
										} else {
											$adminlink = null;
										}
			
										echo "<p class='fragment $activefragment clearfix $fragmentdepth' id='fragment-".$post->ID."'><a href='".get_bloginfo('home')."/?f=".$post->ID."#fragment-".$post->ID."' class='fragment_link'>" . "<span class='fragment_ref'>".$fragtitle. "</span><span class='fragment_content'>" . nl2br(get_the_content()) . "</span></a>{$adminlink}</p>";
									}
								}								
							
								rewind_posts();
							?>
						</div>
					</div>

					<div class="fourcol last" id='discussion'>
						<div class='innerwrapper'>
							
							<?php

								if ($_REQUEST['f']) {		
								
									$args = array(
										"post_id" => $_REQUEST['f'],
										"status" => 'approve',
										"order" => 'asc'
									);			
									$fragcomments = get_comments($args);
									
									
									if (count($fragcomments)>0) { // some comments
										
										echo "<h2>" . number_format(count($fragcomments)) . " comments</h2>";							
	
										foreach((array)$fragcomments as $c) {
										
											$byline = (get_comment_meta($c->comment_ID,'commentcapacity',true)) ? $c->comment_author . ", " . get_comment_meta($c->comment_ID,'commentcapacity',true) : $c->comment_author;
											echo "<div class='fragment_comment'><p><span class='fragment_commenter'>".$byline."</span>: " . $c->comment_content. "<br /><span class='fragment_commentdate'>" . human_time_diff( strtotime($c->comment_date_gmt), current_time('timestamp') ) . " ago</span> <span class='report'><a href='/report/?f=" . intval($_REQUEST['f']). "&fc=" . $c->comment_ID . "&cn=".urlencode($c->comment_author)."'>Report this comment</a></span></p></div>";	
										}
									
																										
									} else { // no comments
										echo "<h2>Discussion</h2>";							
										echo "<div class='fragment_comment'><p>No comments yet.</p></div>";								
									}
									
									// set up comment form
																	
									$commenter = wp_get_current_commenter();
									$req = get_option( 'require_name_email' );
									$aria_req = ( $req ? " aria-required='true'" : '' );
									
									$type_options = array(
										"drafting" => "Drafting or implementation?",
										"policy" => "Policy this Bill relates to?",
										"question" => "A question you have?"
									);
									
									foreach($type_options as $k => $v) {
										$typeoutput .= ($k == $_SESSION['commenttype']) ? "<option value='".$k."' selected='selected'>".$v."</option>\r" : "<option value='".$k."'>".$v."</option>\r";
									}	
									
									$fields =  array(
									'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'domainreference' ) .( $req ? '<span class="required">*</span>' : '' ) . '</label> ' .
												'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
									'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'domainreference' ) . ( $req ? '<span class="required">*</span>' : '' ) . '</label> ' .
												'<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
									'capacity' => '<p class="comment-form-author">' .
										'<label for="commentcapacity">'. __('In what capacity are you commenting? (e.g. organisation name, member of public)') . '</label>
										<input type="text" id="commentcapacity" name="commentcapacity" size="30" value="'.$_SESSION['commentcapacity'].'" />
										</p>',
									'commenttype' => '<p class="comment-form-author">' .
										'<label for="commenttype">'. __('Is your comment about:') . '</label>
										<select id="commenttype" name="commenttype">
											   <option value="">-- please select --</option>
											   ' . $typeoutput . '
										 </select></p>',
									);


									$args = array(
										'fields' => apply_filters('comment_form_default_fields', $fields),
										'comment_notes_before' => '<p>Your email address will not be published. Name, email address and comment are required fields. Please note our <a href="/moderation">moderation policy</a>.</p><br />',
										'comment_notes_after' => '',
										'title_reply' => 'Leave a comment',
										'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '<span class="required">*</span></label><textarea id="comment" name="comment" rows="4" aria-required="true"></textarea></p>',
									);

									
									comment_form($args,$_REQUEST['f']); 
									
								} else {
									echo "<h2>Discussion</h2>";
									echo "<div class='fragment_notes'><span>Choose a part of the document to read and comment on</span></div>";
								}
							
							?>
							
						</div>
					</div>

				</div>		

<?php if (!is_user_logged_in()) : // don't show preview for logged in users, as name/capacity fields aren't populated ?>

<script type='text/javascript'>

jQuery(document).ready(function() {
   jQuery('#comment').one("focus", function() {
     jQuery('#comment').parent().after('<div id="comment-preview"><p class="comment-by">How your comment will appear:</p><div id="live-preview"></div></div>');
   });
   
   var $comment = '';
   var $name = '';
   var $capacity = '';

   jQuery('#comment').keyup(function() {
     $comment = jQuery(this).val();
     $name = jQuery('#author').val();
   	 $capacity = jQuery('#commentcapacity').val();

     $comment = $comment.replace(/\n/g, "<br />")
       .replace(/\n\n+/g, '<br /><br />');
     jQuery('#live-preview').html("<strong>"+$name+", "+$capacity+"</strong>: "+$comment );
   });
 
   jQuery('#author').keyup(function() {
     $comment = jQuery('#comment').val();
     $name = jQuery(this).val();
   	 $capacity = jQuery('#commentcapacity').val();

     $comment = $comment.replace(/\n/g, "<br />")
       .replace(/\n\n+/g, '<br /><br />');
     jQuery('#live-preview').html("<strong>"+$name+", "+$capacity+"</strong>: "+$comment );
   });   

   jQuery('#commentcapacity').keyup(function() {
     $comment = jQuery('#comment').val();
     $name = jQuery('#author').val();
   	 $capacity = jQuery(this).val();

     $comment = $comment.replace(/\n/g, "<br />")
       .replace(/\n\n+/g, '<br /><br />');
     jQuery('#live-preview').html("<strong>"+$name+", "+$capacity+"</strong>: "+$comment );
   });   

 });

</script>

<?php endif; ?>
<?php get_footer(); ?>