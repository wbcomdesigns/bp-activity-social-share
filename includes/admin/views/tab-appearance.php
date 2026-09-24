<?php
/**
 * Appearance tab with a live preview of the real menu.
 *
 * @package BPAS
 * @var array  $settings Settings.
 * @var string $preview  Rendered share menu.
 */

defined( 'ABSPATH' ) || exit;
?>
<form class="bpas-admin__form" data-bpas-form>
	<section class="bpas-card">
		<header class="bpas-card__head">
			<h2 class="bpas-card__title"><?php esc_html_e( 'Button', 'bp-activity-social-share' ); ?></h2>
		</header>
		<div class="bpas-fields">
			<div class="bpas-field">
				<label for="bpas-label"><strong><?php esc_html_e( 'Button label', 'bp-activity-social-share' ); ?></strong></label>
				<input id="bpas-label" class="regular-text" type="text" name="label" value="<?php echo esc_attr( $settings['label'] ); ?>" placeholder="<?php esc_attr_e( 'Share', 'bp-activity-social-share' ); ?>" />
				<p class="bpas-field__help"><?php esc_html_e( 'Leave empty to use "Share" in each visitor\'s language.', 'bp-activity-social-share' ); ?></p>
			</div>

			<fieldset class="bpas-field">
				<legend><strong><?php esc_html_e( 'Position on activity', 'bp-activity-social-share' ); ?></strong></legend>
				<label class="bpas-choice"><input type="radio" name="position" value="after" <?php checked( 'after', $settings['position'] ); ?> /> <?php esc_html_e( 'After the other buttons', 'bp-activity-social-share' ); ?></label>
				<label class="bpas-choice"><input type="radio" name="position" value="before" <?php checked( 'before', $settings['position'] ); ?> /> <?php esc_html_e( 'Before the other buttons', 'bp-activity-social-share' ); ?></label>
			</fieldset>

			<fieldset class="bpas-field">
				<legend><strong><?php esc_html_e( 'Icon colours', 'bp-activity-social-share' ); ?></strong></legend>
				<label class="bpas-choice"><input type="radio" name="colors" value="brand" <?php checked( 'brand', $settings['colors'] ); ?> /> <?php esc_html_e( 'Each network\'s own colour', 'bp-activity-social-share' ); ?></label>
				<label class="bpas-choice"><input type="radio" name="colors" value="theme" <?php checked( 'theme', $settings['colors'] ); ?> /> <?php esc_html_e( 'Match my theme', 'bp-activity-social-share' ); ?></label>
			</fieldset>
		</div>
	</section>

	<section class="bpas-card">
		<header class="bpas-card__head">
			<h2 class="bpas-card__title"><?php esc_html_e( 'Preview', 'bp-activity-social-share' ); ?></h2>
			<p class="bpas-card__desc"><?php esc_html_e( 'This is the real menu your members see. Open it to check the result.', 'bp-activity-social-share' ); ?></p>
		</header>
		<div class="bpas-preview activity-meta" data-bpas-preview>
			<?php echo $preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- template escapes. ?>
		</div>
	</section>

	<p class="bpas-admin__actions">
		<button type="submit" class="button button-primary"><?php esc_html_e( 'Save changes', 'bp-activity-social-share' ); ?></button>
	</p>
</form>
