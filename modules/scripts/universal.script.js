var loading = 0;
var startLoading = function(target) {

	loading = 1;
	$(target).css('opacity', '0.4');
	$('body').addClass('loading');
	$('body *').addClass('loading');
	$('body input[type=submit]').attr('disabled', 'true');
		
};

var stopLoading = function(target) {

	loading = 0;
	$(target).css('opacity', '1');
	$('body').removeClass('loading');
	$('body *').removeClass('loading');
	$('body input[type=submit]').removeAttr('disabled');
	
};

var getLocation = function() {

	return window.location.origin+window.location.pathname+window.location.search;

}

var go = function(url, target) {

	$("#hidden").load( url + '&redirect=0', function( result ) {

		if(getLocation() == result) $(window).trigger('statechange');
		else History.pushState({ target: target }, "Remich Bookmarks", result);
	
	});

}
$('body').on('click', '.url', function(e) {
	
	e.preventDefault();

	if(loading == 1) return 0;
	
	var target = $(this).attr('target');
	var href = $(this).attr('href');

	go(href, target);

}); 

var searchVal = function(module) {

	$("#hidden").load('index.php?module='+ module +'&ajax=1&page=urlGetSearch&windowLocation='+encodeURIComponent(getLocation()), function ( result ) {
		
		if(result == '') result = 'Search';
		$("#search").val(result);
	
	});
	
}
