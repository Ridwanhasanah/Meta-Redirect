<?php
/*
  Plugin Name: Redirecr Meta
  Plugin URI: https://www.facebook.com/ridwan.hasanah3
  Description: Plugin Redirect Meta
  Version: 1.0
  Author: Ridwan Hasanah
  Author URI: https://www.facebook.com/ridwan.hasanah3
*/

add_action('add_meta_boxes','rh_meta_box_redirect_add');

function rh_meta_box_redirect_add(){
	add_meta_box(
		'rh_custom_fields_redirect',
		'Redirect',
		'rh_custom_fields_redirect_form',
		'post',
		'normal',
		'high' );
}

function rh_custom_fields_redirect_form(){

	$data = get_post_custom(get_the_ID() );

	if (!is_null($data['rh-custom-fields-redirect'])) {
		extract(unserialize($data['rh-custom-fields-redirect'][0]));
	}

	wp_nonce_field('rh_custom_fields_nonce','rh_redirect_nonce' );

	?>
	<label for="url-target">Url Targer</label>
	<input type="text" name="url-target" id="url-target" value="<?php echo $url; ?>">
	<?php
}

add_action('save_post','rh_custom_fields_redirect_simpan' );

function rh_custom_fields_redirect_simpan(){

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post') ) return;
	if (!isset($_POST['rh_redirect_nonce']) || !wp_verify_nonce( $_POST['rh_redirect_nonce'], 'rh_custom_fields_nonce') ) return;
	$custom_fields['url'] = $_POST['url-target'];
	update_post_meta(get_the_ID(),'rh-custom-fields-redirect', $custom_fields );
}

add_action('wp_head','rh_custom_fields_do_redirect' );

function rh_custom_fields_do_redirect(){

	if (is_singular('post' )) {
		$data = get_post_custom(get_the_ID() );
		if (!empty($data['rh-custom-fields-redirect'][0])) {
			extract(unserialize($data['rh-custom-fields-redirect'][0]));
			if (!empty($url)) {
				echo '<meta http-equiv="refresh" content="0; url='.$url.'">';
			}
		}
	}
}

add_action('admin_menu', 'rh_meta_reirect_menu' );

function rh_meta_reirect_menu(){
	add_menu_page(
	'Meta Redirect',
	'Meta Redirect',
	'manage_options',
	__FILE__ ,
	'rh_meta_redirect_options',
	'dashicons-palmtree',
	6);	
}



function rh_meta_redirect_options(){

	echo '<h2>META Redirect</h2>';

	if ($_POST['rh-meta-redirect-submit']) {
		$options['rh-meta-redirect-referer'] = $_POST['rh-meta-redirect-referer'];
		update_option("rh-meta-redirect-option-fields" );
		echo'<div class="updated"><p><strong>Options Saved</strong></p></div>';
	}

	$options = get_option("rh-meta-redirect-option-fields" );
	?>
		<form method="post">
			<label for="rh-meta-redirect-referer">Referer:</label><br>
			<textarea cols="50" rows="5" id="rh-meta-redirect-referer" name="rh-meta-redirect-referer"><?php echo $options['rh-meta-redirect-referer']; ?></textarea><br>
			<input type="submit" name="rh-meta-redirect-submit" id="rh-meta-redirect-submit" class="button" value="Simpan">
		</form>
	<?php
}
?>