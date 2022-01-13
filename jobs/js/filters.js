function goToAnchor(anchor) {
  var loc = document.location.toString().split('#')[0];
  document.location = loc + '#' + anchor;
  return false;
}

jQuery(document).ready(function($) {
	
	$('#ajax_results_spinner').show();

	var company_filters = [];
	var source_filters = [];
	var type_filters = [];
	var city_filters = [];
	var offset;
	var page_number;
	var featured = $('#hidden_featured').val();

	$(".job_city.selected").each(function( index ) {
  		city_filters.push($(this).data('filter-name'));
	});
	var cifilters = JSON.stringify(city_filters);

	$(".job_company.selected").each(function( index ) {
  		company_filters.push($(this).data('filter-name'));
	});
	var cfilters = JSON.stringify(company_filters);

	$(".job_type.selected").each(function( index ) {
  		type_filters.push($(this).data('filter-name'));
	});
	var tfilters = JSON.stringify(type_filters);

	$(".job_source.selected").each(function( index ) {
  		source_filters.push($(this).data('filter-name'));
	});
	var sfilters = JSON.stringify(source_filters);

	//initial page load
	$.ajax({
		url: jfs_vars.ajaxurl,
		type: 'post',
		data: {
			action         : 'show_filtered_jobs',
			security       : jfs_vars.security,
			dataType       : 'html',
			city_filters   : cifilters,
			company_filters: cfilters,
			type_filters   : tfilters,
			source_filters : sfilters,
			page_number    : $('#hidden-page-number').val(),
			search_term    : $('#s').val(),
			featured       : featured
		},
		success: function ( html ){
			$('#ajax_results_html').html(html);
			$('#ajax_results_spinner').hide();

		}
	});



	$('#ajax_results_html').on('click', '#saved-search-button', function(e){
		e.preventDefault();
		if($('#saved-search-email').val() != '') {
			var cfilters = JSON.stringify(company_filters);
			var sfilters = JSON.stringify(source_filters);
			var tfilters = JSON.stringify(type_filters);
			var cifilters= JSON.stringify(city_filters);

			$.ajax({
				url: jfs_vars.ajaxurl,
				type: 'post',
				data: {
					action         : 'create_saved_search',
					security       : jfs_vars.security,
					dataType       : 'html',
					email          : $('#saved-search-email').val(),
					company_filters: cfilters,
					source_filters : sfilters,
					type_filters   : tfilters,
					city_filters   : cifilters,
					search_term    : $('#s').val(),
					featured       : featured
				},
				success: function(data) {
					$("#saved-search-div").html('Saved Search Saved. You will receive an email to confirm your request.');
				}
			});
		}
	});

	$('#job_filter_clear').on('click' , function(e){
		e.preventDefault();
		$('#ajax_results_spinner').show();
		$( '.selected' ).removeClass( 'selected' );


		company_filters = [];
		source_filters = [];
		type_filters = [];
		city_filters = [];
		offset = '';
		page_number = '';

		$(".job_city.selected").each(function( index ) {
	  		city_filters.push($(this).data('filter-name'));
		});
		var cifilters = JSON.stringify(city_filters);

		$(".job_company.selected").each(function( index ) {
	  		company_filters.push($(this).data('filter-name'));
		});
		var cfilters = JSON.stringify(company_filters);

		$(".job_type.selected").each(function( index ) {
	  		type_filters.push($(this).data('filter-name'));
		});
		var tfilters = JSON.stringify(type_filters);

		$(".job_source.selected").each(function( index ) {
	  		source_filters.push($(this).data('filter-name'));
		});
		var sfilters = JSON.stringify(source_filters);
// Clear the search box & reload filters
	    document.getElementById('s').value = "";
		window.location = window.location.href.split("?")[0];


		//initial page load
		$.ajax({
			url: jfs_vars.ajaxurl,
			type: 'post',
			data: {
				action         : 'show_filtered_jobs',
				security       : jfs_vars.security,
				dataType       : 'html',
				city_filters   : cifilters,
				company_filters: cfilters,
				type_filters   : tfilters,
				source_filters : sfilters,
				page_number    : $('#hidden-page-number').val(),
				search_term    : $('#s').val(),
				featured       : featured
			},
			success: function ( html ){
				$('#ajax_results_html').html(html);
				$('#ajax_results_spinner').hide();

			}
		});

	});

	$('.jobs_filter').on('click' , function(e){
		e.preventDefault();

		$('#ajax_results_spinner').show();

		$(this).toggleClass('selected');
		page_number = $('.page-numbers.current').text();
		// console.log(page_number);

		var val = $(this).data('filter-name');
		//toggle selected elements in/out of array
		if ($(this).hasClass('job_company') && $(this).hasClass('selected')) {
			company_filters.push(val);
		} else {
			company_filters = $.grep(company_filters, function(value){
				return value != val;
			});
		}
		if ($(this).hasClass('job_source') && $(this).hasClass('selected')) {
			source_filters.push(val);
		} else {
			source_filters = $.grep(source_filters, function(value){
				return value != val;
			});
		}
		if ($(this).hasClass('job_type') && $(this).hasClass('selected')) {
			type_filters.push(val);
		} else {
			type_filters = $.grep(type_filters, function(value){
				return value != val;
			});
		}
		if ($(this).hasClass('job_city') && $(this).hasClass('selected')) {
			city_filters.push(val);
		} else {
			city_filters = $.grep(city_filters, function(value){
				return value != val;
			});
		}

		//AJAX call on filter click
		var cfilters = JSON.stringify(company_filters);
		var sfilters = JSON.stringify(source_filters);
		var tfilters = JSON.stringify(type_filters);
		var cifilters= JSON.stringify(city_filters);

		var data = {
			action         : 'show_filtered_jobs',
			security       : jfs_vars.security,
			//send selected filters
			company_filters: cfilters,
			source_filters : sfilters,
			type_filters   : tfilters,
			city_filters   : cifilters,
			page_number    : $('#page_num').val(),
			search_term    : $('#s').val(),
			featured       : featured
	    };
	    $.ajax({
	    	url     : jfs_vars.ajaxurl,
	    	type    : "post",
	    	data    : data,
	    	dataType: "html",
	    	success : function(html){
	    		$('#ajax_results_spinner').hide();
	    		$('#ajax_results_html').html(html);
	    		$('html, body').animate({scrollTop: 0}, 'fast');
	    	},
	    	error   : function(error){
	    		$('#ajax_results_spinner').hide();
	    		$('#ajax_results_html').html(error);
	    	}

	    });

	}); //END $('.jobs_filter').on('click' , function(e)

//prevent page links from navigating,
//ajax call with offset from pagination
	// $('#ajax_results_html').on('click', '.page-links a', function(e){
		$('#ajax_results_html').on('click', 'a.page-numbers', function(e){
		e.preventDefault();

		$('#ajax_results_spinner').show();

		var page_number = $(this).html();

		//grab filters
		var cfilters  = JSON.stringify(company_filters);
		var sfilters  = JSON.stringify(source_filters);
		var tfilters  = JSON.stringify(type_filters);
		var cifilters = JSON.stringify(city_filters);

		//ajax request with offset
		var data = {
			action         : 'show_filtered_jobs',
			security       : jfs_vars.security,
			//send selected filters
			company_filters: cfilters,
			source_filters : sfilters,
			type_filters   : tfilters,
			city_filters   : cifilters,
			page_number    : page_number,
			offset         : offset,
			search_term    : $('#s').val(),
			featured       : featured
	    };
	    $.ajax({
	    	url     : jfs_vars.ajaxurl,
	    	type    : "post",
	    	data    : data,
	    	dataType: "html",
	    	success : function(html){
	    		$('#ajax_results_spinner').hide();
	    		$('#ajax_results_html').html(html); 
	    		$('html, body').animate({scrollTop: 0}, 'fast');
	    	},
	    	error   : function(error){
	    		$('#ajax_results_spinner').hide();
	    		$('#ajax_results_html').html(error);
	    	}

	    });
	}); //END $('#ajax_results_html').on('click', '.page-links a', function(e)

//Filter toggles
	$('#companies-title').click(function() {
		$('#j-companies').toggle();
		$('#c-plus').text(function(_, text) {
			return text === '+' ? '-' : '+';
		});
	});
	$('#source-title').click(function() {
		$('#j-sources').toggle();
		$('#s-plus').text(function(_, text) {
			return text === '+' ? '-' : '+';
		});
	});
	$('#type-title').click(function() {
		$('#j-types').toggle();
		$('#t-plus').text(function(_, text) {
			return text === '+' ? '-' : '+';
		});
	});
	$('#city-title').click (function() {
		$('#j-cities').toggle();
		$('#ci-plus').text(function(_, text) {
			return text === '+' ? '-' : '+'; 
		});
	});

	$('.jobs_filter').click(function() {
    });

}); //END jQuery(document).ready(function($)
