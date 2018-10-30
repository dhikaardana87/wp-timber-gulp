<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
	});

	add_filter('template_include', function( $template ) {
		return get_stylesheet_directory() . '/static/no-timber.html';
	});

	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site {
	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		parent::__construct();
	}
	/** This is where you can register custom post types. */
	public function register_post_types() {

	}
	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

	}
	
	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['foo'] = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::get_context();';
		$context['menu'] = new Timber\Menu();
		$context['site'] = $this;
		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5', array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats', array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );
	}

	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter( new Twig_SimpleFilter( 'myfoo', array( $this, 'myfoo' ) ) );
		return $twig;
	}

}

new StarterSite();

/** register footer widget */

register_sidebar( array(
	'name' => 'Footer',
	'id' => 'footer_widget',
	'before_widget' => '<div class="footer_item">',
	'after_widget' => '</div>',
	'before_title' => '<h4 class="footer__title">',
	'after_title' => '</h4>',
) );


add_action( 'init', 'tid_create_post_type' );
function tid_create_post_type() {  // Products custom post type
    // set up labels
    $labels = array(
        'name' => 'Products',
        'singular_name' => 'Product Item',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Product Item',
        'edit_item' => 'Edit Product Item',
        'new_item' => 'New Product Item',
        'all_items' => 'All Products',
        'view_item' => 'View Product Item',
        'search_items' => 'Search Products',
        'not_found' =>  'No Products Found',
        'not_found_in_trash' => 'No Products found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Products',
    );
    register_post_type(
        'Products',
        array(
            'labels' => $labels,
            'has_archive' => true,
            'public' => true,
            'hierarchical' => true,
            'supports' => array( 'title', 'editor', 'excerpt', 'custom-fields', 'thumbnail','page-attributes' ),
            'taxonomies' => array( 'post_tag', 'category' ),
            'exclude_from_search' => true,
            'capability_type' => 'post',
        )
    );
}
 
// register two taxonomies to go with the post type
add_action( 'init', 'tid_create_taxonomies', 0 );
function tid_create_taxonomies() {
    // color taxonomy
    $labels = array(
        'name'              => _x( 'Colors', 'taxonomy general name' ),
        'singular_name'     => _x( 'Color', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Colors' ),
        'all_items'         => __( 'All Colors' ),
        'parent_item'       => __( 'Parent Color' ),
        'parent_item_colon' => __( 'Parent Color:' ),
        'edit_item'         => __( 'Edit Color' ),
        'update_item'       => __( 'Update Color' ),
        'add_new_item'      => __( 'Add New Color' ),
        'new_item_name'     => __( 'New Color' ),
        'menu_name'         => __( 'Colors' ),
    );
    register_taxonomy(
        'color',
        'Products',
        array(
            'hierarchical' => true,
            'labels' => $labels,
            'query_var' => true,
            'rewrite' => true,
            'show_admin_column' => true
        )
    );

    // type taxonomy
    $labels = array(
        'name'              => _x( 'Types', 'taxonomy general name' ),
        'singular_name'     => _x( 'Type', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Types' ),
        'all_items'         => __( 'All Types' ),
        'parent_item'       => __( 'Parent Type' ),
        'parent_item_colon' => __( 'Parent Type:' ),
        'edit_item'         => __( 'Edit Type' ),
        'update_item'       => __( 'Update Type' ),
        'add_new_item'      => __( 'Add New Type' ),
        'new_item_name'     => __( 'New Type' ),
        'menu_name'         => __( 'Types' ),
    );
    register_taxonomy(
        'type',
        'Products',
        array(
            'hierarchical' => true,
            'labels' => $labels,
            'query_var' => true,
            'rewrite' => true,
            'show_admin_column' => true
        )
    );
}

// create shortcode with parameters so that the user can define what's queried - default is to list all blog posts
add_shortcode( 'list-products', 'tid_products_listing_parameters_shortcode' );
function tid_products_listing_parameters_shortcode( $atts ) {
    ob_start();
 
    // define attributes and their defaults
    extract( shortcode_atts( array (
        'post_type' => 'products',
        'order' => 'date',
        'orderby' => 'title',
        'posts' => -1,
        'color' => '',
        'type' => '',
        'category' => '',
    ), $atts ) );
 
    // define query parameters based on attributes
    $options = array(
        'post_type' => $post_type,
        'order' => $order,
        'orderby' => $orderby,
        'posts_per_page' => $posts,
        'color' => $color,
        'type' => $type,
        'category_name' => $category,
    );
    $query = new WP_Query( $options );
    // run the loop based on the query
    if ( $query->have_posts() ) { ?>
        <div class="row products-box my-4">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <div class="col-md-4 item-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <a href="<?php the_permalink(); ?>" class="products-box__item">
                        <div class="products-box__item__wrapper">
                            <?php 

                            $image = get_field('product_image');
                            if( !empty($image) ): ?>
                            <div class="products-box__item__img" style="background-image: url(<?php echo $image['url']; ?>)"></div>
                            <?php endif; ?>
                            <div class="products-box__item__content">
                                <div class="ttl-wrapper">
                                    <h4> <?php the_title(); ?> </h4>
                                </div>
                                <div class="desc-wrapper">
                                    <p><?php the_excerpt() ?></p>
                                </div>
                            </div>
                            <div class="link-wrapper">
                                Explore
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>
    <?php
        $myvariable = ob_get_clean();
        return $myvariable;
    }
}
