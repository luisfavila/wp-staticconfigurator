<?php

/*
Plugin Name: WordPress Static Configurator
Plugin URI: https://github.com/luisfavila/wp-staticconfigurator
Description: Configurate WordPress statically through wp-options.php
Version: 1.0.0
Author: Luis Ávila <me@luisavila.com>
Author URI: https://luisavila.com
*/

class WPStaticConfigurator {
	static $remove_fields_js = <<<JS
	(function (){
		const disableFields = function(i){
			if(wp_disable_options.indexOf(i.name) !== -1 || wp_disable_options.indexOf(i.id) !== -1){
				i.disabled = 'disabled'
				i.title = 'This field was statically configured on wp-config so it cannot be edited.'
			} 
		}
		document.querySelectorAll("input").forEach(disableFields)
		document.querySelectorAll("select").forEach(disableFields)
		document.querySelectorAll("textarea").forEach(disableFields)
	})()
JS;

	static function setup(){
		if( defined( 'WP_OPTIONS' )){
			add_filter( 'alloptions', array(__CLASS__, 'alloptions'), 10, 1 );
			if( defined( 'WP_OPTIONS_DISABLE_FIELDS' ) && WP_OPTIONS_DISABLE_FIELDS ){
				add_action( 'admin_footer', array(__CLASS__, 'disable_fields'), 10 );
			}
		}
	}
	
	static function alloptions( $options = [] ){
		return array_merge( $options, WP_OPTIONS );
	}
	
	static function disable_fields() {
		$options = is_array( WP_OPTIONS_DISABLE_FIELDS ) ? WP_OPTIONS_DISABLE_FIELDS : array_keys(WP_OPTIONS);
		if( defined( 'WP_OPTIONS_ENABLE_FIELDS' ) && is_array( WP_OPTIONS_ENABLE_FIELDS )){
			// Careful: json_encode'ing a result of array_diff will return an object instead of an array!
			$options = array_values(array_diff( $options, WP_OPTIONS_ENABLE_FIELDS ));
		}
		echo '<script type="text/javascript">';
		echo 'var wp_disable_options = ' . json_encode($options) . ';';
		echo self::$remove_fields_js;
		echo '</script>';
	}
}

WPStaticConfigurator::setup();
