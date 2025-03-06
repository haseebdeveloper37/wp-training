<?php

/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

use Elementor\WPNotificationsPackage\V110\Notifications as ThemeNotifications;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_VERSION', '3.3.0');

if (! isset($content_width)) {
	$content_width = 800; // Pixels.
}

if (! function_exists('hello_elementor_setup')) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup()
	{
		if (is_admin()) {
			hello_maybe_update_theme_version_in_db();
		}

		if (apply_filters('hello_elementor_register_menus', true)) {
			register_nav_menus(['menu-1' => esc_html__('Header', 'hello-elementor')]);
			register_nav_menus(['menu-2' => esc_html__('Footer', 'hello-elementor')]);
		}

		if (apply_filters('hello_elementor_post_type_support', true)) {
			add_post_type_support('page', 'excerpt');
		}

		if (apply_filters('hello_elementor_add_theme_support', true)) {
			add_theme_support('post-thumbnails');
			add_theme_support('automatic-feed-links');
			add_theme_support('title-tag');
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);
			add_theme_support('align-wide');
			add_theme_support('responsive-embeds');

			/*
			 * Editor Styles
			 */
			add_theme_support('editor-styles');
			add_editor_style('editor-styles.css');

			/*
			 * WooCommerce.
			 */
			if (apply_filters('hello_elementor_add_woocommerce_support', true)) {
				// WooCommerce in general.
				add_theme_support('woocommerce');
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support('wc-product-gallery-zoom');
				// lightbox.
				add_theme_support('wc-product-gallery-lightbox');
				// swipe.
				add_theme_support('wc-product-gallery-slider');
			}
		}
	}
}
add_action('after_setup_theme', 'hello_elementor_setup');

function hello_maybe_update_theme_version_in_db()
{
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option($theme_version_option_name);

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if (! $hello_theme_db_version || version_compare($hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<')) {
		update_option($theme_version_option_name, HELLO_ELEMENTOR_VERSION);
	}
}

if (! function_exists('hello_elementor_display_header_footer')) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer()
	{
		$hello_elementor_header_footer = true;

		return apply_filters('hello_elementor_header_footer', $hello_elementor_header_footer);
	}
}

if (! function_exists('hello_elementor_scripts_styles')) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles()
	{
		$min_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if (apply_filters('hello_elementor_enqueue_style', true)) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if (apply_filters('hello_elementor_enqueue_theme_style', true)) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if (hello_elementor_display_header_footer()) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				get_template_directory_uri() . '/header-footer' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action('wp_enqueue_scripts', 'hello_elementor_scripts_styles');

if (! function_exists('hello_elementor_register_elementor_locations')) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations($elementor_theme_manager)
	{
		if (apply_filters('hello_elementor_register_elementor_locations', true)) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action('elementor/theme/register_locations', 'hello_elementor_register_elementor_locations');

if (! function_exists('hello_elementor_content_width')) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width()
	{
		$GLOBALS['content_width'] = apply_filters('hello_elementor_content_width', 800);
	}
}
add_action('after_setup_theme', 'hello_elementor_content_width', 0);

if (! function_exists('hello_elementor_add_description_meta_tag')) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag()
	{
		if (! apply_filters('hello_elementor_description_meta_tag', true)) {
			return;
		}

		if (! is_singular()) {
			return;
		}

		$post = get_queried_object();
		if (empty($post->post_excerpt)) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($post->post_excerpt)) . '">' . "\n";
	}
}
add_action('wp_head', 'hello_elementor_add_description_meta_tag');

