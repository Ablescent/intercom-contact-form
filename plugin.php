<?php
/**
 * Plugin Name: Intercom Contact Form
 * Description: Simple contact form with 3 fields: name, email and message, which sends message to intercom.
 * Version: 1.0.0
 */

defined( 'ABSPATH' ) or die( '' );

function my_enqueue_scripts() {

	wp_register_style( 'intercom-contact-form-css',  plugin_dir_url( __FILE__ ) . 'style.css' );
	wp_enqueue_style( 'intercom-contact-form-css' );

    	wp_enqueue_script( 'ajax-script', plugin_dir_url( __FILE__ ) . 'ajax-script.js', array('jquery') );
    	wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

function my_menu_pages(){
        if (is_admin()) {
                add_menu_page("Intercom Contact Form", "Intercom Contact Form", 'manage_options', 'intercom-contact-form', 'display_settings');
                register_setting('intercom-contact-form', 'intercom-key');
        }
}

function display_settings() {
        include_once( dirname(__FILE__) . '/settings.php' );
}

function CurlPOST($url, $data)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_POST, 1);

    $payload = json_encode( $data );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Accept: application/json'));

    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, get_option('intercom-key'));

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return $result;
}

function CurlGET($url, $data) {
    $ch = curl_init();

    $url = sprintf("%s?%s", $url, http_build_query($data));

    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, get_option('intercom-key'));

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return $result;
}

function intercom_user_exists($email) {
        $data = array("email" => $email);
        $res = CurlGET("https://api.intercom.io/users", $data);
        if (stristr($res, 'not found')===FALSE) {
		return TRUE;
	}
	return FALSE;
}

function intercom_message_create($email, $message) {
        $data = array("body" => $message,
                        "from" => array("type"=>"user", "email"=>$email)
        );
        $res = CurlPOST("https://api.intercom.io/messages", $data);
	return TRUE;
}

function intercom_user_create($email, $fullname) {
        $data = array("email" => $email, "name" => $fullname);
        $res = CurlPOST("https://api.intercom.io/users", $data);
        return TRUE;
}

function intercom_contact_form_callback() {
	global $wpdb;
	$fullname = $_POST['fullname'];
	$email = $_POST['email'];
	$message = $_POST['message'];
	if (!$fullname || !$email || !$message) {
	        echo -1;
		wp_die();
	}
	check_ajax_referer( 'intercom_contact_form', 'nonce' );
	
	if (!intercom_user_exists($email)) {
		intercom_user_create($email, $fullname);
	}

	intercom_message_create($email, $message);
	echo 0;
	wp_die();
}

function get_include($path) {
	ob_start();
	include_once $path;
	$res = ob_get_contents();
	ob_clean();

	return $res;
}

function intercom_contact_form($atts) {
	return get_include( dirname(__FILE__) . '/form.php' );
}

add_shortcode('intercom_contact_form', 'intercom_contact_form');
add_action( 'wp_enqueue_scripts', 'my_enqueue_scripts' );
add_action('admin_menu', 'my_menu_pages');
add_action( 'wp_ajax_intercom_contact_form', 'intercom_contact_form_callback' );
add_action( 'wp_ajax_nopriv_intercom_contact_form', 'intercom_contact_form_callback' );
