<form class='intercom-form' id='intercom-form' action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">

        <p><input type="text" name="fullname" id="fullname" required placeholder="Name"></p>

        <p><input type="email" name="email" id="email" required placeholder="Email"></p>

        <p><textarea name="message" id="message" required placeholder="Message"></textarea></p>

        <input type="hidden" name="action" value="intercom_contact_form">

	<?php wp_nonce_field('intercom_contact_form'); ?>

        <p><input type="submit" value="Submit" id='intercom-submit'>
	   <img alt="Sending ..." src="<?php echo plugin_dir_url( __FILE__ ); ?>styles/ajax-loader.gif" class="ajax-loader hidden">
	</p>
	
	<div class='message hidden'></div>
</form>

