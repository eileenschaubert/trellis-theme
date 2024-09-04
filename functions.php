<?php
/**
 * Genesis Sample.
 *
 * This file adds functions to the Genesis Sample Theme.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */

// Starts the engine.
require_once get_template_directory() . '/lib/init.php';

// Sets up the Theme.
require_once get_stylesheet_directory() . '/lib/theme-defaults.php';

add_action( 'after_setup_theme', 'genesis_sample_localization_setup' );
/**
 * Sets localization (do not remove).
 *
 * @since 1.0.0
 */
function genesis_sample_localization_setup() {

	load_child_theme_textdomain( genesis_get_theme_handle(), get_stylesheet_directory() . '/languages' );

}

// Adds helper functions.
require_once get_stylesheet_directory() . '/lib/helper-functions.php';

// Adds image upload and color select to Customizer.
require_once get_stylesheet_directory() . '/lib/customize.php';

// Includes Customizer CSS.
require_once get_stylesheet_directory() . '/lib/output.php';

// Adds WooCommerce support.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';

// Adds the required WooCommerce styles and Customizer CSS.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';

// Adds the Genesis Connect WooCommerce notice.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';

add_action( 'after_setup_theme', 'genesis_child_gutenberg_support' );
/**
 * Adds Gutenberg opt-in features and styling.
 *
 * @since 2.7.0
 */
function genesis_child_gutenberg_support() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- using same in all child themes to allow action to be unhooked.
	require_once get_stylesheet_directory() . '/lib/gutenberg/init.php';
}

// Registers the responsive menus.
if ( function_exists( 'genesis_register_responsive_menus' ) ) {
	genesis_register_responsive_menus( genesis_get_config( 'responsive-menus' ) );
}

add_action( 'wp_enqueue_scripts', 'genesis_sample_enqueue_scripts_styles' );
/**
 * Enqueues scripts and styles.
 *
 * @since 1.0.0
 */
function genesis_sample_enqueue_scripts_styles() {

	$appearance = genesis_get_config( 'appearance' );

	wp_enqueue_style( // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- see https://core.trac.wordpress.org/ticket/49742
		genesis_get_theme_handle() . '-fonts',
		$appearance['fonts-url'],
		[],
		null
	);

	wp_enqueue_style( 'dashicons' );

	if ( genesis_is_amp() ) {
		wp_enqueue_style(
			genesis_get_theme_handle() . '-amp',
			get_stylesheet_directory_uri() . '/lib/amp/amp.css',
			[ genesis_get_theme_handle() ],
			genesis_get_theme_version()
		);
	}

}

add_filter( 'body_class', 'genesis_sample_body_classes' );
/**
 * Add additional classes to the body element.
 *
 * @since 3.4.1
 *
 * @param array $classes Classes array.
 * @return array $classes Updated class array.
 */
function genesis_sample_body_classes( $classes ) {

	if ( ! genesis_is_amp() ) {
		// Add 'no-js' class to the body class values.
		$classes[] = 'no-js';
	}
	return $classes;
}

add_action( 'genesis_before', 'genesis_sample_js_nojs_script', 1 );
/**
 * Echo the script that changes 'no-js' class to 'js'.
 *
 * @since 3.4.1
 */
function genesis_sample_js_nojs_script() {

	if ( genesis_is_amp() ) {
		return;
	}

	?>
	<script>
	//<![CDATA[
	(function(){
		var c = document.body.classList;
		c.remove( 'no-js' );
		c.add( 'js' );
	})();
	//]]>
	</script>
	<?php
}

add_filter( 'wp_resource_hints', 'genesis_sample_resource_hints', 10, 2 );
/**
 * Add preconnect for Google Fonts.
 *
 * @since 3.4.1
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed.
 * @return array URLs to print for resource hints.
 */
function genesis_sample_resource_hints( $urls, $relation_type ) {

	if ( wp_style_is( genesis_get_theme_handle() . '-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = [
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		];
	}

	return $urls;
}

add_action( 'after_setup_theme', 'genesis_sample_theme_support', 9 );
/**
 * Add desired theme supports.
 *
 * See config file at `config/theme-supports.php`.
 *
 * @since 3.0.0
 */
