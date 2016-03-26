<?php
/*
Plugin Name: WP Lazy Load
Plugin URI: http://stomptheweb.co.uk
Description: Lazy Load images
Version: 1.0.0
Author: Steven Jones
Author URI: http://stomptheweb.co.uk/
License: GPL2
*/

include 'lib/simple_html_dom.php';

function wp_register_lazy_load_script() {
	
	wp_register_script( 'lazy-load', plugin_dir_url( __FILE__ ) . 'js/lazysizes.min.js', array( 'jquery'), NULL, true);

	if (is_singular()) {
		wp_enqueue_script( 'lazy-load' );
	}
	
}
add_action('wp_enqueue_scripts', 'wp_register_lazy_load_script');

function wp_filter_img_tags($content) {

	if (empty($content)) {
        return;
    }

	if (!is_singular() || is_front_page()) {
		return $content;
	}

	$html = str_get_html($content, '', '', '', false);
	$placeholder = 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';

    foreach ($html->find('img') as $element) {

        // Add lazyload class
        $element->class = 'lazyload ' . $element->class;
        
        // Populate data attribute
        $element->{'data-srcset'} = $element->srcset;
        $element->{'data-src'} = $element->src;

        // Remove standard image properties
        $element->{'src'} = $placeholder;
    	$element->{'srcset'} = '';
    }

	return $html;

}
add_filter('the_content', 'wp_filter_img_tags', 99);