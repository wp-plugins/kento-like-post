<?php
/*
Plugin Name: Kento Like Post
Plugin URI: http://kentothemes.com
Description: Post Like Button For WordPress like Facebook
Version: 1.0
Author: KentoThemes
Author URI: http://kentothemes.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function kento_like_post_latest_jquery() {
	wp_enqueue_script('jquery');

	
}
add_action('init', 'kento_like_post_latest_jquery');




//Include Javascript library
wp_enqueue_script('kento_like_post_js', plugins_url( '/js/kento_like_post.js' , __FILE__ ) , array( 'jquery' ));
// including ajax script in the plugin Myajax.ajaxurl
wp_localize_script( 'kento_like_post_js', 'kento_like_post_ajax', array( 'kento_like_post_ajaxurl' => admin_url( 'admin-ajax.php')));

define('KENTO_LIKE_POST_PLUGIN_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );


wp_enqueue_style('kento-like-post-style', KENTO_LIKE_POST_PLUGIN_PATH.'css/style.css');





register_activation_hook(__FILE__, kento_like_post_install());
register_uninstall_hook(__FILE__, kento_like_post_drop());


function kento_like_post_install() {
    global $wpdb;
        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "kento_like_post_info"
                 ."( UNIQUE KEY id (id),
					id int(100) NOT NULL AUTO_INCREMENT,
					postid  int(10) NOT NULL,
					userid  int(10) NOT NULL)";
		$wpdb->query($sql);
		}

function kento_like_post_drop() {
	if ( get_option('kento_like_post_deletion') == 1 ) {
		
		global $wpdb;
		$table = $wpdb->prefix . "kento_like_post_info";
		$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'kento_like_post_info');
	}
}






function kento_like_post_is_user_logged()

	{
		if ( is_user_logged_in() )
		return "logged";
		else
		return "notlogged";

	}



function kento_like_post_voted($postid)
//is already vote
	{
		if ( is_user_logged_in())
			{
			global $wpdb;
			$userid = get_current_user_id();
			$table = $wpdb->prefix . "kento_like_post_info";
			$wpdb->get_results("SELECT * FROM $table WHERE userid = '$userid' AND postid = '$postid'", ARRAY_A);
			
			
			if($wpdb->num_rows > 0 )
				{
					return "voted";
				}
				
			else
				{
				return "notvoted";
				}
			}
		else {
			return "notvoted";
			}
	}



function kento_like_post_voted_status($postid)

	{
	if ( is_user_logged_in() ) {
		$postid = $postid;
		global $wpdb;
		$userid = get_current_user_id();
    	$table = $wpdb->prefix . "kento_like_post_info";
		
		$result = $wpdb->get_results("SELECT * FROM $table WHERE userid = '$userid' AND postid = '$postid' ", ARRAY_A);
		
		$is_voted = $wpdb->num_rows;
		
		if($is_voted > 0){
			return "voted";
			}
		else{
			return "notvoted";
			}

		}
		
		
	}



function kento_like_post_has_vote($postid)

	{
		
		global $wpdb;
    	$table = $wpdb->prefix . "kento_like_post_info";
		
		$wpdb->get_results("SELECT * FROM $table WHERE postid = '$postid'", ARRAY_A);
		$has_vote= $wpdb->num_rows;
		
		if($has_vote==NULL){
			return "Be The First of This Site Users";
			}
		else {
			return "<span id='total-like'>".$has_vote."</span> People Like This Post";
			}
		
		
	}









function kento_like_post_who_voted($postid)
	{

			
		global $wpdb;
    	$table = $wpdb->prefix . "kento_like_post_info";
		$result = $wpdb->get_results("SELECT userid FROM $table WHERE postid = '$postid' LIMIT 10", ARRAY_A);
		$total_user = $wpdb->num_rows;
		for($i=0; $i <$total_user ; $i++)
			{	
				$userid.= get_avatar($result[$i]['userid'],100);

			}
		return $userid;


                                           

		
	}









function kento_like_post_insert()
	{
	$postid = $_POST['postid'];
	$votestatus = $_POST['votestatus'];
	$userid = get_current_user_id();
    global $wpdb;
    $table = $wpdb->prefix . "kento_like_post_info";
	
	$wpdb->get_results("SELECT * FROM $table WHERE userid=$userid AND  postid =$postid", ARRAY_A);
	$has_postid = $wpdb->num_rows;
	
	if( $has_postid > 0 )
		{
			if($votestatus=="voted")
				{

					$wpdb->query("DELETE FROM  $table WHERE userid=$userid AND  postid =$postid");
					
				}
			elseif($votestatus=="notvoted")
				{
					$wpdb->query("UPDATE $table SET count = count+1 WHERE postid = '".$postid."'");
				}

		}
	
	else 
		{

			
			$wpdb->query("INSERT INTO $table VALUES('',$postid,$userid)");
		}

		


echo kento_like_post_who_voted($postid);
echo "<div style='display:none'><span  id='has-vote-update'>".kento_like_post_has_vote($postid)."</span><div>";






	die();
	return true;

	}



add_action('wp_ajax_kento_like_post_insert', 'kento_like_post_insert');
add_action('wp_ajax_nopriv_kento_like_post_insert', 'kento_like_post_insert');

//Login form Arguments
function kento_like_post_login_box()
	{

	$login_box .= "<div id='kento-like-post-login' >";
	$login_box .= "<p><strong>Please Login To Like this Post</strong></p>";
	$login_box  .= "<form action='".get_option('home')."/wp-login.php' method='post'>";
	$login_box .= "<p class='login-username'><label for='user_login'>Username</label><input type='text' name='log' id='log' value='".wp_specialchars(stripslashes($user_login), 1)."' size='20' /></p>";
	
	$login_box .= "<p class='login-password'><label for='user_pass'>Password</label><input type='password' name='pwd' id='pwd' size='20' /></p>";
	$login_box .= "<p class='login-remember'><input name='rememberme' id='rememberme' type='checkbox' checked='checked' value='forever' /><label for='rememberme'>Remember me</label></p>";
	$login_box .= "<p class='login-submit'><input type='submit' name='submit' value='Send' class='button' /></p>";
	$login_box .= "<p>";

 	$login_box .= "<input type='hidden' name='redirect_to' value='".$_SERVER['REQUEST_URI']."' />";   
	$login_box .= "</p>";
	$login_box .= "</form>";
	$login_box .= "<div>";
	return $login_box ;
	}


function kento_like_post_form($cont){


$cont.= "<div id='kento-fb-vote'  logged='".kento_like_post_is_user_logged()."' >";
$cont.= "<div id='vote-button' postid='".get_the_ID()."' votestatus='".kento_like_post_voted_status(get_the_ID())."' ><div class='".kento_like_post_voted(get_the_ID())."'  ></div><div class='vote-text'>Like</div></div>";
$cont.= "<div id='vote-info'>".kento_like_post_has_vote(get_the_ID())."</div>";
$cont.= "<div id='black-bg'>hh</div>";
$cont.= "<div class='who-voted'>".kento_like_post_who_voted(get_the_ID())."</div>";
$cont.= kento_like_post_login_box();

$cont.=  "</div>";

if(is_single()){
return $cont;
}


}
add_filter('the_content', 'kento_like_post_form');



?>