function genesis_sample_theme_support() {

	$theme_supports = genesis_get_config( 'theme-supports' );

	foreach ( $theme_supports as $feature => $args ) {
		add_theme_support( $feature, $args );
	}

}

add_action( 'after_setup_theme', 'genesis_sample_post_type_support', 9 );
/**
 * Add desired post type supports.
 *
 * See config file at `config/post-type-supports.php`.
 *
 * @since 3.0.0
 */
function genesis_sample_post_type_support() {

	$post_type_supports = genesis_get_config( 'post-type-supports' );

	foreach ( $post_type_supports as $post_type => $args ) {
		add_post_type_support( $post_type, $args );
	}

}

// Adds image sizes.
add_image_size( 'sidebar-featured', 75, 75, true );
add_image_size( 'genesis-singular-images', 702, 526, true );

// Removes header right widget area.
unregister_sidebar( 'header-right' );

// Removes secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Removes site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

// Repositions primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Repositions the secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
// puts secondary nav into sticky header prior to title, etc.
add_action( 'genesis_before_header', 'genesis_do_subnav', 10 );

add_filter( 'wp_nav_menu_args', 'genesis_sample_secondary_menu_args' );
/**
 * Reduces secondary navigation menu to one level depth.
 *
 * @since 2.2.3
 *
 * @param array $args Original menu options.
 * @return array Menu options with depth set to 1.
 */
function genesis_sample_secondary_menu_args( $args ) {

	if ( 'secondary' === $args['theme_location'] ) {
		$args['depth'] = 1;
	}

	return $args;

}

add_filter( 'genesis_author_box_gravatar_size', 'genesis_sample_author_box_gravatar' );
/**
 * Modifies size of the Gravatar in the author box.
 *
 * @since 2.2.3
 *
 * @param int $size Original icon size.
 * @return int Modified icon size.
 */
function genesis_sample_author_box_gravatar( $size ) {

	return 90;

}

add_filter( 'genesis_comment_list_args', 'genesis_sample_comments_gravatar' );
/**
 * Modifies size of the Gravatar in the entry comments.
 *
 * @since 2.2.3
 *
 * @param array $args Gravatar settings.
 * @return array Gravatar settings with modified size.
 */
function genesis_sample_comments_gravatar( $args ) {

	$args['avatar_size'] = 60;
	return $args;

}


// Customizations

// Test Code for secondary style sheet
add_action( 'wp_enqueue_scripts', 'wsm_custom_stylesheet', 20 );
function wsm_custom_stylesheet() {
    wp_enqueue_style( 'custom-style', get_stylesheet_directory_uri() . '/custom.css' );
}


/** Customize Read More Text */
add_filter( 'excerpt_more', 'child_read_more_link' );
add_filter( 'get_the_content_more_link', 'child_read_more_link' );
add_filter( 'the_content_more_link', 'child_read_more_link' );
function child_read_more_link() {
 
return '<span>...</span><a class="read-more" href="' . get_permalink() . '" rel="nofollow">Learn more</a>';
}

//* Customize search form input box text
add_filter( 'genesis_search_text', 'sp_search_text' );
function sp_search_text( $text ) {
	return esc_attr( 'Search' );
}

/** Add support for custom background */
add_theme_support( 'custom-background' );


