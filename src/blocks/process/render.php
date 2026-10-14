<?php
/**
 * Render: ci/process — numbered steps.
 * - timeline: a timeline with colored numbers, a line and an arrow.
 * - cover: a list next to an illustration. Without its own image it uses the cover of the
 *   service being viewed.
 * The numbers alternate white, yellow and blue.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$steps = array_values(
	array_filter(
		isset( $attributes['steps'] ) && is_array( $attributes['steps'] ) ? $attributes['steps'] : array(),
		static function ( $step ) {
			return ! empty( $step['title'] ) || ! empty( $step['text'] );
		}
	)
);
$id    = wp_unique_id( 'ci-process-title-' );
$cover = isset( $attributes['layout'] ) && 'cover' === $attributes['layout'];

if ( $cover ) :
	$seed = isset( $attributes['coverSeed'] ) ? (string) $attributes['coverSeed'] : '0';
	$post = get_post();
	if ( ! $post || CI_Theme_Content::SERVICE === $post->post_type ) {
		$service = CI_Theme_Content::context_post( CI_Theme_Content::SERVICE );
		if ( $service ) {
			$seed = (string) CI_Theme_Content::cover_seed( $service->ID );
		}
	}
	$eyebrow = isset( $attributes['eyebrow'] ) ? trim( (string) $attributes['eyebrow'] ) : '';
	?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-process ci-process--cover' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo '' !== $eyebrow ? ' aria-labelledby="' . esc_attr( $id ) . '"' : ''; ?>>
	<div class="ci-container ci-process-grid">
		<?php
		echo CI_Theme_Cover::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cover.
			array(
				'seed'  => $seed,
				'ratio' => '4 / 3',
				'image' => isset( $attributes['image'] ) ? (int) $attributes['image'] : 0,
				'sizes' => '(max-width: 1023px) 100vw, 560px',
			)
		);
		?>
		<div class="ci-process-body">
			<?php if ( '' !== $eyebrow ) : ?>
				<h2 id="<?php echo esc_attr( $id ); ?>" class="ci-eyebrow"><?php echo esc_html( $eyebrow ); ?></h2>
			<?php endif; ?>
			<?php if ( $steps ) : ?>
				<ol class="ci-process-rows">
					<?php foreach ( $steps as $i => $step ) : ?>
						<li class="ci-process-row">
							<span class="ci-process-num ci-process-num-<?php echo (int) ( $i % 3 ); ?>" aria-hidden="true"><?php echo esc_html( CI_Theme_Content::pad( $i + 1 ) ); ?></span>
							<div class="ci-process-row-body">
								<h3 class="ci-process-row-title"><?php echo esc_html( isset( $step['title'] ) ? $step['title'] : '' ); ?></h3>
								<?php if ( ! empty( $step['text'] ) ) : ?>
									<p class="ci-process-row-text"><?php echo esc_html( $step['text'] ); ?></p>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</div>
	</div>
</section>
	<?php
	return;
endif;
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-process' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
	<div class="ci-container ci-stack ci-stack--lg">
		<?php echo CI_Theme_Section::intro( $attributes, array( 'id' => $id, 'link' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		<?php if ( $steps ) : ?>
			<ol data-anim-stagger class="ci-process-list">
				<?php foreach ( $steps as $i => $step ) : ?>
					<li class="ci-process-step">
						<div class="ci-process-head" aria-hidden="true">
							<span class="ci-process-num ci-process-num-<?php echo (int) ( $i % 3 ); ?>"><?php echo esc_html( CI_Theme_Content::pad( $i + 1 ) ); ?></span>
							<span data-anim="line" class="ci-process-line"></span>
							<span data-anim="tip" class="ci-process-tip"></span>
						</div>
						<h3 class="ci-h3"><?php echo esc_html( isset( $step['title'] ) ? $step['title'] : '' ); ?></h3>
						<?php if ( ! empty( $step['text'] ) ) : ?>
							<p class="ci-process-text"><?php echo esc_html( $step['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
