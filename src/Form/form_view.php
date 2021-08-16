<?php
/**
 * Main template used to render settings pages and fields.
 *
 * @since 0.1.0
 */
?>

<div class="wrap settings-page__wrap">
	<h1 class="wp-heading-inline settings-page__title"><?php echo $data['title']; ?></h1>
	<div class="settings-page__notices"></div>
	<?php if ( '' !== $data['header'] ) : ?>
		<div class="settings-page__header">
			<?php echo $data['header']; ?>
		</div>
	<?php endif; ?>

	<div class="settings-page__fields">
		<form action="" method="post">
			<input type="hidden" name="page" value="<?php echo $data['page']; ?>">
			<input type="hidden" name="pc_settings_nonce" value="<?php echo $data['nonce']; ?>">
			<?php echo $data['fields']; ?>
			<input type="submit" value="Save">
		</form>
	</div>

	<?php if ( '' !== $data['footer'] ) : ?>
		<div class="settings-page__footer">
			<?php echo $data['footer']; ?>
		</div>
	<?php endif; ?>

</div>
