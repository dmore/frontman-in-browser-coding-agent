<?php
/**
 * UI — serves the Frontman client at /frontman.
 *
 * This page is served directly by the router's parse_request interception,
 * not via wp-admin. The client JS fetches /frontman/tools and
 * /frontman/tools/call at the same origin — identical to Vite/Astro/Next.js.
 *
 * Auth is already verified by the router before render_page() is called.
 *
 * @package Frontman
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Frontman_UI {
	private Frontman_Settings $settings;

	public function __construct( Frontman_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Register admin menu items.
	 *
	 * We keep a menu entry so admins can find Frontman in the sidebar,
	 * but it links to /frontman (the direct path) rather than a wp-admin page.
	 */
	public function register(): void {
		add_action( 'admin_menu', [ $this, 'add_admin_menu_link' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_head', [ $this, 'output_admin_menu_icon_css' ] );
	}

	/**
	 * Load branded admin styles for Frontman screens.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	public function enqueue_admin_assets( string $hook_suffix ): void {
		if ( ! in_array( $hook_suffix, [ 'toplevel_page_frontman', 'frontman_page_frontman-settings' ], true ) ) {
			return;
		}

		wp_enqueue_style(
			'frontman-admin',
			FRONTMAN_PLUGIN_URL . 'assets/frontman-admin.css',
			[],
			FRONTMAN_VERSION,
		);
	}

	/**
	 * Add a menu link that points to /frontman (external to wp-admin).
	 */
	public function add_admin_menu_link(): void {
		$menu_icon_url = FRONTMAN_PLUGIN_URL . 'assets/frontman-menu-icon.svg';

		// Register a top-level menu page so Settings can be a submenu.
		add_menu_page(
			__( 'Frontman', 'frontman' ),
			__( 'Frontman', 'frontman' ),
			'manage_options',
			'frontman',
			'__return_null', // Callback unused — we redirect below.
			$menu_icon_url,
			3,
		);

		// Redirect the wp-admin menu click to /frontman.
		add_action( 'load-toplevel_page_frontman', function (): void {
			wp_safe_redirect( home_url( '/frontman' ) );
			exit;
		} );
	}

	/**
	 * Output admin menu icon styling so the Frontman icon matches
	 * the native WordPress admin menu color scheme.
	 */
	public function output_admin_menu_icon_css(): void {
		$default_icon = $this->get_menu_icon_data_uri( '#a7aaad' );
		$active_icon  = $this->get_menu_icon_data_uri( '#ffffff' );
		?>
		<style>
			#adminmenu .toplevel_page_frontman .wp-menu-image img {
				display: none;
			}

			#adminmenu .toplevel_page_frontman .wp-menu-image {
				background-image: url("<?php echo esc_attr( $default_icon ); ?>") !important;
				background-position: center;
				background-repeat: no-repeat;
				background-size: 20px 20px;
			}

			#adminmenu .toplevel_page_frontman:hover .wp-menu-image,
			#adminmenu .toplevel_page_frontman.current .wp-menu-image,
			#adminmenu .toplevel_page_frontman.wp-has-current-submenu .wp-menu-image {
				background-image: url("<?php echo esc_attr( $active_icon ); ?>") !important;
			}
		</style>
		<?php
	}

	/**
	 * Build a base64 menu icon data URI for the requested fill color.
	 *
	 * @param string $fill Hex fill color.
	 */
	private function get_menu_icon_data_uri( string $fill ): string {
		$svg = sprintf(
			'<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="402 165 198 370" fill="none"><path d="M439.343 521.203C429.341 526.978 422.228 529.416 418.005 528.518C413.781 527.363 411.225 524.347 410.336 519.47C409.447 514.337 409.002 508.947 409.002 503.3L409.002 304.256C409.002 290.91 411.225 280.515 415.671 273.071C420.339 265.243 428.563 257.928 440.344 251.127L562.375 180.672C567.487 177.72 572.266 175.474 576.712 173.935C581.38 172.266 585.158 173.036 588.048 176.244C590.938 179.453 592.382 186.96 592.382 198.767C592.382 210.317 590.826 219.428 587.714 226.102C584.825 232.647 581.046 237.78 576.378 241.502C571.71 245.223 566.82 248.56 561.708 251.512L470.018 304.449L470.018 338.714L531.367 303.294C536.479 300.342 541.036 298.225 545.037 296.941C549.26 295.273 552.483 295.979 554.706 299.059C557.151 301.754 558.374 308.235 558.374 318.501C558.374 328.255 557.151 335.89 554.706 341.409C552.261 346.927 548.927 351.29 544.704 354.499C540.703 357.579 536.146 360.594 531.033 363.546L470.018 398.773L470.018 468.458C470.018 474.105 469.573 479.88 468.684 485.783C467.795 491.686 465.239 497.654 461.016 503.685C456.792 509.46 449.568 515.299 439.343 521.203Z" fill="%s"/></svg>',
			$fill
		);

		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}

	/**
	 * Render the full Frontman client page.
	 *
	 * Called directly by the router — this outputs a complete HTML document
	 * (no wp-admin chrome). The client is loaded from the production CDN.
	 *
	 * @param string|null $preview_path Path to load in the web preview iframe.
	 *                                  null or '/' means homepage. '/about' previews /about.
	 *                                  Set by suffix-based routing: /about/frontman → '/about'.
	 */
	public function render_page( ?string $preview_path = null ): void {
		$is_dev = (bool) $this->settings->get( 'dev_mode', false );
		$logo_url = FRONTMAN_PLUGIN_URL . 'assets/frontman-logo.svg';

		// "host" is the Frontman cloud server used for WebSocket, auth tokens, and API calls.
		// In production: api.frontman.sh. In dev: frontman.local:4000 (or FRONTMAN_HOST env var).
		if ( $is_dev ) {
			$host        = $this->settings->get( 'frontman_host', 'frontman.local:4000' );
			$dev_port    = (int) $this->settings->get( 'dev_client_port', 5173 );
			$base_js_url = "http://localhost:{$dev_port}/src/Main.res.mjs";
			$client_css  = ''; // Vite injects CSS via HMR in dev
		} else {
			$host        = 'api.frontman.sh';
			$base_js_url = 'https://app.frontman.sh/frontman.es.js';
			$client_css  = 'https://app.frontman.sh/frontman.css';
		}

		// The client reads host + clientName from import.meta.url query params.
		// This is how all Frontman adapters pass the Frontman server host to the client bundle.
		$client_url = add_query_arg(
			[
				'host'       => $host,
				'clientName' => 'wordpress',
			],
			$base_js_url
		);

		// Inline runtime config — same shape as FrontmanCore__UIShell produces
		// for Vite/Astro/Next.js. The client reads window.__frontmanRuntime.
		// basePath is used by Client__BrowserUrl.syncBrowserUrl() to keep the
		// browser URL in sync as the user navigates within the preview iframe.
		// API keys are passed so the server can use the user's own key for LLM requests.
		$runtime = [
			'framework' => 'wordpress',
			'basePath'  => 'frontman',
			'wpNonce'   => Frontman_Auth::create_nonce(),
		];

		$openrouter_key = $this->settings->get( 'openrouter_api_key', '' );
		$anthropic_key  = $this->settings->get( 'anthropic_api_key', '' );

		if ( ! empty( $openrouter_key ) ) {
			$runtime['openrouterKeyValue'] = $openrouter_key;
		}
		if ( ! empty( $anthropic_key ) ) {
			$runtime['anthropicKeyValue'] = $anthropic_key;
		}

		$runtime_config = wp_json_encode( $runtime );

		// Build the entrypoint URL for the web preview iframe.
		// When suffix routing is used (e.g. /about/frontman), this points the
		// preview at /about. The client reads this from the DOM via getInitialUrl().
		$entrypoint_url = null;
		if ( $preview_path !== null && $preview_path !== '/' ) {
			$entrypoint_url = home_url( $preview_path );
		}

		status_header( 200 );
		header( 'Content-Type: text/html; charset=utf-8' );
		// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet,WordPress.WP.EnqueuedResources.NonEnqueuedScript
		?>
<!DOCTYPE html>
<html lang="en" class="dark">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php esc_html_e( 'Frontman', 'frontman' ); ?></title>
		<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( $logo_url ); ?>">
		<?php if ( $client_css ) : ?>
		<link rel="stylesheet" href="<?php echo esc_url( $client_css ); ?>">
		<?php endif; ?>
	<style>
		html, body, #root {
			height: 100%;
			margin: 0;
			padding: 0;
		}

		#frontman-warning-overlay {
			position: fixed;
			inset: 0;
			background: rgba(10, 12, 16, 0.74);
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 24px;
			z-index: 99999;
		}

		#frontman-warning-card {
			max-width: 560px;
			width: 100%;
			background: #10151d;
			color: #f5f7fa;
			border: 1px solid rgba(255, 196, 87, 0.25);
			border-radius: 16px;
			box-shadow: 0 18px 60px rgba(0, 0, 0, 0.45);
			padding: 24px;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
		}

		#frontman-warning-card h2 {
			margin: 0;
			font-size: 24px;
			line-height: 1.2;
		}

		#frontman-warning-heading {
			display: flex;
			align-items: center;
			gap: 14px;
			margin: 0 0 12px;
		}

		#frontman-warning-heading img {
			width: 42px;
			height: 42px;
			border-radius: 12px;
			flex: 0 0 auto;
		}

		#frontman-warning-card p {
			margin: 0 0 14px;
			font-size: 15px;
			line-height: 1.6;
			color: #d9e1ea;
		}

		#frontman-warning-card ul {
			margin: 0 0 18px 20px;
			padding: 0;
			color: #d9e1ea;
		}

		#frontman-warning-card li {
			margin-bottom: 8px;
			line-height: 1.5;
		}

		#frontman-warning-card button {
			background: #ffc857;
			color: #1e2329;
			border: 0;
			border-radius: 10px;
			padding: 11px 16px;
			font-size: 14px;
			font-weight: 600;
			cursor: pointer;
		}
	</style>
