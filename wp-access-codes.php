<?php
/*
Plugin Name: WP Access Codes
Description: Allows registered users to be assigned a WordPress role or to be added to a Press Permit group based on which access code they entered.
Plugin URI: http://slickorange.co.za
Author: Slick Orange
Author URI: http://slickorange.co.za
Version:   0.9
License: GPL2

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

$plugin_url = WP_PLUGIN_URL . "/wp-access-codes.php";
$options = array();

global $table_name;
global $wpdb;

$table_name = $wpdb->prefix . 'access_codes';

/**
 * Class AccessCode
 */
class AccessCode{
    var $code;
    var $wp_role;
    var $pp_groups;
    var $email;
    var $remaining;
    var $message;

    /**
     * @param $code
     * @param $wp_role
     * @param $pp_groups
     * @param $email
     * @param $remaining
     */
    public function __construct($code, $wp_role, $pp_groups=null, $email=null, $remaining=null, $message="Access approved."){
        $this->code = $code;
        $this->wp_role = $wp_role;
        $this->pp_groups = $pp_groups;
        $this->remaining = $remaining;
        $this->message = $message;
    }
}


function wpac_plugin_options_install(){
    global $wpdb;
    global $table_name;

    if($wpdb->get_var("show tables like '$table_name''") != $table_name){
        $sql = "CREATE TABLE ". $table_name . " (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        access_code mediumtext NOT NULL,
        role longtext,
        groups longtext,
        remaining mediumint(9),
        email varchar(50),
        message varchar(100),
        UNIQUE KEY id (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}
register_activation_hook(__FILE__, 'wpac_plugin_options_install');

function wpac_pp_activated(){
    if(function_exists('pp_get_groups')){
        return true;
    }
    return false;
}

function wpac_menu(){
	add_options_page(
		'WP Access Codes', //Page Title
		'WP Access Codes', //Menu Title
		'manage_options', //Capability
		'wp-access-codes', //Slug
		'wpac_options_page' //Function
		);
}

add_action('admin_menu', 'wpac_menu');

function wpac_options_page(){
	if(!current_user_can('manage_options')){
		wp_die("You do not have sufficient permission to access this page.");
	}

	global $plugin_url;
	global $options;

		if(isset($_POST['wpac_form_submitted'])){
		$hidden_field = esc_html($_POST['wpac_form_submitted']);

		if($hidden_field == 'Y'){
			$wp_access_code = esc_html($_POST['wp_access_code']);


			
			if(isset($wp_access_code)){
                $limit = esc_html($_POST['wpac_limit']);
                $message = esc_html($_POST['wpac_message']);


                wpac_update_access_code($wp_access_code, wpac_get_selected_wp_role(), wpac_get_checked_pp_groups(), $limit,null, $message);
            }
		}

	}

    if($_GET['access_code'])
    {
        require("edit-page.php");
    }
    else
	require("options-page.php");
}

function wpac_edit_page(){
    require("edit-page.php");
}

function wpac_get_checked_pp_groups(){
    if(wpac_pp_activated()){
        if (isset($_POST['pp_check_group'])) {
            $optionArray = $_POST['pp_check_group'];
            return $optionArray;
        }
    }
    return null;
}

function wpac_get_selected_wp_role(){
    if (isset($_POST['wp_selected_role'])) {
        return $_POST['wp_selected_role'];
    }
    return null;
}

function wpac_update_access_code($wp_access_code, $wp_role, $pp_groups=null, $remaining=null, $email=null, $message=null){
     /** @var $wpdb wpdb */
    global $wpdb;
    global $table_name;

    if(wpac_get_access_code_object($wp_access_code) == null){
        $wpdb->insert($table_name, array(
            'access_code'=>$wp_access_code,
            'role' => $wp_role,
            'groups' => serialize($pp_groups),
            'remaining' => $remaining,
            'email' => $email,
            'message' => $message) );
    }
    else {
        $wpdb->update($table_name,array(
            'role' => $wp_role,
            'groups' => serialize($pp_groups),
            'remaining' => $remaining,
            'email' => $email,
            'message' => $message),

            array('access_code'=>$wp_access_code));
    }
}


/**
 * @param $wp_access_code_object AccessCode
 * @return string
 */
function wpac_decrease_remaining($wp_access_code_object){
    /** @var $wpdb wpdb */
    global $wpdb;
    global $table_name;

    if($wp_access_code_object == null){
            return "Access code does not exist";
    }
    elseif ($wp_access_code_object->remaining > 0) {
        $wpdb->update($table_name,array(
                'remaining' => $wp_access_code_object->remaining-1),
            array('access_code'=>$wp_access_code_object->code));
    }

}

function wpac_get_access_code_object($access_code){
    /** @var $wpdb wpdb */
    global $wpdb;
    global $table_name;

    $row = $wpdb->get_row("SELECT * FROM $table_name WHERE access_code = '$access_code'", ARRAY_A);
    if($row != null)
        return new AccessCode($row['access_code'], $row['role'], unserialize($row['groups']), $row['email'], $row['remaining'], $row['message']);
    else
        return;
}

function wpac_wp_role_dropdown($wp_role=null){
    global $wp_roles;

    echo "<select name='wp_selected_role'>";

        foreach($wp_roles->roles as $role_key=>$role){
            if($wp_role == $role_key)
                echo "<option selected='selected' value='".$role_key."'>".$role['name']."</option>";
            else echo "<option value='".$role_key."'>".$role['name']."</option>";
        }

        if($wp_role != 'none')
            echo "<option value='none'>No Change</option>";
        else echo "<option selected='selected' value='none'>No Change</option>";

    echo "</select>";
}

function wpac_pp_groups_checklist($selected_groups=null){
    if(wpac_pp_activated()){
    $groups_array = pp_get_groups();

    $checklist_html = "<div class='so-access-codes-checkbox-group'>";
    foreach ($groups_array as $group){
       if(empty($group->metagroup_id)){
        $checked="";
           
        if(!is_null($selected_groups)){
            if(in_array($group->ID, $selected_groups))
                 $checked='checked';
        }
        $checklist_html .="<input type='checkbox' name='pp_check_group[]' ".$checked." value='".$group->ID."'>".$group->name."</input>";
       }
    }

    $checklist_html.="</div>";
    return $checklist_html;
    }
    return null;
}

//Gets all the group:accesscode combinations
function wpac_get_active_codes()
{
    /**@var $wpdb wpdb**/
    global $wpdb;
    global $table_name;

    $access_codes=$wpdb->get_results(
        "SELECT access_code
         FROM $table_name", ARRAY_A);

    return $access_codes;
}

function wpac_active_codes_list(){
    $active_codes = wpac_get_active_codes();
    echo "<ul>";
        foreach($active_codes as $active_code){
            echo "<li><a href='options-general.php?page=wp-access-codes&access_code=".$active_code['access_code']."'>".$active_code['access_code']."</a></li>";
        }
    echo "</ul>";
}

function wpac_verify_access($wp_access_code){
    $access_code_object = wpac_get_access_code_object($wp_access_code);
    if(!empty($access_code_object)){

        if($access_code_object->remaining != 0){
            return wpac_update_user(get_current_user_id(), $access_code_object);
        }
        else {
            wpac_output_frontend("The access code has exceeded its usage limit.");
        }

    }
    else wpac_output_frontend("The access code is invalid.");
}

/**
 * @param $user_id
 * @param $access_code_object AccessCode
 */
function wpac_update_user($user_id, $access_code_object)
{
    if($access_code_object->wp_role != 'none' )
    {
        if(wp_update_user(array('ID'=> $user_id, 'role' => $access_code_object->wp_role)) == $user_id){
            $updated_user_data = get_userdata($user_id);
            if(in_array($access_code_object->wp_role, $updated_user_data->roles)){
                wpac_decrease_remaining($access_code_object);
            }
        }
    }


    if(wpac_pp_activated()){
        if(!empty($access_code_object->pp_groups)){
            $new_groups = $access_code_object->pp_groups;
            foreach($new_groups as $group){
                pp_add_group_user($group, $user_id);
            }
        }
    }

    return $access_code_object->message;
}

function wpac_shortcode($atts, $content = null){
	if ( is_user_logged_in() ){
		global $post;

		if(isset($_POST['wpac_frontend_form_submitted'])){
		$hidden_field = esc_html($_POST['wpac_frontend_form_submitted']);

		if($hidden_field == 'Y'){
			$wp_access_code = esc_html($_POST['wp_access_code_frontend']);

            echo wpac_verify_access($wp_access_code);
        }
	}
	else{

	extract(shortcode_atts(array(), $atts));

	ob_start();
	require('front-end.php');
	$content = ob_get_clean();
	}
	return $content;
	}
	}

add_shortcode('wp-access-codes', 'wpac_shortcode' );

function wpac_output_frontend($message=null){
    require('front-end.php');
}

?>