function trellisconnects_child_theme_setup() {
// Add custom color palette for theme colors
add_theme_support( 'editor-color-palette', array(
	array(
		'name'  => __( 'Blue Primary', 'trellisconnects' ),
		'slug'  => 'blu-pri',
		'color'	=> '#13294b',
	),
	array(
		'name'  => __( 'Orange Primary', 'trellisconnects' ),
		'slug'  => 'org-pri',
		'color'	=> '#ff7f32',
	),	
	array(
		'name'  => __( 'Green Primary', 'trellisconnects' ),
		'slug'  => 'grn-pri',
		'color' => '#97d700',
	),
	array(
		'name'  => __( 'Blue Sec MD', 'trellisconnects' ),
		'slug'  => 'blu-md',
		'color'	=> '#0067b9',
	),
	array(
		'name'  => __( 'Blue Sec LT', 'trellisconnects' ),
		'slug'  => 'blu-lt',
		'color'	=> '#407ec9',
	),
	array(
		'name'  => __( 'Orange Sec', 'trellisconnects' ),
		'slug'  => 'org-sec',
		'color' => '#ff6900',
	),
	array(
		'name'	=> __( 'Green Sec', 'trellisconnects' ),
		'slug'	=> 'grn-sec',
		'color'	=> '#78be21',
	),
	array(
		'name'	=> __( 'Brand Blk', 'trellisconnects' ),
		'slug'	=> 'brand-blk',
		'color'	=> '#53565a',
	),
	array(
		'name'	=> __( 'Gray DK', 'trellisconnects' ),
		'slug'	=> 'gry-dk',
		'color'	=> '#888b8d',
	),
	array(
		'name'	=> __( 'Gray MD', 'trellisconnects' ),
		'slug'	=> 'gry-md',
		'color'	=> '#a7a8a9',
	),
	array(
		'name'	=> __( 'Gray LT', 'trellisconnects' ),
		'slug'	=> 'gry-lt',
		'color'	=> '#d0d3d4',
	),
	array(
		'name'	=> __( 'Sec Background', 'trellisconnects' ),
		'slug'	=> 'sec-bkgd',
		'color'	=> '#f1f1f1',
	),
	array(
		'name'	=> __( 'White', 'trellisconnects' ),
		'slug'	=> 'white',
		'color'	=> '#fff',
	),
	array(
		'name'	=> __( 'Black', 'trellisconnects' ),
		'slug'	=> 'black',
		'color'	=> '#000',
	)
) );
}
add_action( 'after_setup_theme', 'trellisconnects_child_theme_setup', 15 );


add_theme_support(
    'genesis-custom-logo',
    [
        'height'      => 74,
        'width'       => 260,
        'flex-height' => true,
        'flex-width'  => true,
    ]
);


// Bill Erickson
/**
 * Secondary Menu Extras
 *
 */
function ea_secondary_menu_extras( $menu, $args ) {
    if( 'secondary' == $args->theme_location )
      $menu .= '<li class="menu-item search"><a href="#" class="search-toggle"><i class="fas fa-search"></i></a>' . get_search_form( false ) . '</li>';
    return $menu;
}
add_filter( 'wp_nav_menu_items', 'ea_secondary_menu_extras', 10, 2 );

//     // Enqueue Toggle JS script 
add_action( 'wp_enqueue_scripts', 'enqueue_search_script' );

function enqueue_search_script() {
        wp_enqueue_script( 'search', get_stylesheet_directory_uri() . '/js/search.js', array( 'jquery' ),  '1.0.0', true );
}


//Remove Pension Rights Blog Posts from Insights & Updates Blog Page
// 
function exclude_category($query)
{
    if (is_front_page() && is_home()) {
        return false;
    } elseif (is_front_page()) {
        return false;
    } elseif (is_home()) {
        return $query->set('cat', '-21'); // Returns query set of Updates only 
    } else {
        return false;
    }
}

add_filter( 'pre_get_posts', 'exclude_category' );


function remove_widget_categories($args)
  {
    $exclude = "1,19";
    $args["exclude"] = $exclude;
    return $args;
  }
  add_filter("widget_categories_args","remove_widget_categories");



// Pulled from the Events Calendar knowledgebase

/**
 * The Events Calendar - Bypass Genesis genesis_do_post_content in Event Views
 *
 * This snippet overrides the Genesis Content Archive settings for Event Views
 *
 * Event Template set to: Admin > Events > Settings > Display Tab > Events template > Default Page Template
 *
 * The Events Calendar @4.0.4
 * Genesis @2.2.6
 */
