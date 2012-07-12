<?php
/**
 * TwentyTen functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twentyten_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyten_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Starkers
 * @since Starkers 3.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;


/** Tell WordPress to run twentyten_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'twentyten_setup' );

if ( ! function_exists( 'twentyten_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twentyten_setup() in a child theme, add your own twentyten_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'twentyten', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'twentyten' ),
	) );

	// This theme allows users to set a custom background
	add_custom_background();

	// Your changeable header business starts here
	define( 'HEADER_TEXTCOLOR', '' );
	// No CSS, just IMG call. The %s is a placeholder for the theme template directory URI.
	define( 'HEADER_IMAGE', '%s/images/headers/path.jpg' );

	// The height and width of your custom header. You can hook into the theme's own filters to change these values.
	// Add a filter to twentyten_header_image_width and twentyten_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width', 940 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height', 198 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be 940 pixels wide by 198 pixels tall.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Don't support text inside the header image.
	define( 'NO_HEADER_TEXT', true );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See twentyten_admin_header_style(), below.
	add_custom_image_header( '', 'twentyten_admin_header_style' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'berries' => array(
			'url' => '%s/images/headers/starkers.png',
			'thumbnail_url' => '%s/images/headers/starkers-thumbnail.png',
			/* translators: header image description */
			'description' => __( 'Starkers', 'twentyten' )
		)
	) );
}
endif;

if ( ! function_exists( 'twentyten_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in twentyten_setup().
 *
 * @since Twenty Ten 1.0
 */
function twentyten_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#headimg {
	border-bottom: 1px solid #000;
	border-top: 4px solid #000;
}
/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;

/**
 * Makes some changes to the <title> tag, by filtering the output of wp_title().
 *
 * If we have a site description and we're viewing the home page or a blog posts
 * page (when using a static front page), then we will add the site description.
 *
 * If we're viewing a search result, then we're going to recreate the title entirely.
 * We're going to add page numbers to all titles as well, to the middle of a search
 * result title and the end of all other titles.
 *
 * The site title also gets added to all titles.
 *
 * @since Twenty Ten 1.0
 *
 * @param string $title Title generated by wp_title()
 * @param string $separator The separator passed to wp_title(). Twenty Ten uses a
 * 	vertical bar, "|", as a separator in header.php.
 * @return string The new title, ready for the <title> tag.
 */
function twentyten_filter_wp_title( $title, $separator ) {
	// Don't affect wp_title() calls in feeds.
	if ( is_feed() )
		return $title;

	// The $paged global variable contains the page number of a listing of posts.
	// The $page global variable contains the page number of a single post that is paged.
	// We'll display whichever one applies, if we're not looking at the first page.
	global $paged, $page;

	if ( is_search() ) {
		// If we're a search, let's start over:
		$title = sprintf( __( 'Search results for %s', 'twentyten' ), '"' . get_search_query() . '"' );
		// Add a page number if we're on page 2 or more:
		if ( $paged >= 2 )
			$title .= " $separator " . sprintf( __( 'Page %s', 'twentyten' ), $paged );
		// Add the site name to the end:
		$title .= " $separator " . get_bloginfo( 'name', 'display' );
		// We're done. Let's send the new title back to wp_title():
		return $title;
	}

	// Otherwise, let's start by adding the site name to the end:
	$title .= get_bloginfo( 'name', 'display' );

	// If we have a site description and we're on the home/front page, add the description:
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $separator " . $site_description;

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $separator " . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	// Return the new title to wp_title():
	return $title;
}
add_filter( 'wp_title', 'twentyten_filter_wp_title', 10, 2 );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentyten_page_menu_args' );

/**
 * Sets the post excerpt length to 40 characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Twenty Ten 1.0
 * @return int
 */
function twentyten_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'twentyten_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 *
 * @since Twenty Ten 1.0
 * @return string "Continue Reading" link
 */
function twentyten_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyten_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string An ellipsis
 */
function twentyten_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyten_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyten_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Twenty Ten 1.0
 * @return string Excerpt with a pretty "Continue Reading" link
 */
function twentyten_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyten_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyten_custom_excerpt_more' );

/**
 * Remove inline styles printed when the gallery shortcode is used.
 *
 * Galleries are styled by the theme in Twenty Ten's style.css.
 *
 * @since Twenty Ten 1.0
 * @return string The gallery style filter, with the styles themselves removed.
 */
function twentyten_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'twentyten_remove_gallery_css' );