// Admin notice
if (is_admin()) {
	require get_template_directory() . '/includes/admin-functions.php';
}

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if (! function_exists('hello_elementor_customizer')) {
	// Customizer controls
	function hello_elementor_customizer()
	{
		if (! is_customize_preview()) {
			return;
		}

		if (! hello_elementor_display_header_footer()) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action('init', 'hello_elementor_customizer');

if (! function_exists('hello_elementor_check_hide_title')) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title($val)
	{
		if (defined('ELEMENTOR_VERSION')) {
			$current_doc = Elementor\Plugin::instance()->documents->get(get_the_ID());
			if ($current_doc && 'yes' === $current_doc->get_settings('hide_title')) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter('hello_elementor_page_title', 'hello_elementor_check_hide_title');

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if (! function_exists('hello_elementor_body_open')) {
	function hello_elementor_body_open()
	{
		wp_body_open();
	}
}

function hello_elementor_get_theme_notifications(): ThemeNotifications
{
	static $notifications = null;

	if (null === $notifications) {
		require get_template_directory() . '/vendor/autoload.php';

		$notifications = new ThemeNotifications(
			'hello-elementor',
			HELLO_ELEMENTOR_VERSION,
			'theme'
		);
	}

	return $notifications;
}

hello_elementor_get_theme_notifications();


function allow_webp_uploads($mime_types)
{
	$mime_types['webp'] = 'image/webp';
	return $mime_types;
}
add_filter('upload_mimes', 'allow_webp_uploads');



// function convert_image_to_webp($image_data) {
//     $file_path = $image_data['file'];
//     $file_type = $image_data['type'];

//     // Define allowed image types
//     $allowed_types = ['image/jpeg', 'image/png'];

//     // Check if the uploaded file is a JPG or PNG
//     if (!in_array($file_type, $allowed_types)) {
//         return $image_data;
//     }

//     // Get WebP file path
//     $webp_path = preg_replace('/\.(jpe?g|png)$/', '.webp', $file_path);

//     // Load image using GD or Imagick
//     if (extension_loaded('gd')) {
//         $image = ($file_type === 'image/png') ? imagecreatefrompng($file_path) : imagecreatefromjpeg($file_path);
//         imagewebp($image, $webp_path, 80); // 80 is the quality level
//         imagedestroy($image);
//     } elseif (extension_loaded('imagick')) {
//         $image = new Imagick($file_path);
//         $image->setImageFormat('webp');
//         $image->setImageCompressionQuality(80);
//         $image->writeImage($webp_path);
//         $image->clear();
//         $image->destroy();
//     } else {
//         return $image_data;
//     }

//     return $image_data;
// }
// add_filter('wp_handle_upload', 'convert_image_to_webp');
// 

function add_lazy_loading_to_images($content)
{
	$content = preg_replace('/<img(.*?)src=/', '<img$1loading="lazy" src=', $content);
	return $content;
}
add_filter('the_content', 'add_lazy_loading_to_images');


/*
* Creating a function to create our CPT
*/

function custom_post_type()
{

	// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x('Team', 'Post Type General Name', 'twentytwentyone'),
		'singular_name'       => _x('Team', 'Post Type Singular Name', 'twentytwentyone'),
		'menu_name'           => __('Team', 'twentytwentyone'),
		'parent_item_colon'   => __('Parent Movie', 'twentytwentyone'),
		'all_items'           => __('All Team', 'twentytwentyone'),
		'view_item'           => __('View Team', 'twentytwentyone'),
		'add_new_item'        => __('Add New Team', 'twentytwentyone'),
		'add_new'             => __('Add New', 'twentytwentyone'),
		'edit_item'           => __('Edit Team', 'twentytwentyone'),
		'update_item'         => __('Update Team', 'twentytwentyone'),
		'search_items'        => __('Search Team', 'twentytwentyone'),
		'not_found'           => __('Not Found', 'twentytwentyone'),
		'not_found_in_trash'  => __('Not found in Trash', 'twentytwentyone'),
	);

	// Set other options for Custom Post Type

	$args = array(
		'label'               => __('team', 'twentytwentyone'),
		'description'         => __('Team news and reviews', 'twentytwentyone'),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields',),
		// You can associate this CPT with a taxonomy or custom taxonomy. 
		'taxonomies'          => array('genres'),
		/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest' => true,

	);

	// Registering your Custom Post Type
	register_post_type('team', $args);
}

/* Hook into the 'init' action so that the function
	* Containing our post type registration is not 
	* unnecessarily executed. 
	*/

add_action('init', 'custom_post_type', 0);

add_theme_support('post-thumbnails');
add_post_type_support('team', 'thumbnail');

add_shortcode('team_shortcode', 'team_shortcode_function');
function team_shortcode_function()
{

	$args = array(
		'post_type'      => 'team',
		'posts_per_page' => -1,
		'nopaging'       => true, // Retrieves all posts without pagination
	);
	$loop = new WP_Query($args);
?>
	<ul>
		<?php
		while ($loop->have_posts()) {
			$loop->the_post();
			$featured_img_url = get_the_post_thumbnail_url(get_the_id(), 'full');
			$position = get_field("position", get_the_id());
		?>
			<li>
				<div class="team-title">
					<a href="javascript:void(0)" data-image='<?php echo esc_url($featured_img_url); ?>'>
						<h3>
							<?php the_title(); ?>
						</h3>
						<span><?php echo $position; ?></span>
					</a>
				</div>
				<div class="team-content">
					<?php the_content(); ?>
				</div>
			</li>
		<?php
		}
		?>
	</ul>
<?php }
