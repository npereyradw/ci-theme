<?php
/**
 * Render: ci/service-hero — header of a single service.
 *
 * Active: quote button (with the service preselected in the contact form) and WhatsApp.
 * Coming soon: a "notify me" form (Contact Form 7) and the "In preparation" box.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$post = CI_Theme_Content::context_post( CI_Theme_Content::SERVICE );
if ( ! $post ) {
	return;
}

$s    = CI_Theme_Content::service_item( $post );
$site = CI_Theme_Section::site();
$text = static function ( $key ) use ( $attributes ) {
	return isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
};
$meta  = trim( $s['n'] . ( $s['area'] ? ' — ' . $s['area'] : '' ) );
$description = CI_Theme_Content::content_html( $post );
$uid   = wp_unique_id( 'ci-service-hero-' );
$quote = add_query_arg( 'service', $s['slug'], CI_Theme_Section::url( $text( 'quoteUrl' ) ) );
$privacy = CI_Theme_Section::privacy_url( $text( 'notifyPrivacyUrl' ) );
$form  = $s['soon'] ? CI_Theme_Forms::form_id( 'notify', isset( $attributes['notifyFormId'] ) ? $attributes['notifyFormId'] : 0 ) : 0;
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'ci-service-hero' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<div class="ci-container ci-service-hero-inner">
		<?php echo CI_Theme_Breadcrumbs::render( array( 'post' => $post ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Breadcrumbs. ?>
		<div class="ci-service-hero-grid">
			<div class="ci-service-hero-text">
				<div class="ci-service-hero-meta">
					<?php echo CI_Theme_Section::eyebrow( $meta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
					<?php if ( $s['soon'] && '' !== $text( 'soonBadge' ) ) : ?>
						<span class="ci-service-hero-badge"><?php echo esc_html( $text( 'soonBadge' ) ); ?></span>
					<?php endif; ?>
				</div>
				<h1 class="ci-h1" data-anim-css="mask" data-anim-step="1"><span><?php echo esc_html( $s['title'] ); ?></span></h1>
				<?php if ( '' !== trim( $description ) ) : ?>
					<div data-anim="rise">
						<div class="ci-prose ci-service-hero-description">
							<?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- post content filtered by the_content. ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ! $s['soon'] ) : ?>
					<div class="ci-btn-row">
						<a href="<?php echo esc_url( $quote ); ?>" class="ci-btn ci-btn--primary"><?php echo esc_html( $text( 'quoteLabel' ) ); ?></a>
						<?php if ( $site['whatsapp_url'] && '' !== $text( 'whatsappLabel' ) ) : ?>
							<a href="<?php echo esc_url( $site['whatsapp_url'] ); ?>" class="ci-btn ci-btn--outline" target="_blank" rel="noopener noreferrer">
								<span class="ci-service-hero-whatsapp-dot" aria-hidden="true"></span><?php echo esc_html( $text( 'whatsappLabel' ) ); ?><?php echo CI_Theme_Section::new_tab_note(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
							</a>
						<?php endif; ?>
					</div>
				<?php elseif ( $form ) : ?>
					<div class="ci-notify" data-ci-form-wrap>
						<form class="ci-notify-form" <?php echo CI_Theme_Forms::data_attributes( $form, 'notify' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?> data-msg-required="<?php echo esc_attr( $text( 'msgRequired' ) ); ?>" data-msg-email="<?php echo esc_attr( $text( 'msgEmail' ) ); ?>" data-msg-error="<?php echo esc_attr( $text( 'msgError' ) ); ?>" novalidate>
							<label for="<?php echo esc_attr( $uid ); ?>-email" class="ci-notify-label"><?php echo esc_html( $text( 'notifyLabel' ) ); ?></label>
							<div class="ci-notify-row">
								<input id="<?php echo esc_attr( $uid ); ?>-email" name="email" type="email" required autocomplete="email" placeholder="<?php echo esc_attr( $text( 'notifyPlaceholder' ) ); ?>" class="ci-notify-input">
								<button type="submit" class="ci-btn ci-btn--dark" data-sending="<?php echo esc_attr( $text( 'notifySending' ) ); ?>"><?php echo esc_html( $text( 'notifyButton' ) ); ?></button>
							</div>
							<input type="hidden" name="service" value="<?php echo esc_attr( $s['title'] ); ?>">
							<?php /* Honeypot: hidden from people and screen readers. */ ?>
							<div class="ci-notify-trap" aria-hidden="true">
								<label for="<?php echo esc_attr( $uid ); ?>-website"><?php esc_html_e( 'Leave empty', 'ci-theme' ); ?></label>
								<input id="<?php echo esc_attr( $uid ); ?>-website" name="website" tabindex="-1" autocomplete="off">
							</div>
							<span class="ci-notify-error" id="<?php echo esc_attr( $uid ); ?>-email-error" data-error-for="email" hidden></span>
							<p class="ci-notify-error" role="alert" data-ci-status></p>
							<p class="ci-notify-note">
								<?php echo esc_html( $text( 'notifyPrivacyText' ) ); ?>
								<?php if ( $privacy ) : ?>
									<a href="<?php echo $privacy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>"><?php echo esc_html( $text( 'notifyPrivacyLabel' ) ); ?></a>.
								<?php endif; ?>
							</p>
							<?php if ( '' !== $text( 'notifyLinkLabel' ) ) : ?>
								<a href="<?php echo CI_Theme_Section::url( $text( 'notifyLinkUrl' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by CI_Theme_Section::url(). ?>" class="ci-notify-link"><?php echo esc_html( $text( 'notifyLinkLabel' ) ); ?></a>
							<?php endif; ?>
						</form>
						<template data-ci-success>
							<div class="ci-notify-form">
								<p class="ci-notify-label"><?php echo esc_html( $text( 'notifyLabel' ) ); ?></p>
								<p class="ci-notify-ok" role="status" tabindex="-1" data-ci-message></p>
								<?php if ( '' !== $text( 'notifyLinkLabel' ) ) : ?>
									<a href="<?php echo CI_Theme_Section::url( $text( 'notifyLinkUrl' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by CI_Theme_Section::url(). ?>" class="ci-notify-link"><?php echo esc_html( $text( 'notifyLinkLabel' ) ); ?></a>
								<?php endif; ?>
							</div>
						</template>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $s['items'] ) : ?>
				<aside class="ci-service-hero-includes" aria-labelledby="<?php echo esc_attr( $uid ); ?>-includes">
					<span class="ci-arc ci-service-hero-includes-arc" aria-hidden="true"></span>
					<h2 id="<?php echo esc_attr( $uid ); ?>-includes" class="ci-service-hero-includes-title"><?php echo esc_html( $s['soon'] ? $text( 'soonIncludesTitle' ) : $text( 'includesTitle' ) ); ?></h2>
					<ol class="ci-service-hero-includes-list">
						<?php foreach ( $s['items'] as $i => $item ) : ?>
							<li class="ci-service-hero-includes-item">
								<span class="ci-service-hero-includes-num"><?php echo esc_html( CI_Theme_Content::pad( $i + 1 ) ); ?></span>
								<span class="ci-service-hero-includes-text"><?php echo esc_html( $item ); ?></span>
							</li>
						<?php endforeach; ?>
					</ol>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</section>
