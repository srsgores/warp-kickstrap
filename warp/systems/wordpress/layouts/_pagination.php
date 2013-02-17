<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

global $wp_query, $post, $wpdb;

if (!isset($range)) $range = 4;
if (!isset($type))  $type  = 'posts';

if ($type === 'comments' && !get_option('page_comments')) return;

if (!isset($page) && !isset($pages)) {

	if ($type === 'posts') {
		$page = get_query_var('paged');
		$posts_per_page = intval(get_query_var('posts_per_page'));
		$pages = intval(ceil($wp_query->found_posts / $posts_per_page));

	} else {
		$comments = $wpdb->get_var("
			SELECT COUNT(*)
			FROM $wpdb->comments
			WHERE comment_approved = '1'
			AND comment_parent = '0'
			AND comment_post_ID = $post->ID");

		$page = get_query_var('cpage');
		$comments_per_page = get_option('comments_per_page');
		$pages = intval(ceil($comments / $comments_per_page));
	}	

	$page = !empty($page) ? intval($page) : 1;
}

$output = array();

if ($pages > 1) {	
	$output[] = '<div class="pagination">';

	$range_start = max($page - $range, 1);
	$range_end   = min($page + $range - 1, $pages);

	if ($page > 1) {
		$link     = ($type === 'posts') ? get_pagenum_link(1) : get_comments_pagenum_link(1);
		$output[] = '<a class="first" href="'.$link.'">'.__('First', 'warp').'</a>';

		$link     = ($type === 'posts') ? get_pagenum_link($page-1) : get_comments_pagenum_link($page-1);
		$output[] = '<a class="previous" href="'.$link.'">«</a>';
	}

	for ($i = $range_start; $i <= $range_end; $i++) {
		if ($i == $page) {
			$output[] = '<strong>'.$i.'</strong>';
		} else {
			$link  = ($type === 'posts') ? get_pagenum_link($i) : get_comments_pagenum_link($i);
			$output[] = '<a href="'.$link.'">'.$i.'</a>';
		} 
	}

	if ($page < $pages) {
		$link     = ($type === 'posts') ? get_pagenum_link($page+1) : get_comments_pagenum_link($page+1);
		$output[] = '<a class="next" href="'.$link.'">»</a>';    

		$link     = ($type === 'posts') ? get_pagenum_link($pages) : get_comments_pagenum_link($pages);
		$output[] = '<a class="last" href="'.$link.'">'.__('Last', 'warp').'</a>';
	} 

	$output[] = '</div>';

	echo implode("", $output);
}