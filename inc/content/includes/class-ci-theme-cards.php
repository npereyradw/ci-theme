<?php
/**
 * Cards shared by several sections: service, project, news and testimonial. Each method
 * enqueues "ci-theme-cards" (and the cover stylesheet when it draws a cover), so a page only
 * loads the styles of the cards it shows.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Cards' ) ) {

	/**
	 * Card markup.
	 */
	class CI_Theme_Cards {

		/**
		 * Heading tag: h2 in a listing page, h3 inside a section.
		 *
		 * @param string $tag Requested tag.
		 * @return string
		 */
		private static function tag( $tag ) {
			return in_array( $tag, array( 'h2', 'h3' ), true ) ? $tag : 'h3';
		}

		/**
		 * Service card.
		 *
		 * @param array  $s      Service (CI_Theme_Content::services()).
		 * @param string $tag    Title tag.
		 * @param bool   $hidden Rendered hidden (filtered out of a listing).
		 * @return string
		 */
		public static function service( $s, $tag = 'h3', $hidden = false ) {
			wp_enqueue_style( 'ci-theme-cards' );
			$tag  = self::tag( $tag );
			$meta = trim( $s['n'] . ( $s['area'] ? ' — ' . $s['area'] : '' ) );
			return sprintf(
				'<a href="%1$s" class="ci-service-card%2$s" data-area="%10$s"%11$s><div class="ci-service-card-head"><span class="ci-service-card-meta">%3$s</span>%4$s</div><%5$s class="ci-service-card-title">%6$s</%5$s>%7$s<span class="ci-service-card-foot">%8$s<span%9$s aria-hidden="true">→</span></span></a>',
				esc_url( $s['url'] ),
				$s['soon'] ? ' ci-service-card-soon' : '',
				esc_html( $meta ),
				$s['soon'] ? '<span class="ci-service-card-badge">' . esc_html__( 'Coming soon', 'ci-theme' ) . '</span>' : '',
				$tag,
				esc_html( $s['title'] ),
				'' !== $s['excerpt'] ? '<p class="ci-service-card-text">' . esc_html( $s['excerpt'] ) . '</p>' : '',
				$s['soon'] ? esc_html__( 'Notify me when available', 'ci-theme' ) : esc_html__( 'View service', 'ci-theme' ),
				$s['soon'] ? '' : ' class="ci-service-card-arrow"',
				esc_attr( $s['area_slug'] ),
				$hidden ? ' hidden' : ''
			);
		}

		/**
		 * Project card.
		 *
		 * @param array  $p      Project (CI_Theme_Content::projects()).
		 * @param string $tag    Title tag.
		 * @param bool   $hidden Hidden by the listing filter.
		 * @return string
		 */
		public static function project( $p, $tag = 'h3', $hidden = false ) {
			wp_enqueue_style( 'ci-theme-cards' );
			wp_enqueue_style( 'ci-theme-cover' );
			$tag   = self::tag( $tag );
			$tags  = $p['area'] ? '<span class="ci-project-card-area">' . esc_html( $p['area'] ) . '</span>' : '';
			$tags .= $p['service'] ? '<span class="ci-project-card-service">' . esc_html( $p['service'] ) . '</span>' : '';
			$info  = $p['client'] ? '<span>' . esc_html( $p['client'] ) . '</span>' : '';
			$info .= $p['location'] ? '<span class="ci-project-card-location">' . esc_html( $p['location'] ) . '</span>' : '';

			return sprintf(
				'<a href="%1$s" class="ci-project-card" data-area="%7$s" data-service="%8$s"%9$s><div class="ci-project-card-media">%2$s</div>%3$s<%4$s class="ci-project-card-title">%5$s</%4$s>%6$s</a>',
				esc_url( $p['url'] ),
				CI_Theme_Cover::render(
					array(
						'seed'  => $p['cover'],
						'ratio' => '4 / 3',
						'image' => $p['image'],
					)
				),
				$tags ? '<div class="ci-project-card-tags">' . $tags . '</div>' : '',
				$tag,
				esc_html( $p['title'] ),
				$info ? '<div class="ci-project-card-info">' . $info . '</div>' : '',
				esc_attr( $p['area_slug'] ),
				esc_attr( $p['service_slug'] ),
				$hidden ? ' hidden' : ''
			);
		}

		/**
		 * News card.
		 *
		 * @param array  $n   News item (CI_Theme_Content::news()).
		 * @param string $tag Title tag.
		 * @return string
		 */
		public static function news( $n, $tag = 'h3' ) {
			wp_enqueue_style( 'ci-theme-cards' );
			wp_enqueue_style( 'ci-theme-cover' );
			$tag = self::tag( $tag );
			return sprintf(
				'<a href="%1$s" class="ci-news-card">%2$s<div class="ci-news-card-meta"><span class="ci-news-card-category">%3$s</span><time datetime="%4$s">%5$s</time><span>%6$s</span></div><%7$s class="ci-news-card-title">%8$s</%7$s>%9$s<span class="ci-news-card-more">%10$s <span class="ci-news-card-arrow" aria-hidden="true">→</span></span></a>',
				esc_url( $n['url'] ),
				CI_Theme_Cover::render(
					array(
						'seed'  => $n['cover'],
						'ratio' => '16 / 10',
						'image' => $n['image'],
					)
				),
				esc_html( $n['category'] ),
				esc_attr( $n['date_iso'] ),
				esc_html( $n['date'] ),
				esc_html( $n['reading'] ),
				$tag,
				esc_html( $n['title'] ),
				'' !== $n['excerpt'] ? '<p class="ci-news-card-text">' . esc_html( $n['excerpt'] ) . '</p>' : '',
				esc_html__( 'Read more', 'ci-theme' )
			);
		}

		/**
		 * Testimonial card.
		 *
		 * @param array $t Testimonial (CI_Theme_Content::testimonials()).
		 * @return string
		 */
		public static function testimonial( $t ) {
			wp_enqueue_style( 'ci-theme-cards' );
			return sprintf(
				'<figure class="ci-testimonial-card"><div class="ci-testimonial-card-head"><span class="ci-testimonial-card-quote" aria-hidden="true">“</span></div><blockquote class="ci-testimonial-card-text">%1$s</blockquote><figcaption class="ci-testimonial-card-author"><span class="ci-testimonial-card-avatar" aria-hidden="true"></span><span class="ci-testimonial-card-who"><span class="ci-testimonial-card-name">%2$s</span>%3$s</span></figcaption></figure>',
				esc_html( $t['text'] ),
				esc_html( $t['name'] ),
				'' !== $t['role'] ? '<span class="ci-testimonial-card-role">' . esc_html( $t['role'] ) . '</span>' : ''
			);
		}
	}
}
