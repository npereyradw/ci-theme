<?php
/**
 * Render: ci/contact — quote or question form and contact details.
 *
 * The form is sent to Contact Form 7 (see src/forms). Field names of the Contact Form 7 form:
 * type, name*, company, email*, phone, service, location, message*.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$site    = CI_Theme_Section::site();
$form_id = CI_Theme_Forms::form_id( 'contact', isset( $attributes['formId'] ) ? $attributes['formId'] : 0 );
$uid     = wp_unique_id( 'ci-contact-' );
$text    = static function ( $key ) use ( $attributes ) {
	return isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
};
$fields  = isset( $attributes['fields'] ) && is_array( $attributes['fields'] ) ? $attributes['fields'] : array();
$types   = array_values(
	array_filter(
		isset( $attributes['types'] ) && is_array( $attributes['types'] ) ? $attributes['types'] : array(),
		static function ( $type ) {
			return ! empty( $type['value'] ) && ! empty( $type['label'] );
		}
	)
);

/**
 * A field with its label and its error container.
 *
 * @param string $name     Field name in Contact Form 7.
 * @param string $control  Control markup (input, select or textarea) with a <CONTROL placeholder
 *                         where the id, name and required attributes go.
 * @param bool   $required Required field.
 * @param string $class    Extra class of the container.
 */
$field = static function ( $name, $control, $required = false, $class = '' ) use ( $fields, $uid ) {
	$id    = $uid . '-' . $name;
	$label = isset( $fields[ $name ]['label'] ) ? $fields[ $name ]['label'] : $name;
	return sprintf(
		'<div class="ci-contact-field %1$s"><label for="%2$s">%3$s%4$s</label>%5$s<span class="ci-contact-error" id="%2$s-error" data-error-for="%6$s" hidden></span></div>',
		esc_attr( $class ),
		esc_attr( $id ),
		esc_html( $label ),
		$required ? ' *' : '',
		str_replace( '<CONTROL', sprintf( ' id="%1$s" name="%2$s"%3$s', esc_attr( $id ), esc_attr( $name ), $required ? ' required' : '' ), $control ),
		esc_attr( $name )
	);
};
$placeholder = static function ( $name ) use ( $fields ) {
	return isset( $fields[ $name ]['placeholder'] ) ? esc_attr( $fields[ $name ]['placeholder'] ) : '';
};

$services = '<option value="">' . esc_html( isset( $fields['service']['placeholder'] ) ? $fields['service']['placeholder'] : '' ) . '</option>';
foreach ( CI_Theme_Content::services() as $service ) {
	$services .= sprintf( '<option value="%1$s" data-slug="%2$s">%1$s</option>', esc_attr( $service['title'] ), esc_attr( $service['slug'] ) );
}
if ( '' !== $text( 'unsureOption' ) ) {
	$services .= sprintf( '<option value="%1$s">%1$s</option>', esc_attr( $text( 'unsureOption' ) ) );
}

