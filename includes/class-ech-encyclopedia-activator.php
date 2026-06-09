<?php

/**
 * Fired during plugin activation
 *
 * @link       https://127.0.0.1
 * @since      1.0.0
 *
 * @package    Ech_Encyclopedia
 * @subpackage Ech_Encyclopedia/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ech_Encyclopedia
 * @subpackage Ech_Encyclopedia/includes
 * @author     Rowan Chang <rowanchang@prohaba.com>
 */
class Ech_Encyclopedia_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$domain_api = get_option( 'ech_encyclopedia_domain_url' );   
		if(empty($domain_api) || !$domain_api ) {
			add_option( 'ech_encyclopedia_domain_url', 'https://aimedicalcentre.com' );
		}
	}

}
