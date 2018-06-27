<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'guest');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'india@123');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'CFW/OSj~kB0T0[?u7yel28y/yon~8],3GCr~FxJsI>:yj9&kkq41DQhJ@W6:1f4b');
define('SECURE_AUTH_KEY',  'AY_J<nT6*g41%BYHs:.7dC4uPjIeoZj3HUnPo$EK3[&gAceG=-rJ>!U0lgnRDoE|');
define('LOGGED_IN_KEY',    'Y5l%g@?)/P+)zmj_XRBM. u|lt*QtNai!4a!a(&zczT^2HY(XBv::X8BC}Q-G7$K');
define('NONCE_KEY',        'f?gn!9~l[F8_UkG?%n0$bQ{2yW^zRgF~(q9(c6t9ml5Ap/ZEt_$cU mst$UgsU;}');
define('AUTH_SALT',        '^](RB4xd%:j6SHqttTMf.|L0=iWg71;@BQ4{4?by,L<d2s!GG)B;yCBFfF@LpG{)');
define('SECURE_AUTH_SALT', 'qp;~TfM8~JMIsno^a92>C/iHZC]0HN}2qz27nMEZUu~?]Qo{fF&O(m=Sn djF]%t');
define('LOGGED_IN_SALT',   'ZLMw^@`[v6?HlHOW_X7qH8nK.f5_dwwXP0Hfv</~BH.DegEn>QR},Jcs@aR^}mH?');
define('NONCE_SALT',       'l#]O0bgSAWWPM^ecic.[$f1eOjz[ss|FAg@8sWL]v=0v/C%0GPjpXZORBkXzZyHS');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
