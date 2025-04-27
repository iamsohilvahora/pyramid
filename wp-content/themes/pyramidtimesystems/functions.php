<?php
/**
 * pyramidtimesystems functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package pyramidtimesystems
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

if ( ! function_exists( 'pyramidtimesystems_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function pyramidtimesystems_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on pyramidtimesystems, use a find and replace
		 * to change 'pyramidtimesystems' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'pyramidtimesystems', get_template_directory() . '/languages' );

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

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary', 'pyramidtimesystems' ),
				'menu-2' => esc_html__( 'Secondary', 'pyramidtimesystems' ),
				'menu-3' => esc_html__( 'About Us', 'pyramidtimesystems' ),
				'menu-4' => esc_html__( 'News Type', 'pyramidtimesystems' ),
				'menu-5' => esc_html__( 'Resources', 'pyramidtimesystems' ),
				'menu-6' => esc_html__( 'Social Icons', 'pyramidtimesystems' ),
				'menu-7' => esc_html__( 'Industry Applications', 'pyramidtimesystems' ),
				'menu-8' => esc_html__( 'Support', 'pyramidtimesystems' ),
				
				'menu-9' => esc_html__( 'Manual Punch Clocks', 'pyramidtimesystems' ),
				'menu-10' => esc_html__( 'Software Based Time Clocks', 'pyramidtimesystems' ),
				'menu-11' => esc_html__( 'Time Clock Supplies', 'pyramidtimesystems' ),
				'menu-12' => esc_html__( 'Synchronized Clock Systems', 'pyramidtimesystems' ),
				'menu-13' => esc_html__( 'Wall Clocks', 'pyramidtimesystems' ),
				'menu-14' => esc_html__( 'Wall Clock Accessories', 'pyramidtimesystems' ),

				'menu-15' => esc_html__( 'Products Categories', 'pyramidtimesystems' ),
				'menu-16' => esc_html__( 'Support (Footer', 'pyramidtimesystems' ),
				'menu-17' => esc_html__( 'Mobile Menu', 'pyramidtimesystems' )
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'pyramidtimesystems_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

	}
endif;
add_action( 'after_setup_theme', 'pyramidtimesystems_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function pyramidtimesystems_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'pyramidtimesystems_content_width', 640 );
}
add_action( 'after_setup_theme', 'pyramidtimesystems_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function pyramidtimesystems_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'pyramidtimesystems' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'pyramidtimesystems' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	// Register Footer 1 widgets
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 1', 'pyramidtimesystems' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'Add widgets here.', 'pyramidtimesystems' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	// Register Footer 2 widgets
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 2', 'pyramidtimesystems' ),
			'id'            => 'footer-2',
			'description'   => esc_html__( 'Add widgets here.', 'pyramidtimesystems' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	// Register Footer 3 widgets
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 3', 'pyramidtimesystems' ),
			'id'            => 'footer-3',
			'description'   => esc_html__( 'Add widgets here.', 'pyramidtimesystems' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	// News Sidebar widgets
	register_sidebar(
		array(
			'name'          => esc_html__( 'News Sidebar', 'pyramidtimesystems' ),
			'id'            => 'news-sidebar',
			'description'   => esc_html__( 'Add widgets here.', 'pyramidtimesystems' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);


	// RECENTLY VIEWED PRODUCTS SIDEBAR
	register_sidebar(
		array(
			'name'          => esc_html__( 'Recent View Product', 'pyramidtimesystems' ),
			'id'            => 'recent_view_product',
			'description'   => esc_html__( 'Add widgets here.', 'pyramidtimesystems' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'pyramidtimesystems_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function pyramidtimesystems_scripts(){
	wp_enqueue_style( 'pyramidtimesystems-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'pyramidtimesystems-style', 'rtl', 'replace' );

	wp_enqueue_style('pyramidtimesystems-jquery-ui-style', get_template_directory_uri() . '/css/jquery-ui.css', array(), _S_VERSION );

	wp_enqueue_style('pyramidtimesystems-custom-style', get_template_directory_uri() . '/css/custom-style.css', array(), _S_VERSION );

	// css file for single product page tab (responsive - mobile)
	wp_enqueue_style('pyramidtimesystems-tab-style', get_template_directory_uri() . '/css/style-tabbing.css', array(), _S_VERSION, true);

	// css file for date picker
	wp_enqueue_style('pyramidtimesystems-datepicker-style', get_template_directory_uri() . '/css/datepicker-ui.css', array(), _S_VERSION, true);

	wp_enqueue_script('pyramidtimesystems-jquery-ui', get_template_directory_uri() . '/js/jquery-ui.js', array(), _S_VERSION, true);
	
	// Js for prouct tab in single product page tab (responsive - mobile)
	wp_enqueue_script('pyramidtimesystems-tabjs', get_template_directory_uri() . '/js/jquery.multipurpose_tabcontent.js', array(), "", true);

	wp_enqueue_script('pyramidtimesystems-main', get_template_directory_uri() . '/js/main.js', array('jquery'), _S_VERSION, false);

	wp_localize_script('pyramidtimesystems-main', 'pyramid_ajax_object',
	   array( 
	       'ajaxurl' => admin_url('admin-ajax.php')
	   )
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'pyramidtimesystems_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 *  Load all action and filter hooks
 */
require get_template_directory().'/inc/general_functions.php';

/**
 *  Load all shortcode 
 */
require get_template_directory().'/inc/wp_shortcode.php';

/** 
 * Load custom post type 
 */
require get_template_directory().'/inc/custom_post_type.php';

/**
 *  Load woocommerce hooks 
 */
require get_template_directory().'/inc/woocommerce.php';
