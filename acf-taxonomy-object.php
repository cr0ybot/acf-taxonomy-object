<?php
/*
 * Plugin Name: Advanced Custom Fields: Taxonomy Object
 * Plugin URI: https://github.com/cr0ybot/acf-taxonomy-object
 * Description: ACF 5 plugin for selecting taxonomies (NOT taxonomy terms)
 * Version: 1.0.1
 * Author: Cory Hughart
 * Author URI: http://coryhughart.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/cr0ybot/acf-taxonomy-object
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// check if class already exists
if ( ! class_exists('acf_plugin_taxonomy_object') ) :
class acf_plugin_taxonomy_object {

	function __construct() {

		// vars
		$this->settings = array(
			'version' => '1.0.1',
			'url' => plugin_dir_url( __FILE__ ),
			'path' => plugin_dir_path( __FILE__ )
		);

		// set text domain
		// https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
		load_plugin_textdomain( 'acf-taxonomy-object', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );

		// include field
		add_action('acf/include_field_types', array($this, 'include_field_types')); // v5
		//add_action('acf/register_fields', array($this, 'include_field_types')); // v4
	}

	function include_field_types( $version = false ) {

		// support empty $version
		if( !$version ) $version = 4;

		if ( $version == 5 ) {
			include_once('fields/acf-taxonomy-object-v5.php');
		}
	}
}
// initialize
new acf_plugin_taxonomy_object();
// class_exists check
endif;

?>
