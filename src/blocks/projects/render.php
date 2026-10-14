<?php
/**
 * Render: ci/projects — project cards (ci_project post type).
 *
 * - cards: a section with a header and up to "limit" cards. Sources: latest, area, service,
 *   hand-picked, the service being viewed (service template; the "View all" link goes to the
 *   projects filtered by that service) or related to the project being viewed.
 * - listing: every project with filters by area and service. On the URL filtered by service
 *   the list arrives filtered from the server.
 *
 * @var array $attributes Block attributes.
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

$text = static function ( $key ) use ( $attributes ) {
	return isset( $attributes[ $key ] ) ? trim( (string) $attributes[ $key ] ) : '';
};

/* ── Listing with filters ─────────────────────────────────────── */

if ( isset( $attributes['layout'] ) && 'listing' === $attributes['layout'] ) :
	CI_Theme_Blocks::needs_view( 'ci/projects' );
	$projects = CI_Theme_Content::projects();
	$services = CI_Theme_Content::services();
	$areas    = CI_Theme_Content::areas();
	$initial  = CI_Theme_Routes::service_item() ? CI_Theme_Routes::service() : '';
	$uid      = wp_unique_id( 'ci-projects-listing-' );
	$shown    = 0;
	$cards    = '';
	foreach ( $projects as $project ) {
		$hidden = '' !== $initial && $project['service_slug'] !== $initial;
		$shown += $hidden ? 0 : 1;
		$cards .= CI_Theme_Cards::project( $project, 'h2', $hidden );
	}
	/* translators: %d: number of projects shown. */
	$count = sprintf( _n( '%d project', '%d projects', $shown, 'ci-theme' ), $shown );
	?>
<div <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-projects-listing', 'section' => false, 'attrs' => array( 'data-ci-projects-listing' => '' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?>>
	<div class="ci-projects-listing-bar">
		<div class="ci-container ci-projects-listing-bar-inner">
			<div class="ci-chips" role="group" aria-label="<?php echo esc_attr( $text( 'filterLabel' ) ); ?>">
				<button type="button" class="ci-chip" aria-pressed="true" data-area=""><?php echo esc_html( $text( 'allAreasLabel' ) ); ?></button>
				<?php foreach ( $areas as $area ) : ?>
					<button type="button" class="ci-chip" aria-pressed="false" data-area="<?php echo esc_attr( $area['slug'] ); ?>"><?php echo esc_html( $area['name'] ); ?></button>
				<?php endforeach; ?>
			</div>
			<div class="ci-projects-listing-select-wrap">
				<label for="<?php echo esc_attr( $uid ); ?>-service" class="ci-projects-listing-select-label"><?php echo esc_html( $text( 'serviceLabel' ) ); ?></label>
				<select id="<?php echo esc_attr( $uid ); ?>-service" class="ci-projects-listing-select" data-ci-service>
					<option value=""><?php echo esc_html( $text( 'allServicesLabel' ) ); ?></option>
					<?php foreach ( $services as $service ) : ?>
						<option value="<?php echo esc_attr( $service['slug'] ); ?>"<?php selected( $service['slug'], $initial ); ?>><?php echo esc_html( $service['title'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>
	<section class="ci-projects-listing-results" aria-label="<?php esc_attr_e( 'Results', 'ci-theme' ); ?>">
		<div class="ci-container ci-projects-listing-stack">
			<p class="ci-projects-listing-count" aria-live="polite" data-ci-count data-one="<?php esc_attr_e( 'project', 'ci-theme' ); ?>" data-many="<?php esc_attr_e( 'projects', 'ci-theme' ); ?>"><?php echo esc_html( $count ); ?></p>
			<div class="ci-cards ci-projects-listing-grid"<?php echo $shown ? '' : ' hidden'; ?> data-ci-cards>
				<?php echo $cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cards. ?>
			</div>
			<div class="ci-empty"<?php echo $shown ? ' hidden' : ''; ?> data-ci-empty>
				<p class="ci-empty-title"><?php echo esc_html( $text( 'emptyTitle' ) ); ?></p>
				<?php if ( '' !== $text( 'emptyButton' ) ) : ?>
					<button type="button" class="ci-btn ci-btn--outline ci-btn--sm" data-ci-reset><?php echo esc_html( $text( 'emptyButton' ) ); ?></button>
				<?php endif; ?>
			</div>
		</div>
	</section>
</div>
	<?php
	return;
endif;

/* ── Tarjetas ─────────────────────────────────────────────────── */

$source = isset( $attributes['source'] ) ? $attributes['source'] : 'all';
$limit  = max( 0, (int) $attributes['limit'] );
$link   = $attributes;

if ( 'current-service' === $source ) {
	$post    = CI_Theme_Content::context_post( CI_Theme_Content::SERVICE );
	$service = $post ? CI_Theme_Content::service_item( $post ) : null;
	// Template preview (Site Editor): use a service that has projects as the example.
	$viewed = get_post();
	if ( ( ! $viewed || CI_Theme_Content::SERVICE !== $viewed->post_type ) && CI_Theme_Content::is_preview() ) {
		foreach ( CI_Theme_Content::projects() as $example ) {
			if ( '' !== $example['service_slug'] ) {
				$service = CI_Theme_Content::service_by_slug( $example['service_slug'] );
				break;
			}
		}
	}
	if ( ! $service || $service['soon'] ) {
		return;
	}
	$projects          = CI_Theme_Content::projects(
		array(
			'service' => $service['slug'],
			'limit'   => $limit,
		)
	);
	$link['linkUrl'] = CI_Theme_Routes::service_projects_url( $service['slug'] );
} elseif ( 'related' === $source ) {
	$post     = CI_Theme_Content::context_post( CI_Theme_Content::PROJECT );
	$projects = $post ? CI_Theme_Content::related_projects( $post->ID, $limit ) : array();
} else {
	$projects = CI_Theme_Content::projects(
		array(
			'limit'   => 'manual' === $source ? 0 : $limit,
			'area'    => 'area' === $source ? $attributes['area'] : '',
			'service' => 'service' === $source ? $attributes['service'] : '',
			'ids'     => 'manual' === $source ? $attributes['ids'] : array(),
		)
	);
}
if ( ! $projects ) {
	return;
}

$id      = wp_unique_id( 'ci-projects-title-' );
$divider = ! empty( $attributes['divider'] );
?>
<section <?php echo CI_Theme_Section::wrapper( $attributes, array( 'class' => 'ci-projects', 'classes' => $divider ? array( 'ci-projects--divider' ) : array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by get_block_wrapper_attributes(). ?><?php echo CI_Theme_Section::labelled( $attributes, $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>>
	<div class="ci-container ci-stack ci-stack--md">
		<div class="ci-projects-head">
			<?php echo CI_Theme_Section::intro( $link, array( 'id' => $id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the helper. ?>
		</div>
		<div data-anim-stagger class="ci-cards ci-cards--tall">
			<?php
			foreach ( $projects as $project ) {
				echo CI_Theme_Cards::project( $project, empty( $attributes['title'] ) ? 'h2' : 'h3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in CI_Theme_Cards.
			}
			?>
		</div>
	</div>
</section>
