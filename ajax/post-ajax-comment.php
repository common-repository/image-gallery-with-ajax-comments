<?php 
if(!function_exists('get_option'))
  require_once('../../../../wp-config.php');
  global $comment, $comments, $post, $wpdb, $user_ID, $user_identity, $user_email, $user_url;
   $commenter = wp_get_current_commenter();
   /**
	 * The name of the current comment author escaped for use in attributes.
	 */
	$comment_author = $commenter['comment_author']; // Escaped by sanitize_comment_cookies()

	/**
	 * The email address of the current comment author escaped for use in attributes.
	 */
	$comment_author_email = $commenter['comment_author_email'];  // Escaped by sanitize_comment_cookies()

	/**
	 * The url of the current comment author escaped for use in attributes.
	 */
	$comment_author_url = esc_url($commenter['comment_author_url']);
	
	if($_POST['submit_post']!='')
	{
	function fail($s) { header('HTTP/1.0 406 Not Acceptable'); die($s); }
	// trim and decode all POST variables
	
  foreach($_POST as $k => $v)
    $_POST[$k] = trim(urldecode($v));

  // extract & alias POST variables
   extract($_POST, EXTR_PREFIX_ALL, '');
	// get the post comment_status
  $post_status = $wpdb->get_var("SELECT comment_status FROM {$wpdb->posts} WHERE ID = '".$wpdb->escape($_comment_post_ID)."' LIMIT 1;");
  if ( empty($post_status) ) // make sure the post exists
    fail("That post doesn't even exist!");
  if ( $post_status == 'closed' ) // and the post is not closed for comments
    fail("Sorry, comments are closed.");

  // if the user is already logged in
  get_currentuserinfo();
  if ( $user_ID ) {
    $_author = addslashes($user_identity); // get their name
    $_email = addslashes($user_email); // email
    $_url = addslashes($user_url); // and url
  } else if ( get_option('comment_registration') ) // otherwise, if logging in is required
    fail("Sorry, you must login to post a comment.");

  // if a Name and Email Address are required to post comments
  if ( get_settings('require_name_email') && !$user_ID )
    if ( $_author == '' ) // make sure the Name isn't blank
      fail('You forgot to fill-in your Name!');
    elseif ( $_email == '' ) // make sure the Email Address isn't blank
      fail('You forgot to fill-in your Email Address!');
    elseif ( !is_email($_email) ) // make sure the Email Address looks right
      fail('Your Email Address appears invalid. Please try another.');

  if ( $_comment == '' ) // make sure the Comment isn't blank
    fail('You forgot to fill-in your Comment!');

//  if ( !checkAICode($_code) && !$user_ID ) // must pass AuthImage Word Verification
//    fail('Your Word Verification code did not match the picture. Please try again.');

  // Simple duplicate check
  if($wpdb->get_var("
  SELECT comment_ID FROM {$wpdb->comments}
  WHERE comment_post_ID = '".$wpdb->escape($_comment_post_ID)."'
    AND ( comment_author = '".$wpdb->escape($_author)."'
  ".($_email? " OR comment_author_email = '".$wpdb->escape($_email)."'" : "")."
  ) AND comment_content = '".$wpdb->escape($_comment)."'
  LIMIT 1;"))
    fail("You've said that before. No need to repeat yourself.");

  // Simple flood-protection
  if ( $lasttime = $wpdb->get_var("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author_IP = '$comment_author_IP' OR comment_author_email = '".$wpdb->escape($_email)."' ORDER BY comment_date DESC LIMIT 1") ) {
    $time_lastcomment = mysql2date('U', $lasttime);
    $time_newcomment  = mysql2date('U', current_time('mysql', 1));

    if ( ($time_newcomment - $time_lastcomment) < 15 ) {
      do_action('comment_flood_trigger', $time_lastcomment, $time_newcomment);
      fail("Sorry, you can only post a new comment once every 15 seconds. Slow down cowboy.");
    }
  }

  // insert comment into WordPress database
  wp_new_comment(array(
    'comment_post_ID' => $_comment_post_ID,
    'comment_author' => $_author,
    'comment_author_email' => $_email,
    'comment_author_url' => $_url,
    'comment_content' => $_comment,
    'comment_type' => '',
    'user_ID' => $user_ID
  ));

  // if the user is not already logged in and wants to be Remembered
  if ( !$user_ID && isset($_remember) ) { // remember cookie
    setcookie('comment_author_' . COOKIEHASH, $_author, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
    setcookie('comment_author_email_' . COOKIEHASH, $_email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
    setcookie('comment_author_url_' . COOKIEHASH, $_url, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
  } else { // forget cookie
    setcookie('comment_author_' . COOKIEHASH, '', time() - 30000000, COOKIEPATH, COOKIE_DOMAIN);
    setcookie('comment_author_email_' . COOKIEHASH, '', time() - 30000000, COOKIEPATH, COOKIE_DOMAIN);
    setcookie('comment_author_url_' . COOKIEHASH, '', time() - 30000000, COOKIEPATH, COOKIE_DOMAIN);
  }

  // grab comment as it exists in the WordPress database (after being manipulated by wp_new_comment())
  $comment = $wpdb->get_row("SELECT * FROM {$wpdb->comments} WHERE comment_ID = {$wpdb->insert_id} LIMIT 1;");
  $commentcount = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_post_ID = '".$wpdb->escape($_comment_post_ID)."' LIMIT 1;");
  $post->comment_status = $wpdb->get_var("SELECT comment_status FROM {$wpdb->posts} WHERE ID = '".$wpdb->escape($_comment_post_ID)."' LIMIT 1;");
	
	}
   ?>