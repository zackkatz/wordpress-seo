<?php

declare( strict_types=1 );

use Isolated\Symfony\Component\Finder\Finder;

return array(

	/*
	 * By default when running php-scoper add-prefix, it will prefix all relevant code found in the current working
	 * directory. You can however define which files should be scoped by defining a collection of Finders in the
	 * following configuration key.
	 *
	 * For more see: https://github.com/humbug/php-scoper#finders-and-paths
	 */
	'finders'                    => [
		Finder::create()->files()->in( 'vendor/trustedlogin/client' )->name( [
			'LICENSE',
			'composer.json'
		] ),
		Finder::create()->files()->in( 'vendor/trustedlogin/client/src' )->name( [
			'*.php',
			'*.css',
			'*.js',
		] ),
	],

	/*
	 * When scoping PHP files, there will be scenarios where some of the code being scoped indirectly references the
	 * original namespace. These will include, for example, strings or string manipulations. PHP-Scoper has limited
	 * support for prefixing such strings. To circumvent that, you can define patchers to manipulate the file to your
	 * heart contents.
	 *
	 * For more see: https://github.com/humbug/php-scoper#patchers
	 */
	'patchers' => [
		/**
		 * Replaces the Adapter prefixed versions with the original ones.
		 *
		 * @param string $filePath The path of the current file.
		 * @param string $prefix   The prefix to be used.
		 * @param string $content  The content of the specific file.
		 *
		 * @return string The modified content.
		 */
		function( $file_path, $prefix, $content ) {

			$allowlist = [
				'DateTime',
				'Exception',
				'ImagickException',
				'RuntimeException',
				'WP_Admin_Bar',
				'WP_Debug_Data',
				'WP_Error',
				'WP_Filesystem_Base',
				'WP_Filesystem',
				'wp_get_environment_type',
				'WP_User',
			];

			foreach ( $allowlist as $class ) {
				$content = str_replace( [
					$prefix . '\\' . $class, // Adapter-prefixed.
					$prefix . '\\\\' . $class // Catch double-escaped classes.
				], $class, $content );
			}

			return $content;
		},
	],
);
