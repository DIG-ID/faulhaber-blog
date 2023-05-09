<?php
add_action( 'wp_ajax_fb_load_posts', 'fb_load_posts' );
add_action( 'wp_ajax_nopriv_fb_load_posts', 'fb_load_posts' );

function fb_load_posts() {

	// Verify nonce
	$nonce = $_POST['nonce'];
	if ( ! wp_verify_nonce( $nonce, 'fb_load_posts_nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.' );
	}

	$msg           = '';
	$pag_container = '';

	if ( isset( $_POST['data']['page'] ) ) :
		$page         = absint( $_POST['data']['page'] ); // The page we are currently at
		$blog_cat     = sanitize_text_field( $_POST['data']['blog_cat'] );
		$search_text  = sanitize_text_field( $_POST['data']['search'] );
		$cur_page     = $page;
		$page        -= 1;
		$per_page     = 32; // Number of items to display per page
		$previous_btn = false;
		$next_btn     = true;
		$first_btn    = true;
		$last_btn     = true;
		$start        = $page * $per_page;

		if ( $blog_cat && ! term_exists( $blog_cat, 'category' ) ) :
			wp_send_json_error( 'Invalid category.' );
		endif;

		if ( $search_text ) :
			$search_text = wp_unslash( $search_text );
			if ( strlen( $search_text ) < 2 ) :
				wp_send_json_error( 'Search text must be at least 3 characters.' );
			endif;
		endif;

		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'offset'         => $start,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		$count_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		if ( $blog_cat ) :
			$args['category_name']       = $blog_cat;
			$count_args['category_name'] = $blog_cat;
		endif;

		if ( $search_text ) :
			$args['s']       = $search_text;
			$count_args['s'] = $search_text;
		endif;

		$count = new WP_Query( $count_args );
		// Loop into all the posts to cout them
		if ( $count->have_posts() ) :
			$count = $count->post_count;
			wp_reset_postdata();
		else :
			$count = 0;
		endif;

		$all_blog_posts = new WP_Query( $args );
		// Loop into all the posts
		if ( $all_blog_posts->have_posts() ) :
			while ( $all_blog_posts->have_posts() ) :
				$all_blog_posts->the_post();
				$msg .= '<article id="post-' . get_the_ID() . '" class="' . implode( ' ', get_post_class( 'fb-post col-xs-12 col-sm-6 col-md-4 col-lg-4' ) ) . '">';
				if ( has_post_thumbnail() ) :
					$msg .= '<figure class="fb-post--image-wrapper">' . get_the_post_thumbnail( get_the_ID(), 'large', array( 'class' => 'fb-post--image' ) ) . '</figure>';
				endif;
				$msg .= '<h2 class="fb-post--title">' . get_the_title() . '</h2>';
				$msg .= '<p class="fb-post--description">' . wp_kses_post( get_the_excerpt() ) . '</p>';
				$msg .= '<a href="' . get_the_permalink() . '" class="fb-post--link">' . __( 'weiterlesen', 'faulhaber-blog' ) . '</a>';
				$msg .= '</article>';
			endwhile;
			wp_reset_postdata();
		else :
			$msg .= '<div class="col-xs-12"><p class="warning" style="text-align:center; font-size:15px;">' . __( 'Es wurden keine Veröffentlichungen gefunden, die Ihren Suchkriterien entsprechen.', 'faulhaber-blog' ) . '</p></div>';
		endif;

		$msg = '<div class="fb-content row">' . $msg . '</div>';

		$no_of_paginations = ceil( $count / $per_page );

		if ( $cur_page >= 7 ) :
			$start_loop = $cur_page - 3;
			if ( $no_of_paginations > $cur_page + 3 ) :
				$end_loop = $cur_page + 3;
			elseif ( $cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6 ) :
				$start_loop = $no_of_paginations - 6;
				$end_loop   = $no_of_paginations;
			else :
				$end_loop = $no_of_paginations;
			endif;
		else :
			$start_loop = 1;
			if ( $no_of_paginations > 7 ) :
				$end_loop = 7;
			else :
				$end_loop = $no_of_paginations;
			endif;
		endif;

		$pag_container .= '<ul class="fb-pagination">';

		if ( $previous_btn && $cur_page > 1 ) :
			$pre = $cur_page - 1;
			$pag_container .= "<li p='$pre' class='active'> < </li>";
		elseif ( $previous_btn && $no_of_paginations > 1 ) :
			$pag_container .= "<li class='inactive'> < </li>";
		endif;

		for ( $i = $start_loop; $i <= $end_loop; $i++ ) :
			if ( $cur_page === $i ) :
				$pag_container .= "<li p='$i' class = 'selected' >{$i}</li>";
			else :
				$pag_container .= "<li p='$i' class='active'>{$i}</li>";
			endif;
		endfor;

		if ( $next_btn && $cur_page < $no_of_paginations ) :
			$nex = $cur_page + 1;
			$pag_container .= "<li p='$nex' class='active fb-next-page'> > </li>";
		elseif ( $next_btn && $no_of_paginations > 1) :
			$pag_container .= "<li class='inactive'> > </li>";
		endif;

		$pag_container = $pag_container . '</ul>';

		echo wp_kses_post( $msg );
		echo '<div class = "fb-pagination-nav">' . $pag_container . '</div>';

	else :
		wp_send_json_error( 'Invalid request.' );
	endif;

	exit();

}
?>
