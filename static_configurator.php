<?php
/**
 * Plugin Name: WordPress Static Configurator
 * Plugin URI: https://github.com/luisfavila/wp-staticconfigurator
 * Description: Configurate WordPress statically through wp-options.php
 * Version: 1.1.0
 * Author: Luis Ávila <me@luisavila.com>
 * Author URI: https://luisavila.com
 */

class WPStaticConfigurator {
	static $wp_options = [];
	static $wp_options_fix = [];
	static $options_fixed = true;
	static function setup() {
		if( defined( 'WP_OPTIONS' )) {
			self::$wp_options = WP_OPTIONS;
			add_filter( 'alloptions', array(__CLASS__, 'alloptions'), 10, 1 );
			if( defined( 'WP_OPTIONS_DISABLE_FIELDS' ) && WP_OPTIONS_DISABLE_FIELDS ) {
				add_action( 'admin_footer', array( __CLASS__, 'disable_fields' ), 10 );
			}
		}
		if( defined( 'WP_OPTIONS_FIX_DB_FIELDS' ) ) {
			self::$wp_options_fix = WP_OPTIONS_FIX_DB_FIELDS;
			self::$options_fixed = false;
		}
	}
	/**
	 * Loads custom WordPress options on `alloptions` filter
	 *
	 * @param array $options Options.
	 */
	static function alloptions( $options = [] ) {
		if( ! self::$options_fixed ) {
			self::fixoptions( $options );
		}
		return array_merge( $options, self::$wp_options );
	}
	/**
	 * Disables WordPress configuration fields on wp-admin.
	 */
	static function disable_fields() {
		$options = is_array( WP_OPTIONS_DISABLE_FIELDS ) ? WP_OPTIONS_DISABLE_FIELDS : array_keys( WP_OPTIONS );
		if ( defined( 'WP_OPTIONS_ENABLE_FIELDS' ) && is_array( WP_OPTIONS_ENABLE_FIELDS ) ) {
			// Careful: json_encode'ing a result of array_diff will return an object instead of an array!
			$options = array_values( array_diff( $options, WP_OPTIONS_ENABLE_FIELDS ) );
		}
		echo '<script type="text/javascript">';
		echo 'var wp_disable_options = ' . wp_json_encode( $options ) . ';';
		echo
			"(function (){
				const disableFields = function(i){
					if(wp_disable_options.indexOf(i.name) !== -1 || wp_disable_options.indexOf(i.id) !== -1){
						i.disabled = 'disabled'
						i.title = 'This field was statically configured on wp-config so it cannot be edited.'
					} 
				}
				document.querySelectorAll('input').forEach(disableFields)
				document.querySelectorAll('select').forEach(disableFields)
				document.querySelectorAll('textarea').forEach(disableFields)
			})()";
		echo '</script>';
	}
	/**
	 * Fixes WordPress options in database
	 */
	static function fixoptions( $options ) {
		$to_fix = [];
		foreach( self::$wp_options_fix as $option ) {
			if( ! isset( $options[$option] ) || $options[$option] !== self::$wp_options[$option] ) {
				$to_fix[$option] = self::$wp_options[$option];
				self::$wp_options[$option] = null;
			}
		}
		add_filter( 'init', function() use ( $to_fix ) {
			foreach( $to_fix as $option => $value ) {
				update_option( $option, $value );
			}
		}, 10, 1 );
		self::$options_fixed = true;
	}
}
WPStaticConfigurator::setup();