add_action( 'get_header', 'tribe_genesis_bypass_genesis_do_post_content' );
function tribe_genesis_bypass_genesis_do_post_content() {
  if ( ! class_exists( 'Tribe__Events__Main' ) ) {
    return;
  }
/* Test customization */
  if ( is_archive() && is_category()) {
	     return;
  }
/*  End Test */
  if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
    if ( tribe_is_month() 
      || tribe_is_upcoming() 
      || tribe_is_past() 
      || tribe_is_day() 
      || tribe_is_map() 
      || tribe_is_photo() 
      || tribe_is_week() 
      || ( tribe_is_recurring_event() 
        && ! is_singular( 'tribe_events' ) ) 
    ) {
      remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
      remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
	  add_action( 'genesis_entry_content', 'the_content', 15 );
    }
  } else {
    if ( tribe_is_month() || tribe_is_upcoming() || tribe_is_past() || tribe_is_day() ) {
      remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
      remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
      add_action( 'genesis_entry_content', 'the_content', 15 );
    }
  }
 
}
/**
 * Genesis Layout of The Event Calendar Views for all Templates
 * The Events Calendar @3.10
 * Genesis @2.1.2
 * Options - full-width-content, content-sidebar, sidebar-content, content-sidebar-sidebar, sidebar-sidebar-content, sidebar-content-sidebar
 */
// Target all Event Views (Month, List, Map etc), Single Events, Single Venues, and Single Organizers

// add_filter( 'genesis_pre_get_option_site_layout', 'tribe_genesis_all_layouts' );
// function tribe_genesis_all_layouts( $layout ) {
//   if ( class_exists( 'Tribe__Events__Main' ) && tribe_is_event_query() ) {
//     return 'full-width-content';
//   }
//  
//   return $layout;
// }

// Another customization for Community Calendar Add-On

/* Change back link on single events to go to styled calendar page */

function change_backlink_url ( $link ) {
	$link = 'https://trellisconnects.org/caregiver-collaborative-events/';
	return $link;
}
add_filter( 'tribe_get_events_link', 'change_backlink_url');

/* Change default name of Calendar generated by 'Subscribe' link */
add_filter( 'tribe_events_ical_feed_filename', function() {
	return 'MetroCaregivers.ics';
} );

// Code to allow edits to the MCSC Calendar submission page. 
//Only using the intro but leaving full list here for reference

/* Add intro text to MCSC Calendar submission page */
function trellis_add_submission_intro_message() {
    echo '<p class="community-intro">Please note: Events will not appear on the calendar until approved by MCSC. Please wait a few days before reaching out.</p>';
}
 
add_action( 'tribe_events_community_form_before_template', 'trellis_add_submission_intro_message' );

// Above the submission page content.
// add_action( 'tribe_events_community_form_before_template', 'trellis_add_submission_intro_message' );
 
// Below the submission page content.
// add_action( 'tribe_events_community_form_after_template', 'trellis_add_submission_intro_message' );
 
// Above the "My Events" list page content.
// add_action( 'tribe_ce_before_event_list_top_buttons', 'trellis_add_submission_intro_message' );
 
// Below the "My Events" list page content.
// add_action( 'tribe_ce_after_event_list_table', 'trellis_add_submission_intro_message' );


// This code supplied by Events Calendar tech support - Shortcode Breakpoint Override
/**
 * The Events Calendar - 
 *
 * This snippet corrected the month view for Caregiver pages showing mobile view
 *
 * 
 */

// add_filter( 'tribe_events_views_v2_view_breakpoints', 'customize_tribe_events_breakpoints', 10, 2 );
//  
// function customize_tribe_events_breakpoints( $breakpoints, $view ) {
// 
// 	$context = $view->get_context();
// 
// 	// Not doing a shortcode - bail.
// 	if ( empty( $context->get( 'shortcode' ) ) ) {
// 		return $breakpoints;
// 	}
// 
// 	$breakpoints = [
// 		'xsmall' => 100,
// 		'medium' => 200,
// 		'full'   => 300
// 	];
//  
//     return $breakpoints;
// }



function my_rss_modify_item() {
  global $post;
  // Add event location (fudged into the <source> tag)
  if ($post->post_type == 'tribe_events') {
    if ($location = strip_tags(tribe_get_venue($post->ID))) {
      echo '<source url="' . get_permalink($post->ID) . '">' . $location . '</source>';
    }
  }
}
add_action('rss2_item','my_rss_modify_item');
