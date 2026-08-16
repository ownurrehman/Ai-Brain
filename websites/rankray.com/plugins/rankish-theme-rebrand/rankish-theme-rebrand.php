<?php
/**
 * Plugin Name: Rankish Theme Rebrand
 * Description: One-shot: copy sifoxen → rankish, strip sifoxen branding, switch theme, migrate mods. Safe for Elementor sites.
 * Version: 1.0.0
 * Author: Rank Ray
 * Author URI: https://rankray.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recursively copy a directory.
 */
function rankish_rebrand_copy_dir( string $src, string $dst ): bool {
	if ( ! is_dir( $src ) ) {
		return false;
	}
	if ( ! wp_mkdir_p( $dst ) ) {
		return false;
	}
	$dir = opendir( $src );
	if ( ! $dir ) {
		return false;
	}
	while ( false !== ( $file = readdir( $dir ) ) ) {
		if ( '.' === $file || '..' === $file ) {
			continue;
		}
		$from = $src . '/' . $file;
		$to   = $dst . '/' . $file;
		if ( is_dir( $from ) ) {
			if ( ! rankish_rebrand_copy_dir( $from, $to ) ) {
				closedir( $dir );
				return false;
			}
		} else {
			if ( ! copy( $from, $to ) ) {
				closedir( $dir );
				return false;
			}
		}
	}
	closedir( $dir );
	return true;
}

/**
 * Replace sifoxen branding in a text file (case-aware).
 */
function rankish_rebrand_rewrite_file( string $path ): void {
	$ext = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
	$ok  = array( 'php', 'css', 'js', 'json', 'txt', 'md', 'html', 'svg', 'xml', 'pot', 'po', 'scss', 'less' );
	if ( ! in_array( $ext, $ok, true ) ) {
		return;
	}
	$raw = file_get_contents( $path );
	if ( false === $raw || '' === $raw ) {
		return;
	}
	if ( false === stripos( $raw, 'sifoxen' ) ) {
		return;
	}

	$out = $raw;
	// Longer / specific first.
	$pairs = array(
		'Sifoxen' => 'Rankish',
		'SIFOXEN' => 'RANKISH',
		'sifoxen' => 'rankish',
	);
	foreach ( $pairs as $from => $to ) {
		$out = str_replace( $from, $to, $out );
	}

	if ( basename( $path ) === 'style.css' && 0 === strpos( ltrim( $out ), '/*' ) ) {
		$out = preg_replace(
			'/Theme Name:\s*.*/i',
			'Theme Name: Rankish by Rank Ray',
			$out,
			1
		);
		$out = preg_replace(
			'/Description:\s*.*/i',
			'Description: Rankish by Rank Ray — Elementor-ready agency theme (custom refined for Tonic Physio).',
			$out,
			1
		);
		$out = preg_replace(
			'/Version:\s*.*/i',
			'Version: 2.4.0',
			$out,
			1
		);
		$out = preg_replace(
			'/Text Domain:\s*.*/i',
			'Text Domain: rankish',
			$out,
			1
		);
		if ( ! preg_match( '/Text Domain:/i', $out ) ) {
			$out = preg_replace(
				'/(Author URI:\s*.*)/i',
				"$1\nText Domain: rankish",
				$out,
				1
			);
		}
		$out = preg_replace( '/Author:\s*.*/i', 'Author: Rank Ray', $out, 1 );
		$out = preg_replace( '/Author URI:\s*.*/i', 'Author URI: https://rankray.com', $out, 1 );
		$out = preg_replace( '/Theme URI:\s*.*/i', 'Theme URI: https://rankray.com/', $out, 1 );
	}

	if ( $out !== $raw ) {
		file_put_contents( $path, $out );
	}
}

/**
 * Walk directory and rewrite text files.
 */
function rankish_rebrand_rewrite_tree( string $dir ): void {
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS )
	);
	foreach ( $iterator as $file ) {
		if ( $file->isFile() ) {
			rankish_rebrand_rewrite_file( $file->getPathname() );
		}
	}
}

/**
 * Migrate theme_mods and switch active theme.
 */
function rankish_rebrand_switch_theme(): void {
	$old_mods = get_option( 'theme_mods_sifoxen' );
	if ( false === $old_mods ) {
		$old_mods = get_option( 'theme_mods_sifoxen-child' );
	}
	if ( is_array( $old_mods ) ) {
		update_option( 'theme_mods_rankish', $old_mods );
	}

	// Preserve Elementor / kit settings — they are not theme-slug keyed for kits.
	switch_theme( 'rankish' );
}

/**
 * Optionally rebrand companion addon plugin in place (folder rename).
 */
