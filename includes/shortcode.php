<?php
function faulhaber_blog_shortcode( $atts ) {
	// Generate output HTML.
	$output = '<div class="fb-grid">';
	$output .= '<div class="fb-filters">';
	//$output .= '<button class="fb-filters-buttons" data-filter="*" class="active">All</button>';
	foreach ( get_categories( array( 'exclude' => array( 21 ) ) ) as $category ) {
		$output .= '<button class="fb-filters-buttons" data-category="' . esc_attr( $category->slug ) . '">' . esc_html( $category->name ) . '</button>';
	}
	$output .= '</div>';
	$output .= '<div class="fb-filters-search-wrapper">';
	$output .= '<input class="fb-filters-search" type="text" placeholder="Search">';
	$output .= '</div>';
	$output .= '<div class="fb-grid-wrapper"><div class="fb-container"></div><div class="fb-stage"><div class="fb-dot-pulse"></div></div>';
	$output .= '</div>';
	$output .= '<form class="fb-blog-list"><input type="hidden" value="" class="fb-hidden-form"/></form>';
	return $output;
}

add_shortcode( 'faulhaber-blog', 'faulhaber_blog_shortcode' );