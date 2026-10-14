<?php
/**
 * Render: ci/project-header — header of a single project: breadcrumbs, area, title, fact sheet
 * and cover.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$post = CI_Theme_Content::context_post( CI_Theme_Content::PROJECT );
if ( ! $post ) {
	return;
}
$p = CI_Theme_Content::project_item( $post );

$meta = array();
foreach (
	array(
		'clientLabel'   => $p['client'],
		'locationLabel' => $p['location'],
		'serviceLabel'  => $p['service'],
		'yearLabel'     => $p['year'],
	) as $key => $value
) {
	if ( '' !== trim( (string) $value ) ) {
		$meta[] = array( isset( $attributes[ $key ] ) ? (string) $attributes[ $key ] : '', $value );
	}
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'ci-project-header' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<section>
		<div class="ci-container ci-project-header-head">
			<?php echo CI_Theme_Breadcrumbs::render( array( 'post' => $post ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Breadcrumbs. ?>
			<?php if ( '' !== $p['area'] ) : ?>
				<?php echo CI_Theme_Section::eyebrow( $p['area'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
			<?php endif; ?>
			<h1 class="ci-h1 ci-project-header-title" data-anim-css="mask" data-anim-step="1"><span><?php echo esc_html( $p['title'] ); ?></span></h1>
			<?php if ( $meta ) : ?>
				<dl class="ci-project-header-meta">
					<?php foreach ( $meta as $row ) : ?>
						<div class="ci-project-header-meta-item">
							<dt class="ci-project-header-meta-key"><?php echo esc_html( $row[0] ); ?></dt>
							<dd class="ci-project-header-meta-value"><?php echo esc_html( $row[1] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div>
	</section>
	<section>
		<div class="ci-container">
			<?php
			echo CI_Theme_Cover::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cover.
				array(
					'seed'     => $p['cover'],
					'ratio'    => '16 / 8',
					'image'    => $p['image'],
					'priority' => true,
					'sizes'    => '(max-width: 1279px) 100vw, 1240px',
				)
			);
			?>
		</div>
	</section>
</div>
