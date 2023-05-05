import InfiniteScroll from 'infinite-scroll';
import Masonry from 'masonry-layout';
import imagesLoaded from 'imagesloaded';
(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(function() {

		const ajaxUrl = ajax_object.ajaxurl;
		const $dots = $('.fb-stage');
		let blogCat = '';
		let searchValue = '';
		
		function fbLoadAllPosts(page, blogCat = '', searchValue = '') {
			$dots.fadeIn(500);
		
			let postData = {
				page: page,
				search: searchValue,
				blog_cat: blogCat,
			};
		
			let hiddenFormInput = $('form.fb-blog-list input.fb-hidden-form');
			hiddenFormInput.val(JSON.stringify(postData));
		
			let data = {
				action: 'fb_load_posts',
				data: JSON.parse(hiddenFormInput.val()),
				nonce: ajax_object.nonce
			};
		
			$.post(ajaxUrl, data)
				.done(response => {
					// Check if response contains an article element
					if ($(response).find('article').length === 0) {
						// Handle the case where there are no articles in the response
						$('.fb-container').html(response);
					} else {
						$('.fb-container').html(response);
						let elem = document.querySelector('.fb-content');
						let msnry = new Masonry(elem, {
							// options
							itemSelector: '.fb-post',
							columnWidth: '.fb-post',
							percentPosition: true,
						});

						imagesLoaded(elem).on('progress', () => {
							msnry.layout();
						});
					}
				})
				.fail((xhr, status, error) => {
					console.log('AJAX error:', status, error);
				})
				.always(() => {
					$dots.fadeOut(500);
				});
		}
		
		// Check if our hidden form input is not empty, meaning it's not the first time viewing the page.
		function initBlogList() {
			let hiddenFormInput = $('form.fb-blog-list input.fb-hidden-form');
			if (hiddenFormInput.val()) {
				// Submit hidden form input value to load previous page number
				const data = JSON.parse(hiddenFormInput.val());
				fbLoadAllPosts(data.page, data.blog_cat, data.search);
			} else {
				// Load first page
				fbLoadAllPosts(1);
			}
		}
		
		initBlogList();

		// Button functions
		$('body').on('click', '.fb-filters-buttons', function(e) {
			e.preventDefault();

			const category = $(this).data('category');
			$('.fb-filters-buttons').removeClass('active');
			$(this).addClass('active');

			blogCat = category;
			fbLoadAllPosts(1, blogCat, searchValue);
		});
		
		// Define debounce function
		function debounce(func, wait) {
			let timeout;
			return function(...args) {
				clearTimeout(timeout);
				timeout = setTimeout(() => func.apply(this, args), wait);
			};
		}

		// Search input with debounce
		const debouncedSearch = debounce((e) => {
			searchValue = $(e.target).val();
			fbLoadAllPosts(1, blogCat, searchValue);
		}, 500);

		$('body').on('input', '.fb-filters-search', debouncedSearch);
		
		// Pagination Clicks
		$('body').on('click', '.fb-container .fb-pagination li.active', function() {
			let page = $(this).attr('p');
			fbLoadAllPosts(page, blogCat, searchValue);
			// Scroll to top of page
			$('html, body').animate({ scrollTop: 200 }, 'slow')
		});

	});
})( jQuery );
