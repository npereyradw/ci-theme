<?php
/**
 * Render: ci/services — service cards (ci_service post type).
 * - grid: a section header and cards.
 * - listing: the services page listing; it follows the area filter of the page header (the
 *   URLs filtered by area) and adds the inquiry card. Without a header of its own, the card
 *   titles are h2.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$services = CI_Theme_Content::services();
$source   = isset( $attributes['source'] ) ? $attributes['source'] : 'all';
$listing  = isset( $attributes['layout'] ) && 'listing' === $attributes['layout'];

if ( 'manual' === $source ) {
	$by_id    = array_column( $services, null, 'id' );
	$services = array();
	foreach ( (array) $attributes['ids'] as $post_id ) {
		if ( isset( $by_id[ (int) $post_id ] ) ) {
			$services[] = $by_id[ (int) $post_id ];
		}
	}
} elseif ( 'area' === $source && ! empty( $attributes['area'] ) ) {
	$services = array_values( wp_list_filter( $services, array( 'area_slug' => $attributes['area'] ) ) );
}
if ( ! empty( $attributes['hideSoon'] ) ) {
	$services = array_values( wp_list_filter( $services, array( 'soon' => false ) ) );
}
// On a single service, the service being viewed is not repeated ("other services").
$current = get_post();
if ( $current && CI_Theme_Content::SERVICE === $current->post_type ) {
	$services = array_values(
		array_filter(
			$services,
			static function ( $s ) use ( $current ) {
				return $s['id'] !== $current->ID;
			}
		)
	);
}
if ( 'manual' !== $source && ! empty( $attributes['limit'] ) ) {
	$services = array_slice( $services, 0, (int) $attributes['limit'] );
}
if ( ! $services ) {
	return;
}

$id    = wp_unique_id( 'ci-services-title-' );
$area  = $listing && class_exists( 'CI_Theme_Routes' ) ? CI_Theme_Routes::area() : '';
$shown = 0;
$cards = '';
foreach ( $services as $service ) {
	$hidden = '' !== $area && $service['area_slug'] !== $area;
	$shown += $hidden ? 0 : 1;
	$cards .= CI_Theme_Cards::service( $service, $listing || empty( $attributes['title'] ) ? 'h2' : 'h3', $hidden );
}

$inquiry = '';
if ( $listing && ! empty( $attributes['showInquiry'] ) ) {
	$inquiry = sprintf(
		'<a href="%1$s" class="ci-services-inquiry"><span class="ci-arc ci-services-inquiry-arc" aria-hidden="true"></span><span data-anim="pop" class="ci-services-inquiry-dot" aria-hidden="true"></span><span class="ci-services-inquiry-body"><span class="ci-services-inquiry-title">%2$s</span><span class="ci-services-inquiry-text">%3$s</span></span><span class="ci-services-inquiry-more">%4$s</span></a>',
		CI_Theme_Section::url( $attributes['inquiryUrl'] ),
		esc_html( $attributes['inquiryTitle'] ),
		esc_html( $attributes['inquiryText'] ),
		esc_html( $attributes['inquiryLabel'] )
	);
}
?>
<?php if ( $listing ) : ?>
	<?php CI_Theme_Blocks::needs_view( 'ci/services' ); ?>
	<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-services', 'classes' => array( 'ci-services--listing' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?> aria-label="<?php esc_attr_e( 'Services', 'ci-theme' ); ?>" data-ci-services-list>
		<div class="ci-container">
			<p class="visually-hidden" aria-live="polite" data-ci-count data-one="<?php esc_attr_e( 'service', 'ci-theme' ); ?>" data-many="<?php esc_attr_e( 'services', 'ci-theme' ); ?>">
				<?php
				/* translators: %d: number of services shown. */
				echo esc_html( sprintf( _n( '%d service', '%d services', $shown, 'ci-theme' ), $shown ) );
				?>
			</p>
			<div class="ci-cards">
				<?php echo $cards . $inquiry; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cards and above. ?>
			</div>
		</div>
	</section>
<?php else : ?>
	<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-services', 'classes' => empty( $attributes['divider'] ) ? array() : array( 'ci-services--divider' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
		<div class="ci-container ci-stack ci-stack--md">
			<?php echo CI_Theme_Section::intro( $attributes, array( 'id' => $id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
			<div data-anim-stagger class="ci-cards">
				<?php echo $cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cards. ?>
			</div>
		</div>
	</section>
<?php endif; ?>