if ( ! function_exists( 'twentyten_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyten_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'twentyten' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'twentyten' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'twentyten' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'twentyten' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twentyten' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'twentyten'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override twentyten_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Twenty Ten 1.0
 * @uses register_sidebar
 */
function twentyten_widgets_init() {

	register_sidebar( array(
		'name' => __( 'Left promo box', 'twentyten' ),
		'id' => 'left-promo',
		'description' => __( 'Promo area on the homepage', 'twentyten' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h3 class="widget-title promo-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Middle promo box', 'twentyten' ),
		'id' => 'middle-promo',
		'description' => __( 'Promo area on the homepage', 'twentyten' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h3 class="widget-title promo-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Right promo box', 'twentyten' ),
		'id' => 'right-promo',
		'description' => __( 'Promo area on the homepage', 'twentyten' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h3 class="widget-title promo-title">',
		'after_title' => '</h3>',
	) );
		
	register_sidebar( array(
		'name' => __( 'Table of Contents area', 'twentyten' ),
		'id' => 'toc-box',
		'description' => __( 'Collapsible space for Table of Contents menu', 'twentyten' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h3 class="widget-title promo-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Sidebar', 'twentyten' ),
		'id' => 'sidebar-widget-area',
		'description' => __( 'The sidebar widget area shown on some inside pages', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer', 'twentyten' ),
		'id' => 'footer-widget-area',
		'description' => __( 'The footer widget area', 'twentyten' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Cookie warning bar', 'twentyten' ),
		'id' => 'cookiebar',
		'description' => __( 'The cookie warning bar', 'twentyten' ),
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
	) );

}
/** Register sidebars by running twentyten_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'twentyten_widgets_init' );

/**
 * Removes the default styles that are packaged with the Recent Comments widget.
 *
 * To override this in a child theme, remove the filter and optionally add your own
 * function tied to the widgets_init action hook.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'twentyten_remove_recent_comments_style' );

if ( ! function_exists( 'twentyten_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_on() {
	printf( __( '<span class="%1$s">Published:</span> %2$s', 'twentyten' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time() ),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

if ( ! function_exists( 'twentyten_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 *
 * @since Twenty Ten 1.0
 */
function twentyten_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'In: %1$s. Tags: %2$s', 'twentyten' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'In: %1$s.', 'twentyten' );
	} else {
		$posted_in = __( '', 'twentyten' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
}
endif;

function remove_themeoptions_menu() { // needed to hide TwentyTen options 
	global $submenu;

	foreach($submenu['themes.php'] as $k => $m) {
		if ($m[2] == "custom-background" || $m[2] == "custom-header") {
			unset($submenu['themes.php'][$k]);
		}
	}	
}

add_action('admin_head', 'remove_themeoptions_menu');

function ht_custom_excerpt_more( $output ) {
	return preg_replace('/<a[^>]+>Continue reading.*?<\/a>/i','',$output);
}
add_filter( 'get_the_excerpt', 'ht_custom_excerpt_more', 20 );


function custom_excerpt_length( $length ) {
	return 50;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );


// create 'Fragments' CPT for handling multi-section documents

function ht_CPTs() {
	register_post_type( 'fragment',
		array(
			'labels' => array(
				'name' => __( 'Fragment' ),
				'singular_name' => __( 'Fragment' ),
				'add_new' => __( 'Add new' ),
				'add_new_item' => __( 'Add new Fragment' ),
				'edit_item' => __( 'Edit Fragment' ),
				'all_items' => __( 'All Fragments' ),
				'view_item' => __( 'View Fragment' ),
			),
			'description' => 'A fragment is a part of a document',
			'public' => true,
			'hierarchical' => true,
			'query_var' => true,
			'menu_order' => true,
			'supports' => array('title','editor','comments','author','revisions','page-attributes'),
			'taxonomies' => array('post_tag'),
			'register_meta_box_cb' => 'add_fragment_metabox'
		)
	);

	register_post_type( 'note',
		array(
			'labels' => array(
				'name' => __( 'Note' ),
				'singular_name' => __( 'Note' ),
				'add_new' => __( 'Add new' ),
				'add_new_item' => __( 'Add new Note' ),
				'edit_item' => __( 'Edit Note' ),
				'all_items' => __( 'All Notes' ),
				'view_item' => __( 'View Note' ),
			),
			'description' => 'A note helps explain a fragment',
			'public' => true,
			'hierarchical' => false,
			'exclude_from_search' => true,
			'query_var' => true,
			'menu_order' => true,
			'supports' => array('title','editor','author','revisions'),
		)
	);
}

add_action( 'init', 'ht_CPTs' );


function add_fragment_metabox() {
    add_meta_box('fragment_meta', 'Fragment metadata', 'fragment_meta', 'fragment', 'normal', 'default');
}

function fragment_meta() {
    global $post;
 
    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="fragmentmeta_noncename" id="fragmentmeta_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
 
    // Get the project aims data if its already been entered
    $notes = get_post_meta($post->ID, '_notes', true);

    // Echo out the field
    echo '<h4>Fragment-specific notes:</h4>';
    wp_editor($notes,"_notes");
	
}

function fragment_save_meta($post_id, $post) {
 
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['fragmentmeta_noncename'], plugin_basename(__FILE__) )) {
    	return $post->ID;
    }
 
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
 
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
 
    $fragment_meta['_notes'] = $_POST['_notes'];

    // Add values of $fragment_meta as custom fields
 
    foreach ($fragment_meta as $key => $value) { // Cycle through the $events_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = @implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
 
}
 
add_action('save_post', 'fragment_save_meta', 1, 2); // save the custom fields

// relative dates, using class by Invent Partners - http://www.inventpartners.com
require_once('humanRelativeDate.class.php');

// support Posts to Posts plugin linking of Fragments to Notes

function my_connection_types() {
	// Make sure the Posts 2 Posts plugin is active.
	if ( !function_exists( 'p2p_register_connection_type' ) )
		return;

	p2p_register_connection_type( array(
		'name' => 'notes_to_fragments',
		'from' => 'note',
		'to' => 'fragment'
	) );
}
add_action( 'wp_loaded', 'my_connection_types' );


// customise comment form fields

function change_comment_form_defaults( $default ) {
    $commenter = wp_get_current_commenter();
    $default[ 'fields' ][ 'url' ] = null;
    $default[ 'fields' ][ 'capacity' ] = '<p class="comment-form-author">' .
        '<label for="commentcapacity">'. __('In what capacity are you commenting? (e.g. organisation name, member of public)') . '</label>
        <input type="text" id="commentcapacity" name="commentcapacity" size="30" value="'.$_SESSION['commentcapacity'].'" />
		</p>';
		
	$type_options = array(
		"drafting" => "Drafting or implementation?",
		"policy" => "Policy this Bill relates to?",
		"question" => "A question you have?"
	);
	
	foreach($type_options as $k => $v) {
		$typeoutput .= ($k == $_SESSION['commenttype']) ? "<option value='".$k."' selected='selected'>".$v."</option>\r" : "<option value='".$k."'>".$v."</option>\r";
	}	
	
    $default[ 'fields' ][ 'commenttype' ] = '<p class="comment-form-author">' .
        '<label for="commenttype">'. __('Is your comment about:') . '</label>
        <select id="commenttype" name="commenttype">
        	   <option value="">-- please select --</option>
               ' . $typeoutput . '
         </select></p>';
    return $default;
}
add_filter( 'comment_form_defaults', 'change_comment_form_defaults');

function save_comment_meta_data( $comment_id ) {
    add_comment_meta( $comment_id, 'commenttype', $_POST[ 'commenttype' ] );
    add_comment_meta( $comment_id, 'commentcapacity', $_POST[ 'commentcapacity' ] );
}
add_action( 'comment_post', 'save_comment_meta_data' );

function attach_metadata_to_comment( $comment) {
    $type = get_comment_meta( get_comment_ID(), 'commenttype', true );
    $capacity = get_comment_meta( get_comment_ID(), 'commentcapacity', true );
	if ( $type || $capacity) {
        $comment .= "<br /><br />(Comment info: Type - " . $type . "; Capacity - " . $capacity . ")";
    }
    return $comment;
}
if (is_admin()) add_filter( 'get_comment_text', 'attach_metadata_to_comment' );


// redirect after comment

add_filter('comment_post_redirect', 'redirect_after_comment');
function redirect_after_comment($location) {
	return $_SERVER["HTTP_REFERER"];
}


// fancy autofill using the session

function comment_autofill( $comment_data ) {
     
     $_SESSION['commentcapacity'] = $comment_data['comment_as_submitted']['POST_commentcapacity'];
     $_SESSION['commenttype'] = $comment_data['comment_as_submitted']['POST_commenttype'];
     
     return $comment_data;
}
add_filter( 'preprocess_comment', 'comment_autofill' );

function ht_init_session() {
  session_start();
}

add_action('init', 'ht_init_session', 1);


// check jQuery is available and load Google's CDN copies to reduce bandwidth and boost speed

function enqueueThemeScripts() {
	if (!is_admin()) {	
		wp_deregister_script( 'jquery' );
		wp_deregister_script( 'jquery-ui' );
		wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js' );
		wp_register_script( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui' );
	}
}    
 
add_action('wp_enqueue_scripts','enqueueThemeScripts');

function is_active_nav_menu($location){

	if(has_nav_menu($location)){

		$locations = get_nav_menu_locations();
		$menu = wp_get_nav_menu_items($locations[$location]);

		if(!empty($menu)){
			return true;
		}

	}

	return false;

}