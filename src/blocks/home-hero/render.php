<?php
/**
 * Render: ci/home-hero — first screen of the home page.
 *
 * The illustration is the section background. The title is the LCP: it enters with CSS
 * (data-anim-css), without waiting for JavaScript.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$title = isset( $attributes['title'] ) ? trim( (string) $attributes['title'] ) : '';
$lead  = isset( $attributes['lead'] ) ? trim( (string) $attributes['lead'] ) : '';
$badge = isset( $attributes['badgeNumber'] ) ? trim( (string) $attributes['badgeNumber'] ) : '';
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-home-hero', 'section' => false, 'classes' => array( 'ci-under-header' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<?php if ( ! empty( $attributes['showArt'] ) ) : ?>
		<div class="ci-home-hero-art">
			<?php echo CI_Theme_Cover::render( array( 'seed' => 'hero', 'fill' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
		</div>
		<div class="ci-home-hero-shade" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="ci-container ci-home-hero-inner">
		<div class="ci-home-hero-text">
			<?php if ( ! empty( $attributes['eyebrow'] ) ) : ?>
				<p class="ci-eyebrow" data-anim-css="rise"><?php echo esc_html( $attributes['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<h1 class="ci-h1" data-anim-css="mask" data-anim-step="1"><span><?php echo esc_html( $title ); ?></span></h1>
			<?php endif; ?>
			<?php if ( '' !== $lead ) : ?>
				<p class="ci-lead" data-anim-css="rise" data-anim-step="2"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
			<?php
			echo CI_Theme_Section::buttons( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper.
				isset( $attributes['buttons'] ) ? $attributes['buttons'] : array(),
				'',
				array(
					'data-anim-css'  => 'rise',
					'data-anim-step' => '3',
				)
			);
			?>
		</div>
		<?php if ( '' !== $badge ) : ?>
			<p data-anim="rise" class="ci-home-hero-badge">
				<span data-anim="count" class="ci-home-hero-badge-num"><?php echo esc_html( $badge ); ?></span>
				<span class="ci-home-hero-badge-text"><?php echo CI_Theme_Section::lines( isset( $attributes['badgeText'] ) ? $attributes['badgeText'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?></span>
			</p>
		<?php endif; ?>
	</div>
</section>
