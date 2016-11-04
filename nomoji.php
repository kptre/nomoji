<?php namespace Nomoji;

/*
Plugin Name: Nomoji
Description: A simple plugin that removes emoji related scripts from WordPress.
Author URI: http://trevoristall.github.io
Version: 1.0.0
License: MIT
*/


add_action('init', function() {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');	
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');	
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	add_filter('tiny_mce_plugins', __NAMESPACE__.'\\disableEditorEmoji');
	add_filter('wp_resource_hints', __NAMESPACE__.'\\removeEmojiDNSPrefetch', 10, 2);
});

/**
 * Filter and remove TinyMCE emojis
 * @param    array  $plugins  
 * @return   array             Difference betwen the two arrays
 */
function disableEditorEmoji($plugins) {
	return (is_array($plugins)) ? array_diff($plugins, ['wpemoji']) : [];
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 * @param  array  $urls          URLs to print for resource hints.
 * @param  string $relation_type The relation type the URLs are printed for.
 * @return array                 Difference betwen the two arrays.
 */
function removeEmojiDNSPrefetch($urls, $relation_type) {
	if ('dns-prefetch' == $relation_type) {
		/* This filter is documented in wp-includes/formatting.php */
		$emojiURL = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

		$urls = array_diff($urls, [$emojiURL]);
	}
	return $urls;
}
