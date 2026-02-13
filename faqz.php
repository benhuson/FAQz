<?php 

/*
Plugin Name: Frequently Asked Questions (FAQs)
Plugin URI: https://github.com/benhuson/FAQz
Description: Simple management of Frequently Asked Questions (FAQ) via post type and categories.
Version: 1.0
Requires at least: 3.5
Requires PHP: 7.4
Author: Ben Huson
Author URI: https://github.com/benhuson/
License: GPLv2
Text Domain: faqz
*/

/*
Copyright 2012 Ben Huson (email : ben@thewhiteroom.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) || exit;

class FAQz {

	protected $plugin_dir;
	protected $plugin_subdir;
	protected $plugin_url;

	/**
	 * Constructor
	 */
	public function __construct() {

		// Paths
		$this->plugin_dir = plugin_dir_path( __FILE__ );
		$this->plugin_subdir = '/' . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) );
		$this->plugin_url = plugins_url( $this->plugin_subdir );

		// Includes
		include_once( $this->plugin_dir . 'includes/widgets.php' );

		// Setup
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_shortcode( 'faqz', array( $this, 'shortcode_faqz' ) );

	}

	/**
	 * Register Post Types
	 */
	function register_post_types() {

		$args = array(
			'labels'             => array(
				'name'               => _x( 'FAQs', 'Post type general name', 'faqz' ),
				'singular_name'      => _x( 'FAQ', 'Post type singular name', 'faqz' ),
				'add_new'            => _x( 'Add New', 'book', 'faqz' ),
				'add_new_item'       => __( 'Add New FAQ', 'faqz' ),
				'edit_item'          => __( 'Edit FAQ', 'faqz' ),
				'new_item'           => __( 'New FAQ', 'faqz' ),
				'all_items'          => __( 'All FAQs', 'faqz' ),
				'view_item'          => __( 'View FAQ', 'faqz' ),
				'search_items'       => __( 'Search FAQs', 'faqz' ),
				'not_found'          => __( 'No FAQs found', 'faqz' ),
				'not_found_in_trash' => __( 'No FAQs found in Trash', 'faqz' ), 
				'parent_item_colon'  => '',
				'menu_name'          => __( 'FAQs', 'faqz' )
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true, 
			'show_in_menu'       => true, 
			'query_var'          => true,
			'rewrite'            => array(
				'slug'       => _x( 'faqz', 'Single URL slug', 'faqz' ),
				'with_front' => false
			),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'menu_icon'          => 'dashicons-format-chat',
			'supports'           => array( 'title', 'editor', 'author', 'excerpt' )
		);

		register_post_type( 'faqz', apply_filters( 'faqz_register_post_type_args', $args ) );

	}

	/**
	 * Register Taxonomies
	 */
	function register_taxonomies() {

		$args = array(
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => _x( 'Categories', 'taxonomy general name', 'faqz' ),
				'singular_name'     => _x( 'Category', 'taxonomy singular name', 'faqz' ),
				'search_items'      => __( 'Search Categories', 'faqz' ),
				'all_items'         => __( 'All Categories', 'faqz' ),
				'parent_item'       => __( 'Parent Category', 'faqz' ),
				'parent_item_colon' => __( 'Parent Category:', 'faqz' ),
				'edit_item'         => __( 'Edit Category', 'faqz' ),
				'update_item'       => __( 'Update Category', 'faqz' ),
				'add_new_item'      => __( 'Add New Category', 'faqz' ),
				'new_item_name'     => __( 'New Category Name', 'faqz' ),
				'menu_name'         => __( 'Category', 'faqz' ),
			),
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'       => 'faqz-category',
				'with_front' => false
			),
		);

		register_taxonomy( 'faqz_category', array( 'faqz' ), apply_filters( 'faqz_register_taxonomy_args', $args ) );

	}

	/**
	 * Post Updated Messages
	 *
	 * @param   array  $messages  Messages.
	 * @return  array             Messages.
	 */
	public function post_updated_messages( $messages ) {

		global $post, $post_ID;

		$messages['faqz'] = array(
			0 => '', // Unused. Messages start at index 1.
			/* translators: %s: link to view FAQ */
			1 => sprintf( __( 'FAQ updated. <a href="%s">View FAQ</a>', 'faqz' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', 'faqz' ),
			3 => __( 'Custom field deleted.', 'faqz' ),
			4 => __( 'FAQ updated.', 'faqz' ),
			/* translators: %s: date and time of the revision */
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'FAQ restored to revision from %s', 'faqz' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			/* translators: %s: link to view FAQ */
			6 => sprintf( __( 'FAQ published. <a href="%s">View FAQ</a>', 'faqz' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'FAQ saved.', 'faqz' ),
			/* translators: %s: link to preview FAQ */
			8 => sprintf( __( 'FAQ submitted. <a target="_blank" href="%s">Preview FAQ</a>', 'faqz' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			/* translators: %1$s: scheduled publish date, %2$s: link to preview FAQ */
			9 => sprintf( __( 'FAQ scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview FAQ</a>', 'faqz' ),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i', 'faqz' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			/* translators: %s: link to preview FAQ */
			10 => sprintf( __( 'FAQ draft updated. <a target="_blank" href="%s">Preview FAQ</a>', 'faqz' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;

	}

	/**
	 * Template Redirect
	 */
	public function template_redirect() {

		if ( is_search() && ( is_post_type_archive( 'faqz' ) || ( is_archive() && 'faqz' == get_post_type() ) ) ) {

			$search_template = locate_template( 'search-faqz.php' );

			if ( '' != $search_template ) {
				require( $search_template );
				exit;
			}

		}

	}

	/**
	 * Get Search Form
	 *
	 * @param   boolean  $echo  Echo the form?
	 * @return  string          Form HTML.
	 */
	public function get_search_form( $echo = true ) {

		do_action( 'faqz_get_search_form' );

		$search_form_template = locate_template( 'searchform-faqz.php' );
		if ( '' != $search_form_template ) {
			require( $search_form_template );
			return;
		}

		$form = '<form role="search" method="get" id="faqz-searchform" action="' . esc_url( get_post_type_archive_link( 'faqz' ) ) . '" >
		<div><label class="screen-reader-text" for="faqz-s">' . __( 'Search for:', 'faqz' ) . '</label>
		<input type="text" value="' . get_search_query() . '" name="s" id="faqz-s" />
		<input type="submit" id="faqz-searchsubmit" value="'. esc_attr__( 'Search', 'faqz' ) .'" />
		</div>
		</form>';

		$result = apply_filters( 'faqz_get_search_form', $form );

		if ( $echo ) {
			echo $result;
		} else {
			return $result;
		}

	}

	/**
	 * Shortcode: [faq /]
	 *
	 * @param   array   $atts     Shortcode attributes.
	 * @param   string  $content  Default content.
	 * @return  string            Content.
	 */
	public function shortcode_faqz( $atts, $content = '' ) {

		$atts = wp_parse_args( $atts, array(
			'faqz_context' => 'shortcode'
		) );

		return $content . $this->faqz_list( $atts );

	}

	/**
	 * FAQz List
	 *
	 * @param   array   $args  Display args.
	 * @return  string         List HTML.
	 */
	public function faqz_list( $args = null ) {

		// Args
		$args = wp_parse_args( $args, array(
			'posts_per_page'   => -1,
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'faqz_context'     => 'list',
			'faqz_before'      => '<div class="faqz-faqs">',
			'faqz_after'       => '</div>',
			'faqz_before_item' => '<div class="faqz-faq">',
			'faqz_after_item'  => '</div>'
		) );
		$args['post_type'] = 'faqz';

		$faqs = '';
		$faqs_query = new WP_Query( $args );

		// Output FAQz
		if ( $faqs_query->have_posts() ) {
			while ( $faqs_query->have_posts() ) {
				$faqs_query->the_post();

				$faq = '<h3 class="faqz-question"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
				$faq .= '<div class="faqz-answer">' . get_the_content() . '</div>';
				$faqs .= $args['faqz_before_item'] . apply_filters( 'faqz_loop', $faq, $args ) . $args['faqz_after_item'];

			}
			wp_reset_postdata();
		}

		if ( ! empty( $faqs ) ) {
			$faqs = $args['faqz_before'] . $faqs . $args['faqz_after'];
		}

		return $faqs;

	}

	/**
	 * Load Text Domain Language Support
	 */
	public function load_textdomain() {

		load_plugin_textdomain( 'faqz', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}
	
	/**
	 * Register Activation
	 * Perform upgrades etc.
	 */
	public function register_activation() {

		$this->register_post_types();
		flush_rewrite_rules();

	}

}

global $faqz;
$faqz = new FAQz();
register_activation_hook( __FILE__, array( $faqz, 'register_activation' ) );

/**
 * FAQz List
 *
 * @param   array   $args  Display args.
 * @return  string         List HTML.
 */
function faqz_list( $args = null ) {

	global $faqz;

	return $faqz->faqz_list( $args );

}

/**
 * Get Search Form
 *
 * @param   boolean  $echo  Echo the form?
 * @return  string          Form HTML.
 */
function faqz_get_search_form( $echo = true ) {

	global $faqz;

	return $faqz->get_search_form( $echo );

}
