<?php
defined( 'ABSPATH' ) or die( '' );

$intercom_key = get_option('intercom-key');

?>

<h2>Intercom Contact Form</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'intercom-contact-form' ); ?>
    <?php do_settings_sections( 'intercom-contact-form' ); ?>

	<table>
		<tr>
                	<td>
                    		<label for="intercom-key">Intercom key (format: 'appid:key')</label>
            	</td>
                        <td>
                                <input type="password" name="intercom-key" id="intercom-key" value="<?php echo $intercom_key; ?>" />
                        </td>
                </tr>
	</table>

    <?php submit_button(); ?>

</form>

