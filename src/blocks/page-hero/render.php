<?php
/**
 * Render: ci/page-hero — header of the inner pages.
 * - dark: dark background with an arc and an accent dot.
 * - light: white background.
 * - text: white and more compact, for legal texts, with the last updated date.
 * The title is the H1 and the LCP: it enters with CSS (data-anim-css), without waiting for
 * JavaScript.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$dark  = isset( $attributes['variant'] ) && 'dark' === $attributes['variant'];
$text  = isset( $attributes['variant'] ) && 'text' === $attributes['variant'];
$title = isset( $attributes['title'] ) ? trim( (string) $attributes['title'] ) : '';
$lead  = isset( $attributes['lead'] ) ? trim( (string) $attributes['lead'] ) : '';

// In templates: the title and the modified date of the page being viewed.
$post = get_post();
if ( ! empty( $attributes['useTitle'] ) ) {
	$title = $post ? get_the_title( $post ) : __( 'Page title', 'ci-theme' );
}
$updated = '';
if ( ! empty( $attributes['showUpdated'] ) ) {
	$updated = trim(
		( isset( $attributes['updatedLabel'] ) ? (string) $attributes['updatedLabel'] : '' ) . ' ' .
		( $post ? get_the_modified_date( 'j \d\e F \d\e Y', $post ) : wp_date( 'j \d\e F \d\e Y' ) )
	);
}

$classes = array( 'ci-page-hero--' . ( $dark ? 'dark' : 'light' ) );
if ( $text ) {
	$classes[] = 'ci-page-hero--text';
}
if ( $dark ) {
	$classes[] = 'ci-section--dark';
	$classes[] = 'ci-under-header';
} elseif ( ! empty( $attributes['divider'] ) ) {
	$classes[] = 'ci-page-hero--divider';
}
$filter = ! $dark && ! empty( $attributes['areaFilter'] ) ? CI_Theme_Content::areas() : array();
if ( $filter ) {
	$classes[] = 'ci-page-hero--filter';
}
// The variant sets the scheme.
$attrs           = $attributes;
$attrs['scheme'] = 'light';
?>
<section <?php echo CI_Theme_Section::wrapper( $attrs, array( 'class' => 'ci-page-hero', 'section' => false, 'classes' => $classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<?php if ( $dark && ! empty( $attributes['decor'] ) ) : ?>
		<span class="ci-arc ci-page-hero-arc" aria-hidden="true"></span>
		<span data-anim="pop" class="ci-page-hero-dot" aria-hidden="true"></span>
	<?php endif; ?>
	<div class="ci-container ci-page-hero-inner">
		<?php
		if ( ! empty( $attributes['breadcrumbs'] ) ) {
			echo CI_Theme_Breadcrumbs::render( array( 'label' => isset( $attributes['breadcrumbLabel'] ) ? trim( (string) $attributes['breadcrumbLabel'] ) : '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Breadcrumbs.
		}
		?>
		<?php if ( '' !== $title ) : ?>
			<h1 class="ci-h1 ci-page-hero-title" data-anim-css="mask" data-anim-step="1"><span><?php echo esc_html( $title ); ?></span></h1>
		<?php endif; ?>
		<?php if ( '' !== $lead ) : ?>
			<p class="ci-lead" data-anim-css="rise" data-anim-step="2"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $updated ) : ?>
			<p class="ci-page-hero-updated" data-anim-css="rise" data-anim-step="2"><?php echo esc_html( $updated ); ?></p>
		<?php endif; ?>
		<?php
		if ( $filter ) :
			// Area filter: links to the filtered URLs; with JavaScript the Services block filters
			// without reloading.
			$current  = CI_Theme_Routes::area();
			$services = CI_Theme_Routes::services_page();
			?>
			<div class="ci-chips ci-page-hero-chips" role="group" aria-label="<?php esc_attr_e( 'Filter by area', 'ci-theme' ); ?>" data-ci-area-filter>
				<a href="<?php echo esc_url( $services ? get_permalink( $services ) : home_url( '/' ) ); ?>" class="ci-chip" data-area=""<?php echo '' === $current ? ' aria-current="page"' : ''; ?>><?php echo esc_html( isset( $attributes['allLabel'] ) ? $attributes['allLabel'] : '' ); ?></a>
				<?php foreach ( $filter as $area ) : ?>
					<a href="<?php echo esc_url( $area['url'] ); ?>" class="ci-chip" data-area="<?php echo esc_attr( $area['slug'] ); ?>"<?php echo $area['slug'] === $current ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $area['name'] ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
