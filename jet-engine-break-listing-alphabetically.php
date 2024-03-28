<?php
/**
 * Plugin Name: JetEngine - advanced queried term
 * Plugin URI:  
 * Description: Allow to combine queried term macros with filter by the same taxonomy
 * Version:     1.0.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}
add_action( 'jet-engine/register-macros', function() {

	class JE_Advanced_Quried_Term_Marcos extends Jet_Engine_Base_Macros {
		/**
		 * Required method. Defines macros slug. This name is used in string to parse
		 */
		public function macros_tag() {
			return 'je_advanced_queried_term';
		}

		/**
		 * Required method. Defines macros name. Name will be visible in any UI of macros insertion
		 */
		public function macros_name() {
			return 'Advanced Queried Term';
		}

		/**
		 * Optional method. Used to define macros arguments if needed.
		 * 
		 * @return array
		 */
		public function macros_args() {

			return [
				'tax_slug' => [
					'label'   => 'Taxonomy slug',
					'type'    => 'text'
				],
			];

		}

		/**
		 * Required method. It's main function which returns macros value by arguments
		 */
		public function macros_callback( $args = array() ) {

			$tax_slug       = ! empty( $args['tax_slug'] ) ? $args['tax_slug'] : '';
			$current_object = $this->get_macros_object();

			if ( function_exists( 'jet_smart_filters' )
				&& jet_smart_filters()->query->is_ajax_filter()
				&& $tax_slug
			) {
				if ( empty( $_REQUEST['query'][ '_tax_query_' . $tax_slug ] ) ) {
					return null;
				} else {
					return $_REQUEST['query'][ '_tax_query_' . $tax_slug ];
				}
			}

			if ( $current_object && 'WP_Term' === get_class( $current_object ) ) {
				if ( ! $tax_slug || $tax_slug === $current_object->taxonomy ) {
					return $current_object->term_id;
				} else {
					return null;
				}
			}

			$queried_object = get_queried_object();

			if ( $queried_object && 'WP_Term' === get_class( $queried_object ) ) {
				if ( ! $tax_slug || $tax_slug === $queried_object->taxonomy ) {
					return $queried_object->term_id;
				} else {
					return null;
				}
			} else {
				return null;
			}

			return null;
		}
	}
	
	// Include file with macros class. In current example class itself added below
	new My_Macros();
} );
