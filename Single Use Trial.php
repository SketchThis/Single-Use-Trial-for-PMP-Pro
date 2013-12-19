<?php
/*
Plugin Name: Trial Only Once
Plugin URI: http://www.sketchthis.net
Description: Allow users to have a trial only once
Version: .3
Author: Eric
Author URI: http://www.sketchthis.net
*/
/*
	Only allow users to use the trial level once.
*/
//record when users gain the trial level
function trial_membership_after_change_membership_level($level_id, $user_id)
{
	//set this to the id of your trial level
	$trial_level_id = 4;
 
	if($level_id == $trial_level_id)
	{	
		//add user meta to record the fact that this user has had this level before
		update_user_meta($user_id, "pmpro_trial_level_used", "1");
	}	
}
add_action("trial_membership_after_change_membership_level", "my_trial_membership_after_change_membership_level", 10, 2);
 
//check at checkout if the user has used the trial level already
function my_pmpro_registration_checks($value)
{
	global $current_user;
 
	//set this to the id of your trial level
	$trial_level_id = 4;
 
	if($current_user->ID && intval($_REQUEST['level']) == $trial_level_id)
	{
		//check if the current user has already used the trial level
		$already = get_user_meta($current_user->ID, "pmpro_trial_level_used", true);
 
		//yup, don't let them checkout
		if($already)
		{
			global $pmpro_msg, $pmpro_msgt;
			$pmpro_msg = "You have already used up your trial membership. Please select a full membership to checkout.";
			$pmpro_msgt = "pmpro_error";
 
			$value = false;
		}
	}
 
	return $value;
}
add_filter("pmpro_registration_checks", "my_pmpro_registration_checks");
 
//swap the expiration text if the user has used the trial
function my_pmpro_level_expiration_text($text, $level)
{
	global $current_user;
 
	//set this to the id of your trial level
	$trial_level_id = 4;
 
	if($current_user->ID && $level->id == $trial_level_id)
	{
		$text = "You have already used up your trial membership. Please select a full membership to checkout.";
	}
 
	return $text;
}
add_filter("pmpro_level_expiration_text", "my_pmpro_level_expiration_text", 10, 2);
