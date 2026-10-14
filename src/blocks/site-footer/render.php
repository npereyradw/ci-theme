<?php
/**
 * Render: ci/site-footer — site footer. On phones the site links and the services sit side by
 * side and the institution logos fit in one row (container queries in style.css and
 * institutions.css).
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$site = CI_Theme_Section::site();
$text = static function ( $key ) use ( $attributes ) {
	return isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
};

// Short label of each network inside its circle ("in", "f"…).
$social_short = array(
	'linkedin'  => 'in',
	'facebook'  => 'f',
	'instagram' => 'ig',
	'youtube'   => 'yt',
	'x'         => 'x',
);

$services = ! empty( $attributes['showServices'] ) ? CI_Theme_Content::services() : array();
$links    = isset( $attributes['siteLinks'] ) && is_array( $attributes['siteLinks'] ) ? $attributes['siteLinks'] : array();
$legal    = strtr(
	$text( 'legal' ),
	array(
		'{year}' => wp_date( 'Y' ),
		'{site}' => get_bloginfo( 'name' ),
	)
);
?>
<footer <?php echo get_block_wrapper_attributes( array( 'class' => 'ci-footer' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<span class="ci-footer-arc" aria-hidden="true"></span>
	<div class="ci-footer-inner">
		<div class="ci-footer-grid">
			<div class="ci-footer-brand">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ci-footer-logo" aria-label="<?php echo esc_attr( CI_Theme_Logo::home_label() ); ?>">
					<?php
					echo CI_Theme_Logo::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Logo.
						array(
							'height'  => 40,
							'dark'    => true,
							'dark_id' => isset( $attributes['logoDark'] ) ? (int) $attributes['logoDark'] : 0,
						)
					);
					?>
				</a>
				<?php if ( '' !== $text( 'about' ) ) : ?>
					<p class="ci-footer-about"><?php echo esc_html( $text( 'about' ) ); ?></p>
				<?php endif; ?>
				<?php if ( $site['social'] ) : ?>
					<div class="ci-footer-social">
						<?php
						foreach ( $site['social'] as $profile ) :
							$network = isset( $profile['network'] ) ? (string) $profile['network'] : '';
							$label   = isset( $profile['label'] ) ? (string) $profile['label'] : $network;
							?>
							<a href="<?php echo esc_url( $profile['url'] ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" class="ci-footer-social-link" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( isset( $social_short[ $network ] ) ? $social_short[ $network ] : mb_substr( $label, 0, 2 ) ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $links ) : ?>
				<nav class="ci-footer-col" aria-label="<?php echo esc_attr( $text( 'siteTitle' ) ); ?>">
					<span class="ci-footer-heading"><?php echo esc_html( $text( 'siteTitle' ) ); ?></span>
					<?php
					foreach ( $links as $link ) :
						$url = CI_Theme_Section::url( isset( $link['url'] ) ? $link['url'] : '' );
						if ( '' === $url || empty( $link['label'] ) ) {
							continue;
						}
						?>
						<a href="<?php echo $url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by CI_Theme_Section::url(). ?>" class="ci-footer-link"><?php echo esc_html( $link['label'] ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

			<?php if ( $services ) : ?>
				<nav class="ci-footer-col" aria-label="<?php echo esc_attr( $text( 'servicesTitle' ) ); ?>">
					<span class="ci-footer-heading"><?php echo esc_html( $text( 'servicesTitle' ) ); ?></span>
					<?php foreach ( $services as $service ) : ?>
						<a href="<?php echo esc_url( $service['url'] ); ?>" class="ci-footer-link"><?php echo esc_html( $service['title'] ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>

			<div class="ci-footer-col">
				<span class="ci-footer-heading"><?php echo esc_html( $text( 'contactTitle' ) ); ?></span>
				<?php if ( $site['phone'] ) : ?>
					<a href="<?php echo esc_url( $site['phone_href'], array( 'tel' ) ); ?>" class="ci-footer-link"><?php echo esc_html( $site['phone'] ); ?></a>
				<?php endif; ?>
				<?php if ( $site['email'] ) : ?>
					<a href="<?php echo esc_url( 'mailto:' . $site['email'] ); ?>" class="ci-footer-link ci-footer-email"><?php echo esc_html( $site['email'] ); ?></a>
				<?php endif; ?>
				<?php if ( $site['hours'] ) : ?>
					<span><?php echo esc_html( $site['hours'] ); ?></span>
				<?php endif; ?>
				<?php if ( $site['location'] ) : ?>
					<span><?php echo esc_html( $site['location'] ); ?></span>
				<?php endif; ?>
				<?php if ( $site['whatsapp_url'] ) : ?>
					<a href="<?php echo esc_url( $site['whatsapp_url'] ); ?>" class="ci-footer-whatsapp" target="_blank" rel="noopener noreferrer">
						<span class="ci-footer-whatsapp-dot" aria-hidden="true"></span><?php esc_html_e( 'WhatsApp', 'ci-theme' ); ?><?php echo CI_Theme_Section::new_tab_note(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<?php
		$institutions = ! empty( $attributes['showInstitutions'] ) ? CI_Theme_Institutions::render(
			array(
				'dark'    => true,
				'compact' => true,
			)
		) : '';
		if ( $institutions ) :
			?>
			<div class="ci-footer-inst">
				<span class="ci-footer-inst-label"><?php echo esc_html( $text( 'institutionsLabel' ) ); ?></span>
				<?php echo $institutions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Institutions. ?>
			</div>
		<?php endif; ?>

		<div class="ci-footer-legal">
			<span><?php echo esc_html( $legal ); ?></span>
			<?php
			$privacy = CI_Theme_Section::privacy_url( $text( 'privacyUrl' ) );
			if ( $privacy && '' !== $text( 'privacyLabel' ) ) :
				?>
				<a href="<?php echo $privacy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>" class="ci-footer-legal-link"><?php echo esc_html( $text( 'privacyLabel' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</footer>
