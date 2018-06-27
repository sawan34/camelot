<?php

namespace MPHB\Utils;

class ThirdPartyPluginsUtils {

	/**
	 * Check is plugin active.
	 *
	 * @param string $pluginSubDirSlashFile
	 * @return bool
	 */
	public static function isPluginActive( $pluginSubDirSlashFile ){
		if ( !function_exists( 'is_plugin_active' ) ) {
			/**
			 * Detect plugin. For use on Front End only.
			 */
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		return is_plugin_active( $pluginSubDirSlashFile );
	}

	/**
	 * Check is active WooCommerce
	 *
	 * @return bool
	 */
	public static function isActiveWoocommerce(){
		return self::isPluginActive( 'woocommerce/woocommerce.php' );
	}

	/**
	 * Check is active Easy Digital Downloads
	 *
	 * @return bool
	 */
	public static function isActiveEDD(){
		return self::isPluginActive( 'easy-digital-downloads/easy-digital-downloads.php' );
	}

}
