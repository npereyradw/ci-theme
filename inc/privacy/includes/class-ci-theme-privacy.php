<?php
/**
 * Google Analytics 4 and the cookie notice. The measurement ID, the measured domain and the
 * notice texts come from the site settings (see CI_Theme_Section::site()).
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Privacy' ) ) {

	/**
	 * Analytics loader and cookie notice.
	 */
	class CI_Theme_Privacy {

		const HANDLE = 'ci-theme-privacy';

		/**
		 * Register hooks.
		 */
		public static function init() {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
			add_action( 'wp_footer', array( __CLASS__, 'render' ), 5 );
		}

		/**
		 * Settings, or null when Analytics is not configured.
		 *
		 * @return array|null
		 */
		private static function settings() {
			$site = CI_Theme_Section::site();
			return ! empty( $site['analytics_id'] ) ? $site : null;
		}

		/**
		 * Script (deferred, footer) and stylesheet.
		 */
		public static function enqueue() {
			if ( ! self::settings() ) {
				return;
			}
			wp_enqueue_style(
				self::HANDLE,
				CI_THEME_URI . 'inc/privacy/assets/css/privacy.css',
				array(),
				ci_theme_asset_version( 'inc/privacy/assets/css/privacy.css' )
			);
			wp_enqueue_script(
				self::HANDLE,
				CI_THEME_URI . 'inc/privacy/assets/js/privacy.js',
				array(),
				ci_theme_asset_version( 'inc/privacy/assets/js/privacy.js' ),
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
		}

		/**
		 * Settings for the script and the notice, hidden until the script shows it.
		 */
		public static function render() {
			$site = self::settings();
			if ( ! $site ) {
				return;
			}
			$text   = trim( (string) $site['cookie_text'] );
			$link   = trim( (string) $site['cookie_link'] );
			$button = trim( (string) $site['cookie_button'] );
			$url    = get_privacy_policy_url();
			?>
			<div data-ci-privacy data-ga-id="<?php echo esc_attr( $site['analytics_id'] ); ?>" data-ga-host="<?php echo esc_attr( $site['analytics_domain'] ); ?>">
				<?php if ( '' !== $text && '' !== $button ) : ?>
					<div class="ci-cookie-notice" role="region" aria-label="<?php esc_attr_e( 'Cookie notice', 'ci-theme' ); ?>" data-ci-cookie-notice hidden>
						<p class="ci-cookie-notice-text">
							<?php echo esc_html( $text ); ?>
							<?php if ( '' !== $link && $url ) : ?>
								<a href="<?php echo esc_url( $url ); ?>" class="ci-cookie-notice-link"><?php echo esc_html( $link ); ?></a>.
							<?php endif; ?>
						</p>
						<button type="button" class="ci-cookie-notice-button" data-ci-cookie-close><?php echo esc_html( $button ); ?></button>
					</div>
				<?php endif; ?>
			</div>
			<?php
		}
	}
}
