<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO
 */

use YoastSEO_Vendor\TrustedLogin\Config;
use YoastSEO_Vendor\TrustedLogin\Client;
use YoastSEO_Vendor\TrustedLogin\Logging;
use YoastSEO_Vendor\TrustedLogin\Ajax;

/**
 * Implements the TrustedLogin integration.
 */
class WPSEO_TrustedLogin implements WPSEO_WordPress_Integration {

	/**
	 * The API key for the TrustedLogin integration.
	 */
	const TRUSTEDLOGIN_API_KEY = '29a881c9d16467bd';

	/**
	 * The URL of the website running the TrustedLogin Connector plugin.
	 *
	 * TODO: Replace this with the actual URL.
	 */
	const TRUSTEDLOGIN_CONNECTOR_WEBSITE = 'https://yoast.test.trustedlogin.dev';

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// This method is called at priority 10.
		add_action( 'plugins_loaded', [ $this, 'initialize' ], 20 );
	}

	public function register_ajax_hooks() {

		$configuration = new Config( $this->get_configuration() );
		$logging = new Logging( $configuration );
		$ajax = new Ajax( $configuration, $logging );
		$ajax->init();
	}

	public function initialize() {
		try {
			$configuration = new Config( $this->get_configuration() );
			new Client( $configuration );
		} catch ( \Exception $exception ) {
			error_log( $exception->getMessage() );
		}
	}

	/**
	 * Retrieves the configuration for the TrustedLogin integration.
	 *
	 * @see https://docs.trustedlogin.com/Client/configuration
	 *
	 * @return array The configuration.
	 */
	private function get_configuration() {
		return [
			'auth' => [
				'api_key' => self::TRUSTEDLOGIN_API_KEY,
			],
			'vendor' => [
				'namespace' => 'yoast',
				'title' => 'Yoast',
				'email' => 'support@yoast.com',
				'website' => self::TRUSTEDLOGIN_CONNECTOR_WEBSITE,
				'support_url' => 'https://yoast.com/help/support/',
				'logo_url' => plugins_url( 'packages/js/images/Yoast_SEO_Icon.svg', WPSEO_FILE ),
			],
			'role' => 'editor',
			'menu' => [
				'slug' => 'wpseo_dashboard',
				'title' => esc_html__( 'Grant Support Access', 'wordpress-seo' ),
			],
			'paths' => [
				'css' => plugins_url( 'css/dist/trustedlogin.css', WPSEO_FILE ),
				'js'  => plugins_url( 'vendor/trustedlogin/client/src/assets/trustedlogin.js', WPSEO_FILE ),
			],
		];
	}
}
