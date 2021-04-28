<?php
define( 'WP_CACHE', true ); // Added by WP Rocket

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
define( 'DB_NAME', 'kosherplaces' );

/** MySQL database username */
define( 'DB_USER', 'passwordispassword' );

/** MySQL database password */
define( 'DB_PASSWORD', 'password' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'N+L>L*4g#<%tcT_#>F>D%33Va]}`@Uvc}8$H|Eg>7U+ _D-zE)nz%MGWVR-#TiFA' );
define( 'SECURE_AUTH_KEY',   'Srk8sh:8u/@;}Y4kiJ=$J3jQ&@yYN=ZD!saQ$JJ;7r?]J=$+IGQ@wmi.l+HRlqPH' );
define( 'LOGGED_IN_KEY',     'ah@|ri&i{^??ym$^01i<`Gg,JOcHJL)P2-A60Ri2S]q JI,o i9ow1uD9`TK/ou6' );
define( 'NONCE_KEY',         'OAK4kkt@pF^BAczm=?![@.t}3Z@K}&Ct]6Z&O:`&@NaBM0g9]$1]9,MZ!]b1o| !' );
define( 'AUTH_SALT',         '#VFVoTO*m_|V~$*K^|[G;QL@W1Vd)-pOvG&fQ-m)64y**t&L6U=Sc){b>v-)gIoh' );
define( 'SECURE_AUTH_SALT',  'r+Hv5Z;QOZORSPbL~u-d5W1u[3`VW(Nb3aS-|HP>}+&J]>t%xE>Y]!lTCGH}SM=[' );
define( 'LOGGED_IN_SALT',    '7J!>I99x)`?fS;95II(%U.?.)P<qMyi9Sxl+@>crsD^[aqzrd8EOD0f{.<!$5,9x' );
define( 'NONCE_SALT',        '/x:Pb+9blo2z>UK|>l0G2*I;)kdjG%n7G?omm3f2w/ (3?:Xg<f%%6;BD;+HT,`v' );
define( 'WP_CACHE_KEY_SALT', 'P[8+$MP6dDiLC%=HT7T.ZNPH9:Y4k,]J?k+^Ex`vxHc,FSMOvaLfzc/) Imau8JX' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
