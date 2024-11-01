<?PHP
/**
 * Add BP BLACKLISTED WORDS
 *
 * @author 		WangGuard
 * @package 	WangGuard/Add-on
 * @version     1.0
 */

function wangguard_blacklisted_words_wpmu_user_name($result) {

	$words = array_filter(get_site_option( 'wangguard_blacklisted_words_list'));
       		if (!empty($words)){

		$user_name = $_POST['user_name'];

		//check domain against the list of selected blocked domains
		$blocked = wangguard_look_for_bl_word($user_name);
		
		if ($blocked) {
			$result['errors']->add('user_name',   __('<strong>ERROR</strong>: Your user name has words not Allowed in this site.', 'wangguard'));
		}
		
		return $result;
	}

}
function wangguard_blacklisted_words_wpmu_user_email($result) {

	$words = array_filter(get_site_option( 'wangguard_blacklisted_words_list'));
       		if (!empty($words)){
		$user_email = $_POST['user_email'];

		//check domain against the list of selected blocked domains
		$blocked = wangguard_look_for_bl_word($user_email);
		
		if ($blocked) {
			$result['errors']->add('user_email',   __('<strong>ERROR</strong>: Your email has words not Allowed in this site.', 'wangguard'));
		}
		
	return $result; 
	}
}

function wangguard_blacklisted_words_wpmu_blogname($result) {

	
		$words = array_filter(get_site_option( 'wangguard_blacklisted_words_list'));
       		if (!empty($words)){
		$blogname = $_POST['blogname'];

		//check domain against the list of selected blocked domains
		$blocked = wangguard_look_for_bl_word($blogname);
		
		if ($blocked) {
			$result['errors']->add('blogname',   __('<strong>ERROR</strong>: Your domain name has words not Allowed in this site.', 'wangguard'));
		}
		
	return $result; 
	}
}

function wangguard_blacklisted_words_wpmu_blog_title($result) {

	
		$words = array_filter(get_site_option( 'wangguard_blacklisted_words_list'));
       		if (!empty($words)){
       		$blog_title = $_POST['blog_title'];

		//check domain against the list of selected blocked domains
		$blocked = wangguard_look_for_bl_word($blog_title);
		
		if ($blocked) {
			$result['errors']->add('blog_title',   __('<strong>ERROR</strong>: Your title has words not Allowed in this site.', 'wangguard'));
		}
		
	return $result; 
}
}
if (get_site_option( 'wangguard_blacklisted_words_list')){
$words = array_filter(get_site_option( 'wangguard_blacklisted_words_list'));
       		if (!empty($words)){
       			add_filter('wpmu_validate_user_signup', 'wangguard_blacklisted_words_wpmu_user_name',99);
	   			add_filter('wpmu_validate_user_signup', 'wangguard_blacklisted_words_wpmu_user_email',130);
	   			add_filter('wpmu_validate_blog_signup', 'wangguard_blacklisted_words_wpmu_blogname',90);
	   			add_filter('wpmu_validate_blog_signup', 'wangguard_blacklisted_words_wpmu_blog_title',100);
	   			add_action('publish_post', 'wangguard_check_blacklisted_word_post_wpmu');
	   	}
}

function wangguard_check_blacklisted_word_post_wpmu($post_id){
	
	$post = get_post($post_id);
	$website_name = get_option('blogname');
	$siteurl = get_option('siteurl');
	$permanlink_post = get_permalink( $post_id );
	$post_content = $post->post_content;
	$post_title = $post->post_title;
	$author = get_userdata($post->post_author);
	$author_display_name = $author->display_name;
	
	$wordinwebsitename = wangguard_look_for_bl_word($website_name);
	$wordinpermanlink_post = wangguard_look_for_bl_word($permanlink_post);
	$wordinpost_content = wangguard_look_for_bl_word($post_content);
	$wordinpost_title = wangguard_look_for_bl_word($post_title);
	$wordinauthor_display_name = wangguard_look_for_bl_word($author_display_name);
	
		
		//Check blacklisted word in Website title
		if ($wordinwebsitename) {
			wangguard_sent_notice_email_blacklisted_word_used_mu($website_name, $siteurl, $permanlink_post, $post_title, $wordinauthor_display_name);
        }
       
        //Check blacklisted word in Permanlink post
		if ($wordinpermanlink_post) {
			wangguard_sent_notice_email_blacklisted_word_used_mu($website_name, $siteurl, $permanlink_post, $post_title, $wordinauthor_display_name);
        }
       
        //Check blacklisted word in post content
		if ($wordinpost_content) {
			wangguard_sent_notice_email_blacklisted_word_used_mu($website_name, $siteurl, $permanlink_post, $post_title, $wordinauthor_display_name);
        }
        
        //Check blacklisted word in post Title
		if ($wordinpost_title) {
			wangguard_sent_notice_email_blacklisted_word_used_mu($website_name, $siteurl, $permanlink_post, $post_title, $wordinauthor_display_name);
        }
        
        //Check blacklisted word in Author name
		if ($wordinauthor_display_name) {
			wangguard_sent_notice_email_blacklisted_word_used_mu($website_name, $siteurl, $permanlink_post, $post_title, $wordinauthor_display_name);
        }	
}

function wangguard_sent_notice_email_blacklisted_word_used_mu($website_name, $siteurl, $permanlink_post, $post_title, $wordinauthor_display_name){

	$website_name = $website_name;
	$siteurl = $siteurl;
	$permanlink_post = $permanlink_post;
	$post_title = $post_title;
	$wordinauthor_display_name = $wordinauthor_display_name;
	$network_name = get_site_option('blogname');
	$network_admin = get_site_option('admin_email');
	$headers  = "From:" . $network_name . "<" . $network_admin . ">\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\n";
	$headers .= "Content-Transfer-Encoding: 8bit\n";
	$message = "<p>Hi ".$network_name.",</p>
<p><br />The website <a href='".$siteurl."'>".$website_name."</a> has used a blacklisted word.</p>
<p>You can see the post here: <a href=". $permanlink_post.">".$post_title."</a></p>
<p>&nbsp;</p>
<p>The WangGuard Team<br />
<a href='http://www.wangguard.com/'>WangGuard</a>";
   
   wp_mail($network_admin, "Backlisted Word Used!", $message, $headers );
}
?>