$details = array(
	array( $text( 'labelPhone' ), $site['phone'], $site['phone_href'] ),
	array( $text( 'labelEmail' ), $site['email'], $site['email'] ? 'mailto:' . $site['email'] : '' ),
	array( $text( 'labelHours' ), $site['hours'], '' ),
	array( $text( 'labelLocation' ), $site['location'], '' ),
);
$cta     = $types ? $types[0]['cta'] : __( 'Send', 'ci-theme' );
$privacy = CI_Theme_Section::privacy_url( $text( 'privacyUrl' ) );
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-contact' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<div class="ci-container ci-contact-grid">
		<div class="ci-contact-form-wrap" data-ci-form-wrap>
			<?php if ( ! $form_id ) : ?>
				<?php if ( current_user_can( 'edit_posts' ) ) : ?>
					<p class="ci-contact-form ci-contact-missing"><?php esc_html_e( 'Choose a Contact Form 7 form in the block settings (only editors see this message).', 'ci-theme' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<form class="ci-contact-form" <?php echo CI_Theme_Forms::data_attributes( $form_id, 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?> data-msg-required="<?php echo esc_attr( $text( 'msgRequired' ) ); ?>" data-msg-email="<?php echo esc_attr( $text( 'msgEmail' ) ); ?>" data-msg-error="<?php echo esc_attr( $text( 'msgError' ) ); ?>" novalidate>
					<?php if ( count( $types ) > 1 ) : ?>
						<fieldset class="ci-contact-types">
							<legend class="visually-hidden"><?php esc_html_e( 'Request type', 'ci-theme' ); ?></legend>
							<?php foreach ( $types as $i => $type ) : ?>
								<label class="ci-contact-type">
									<input type="radio" name="type-choice" value="<?php echo esc_attr( $type['value'] ); ?>" data-cta="<?php echo esc_attr( $type['cta'] ); ?>" class="ci-contact-type-input"<?php checked( 0, $i ); ?>>
									<span class="ci-contact-type-label">
										<span class="ci-contact-type-long"><?php echo esc_html( $type['label'] ); ?></span>
										<span class="ci-contact-type-short" aria-hidden="true"><?php echo esc_html( ! empty( $type['short'] ) ? $type['short'] : $type['label'] ); ?></span>
									</span>
								</label>
							<?php endforeach; ?>
						</fieldset>
					<?php elseif ( $types ) : ?>
						<input type="hidden" name="type" value="<?php echo esc_attr( $types[0]['value'] ); ?>">
					<?php endif; ?>

					<div class="ci-contact-form-grid">
						<?php
						// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- the closures escape every value.
						echo $field( 'name', '<input<CONTROL autocomplete="name" placeholder="' . $placeholder( 'name' ) . '" class="ci-contact-input">', true );
						echo $field( 'company', '<input<CONTROL autocomplete="organization" placeholder="' . $placeholder( 'company' ) . '" class="ci-contact-input">' );
						echo $field( 'email', '<input<CONTROL type="email" autocomplete="email" placeholder="' . $placeholder( 'email' ) . '" class="ci-contact-input">', true );
						echo $field( 'phone', '<input<CONTROL type="tel" autocomplete="tel" placeholder="' . $placeholder( 'phone' ) . '" class="ci-contact-input">' );
						echo $field( 'service', '<select<CONTROL class="ci-contact-input">' . $services . '</select>' );
						echo $field( 'location', '<input<CONTROL placeholder="' . $placeholder( 'location' ) . '" class="ci-contact-input">' );
						?>
					</div>
					<?php
					echo $field( 'message', '<textarea<CONTROL rows="5" maxlength="5000" placeholder="' . $placeholder( 'message' ) . '" class="ci-contact-input ci-contact-textarea"></textarea>', true );
					// phpcs:enable
					?>

					<?php /* Honeypot: hidden from people and screen readers. */ ?>
					<div class="ci-contact-trap" aria-hidden="true">
						<label for="<?php echo esc_attr( $uid ); ?>-website"><?php esc_html_e( 'Leave empty', 'ci-theme' ); ?></label>
						<input id="<?php echo esc_attr( $uid ); ?>-website" name="website" tabindex="-1" autocomplete="off">
					</div>

					<p class="ci-contact-status" role="alert" data-ci-status></p>

					<div class="ci-contact-foot">
						<span class="ci-contact-note">
							<?php echo esc_html( $text( 'requiredNote' ) ); ?><br>
							<?php echo esc_html( $text( 'privacyText' ) ); ?>
							<?php if ( $privacy ) : ?>
								<a href="<?php echo $privacy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>"><?php echo esc_html( $text( 'privacyLabel' ) ); ?></a>.
							<?php endif; ?>
						</span>
						<button type="submit" class="ci-btn ci-btn--primary" data-sending="<?php echo esc_attr( $text( 'sendingLabel' ) ); ?>"><?php echo esc_html( $cta ); ?></button>
					</div>
				</form>
				<template data-ci-success>
					<div class="ci-contact-form">
						<div class="ci-contact-success" role="status">
							<span class="ci-contact-success-dot" aria-hidden="true"></span>
							<h2 tabindex="-1" class="ci-contact-success-title"><?php echo esc_html( $text( 'successTitle' ) ); ?></h2>
							<p class="ci-contact-success-text">
								<?php echo esc_html( $text( 'successText' ) ); ?>
								<?php if ( $site['whatsapp_url'] && '' !== $text( 'successWhatsapp' ) ) : ?>
									<a href="<?php echo esc_url( $site['whatsapp_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $text( 'successWhatsapp' ) ); ?></a>
								<?php endif; ?>
							</p>
							<button type="button" class="ci-btn ci-btn--outline ci-btn--sm" data-ci-again><?php echo esc_html( $text( 'againLabel' ) ); ?></button>
						</div>
					</div>
				</template>
			<?php endif; ?>
		</div>

		<aside class="ci-contact-aside" aria-label="<?php esc_attr_e( 'Other ways to reach us', 'ci-theme' ); ?>">
			<?php if ( ! empty( $attributes['showWhatsapp'] ) && $site['whatsapp_url'] ) : ?>
				<a href="<?php echo esc_url( $site['whatsapp_url'] ); ?>" class="ci-contact-whatsapp" target="_blank" rel="noopener noreferrer">
					<span class="ci-contact-whatsapp-text">
						<span class="ci-contact-whatsapp-title"><span class="ci-contact-whatsapp-dot" aria-hidden="true"></span><?php echo esc_html( $text( 'whatsappTitle' ) ); ?></span>
						<span class="ci-contact-whatsapp-sub"><?php echo esc_html( $text( 'whatsappText' ) ); ?></span>
					</span>
					<span class="ci-contact-whatsapp-arrow" aria-hidden="true">→</span>
					<?php echo CI_Theme_Section::new_tab_note(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
				</a>
			<?php endif; ?>
			<div class="ci-contact-details">
				<dl class="ci-contact-details-list">
					<?php foreach ( $details as $detail ) : ?>
						<?php
						if ( '' === trim( (string) $detail[1] ) ) {
							continue;
						}
						?>
						<div class="ci-contact-detail">
							<dt class="ci-contact-detail-key"><?php echo esc_html( $detail[0] ); ?></dt>
							<dd class="ci-contact-detail-value">
								<?php if ( $detail[2] ) : ?>
									<a href="<?php echo esc_url( $detail[2], array( 'tel', 'mailto' ) ); ?>"><?php echo esc_html( $detail[1] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $detail[1] ); ?>
								<?php endif; ?>
							</dd>
						</div>
					<?php endforeach; ?>
				</dl>
				<?php if ( ! empty( $attributes['showSocials'] ) && $site['social'] ) : ?>
					<div class="ci-contact-socials">
						<?php foreach ( $site['social'] as $profile ) : ?>
							<a href="<?php echo esc_url( $profile['url'] ); ?>" class="ci-contact-social" target="_blank" rel="noopener noreferrer"><?php echo esc_html( isset( $profile['label'] ) ? $profile['label'] : $profile['url'] ); ?><?php echo CI_Theme_Section::new_tab_note(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</aside>
	</div>
</section>
