<?php
/**
 * Share menu.
 *
 * Override by copying to {theme}/bp-activity-social-share/share-menu.php.
 * Receives $view only; do not query the database here.
 *
 * @package BPAS
 * @var array $view {
 *     @type BPAS\Share_Context $ctx          What is shared.
 *     @type string             $url          URL Copy link and the device share sheet use (filtered, may be a short link).
 *     @type string             $label        Button label.
 *     @type string             $position     'before' | 'after'.
 *     @type string             $colors       'brand' | 'theme'.
 *     @type string             $class        Extra classes.
 *     @type string             $title        Optional accessible name and tooltip of the toggle (icon-only header buttons).
 *     @type array              $items        Networks: slug, label, type (link|mailto|copy), href, icon, mobile.
 *     @type string             $extra        Extension rows (already escaped HTML).
 *     @type string             $toggle_extra Extension markup inside the toggle (already escaped HTML).
 *     @type bool               $native       Offer the device share sheet.
 * }
 */

defined( 'ABSPATH' ) || exit;

$bpas_ctx     = $view['ctx'];
$bpas_name    = empty( $view['title'] ) ? '' : sprintf( ' title="%1$s" aria-label="%1$s"', esc_attr( $view['title'] ) );
$bpas_classes = trim( sprintf( 'bpas-share bpas-share--%1$s bpas-share--%2$s %3$s', $view['position'], $view['colors'], $view['class'] ) );
?>
<div class="generic-button bpas-share-wrap bpas-share-wrap--<?php echo esc_attr( $view['position'] ); ?>">
	<details class="<?php echo esc_attr( $bpas_classes ); ?>" data-bpas-type="<?php echo esc_attr( $bpas_ctx->type ); ?>" data-bpas-id="<?php echo esc_attr( (string) $bpas_ctx->id ); ?>" data-bpas-url="<?php echo esc_url( $view['url'] ?? $bpas_ctx->url ); ?>" data-bpas-title="<?php echo esc_attr( $bpas_ctx->title ); ?>">
		<summary class="bpas-share__toggle"<?php echo $bpas_name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>>
			<?php echo BPAS\Icons::get( 'share-2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG. ?>
			<span class="bpas-share__label"><?php echo esc_html( $view['label'] ); ?></span>
			<?php echo $view['toggle_extra']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the extension. ?>
		</summary>

		<?php // No <div> inside the wrapper: themes that split header buttons on the first </div> (Reign) would cut the menu. ?>
		<section class="bpas-share__panel" role="group" aria-label="<?php esc_attr_e( 'Share options', 'bp-activity-social-share' ); ?>">
			<span class="bpas-share__sheet-head">
				<span class="bpas-share__sheet-title"><?php echo esc_html( $view['label'] ); ?></span>
				<button type="button" class="bpas-share__close" data-bpas-close>
					<?php echo BPAS\Icons::get( 'x' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG. ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Close', 'bp-activity-social-share' ); ?></span>
				</button>
			</span>

			<?php echo $view['extra']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the extension. ?>

			<?php if ( $view['items'] || $view['native'] ) : ?>
				<ul class="bpas-share__list">
					<?php foreach ( $view['items'] as $bpas_item ) : ?>
						<li class="bpas-share__item<?php echo $bpas_item['mobile'] ? '' : ' bpas-share__item--desktop'; ?>">
							<?php if ( 'copy' === $bpas_item['type'] ) : ?>
								<button type="button" class="bpas-share__link bpas-share__link--copy" data-bpas-copy data-bpas-network="copy">
									<?php echo $bpas_item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitised SVG. ?>
									<span class="bpas-share__name"><?php echo esc_html( $bpas_item['label'] ); ?></span>
								</button>
							<?php else : ?>
								<a class="bpas-share__link bpas-share__link--<?php echo esc_attr( $bpas_item['slug'] ); ?>"
									style="--bpas-c: var(--bpas-brand-<?php echo esc_attr( sanitize_html_class( $bpas_item['slug'] ) ); ?>, currentColor)"
									href="<?php echo esc_url( $bpas_item['href'], array( 'https', 'mailto' ) ); ?>"
									<?php echo 'mailto' === $bpas_item['type'] ? '' : 'target="_blank" rel="noopener nofollow"'; ?>
									data-bpas-network="<?php echo esc_attr( $bpas_item['slug'] ); ?>"
									<?php echo 'link' === $bpas_item['type'] ? 'data-bpas-popup' : ''; ?>
									aria-label="<?php echo esc_attr( 'mailto' === $bpas_item['type'] ? __( 'Share by email', 'bp-activity-social-share' ) : sprintf( /* translators: %s: network name, e.g. Facebook. */ __( 'Share on %s (opens in a new window)', 'bp-activity-social-share' ), $bpas_item['label'] ) ); ?>">
									<?php echo $bpas_item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitised SVG. ?>
									<span class="bpas-share__name" aria-hidden="true"><?php echo esc_html( $bpas_item['label'] ); ?></span>
								</a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>

					<?php if ( $view['native'] ) : ?>
						<li class="bpas-share__item bpas-share__item--native">
							<button type="button" class="bpas-share__link bpas-share__link--native" data-bpas-native data-bpas-network="native">
								<?php echo BPAS\Icons::get( 'share' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG. ?>
								<span class="bpas-share__name"><?php esc_html_e( 'More options', 'bp-activity-social-share' ); ?></span>
							</button>
						</li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

			<span class="screen-reader-text" role="status" aria-live="polite" data-bpas-status></span>
		</section>
	</details>
</div>
