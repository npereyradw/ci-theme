<?php
/**
 * Render: ci/team — team member cards. Without a photo, the card shows a cover with the
 * person's initials.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$people = CI_Theme_Content::team();
if ( ! $people ) {
	return;
}

$id      = wp_unique_id( 'ci-team-title-' );
$caption = isset( $attributes['placeholderCaption'] ) ? trim( (string) $attributes['placeholderCaption'] ) : '';
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-team' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
	<div class="ci-container ci-team-grid">
		<div class="ci-stack">
			<?php echo CI_Theme_Section::eyebrow( isset( $attributes['eyebrow'] ) ? $attributes['eyebrow'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
			<?php if ( ! empty( $attributes['title'] ) ) : ?>
				<h2 id="<?php echo esc_attr( $id ); ?>" class="ci-h2"><?php echo esc_html( $attributes['title'] ); ?></h2>
			<?php endif; ?>
			<?php echo CI_Theme_Section::paragraphs( isset( $attributes['text'] ) ? $attributes['text'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		</div>
		<?php foreach ( $people as $person ) : ?>
			<article class="ci-team-card">
				<?php
				echo CI_Theme_Cover::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cover.
					array(
						'initials' => '' !== $person['initials'] ? $person['initials'] : '·',
						'ratio'    => '4 / 5',
						'image'    => $person['photo'],
						'caption'  => $person['photo'] ? '' : $caption,
						'sizes'    => '(max-width: 767px) 100vw, 400px',
					)
				);
				?>
				<div class="ci-team-card-body">
					<?php if ( '' !== $person['role'] ) : ?>
						<span class="ci-team-card-role"><?php echo esc_html( $person['role'] ); ?></span>
					<?php endif; ?>
					<h3 class="ci-team-card-name"><?php echo esc_html( $person['name'] ); ?></h3>
					<?php if ( '' !== $person['education'] ) : ?>
						<span class="ci-team-card-education"><?php echo esc_html( $person['education'] ); ?></span>
					<?php endif; ?>
				</div>
				<?php if ( '' !== $person['linkedin'] ) : ?>
					<a href="<?php echo esc_url( $person['linkedin'] ); ?>" class="ci-team-card-link" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'LinkedIn profile', 'ci-theme' ); ?> <?php echo CI_Theme_Section::external_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						<span class="visually-hidden">
							<?php
							/* translators: %s: person name. */
							echo esc_html( sprintf( __( 'of %s (opens in a new tab)', 'ci-theme' ), $person['name'] ) );
							?>
						</span>
					</a>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</section>
