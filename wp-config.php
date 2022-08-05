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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'PnW)4n}Z.p;Tu8VcM71O9R |f-U,{2d+M7ZlPjyi7HmV5/|,aMCe<HyJ`SXAd:f1' );
define( 'SECURE_AUTH_KEY',  'gY[gCQRzMl>Yxl.V=,#x(_pe,5uNGk0_py%iE |_s1C t4[Jb|-dNcBWD/3)Y :g' );
define( 'LOGGED_IN_KEY',    ')m.i sy8Hnlg+#A!WJO&qk=7R;@9$J|X9Q^t_@UZ,q)&m;B(pF7pKh%70&W*2z`X' );
define( 'NONCE_KEY',        '4 0#yPI<&)16d]`<;KEZ/CQ*YW[Wxw;uU? cjwIdZeP/6`:]u?c%:F-dsmFV[N(6' );
define( 'AUTH_SALT',        'N ;`ggX3$p_gB`gCKX_kVt<)O]/in~=Yeg5^L>I!5URu6{8>dh~,<;59Vxb3${dh' );
define( 'SECURE_AUTH_SALT', 'B)A}>J_4n 27`e2k7Pe+x~Q2xTQ}Ok_de}#Sd=~1H@^PW7o_nO%C6uxR<Z$Egm[X' );
define( 'LOGGED_IN_SALT',   'Qck(&:m0?QVvSd%)j%Tv3S$[D*|H}y;Xp<SvW3,1Ian@brTv&u[{s-myJ%x;l|[_' );
define( 'NONCE_SALT',       '0*jLi{Rf4@l;M]Z^~Qh[3Vdq@G_5K&Dpdait`I8GG--~7^)L1kY)O7Yjb}8|7;gi' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
