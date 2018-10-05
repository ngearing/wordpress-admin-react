<?php
/**
 * This is a template for the class-admin-page.php class.
 *
 * @package wp
 */

namespace Ngearing\Wp;

wp_enqueue_script( 'test-admin-script' );

?>

<div id="app-root">
	Heres your app.
</div>

<div class="wrap" id="myplugin-admin">
	<div id="icon-tools" class="icon32"><br></div>
	<h2><?php echo $this->get_page_title(); ?></h2>
	<?php if ( ! empty( $_GET['updated'] ) ) : ?>
		<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
			<p><strong><?php _e( 'Settings saved.' ); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
	<?php endif; ?>

	<div class="js-output">
		<span class="indexed-images">0</span> of <span class="indexed-total">0</span> images indexed.
	</div>

	<form action="<?php echo $this->get_parent_slug() . '?page=' . $this->get_slug(); ?>" method="POST">
		<button class="index-media" type="submit" name='action' value='index-media'>Index Media Files</button>
		<script>
		(function() {
			var button = document.querySelector('button.index-media');

			var output = document.querySelector('.js-output');
			output.indexedImages = output.querySelector('.indexed-images');
			output.indexedTotal = output.querySelector('.indexed-total');

			button.addEventListener('click', function(ev) {
				ev.preventDefault();

				// Get total images.
				fetch(
					"<?php echo home_url( '/wp-json/wp/v2/media?per_page=1' ); ?>"
				).then( resp => {
					console.log(resp)
					if (resp.ok) {
						output.indexedTotal.innerText = resp.headers.get('x-wp-total');
					} else {
						output.indexedTotal.innerText = resp.statusText
					}
				})

				// Get indexed images.
				fetch(
					"<?php echo home_url( '/wp-json/wp/v2/media' ); ?>?filter[meta_key]=wp-smpro-smush-data&filter[meta_compare]=EXISTS"
				).then( resp => {
					if (resp.ok) {
						output.indexedImages.innerText = resp.headers.get('x-wp-total');
					} else {
						output.indexedImages.innerText = resp.statusText
					}
				})
			})
		})()
		</script>
	</form>

	<div class="dupe-images">
		<h2>Duplicate Images found:</h2>
		<?php \list_dupe_images(); ?>
	</div>

</div>