function rankish_rebrand_addon_plugin(): void {
	$plugins = WP_PLUGIN_DIR;
	$old     = $plugins . '/sifoxen-addon';
	$new     = $plugins . '/rankish-addon';
	if ( ! is_dir( $old ) ) {
		// Hostinger random suffix folders.
		$matches = glob( $plugins . '/sifoxen-addon*' );
		if ( ! $matches ) {
			return;
		}
		$old = $matches[0];
	}
	if ( is_dir( $new ) ) {
		return;
	}
	if ( ! rankish_rebrand_copy_dir( $old, $new ) ) {
		return;
	}
	rankish_rebrand_rewrite_tree( $new );

	$main_candidates = glob( $new . '/*.php' );
	$main            = '';
	foreach ( $main_candidates as $cand ) {
		$head = (string) file_get_contents( $cand, false, null, 0, 800 );
		if ( false !== stripos( $head, 'Plugin Name:' ) ) {
			$main = $cand;
			// Fix plugin header name.
			$head_full = file_get_contents( $cand );
			if ( is_string( $head_full ) ) {
				$head_full = preg_replace( '/Plugin Name:\s*.*/i', 'Plugin Name: Rankish Theme Addon', $head_full, 1 );
				$head_full = str_replace( array( 'Sifoxen', 'sifoxen', 'SIFOXEN' ), array( 'Rankish', 'rankish', 'RANKISH' ), $head_full );
				file_put_contents( $cand, $head_full );
			}
			break;
		}
	}
	if ( $main ) {
		$basename = 'rankish-addon/' . basename( $main );
		activate_plugin( $basename, '', false, false );
	}
}

/**
 * Run once on admin_init after activation flag.
 */
function rankish_rebrand_run(): void {
	if ( get_option( 'rankish_theme_rebrand_done' ) === '1' ) {
		return;
	}
	if ( ! current_user_can( 'switch_themes' ) && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		// Allow run during plugin activation hook without current user in some hosts.
		if ( ! doing_action( 'activate_rankish-theme-rebrand/rankish-theme-rebrand.php' )
			&& ! doing_action( 'activated_plugin' ) ) {
			return;
		}
	}

	$themes = get_theme_root();
	$src    = $themes . '/sifoxen';
	$dst    = $themes . '/rankish';

	if ( ! is_dir( $src ) ) {
		update_option(
			'rankish_theme_rebrand_log',
			'Source theme sifoxen not found at ' . $src
		);
		return;
	}

	if ( is_dir( $dst ) ) {
		// Already copied — still ensure branding + activation.
		rankish_rebrand_rewrite_tree( $dst );
	} else {
		if ( ! rankish_rebrand_copy_dir( $src, $dst ) ) {
			update_option( 'rankish_theme_rebrand_log', 'Failed to copy theme to rankish' );
			return;
		}
		rankish_rebrand_rewrite_tree( $dst );
	}

	rankish_rebrand_switch_theme();
	rankish_rebrand_addon_plugin();

	if ( class_exists( '\Elementor\Plugin' ) ) {
		try {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		} catch ( Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		}
	}
	if ( class_exists( 'LiteSpeed\Purge' ) ) {
		try {
			\LiteSpeed\Purge::purge_all();
		} catch ( Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		}
	}

	update_option( 'rankish_theme_rebrand_done', '1' );
	update_option(
		'rankish_theme_rebrand_log',
		'OK: active=' . get_stylesheet() . ' template=' . get_template() . ' at ' . gmdate( 'c' )
	);
}
register_activation_hook( __FILE__, 'rankish_rebrand_run' );
add_action( 'admin_init', 'rankish_rebrand_run', 1 );

/**
 * After successful rebrand, deactivate this one-shot plugin (and old addon if present).
 */
add_action(
	'admin_init',
	static function () {
		if ( get_option( 'rankish_theme_rebrand_done' ) !== '1' ) {
			return;
		}
		if ( get_option( 'rankish_theme_rebrand_cleanup' ) === '1' ) {
			return;
		}
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		// Deactivate legacy addon slug if still active.
		$legacy = array();
		foreach ( (array) get_option( 'active_plugins', array() ) as $plugin ) {
			if ( false !== strpos( $plugin, 'sifoxen-addon' ) ) {
				$legacy[] = $plugin;
			}
			if ( false !== strpos( $plugin, 'rankish-theme-rebrand' ) ) {
				$legacy[] = $plugin;
			}
		}
		if ( $legacy ) {
			deactivate_plugins( $legacy, true );
		}
		update_option( 'rankish_theme_rebrand_cleanup', '1' );
	},
	20
);

add_action(
	'admin_notices',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$log = get_option( 'rankish_theme_rebrand_log' );
		if ( ! $log ) {
			return;
		}
		echo '<div class="notice notice-success"><p><strong>Rankish rebrand:</strong> ' . esc_html( (string) $log ) . '</p></div>';
	}
);
