<?php
/*
Plugin Name: Light weight captcha for contact form7
Plugin URI: 
Description: Lightweight captcha for any form in wordpress
Author: Vaibhav Gangrade
Version: 1.0.0
Author URI: 
*/


if (!defined('ABSPATH')) exit; // Exit if accessed directly
$button_status = get_option('button_status');

        if($button_status == 1){
        	$button_status_disabled = true;
        	//die("true");
		}
		if($button_status == '0'){
			$button_status_hide='hide()';
			//die("hide");

		}

add_action('wpcf7_init', 'custom_add_shortcode_Captcha');

function custom_add_shortcode_Captcha()
{
    wpcf7_add_shortcode('lwfcform', 'custom_captcha_shortcode_handler'); // "helloworld" is the type of the form-tag
    
}

function custom_captcha_shortcode_handler($tag)
{	
		$characters_in_captcha = get_option('characters_in_captcha');
        $length_of_captcha = get_option('length_of_captcha');
        

        /*echo $characters_in_captcha;
        echo $length_of_captcha;
        echo $button_status;*/

        if(empty($characters_in_captcha)){
        	$characters_in_captcha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        }
        if(empty($length_of_captcha)){
        	$length_of_captcha = 6;

        }


    $characters = $characters_in_captcha;
    $captcha_string_length = $length_of_captcha;
    $captcha_code = '';
    $max = strlen($characters) - 1;
    for ($i = 0;$i < $captcha_string_length;$i++)
    {
        $captcha_code .= $characters[mt_rand(0, $max) ];
    }

    $generatedCaptcha = $captcha_code; //passing captcha code to new variable
	$captcha_form = '<form  method="POST">
	<input type="hidden" name="generated_captcha_code" id="generated_captcha_code" value="' . $generatedCaptcha . '">
	<div id="Cpatcha_block">' . $generatedCaptcha . '</div>
	<input type="text" name="insert_captcha_code" id="insert_captcha_code">
</form>';
?>

<?php
    return $captcha_form;
}


 //We'll key on the slug for the settings page so set it here so it can be used in various places
    define('MY_PLUGIN_SLUG', 'my-plugin-slug');

    //Register a callback for our specific plugin's actions
    add_filter('plugin_action_links_' . plugin_basename(__FILE__) , 'my_plugin_action_links');
    function my_plugin_action_links($links)
    {
        $links[] = '<a href="' . menu_page_url(MY_PLUGIN_SLUG, false) . '">Settings</a>';
        return $links;
    }

    //Create a normal admin menu
    add_action('admin_menu', 'register_settings');
    function register_settings()
    {
        add_options_page('Plugin Settings', 'Plugin Settings', 'manage_options', MY_PLUGIN_SLUG, 'my_plugin_settings_page');

     }

    //This is our plugins settings page
    function my_plugin_settings_page()
    { 
    	$characters_in_captcha = get_option('characters_in_captcha');
        $length_of_captcha = get_option('length_of_captcha');
        $button_status = get_option('button_status');

    	?>
    	<div class="captcha_section_form">
    		<h1>Form Settings:</h1>
    	<form method="POST">
    		<label>Captcha Characters:</label>
    		<input type="text" name="characters_in_captcha" id="characters_in_captcha" placeholder="Enter characters for captcha" class="lwcf_custom_calss" value="<?php echo esc_attr($characters_in_captcha); ?>">
    		<br>
    		<label>Captcha Length:</label>
    		<input type="number" name="length_of_captcha" id="length_of_captcha" class="lwcf_custom_calss" value="<?php echo esc_attr($length_of_captcha); ?>">
    		<label>Button Status:</label><br>
    		<input type="radio" id="button_hidden"  name ="button_status" value="0" class="lwcf_custom_calss" <?php if($button_status == 0){ echo "checked='checked'"; } ?>>Hidden Submit Button 
    		<input type="radio" id="disable_button" name ="button_status" value="1" class="lwcf_custom_calss" <?php if($button_status == 1){ echo "checked='checked'"; } ?>>Disable Submit Button <br>
    		<?php
    		if (!empty($characters_in_captcha) && !empty($length_of_captcha)){ ?>
    		<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce('update-settings') ?>">
				<input type="submit" name="update_setting" value="Update Setting" class="captcha_setting_btn">
				<?php }else { ?>
			<input type="submit" name="save_settings" value="Save Settings" class="captcha_setting_btn"> 
				<?php } ?>
    	</form>
    	</div>
    	
    <?php 
		//Inserting plugin setting data
        if (isset($_POST['save_settings']))
        {

            $characters_in_captcha = sanitize_text_field($_POST['characters_in_captcha']);
            $length_of_captcha = sanitize_text_field($_POST['length_of_captcha']);
            $button_status = sanitize_text_field($_POST['button_status']);
            if (!empty($characters_in_captcha) && !empty($length_of_captcha))
            {
                add_option('characters_in_captcha', $characters_in_captcha, '', 'yes');
                add_option('length_of_captcha', $length_of_captcha, '', 'yes');
                add_option('button_status', $button_status, '', 'yes');
                echo "Saved Successfully!!";
                
            }else{
            	echo "All fields are required";
            }

        }
        //Updating plugin setting data
        if (isset($_POST['update_setting']))
        {
            if (wp_verify_nonce($_POST['_nonce'], 'update-settings'))
            {
                $characters_in_captcha = sanitize_text_field($_POST['characters_in_captcha']);
            	$length_of_captcha = sanitize_text_field($_POST['length_of_captcha']);
            	$button_status = sanitize_text_field($_POST['button_status']);
                $form_background_color = sanitize_text_field($_POST['form_background_color']);
                 if (!empty($characters_in_captcha) && !empty($length_of_captcha))
                {
                    
                	update_option('characters_in_captcha', $characters_in_captcha, '', 'yes');
                	update_option('length_of_captcha', $length_of_captcha, '', 'yes');
                	update_option('button_status', $button_status, '', 'yes');
                    
                    //echo "Updated Successfully!!";
                    
                }
            }

        }

	}


?>
<style type="text/css">
	.lwcf_custom_calss{
		padding: 12px;
		display: block;

	}
	.captcha_setting_btn{
		padding: 12px;
		margin-top: 20px;
	}
	.captcha_section_form{
		width: 400px;
		margin: 0 auto;
		margin-top: 200px;
		border:1px solid black;
		padding: 10px;
		border-radius: 5px;
	}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		//var btnsts = <?php //if(empty($button_status_disabled)){ echo ''; }else{ echo $button_status_disabled; } ?>;
		//var hiddenbtnsts = <?php //echo $button_status_hide; ?>;
		//console.log(" bbb"+hiddenbtnsts);
		
		/*if(hiddenbtnsts){
			jQuery(".wpcf7-submit").hiddenbtnsts;
		}*/
		jQuery(".wpcf7-submit").prop("disabled",true);
		jQuery("#insert_captcha_code").keyup(function(){
			//Getting values of variable
			var user_inserted_Captcha = jQuery("#insert_captcha_code").val();
			var generated_captcha_code = jQuery("#generated_captcha_code").val();
			//console.log(generated_captcha_code);
			//checking user inserted captcha value and generated captcha value
			if(generated_captcha_code == user_inserted_Captcha){
				//values matches so color would be green
				jQuery("#insert_captcha_code").css("border-color","green");
				jQuery(".wpcf7-submit").prop("disabled",false);

			}else{
				//value not matched color would be red
				jQuery("#insert_captcha_code").css("border-color","red");
				jQuery(".wpcf7-submit").prop("disabled",true);
			}

		});
		
	});
</script>
