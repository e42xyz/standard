<?php
/**
 * Seismic Standard functions and definitions
 *
 */

/**
 * Include the update script
 */
//Initialize the update checker.
require_once( get_template_directory() .'/inc/theme-update-checker.php' );
$my_theme = wp_get_theme();
$example_update_checker = new ThemeUpdateChecker(
	'standard',                                            //Theme folder name, AKA "slug". 
	'http://version.seismicthemes.com/check.php?theme='.$my_theme->get( 'TextDomain' ).'&domain='.domain(home_url()) //URL of the metadata file.
);

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 640;

/** Tell WordPress to run standard_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'standard_setup' );

if ( ! function_exists( 'standard_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function standard_setup() {

	add_editor_style();
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );

	load_theme_textdomain( 'standard', get_template_directory()  . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory()  . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'standard' ),
		'secondary' => __( 'Secondary Navigation', 'standard' ),
	) );

	// This theme allows users to set a custom background
	add_theme_support( 'custom-background');

	// Your changeable header business starts here
	define( 'HEADER_TEXTCOLOR', '' );

	// Don't support text inside the header image.
	define( 'NO_HEADER_TEXT', true );

	
}
endif;

if ( ! function_exists( 'standard_admin_header_style' ) ) :

	function standard_admin_header_style() {
?>
	<style type="text/css">
	#headimg {
		border-bottom: 1px solid #000;
		border-top: 4px solid #000;
	}
	</style>
<?php
	}
endif;

function standard_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'standard_page_menu_args' );

function standard_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'standard_excerpt_length' );

function standard_continue_reading_link() {
	return ' <a href="'. get_permalink() . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'standard' ) . '</a>';
}

function standard_auto_excerpt_more( $more ) {
	return ' &hellip;' . standard_continue_reading_link();
}
add_filter( 'excerpt_more', 'standard_auto_excerpt_more' );

function standard_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= standard_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'standard_custom_excerpt_more' );

function standard_remove_gallery_css( $css ) {
	return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'standard_remove_gallery_css' );

if ( ! function_exists( 'standard_comment' ) ) :

function standard_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'standard' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.', 'standard' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'standard' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'standard' ), ' ' );
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
		<p><?php _e( 'Pingback:', 'standard' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'standard'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

function standard_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'standard' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'standard' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'standard' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'standard' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 3, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'First Footer Widget Area', 'standard' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'The first footer widget area', 'standard' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 4, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Second Footer Widget Area', 'standard' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'The second footer widget area', 'standard' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Third Footer Widget Area', 'standard' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer widget area', 'standard' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	// Area 6, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Fourth Footer Widget Area', 'standard' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer widget area', 'standard' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
/** Register sidebars by running standard_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'standard_widgets_init' );

function standard_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'standard_remove_recent_comments_style' );



if ( ! function_exists( 'standard_posted_in' ) ) :

function standard_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'standard' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'standard' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'standard' );
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


function standard_add_styles() {
	wp_enqueue_style('standard-style', get_stylesheet_uri(), false, '0.2');
	wp_enqueue_script( 'jquery');
	wp_enqueue_script( 'modernizr', get_template_directory_uri() .'/js/modernizr-1.6.min.js');
	wp_enqueue_script( 'superfish', get_template_directory_uri() .'/js/superfish.js');
	wp_enqueue_script( 'supersubs', get_template_directory_uri() .'/js/supersubs.js');
	wp_enqueue_script( 'dropdowns', get_template_directory_uri() .'/js/dropdowns.js');
	wp_enqueue_script( 'mediaqueries', get_template_directory_uri() .'/js/css3-mediaqueries.js');
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action('wp_enqueue_scripts', 'standard_add_styles');

/**
 * The Seismic Standard Caption shortcode.
 */

add_shortcode('wp_caption', 'standard_img_caption_shortcode');
add_shortcode('caption', 'standard_img_caption_shortcode');

/**
 * Prints HTML with meta information for the current post—date/time and author.
 */
function standard_img_caption_shortcode($attr, $content = null) {

	extract(shortcode_atts(array(
		'id'	=> '',
		'align'	=> 'alignnone',
		'width'	=> '',
		'caption' => ''
	), $attr));

	if ( 1 > (int) $width || empty($caption) )
		return $content;


if ( $id ) $idtag = 'id="' . esc_attr($id) . '" ';
$align = 'class="' . esc_attr($align) . '" ';

  return '<figure ' . $idtag . $align . 'aria-describedby="figcaption_' . $id . '" style="width: ' . (10 + (int) $width) . 'px">' 
  . do_shortcode( $content ) . '<figcaption id="figcaption_' . $id . '">' . $caption . '</figcaption></figure>';
}


if ( ! function_exists( 'standard_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post—date/time and author.
 */
function standard_posted_on() {
		printf( __( 'Posted on %2$s by %3$s', 'standard' ),
			'meta-prep meta-prep-author',
			sprintf( '<a href="%1$s" rel="bookmark"><time datetime="%2$s" pubdate>%3$s</time></a>',
			get_permalink(),
			get_the_date('c'),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'standard' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

?>