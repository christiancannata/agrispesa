<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'agrispesa' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Zipzap91!@' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ' U:`PASZnznlOCYB+j3lXs:f081M0+q8B?7h~,Blg)o*OjGWxgVkEf1w%}z.N^1A' );
define( 'SECURE_AUTH_KEY',  'aB4Ru.Ym[i<9QmB=%akB~,9:yabkrkQ+q!n[=JQ>Z^JxKE[}x:kI Iai@+[e`zGD' );
define( 'LOGGED_IN_KEY',    ']o3A*!YQD*_rM09QJoKe;WCQ,pnZ1,pnOmjzn#vM#x1I]p{X)s/DE:rOwyj8&Gf8' );
define( 'NONCE_KEY',        '>rJn<Q0?n}svIaa>;2,aq1[SbBKA<lKrFhaw VNew DHs1K-PHc+y@&nCcG[a~_z' );
define( 'AUTH_SALT',        'x=YaMEaYvE _g_he92!*$DO?^|g.gOQ/f/5A:d|m{^R#b];5*@+Zn7nlr--bQVU+' );
define( 'SECURE_AUTH_SALT', 'wl[9_;5eG$Py%G%EcXpkoM7J7#_,Q>1I&X().m|Z=#A<#0-wfU,}qR~XX_}M(Gv,' );
define( 'LOGGED_IN_SALT',   '8]LQzd_8H_g0*z*HKxtes/xp]o-VdR/Qnj/9F6WP^AA:s}|$6|r/I=u{}0!*4`_*' );
define( 'NONCE_SALT',       'yu-k%4>,k=M[ucc~_y`L0woT#FtYt683_A3`[`[^=s{X<;LVq?c|4sBDXY/d)G>c' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