</head>
<body>
	<?php if ( $entrypoint_url ) : ?>
	<script type="template" id="frontman-entrypoint-url"><?php echo esc_url( $entrypoint_url ); ?></script>
	<?php endif; ?>
	<div id="frontman-warning-overlay" hidden>
		<div id="frontman-warning-card" role="dialog" aria-modal="true" aria-labelledby="frontman-warning-title">
			<div id="frontman-warning-heading">
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="" aria-hidden="true">
				<h2 id="frontman-warning-title"><?php esc_html_e( 'Use Frontman Carefully', 'frontman' ); ?></h2>
			</div>
			<p><?php esc_html_e( 'Frontman for WordPress is experimental. The agent can change real content, templates, styles, menus, widgets, and settings on your site.', 'frontman' ); ?></p>
			<ul>
				<li><?php esc_html_e( 'Make sure you have a current backup before using it on an important site.', 'frontman' ); ?></li>
				<li><?php esc_html_e( 'Review each change carefully. The agent may not always do exactly what you intended.', 'frontman' ); ?></li>
				<li><?php esc_html_e( 'When possible, start on a staging site first.', 'frontman' ); ?></li>
			</ul>
			<button type="button" id="frontman-warning-dismiss"><?php esc_html_e( 'I Understand', 'frontman' ); ?></button>
		</div>
	</div>
	<div id="root"></div>
	<script>window.__frontmanRuntime=<?php echo wp_json_encode( $runtime ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe JSON for inline script. ?></script>
	<script>if(typeof process==="undefined"){window.process={env:{NODE_ENV:"production"}}}</script>
	<script>
	(function() {
		var storageKey = 'frontman-wordpress-warning-dismissed-v1';
		var overlay = document.getElementById('frontman-warning-overlay');
		var button = document.getElementById('frontman-warning-dismiss');
		if (!overlay || !button) return;

		try {
			if (window.localStorage && window.localStorage.getItem(storageKey) === 'true') {
				return;
			}
		} catch (e) {}

		overlay.hidden = false;
		button.addEventListener('click', function() {
			try {
				if (window.localStorage) {
					window.localStorage.setItem(storageKey, 'true');
				}
			} catch (e) {}
			overlay.hidden = true;
		});
	})();
	</script>
	<script type="module" src="<?php echo esc_url( $client_url ); ?>"></script>
</body>
</html>
		<?php
		// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet,WordPress.WP.EnqueuedResources.NonEnqueuedScript
	}
}
