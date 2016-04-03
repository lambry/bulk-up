<?php
/**
 * Helpers
 *
 * Helper functions for this plugin
 *
 * @package BulkUp
 */

namespace BulkUp;

class Helpers {
	
	/**
	 * Get Post Types
	 *
	 * Get an array of all post types.
	 *
	 * @access public
	 * @return null
	 */
	public static function get_post_types() {

		$types = get_post_types();
		$custom_types = array_diff( $types, self::remove_types() );

		foreach ( $types as $type ) {
			$custom_types[$type] = ucfirst( str_replace( '_', ' ', $type ) );
		}

		return $custom_types;

	}	
	
	/**
	 * Get Custom Fields
	 *
	 * Get an array of all custom fields.
	 *
	 * @access public
	 * @return null
	 */
	public static function get_custom_fields() {

		$custom_fields = [];
		$fields = self::get_meta_keys_alt();

		foreach ( $fields as $field ) {
			$custom_fields[$field] = ucfirst( str_replace( '_', ' ', $field ) );
		}

		return $custom_fields;

	}

	/**
	 * Get Meta Keys Alt
	 *
	 * Gets an array of all meta keys.
	 *
	 * @access public
	 * @return null
	 */
	private static function get_meta_keys_alt() {

	    global $wpdb;
	 
	    return $wpdb->get_col( "SELECT meta_key FROM $wpdb->postmeta GROUP BY meta_key ORDER BY meta_key" );
	 
	}	

	/**
	 * Remove Types
	 *
	 * Create array of disallowed types.
	 *
	 * @access public
	 * @return null
	 */
	private static function remove_types() {

		return [
			'attachment',
			'revision',
			'nav_menu_item'
		];

	}	

}