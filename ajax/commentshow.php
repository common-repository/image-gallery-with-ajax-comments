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
  // print_r($commenter);

$id=$_REQUEST['id'];
$post_id_req=$id;
if ( $user_ID) {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date_gmt", $id, $user_ID));
	} else if ( empty($comment_author) ) {
	
		$comments = get_comments( array('post_id' => $id, 'status' => 'approve', 'order' => 'ASC') );
	} else {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY comment_date_gmt", $id, wp_specialchars_decode($comment_author,ENT_QUOTES), $comment_author_email));
	}

	
	$post = array(
	'post_type' => 'attachment',
	'numberposts' => 1,
	'ID' => $post_id_req,
	 // any parent
	); 
query_posts('attachment_id='.$post_id_req);  ;
while(have_posts()) { 
the_post();

}


	//$post=query_posts('p='.$post_id_req); 
	//print_r($post);
	// keep $comments for legacy's sake
	$wp_query->comments = apply_filters( 'comments_array', $comments, $post->ID );
	$comments = &$wp_query->comments;
	$wp_query->comment_count = count($wp_query->comments);
	update_comment_cache($wp_query->comments);

	if ( $separate_comments ) {
		$wp_query->comments_by_type = &separate_comments($comments);
		$comments_by_type = &$wp_query->comments_by_type;
	}

	$overridden_cpage = FALSE;
	if ( '' == get_query_var('cpage') && get_option('page_comments') ) {
		set_query_var( 'cpage', 'newest' == get_option('default_comments_page') ? get_comment_pages_count() : 1 );
		$overridden_cpage = TRUE;
	}
	
include('../ajax-comment-show.php');
  exit;
 ?>