<?php
/**
 * Render: ci/site-header — floating bar, "Menu" pill, services submenu and full-screen menu.
 *
 * The links are editable (items attribute); the services come from the service post type and
 * the contact details from the site settings.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$site     = CI_Theme_Section::site();
$services = CI_Theme_Content::services();
$items    = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
$text     = static function ( $key ) use ( $attributes ) {
	return isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
};

// A link is active when it points to the current page or to a page inside it.
$current = isset( $_SERVER['REQUEST_URI'] ) ? untrailingslashit( (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) ) : '';
$active  = static function ( $url ) use ( $current ) {
	$path = untrailingslashit( (string) wp_parse_url( $url, PHP_URL_PATH ) );
	return '' !== $path && ( $current === $path || 0 === strpos( $current, $path . '/' ) );
};

$sub_id     = wp_unique_id( 'ci-header-sub-' );
$menu_id    = wp_unique_id( 'ci-header-menu-' );
$mark       = '<span class="ci-header-mark" aria-hidden="true"><span class="ci-header-mark-line"></span><span class="ci-header-mark-dot"></span></span>';
$home       = esc_url( home_url( '/' ) );
$home_label = CI_Theme_Logo::home_label();
$dark_logo  = isset( $attributes['logoDark'] ) ? (int) $attributes['logoDark'] : 0;
$cta_url    = CI_Theme_Section::url( $text( 'ctaUrl' ) );
$has_sub    = false;
$palette    = array(
	'arc' => CI_Theme_Section::hex( 'navy-2' ),
	'dot' => CI_Theme_Section::hex( 'yellow' ),
	'bar' => CI_Theme_Section::hex( 'red-bright' ),
);

// Service link: number, name and the "coming soon" badge. Classes: [link, number, title].
$service_link = static function ( $service, $classes, $badge, $attrs = '' ) {
	return sprintf(
		'<a href="%1$s" class="%2$s"%3$s><span class="%4$s">%5$s</span>%6$s%7$s</a>',
		esc_url( $service['url'] ),
		esc_attr( $classes[0] ),
		$attrs,
		esc_attr( $classes[1] ),
		esc_html( $service['n'] ),
		$classes[2] ? '<span class="' . esc_attr( $classes[2] ) . '">' . esc_html( $service['title'] ) . '</span>' : esc_html( $service['title'] ),
		$service['soon'] ? '<span class="ci-header-badge">' . esc_html( $badge ) . '</span>' : ''
	);
};
?>
<a href="#content" class="ci-skip-link"><?php esc_html_e( 'Skip to content', 'ci-theme' ); ?></a>
<header <?php echo get_block_wrapper_attributes( array( 'class' => 'ci-header' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?> data-ci-header>
	<div class="ci-header-bar" data-header-bar>
		<span data-bar-bg class="ci-header-bar-bg" aria-hidden="true"></span>

		<div data-bar-items class="ci-header-bar-items">
			<?php /* Desktop: navigation on the left. */ ?>
			<nav class="ci-header-nav" aria-label="<?php esc_attr_e( 'Main', 'ci-theme' ); ?>">
				<?php
				foreach ( $items as $item ) :
					$url = CI_Theme_Section::url( isset( $item['url'] ) ? $item['url'] : '' );
					if ( '' === $url || empty( $item['label'] ) ) {
						continue;
					}
					$is_active = $active( $url );
					$link      = sprintf(
						'<a href="%1$s" class="ci-header-nav-link%2$s"%3$s>%4$s%5$s</a>',
						$url,
						$is_active ? ' ci-header-nav-link-active' : '',
						$is_active ? ' aria-current="page"' : '',
						esc_html( $item['label'] ),
						$mark
					);

					if ( ! empty( $item['services'] ) && $services && ! $has_sub ) :
						$has_sub = true;
						?>
						<div data-bar-item data-sub-group class="ci-header-nav-group">
							<?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>
							<button type="button" class="ci-header-nav-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $sub_id ); ?>" aria-label="<?php esc_attr_e( 'Show services', 'ci-theme' ); ?>" data-sub-toggle>
								<svg viewBox="0 0 10 6" width="10" height="6" aria-hidden="true" focusable="false"><path d="M1 1 L5 5 L9 1" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</button>
						</div>
					<?php else : ?>
						<?php echo str_replace( '<a ', '<a data-bar-item ', $link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</nav>

			<a href="<?php echo $home; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url above. ?>" data-bar-item class="ci-header-logo" aria-label="<?php echo esc_attr( $home_label ); ?>">
				<span class="ci-header-logo-full"><?php echo CI_Theme_Logo::render( array( 'height' => 40 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Logo. ?></span>
				<span class="ci-header-logo-compact">
					<span class="ci-header-logo-color"><?php echo CI_Theme_Logo::render( array( 'height' => 30 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Logo. ?></span>
					<span class="ci-header-logo-dark">
						<?php
						echo CI_Theme_Logo::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Logo.
							array(
								'height'  => 30,
								'dark'    => true,
								'dark_id' => $dark_logo,
							)
						);
						?>
					</span>
				</span>
			</a>

			<a href="<?php echo $cta_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by CI_Theme_Section::url(). ?>" data-bar-item class="ci-header-cta">
				<span class="ci-header-cta-full"><?php echo esc_html( $text( 'ctaLabel' ) ); ?></span>
				<span class="ci-header-cta-short"><?php echo esc_html( $text( 'ctaShort' ) ); ?></span>
			</a>
		</div>

		<?php /* Menu button: hamburger in the bar (phones) and "Menu" pill once the page scrolls. */ ?>
		<button type="button" class="ci-header-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $menu_id ); ?>" aria-label="<?php esc_attr_e( 'Open menu', 'ci-theme' ); ?>" data-label-open="<?php esc_attr_e( 'Open menu', 'ci-theme' ); ?>" data-label-close="<?php esc_attr_e( 'Close menu', 'ci-theme' ); ?>" data-menu-toggle>
			<span data-fill-navy class="ci-header-toggle-fill" aria-hidden="true"></span>
			<span data-fill-white class="ci-header-toggle-fill ci-header-toggle-fill-white" aria-hidden="true"></span>
			<span class="ci-header-toggle-icon" aria-hidden="true">
				<span class="ci-header-toggle-line"></span>
				<span class="ci-header-toggle-line"></span>
				<span class="ci-header-toggle-accent"></span>
			</span>
			<span class="ci-header-toggle-label" data-toggle-label aria-hidden="true">
				<span data-label-menu class="ci-header-toggle-word"><?php esc_html_e( 'Menu', 'ci-theme' ); ?></span>
				<span data-label-close class="ci-header-toggle-word ci-header-toggle-word-close"><?php esc_html_e( 'Close', 'ci-theme' ); ?></span>
			</span>
		</button>

		<?php if ( $has_sub ) : ?>
			<?php /* Services submenu (desktop). */ ?>
			<div id="<?php echo esc_attr( $sub_id ); ?>" class="ci-header-sub" data-sub hidden>
				<div class="ci-header-sub-inner">
					<?php
					foreach ( $services as $service ) {
						echo $service_link( $service, array( 'ci-header-sub-link', 'ci-header-sub-num', 'ci-header-sub-title' ), __( 'Coming soon', 'ci-theme' ), ' data-sub-item' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the closure.
					}
					?>
					<a href="<?php echo CI_Theme_Section::url( $text( 'subAllUrl' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by CI_Theme_Section::url(). ?>" class="ci-header-sub-link ci-header-sub-all" data-sub-item><?php echo esc_html( $text( 'subAllLabel' ) ); ?></a>
					<div class="ci-header-sub-aside" data-sub-aside>
						<span class="ci-header-sub-aside-arc" aria-hidden="true"></span>
						<span class="ci-header-sub-aside-dot" aria-hidden="true"></span>
						<span class="ci-header-sub-aside-title"><?php echo esc_html( $text( 'asideTitle' ) ); ?></span>
						<span class="ci-header-sub-aside-text"><?php echo esc_html( $text( 'asideText' ) ); ?></span>
						<a href="<?php echo CI_Theme_Section::url( $text( 'asideUrl' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by CI_Theme_Section::url(). ?>" class="ci-header-sub-aside-link"><?php echo esc_html( $text( 'asideLabel' ) ); ?></a>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php /* Full-screen menu (phones, and desktop once the bar has collapsed). */ ?>
	<div id="<?php echo esc_attr( $menu_id ); ?>" class="ci-header-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'ci-theme' ); ?>" tabindex="-1" data-menu hidden>
		<a href="<?php echo $home; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url above. ?>" class="ci-header-menu-logo" aria-label="<?php echo esc_attr( $home_label ); ?>" data-menu-rise>
			<?php
			echo CI_Theme_Logo::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Logo.
				array(
					'height'  => 34,
					'dark'    => true,
					'dark_id' => $dark_logo,
				)
			);
			?>
		</a>

		<svg class="ci-header-menu-art" viewBox="0 0 100 100" aria-hidden="true" focusable="false">
			<path data-menu-arc d="M 78.28 21.72 A 40 40 0 1 0 78.28 78.28" fill="none" stroke="<?php echo esc_attr( $palette['arc'] ); ?>" stroke-width="15"/>
			<circle data-menu-dot cx="50" cy="50" r="12" fill="<?php echo esc_attr( $palette['dot'] ); ?>"/>
			<rect data-menu-bar x="86" y="27" width="8" height="46" fill="<?php echo esc_attr( $palette['bar'] ); ?>"/>
		</svg>

		<div class="ci-header-menu-grid">
			<nav class="ci-header-menu-nav" aria-label="<?php esc_attr_e( 'Main', 'ci-theme' ); ?>">
				<?php
				foreach ( $items as $item ) :
					$url = CI_Theme_Section::url( isset( $item['url'] ) ? $item['url'] : '' );
					if ( '' === $url || empty( $item['label'] ) ) {
						continue;
					}
					$is_active = $active( $url );
					?>
					<div class="ci-header-menu-item">
						<a href="<?php echo $url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by CI_Theme_Section::url(). ?>" class="ci-header-menu-link<?php echo $is_active ? ' ci-header-menu-link-active' : ''; ?>"<?php echo $is_active ? ' aria-current="page"' : ''; ?>>
							<span class="ci-header-menu-mask"><span data-menu-line><?php echo esc_html( $item['label'] ); ?></span></span>
						</a>
						<?php if ( ! empty( $item['services'] ) && $services ) : ?>
							<?php /* Phones: the services appear under their menu item. */ ?>
							<div class="ci-header-menu-subs-inline">
								<?php
								foreach ( $services as $service ) {
									echo $service_link( $service, array( 'ci-header-menu-sub', 'ci-header-menu-sub-num', '' ), __( 'Soon', 'ci-theme' ), ' data-menu-sub' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the closure.
								}
								?>
							</div>
						<?php endif; ?>
						<span data-menu-rule class="ci-header-menu-rule" aria-hidden="true"></span>
					</div>
				<?php endforeach; ?>
			</nav>

			<div class="ci-header-menu-side">
				<?php if ( $services ) : ?>
					<?php /* Desktop: services in the right column. */ ?>
					<div class="ci-header-menu-subs-side" data-menu-rise>
						<p class="ci-header-menu-side-title"><?php echo esc_html( $text( 'menuServicesTitle' ) ); ?></p>
						<?php
						foreach ( $services as $service ) {
							echo $service_link( $service, array( 'ci-header-menu-sub', 'ci-header-menu-sub-num', '' ), __( 'Soon', 'ci-theme' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the closure.
						}
						?>
					</div>
				<?php endif; ?>

				<div class="ci-header-menu-actions" data-menu-rise>
					<a href="<?php echo $cta_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by CI_Theme_Section::url(). ?>" class="ci-header-menu-primary"><?php echo esc_html( $text( 'ctaLabel' ) ); ?></a>
					<?php if ( $site['whatsapp_url'] ) : ?>
						<a href="<?php echo esc_url( $site['whatsapp_url'] ); ?>" class="ci-header-menu-ghost" target="_blank" rel="noopener noreferrer">
							<span class="ci-header-whatsapp-dot" aria-hidden="true"></span><?php esc_html_e( 'WhatsApp', 'ci-theme' ); ?><?php echo CI_Theme_Section::new_tab_note(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
						</a>
					<?php endif; ?>
				</div>

				<div class="ci-header-menu-contact" data-menu-rise>
					<?php if ( $site['phone'] ) : ?>
						<a href="<?php echo esc_url( $site['phone_href'], array( 'tel' ) ); ?>"><?php echo esc_html( $site['phone'] ); ?></a>
					<?php endif; ?>
					<?php if ( $site['email'] ) : ?>
						<a href="<?php echo esc_url( 'mailto:' . $site['email'] ); ?>"><?php echo esc_html( $site['email'] ); ?></a>
					<?php endif; ?>
					<span class="ci-header-menu-meta"><?php echo esc_html( implode( ' · ', array_filter( array( $site['hours_short'], $site['location'] ) ) ) ); ?></span>
				</div>
			</div>
		</div>
	</div>
</header>
