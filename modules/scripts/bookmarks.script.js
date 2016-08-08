$(document).ready( function() {

	var mode = 'default';
	
	var viewport = function() {
			$('#left').css('height', window.innerHeight - 30 + 'px');
	};
	window.onresize = viewport;
	viewport();

	var hideBookmark = function(id) {

		$(id).hide(200, function() {
						  		
			$(this).remove();
			var a = $.trim($('#bookmarks').html());
			if(a == '') $('#bookmarks').html('<p class="noitems">No Items Found</p>');			  			
								
		});

	}

	var update_lock = 0;
	$('body').on('click', '#update_thumbnails', function(e) {

		e.preventDefault();

		if(update_lock) // only allow ONE call to this function at a time
			return; 

		// console.log("Started Get Thumbnail Ajax Request");
		$(this).addClass("loading_thumbnails");
		var that = $(this);
		update_lock = 1;

		var url = $(this).attr("href");
		$.get( url, function( data ) {
			if(data === 'Script Finished') {
				that.removeClass("loading_thumbnails");
				// console.log("Finished Get Thumbnail Ajax Request Successful");
				update_lock = 0;
			}
	});


	});

	$('body').on('click', '#toggleHidden', function(e) {

		e.preventDefault();
		
		if(loading == 1) return 0;
	
		var className = $(this).attr('class');
		if(className == 'show') {
		
			jPromptPassword("Password:", null, 'Enter Password', function( password ) {
		
				if(password != null) {
				
					$('#hidden').load('index.php?module=bookmarks&ajax=1&page=showHidden&password=' + CryptoJS.SHA256(password), function( result ) {
						
						if(result == '1') {

							window.location.reload();

						} else 	
							if(result == '0')
								jAlert('Wrong Password', 'Error');
							else
								jAlert(result, 'Error');
					
					});
				
				}
			
			});
		
		} else {
	
			$('#hidden').load('index.php?module=bookmarks&ajax=1&page=hideHidden', function() {

				window.location.reload();
			
			});
			
		}
	
	}); // end $('#toggleHidden').();
	
	
	/*
	*
	*	DRAG and DROP Actions
	*
	*/
	
	var drop_tag = function(id, tag) { 
	
		$('#hidden').load('index.php?module=bookmarks&ajax=1&page=getTags&id=' + id, function( value ) {
	
			var tags = value + '#' + tag;
				
			$('#hidden').load('index.php?module=bookmarks&ajax=1&page=tags&id=' + id + '&tags='+encodeURIComponent(tags), function( result ) { 
		
				if(result != '1') jAlert(result, 'Error');
				else {
					// Check if Tag has to be hidden:
				
					// determine pageType and tag (index.php?type=...)
					var pageType = null;
					var tag = null;
					var params = window.location.search;
					params = params.replace('?', '');
					var paramsAr = params.split('&');
				
					for(var i = 0; i < paramsAr.length; i++) {
						paramsAr2 = paramsAr[i].split('=');
						if(paramsAr2[0] == 'type') pageType = paramsAr2[1];
						if(paramsAr2[0] == 'tag') tag = paramsAr2[1];
					}
				
					tags = tags.trim();
				
					if(pageType == 'tag') {
				
						// clean tags
						var tagsAr = tags.split('#');
						var tagsAr2 = new Array();
						for(var ii = 0; ii < tagsAr.length; ii++) 
							if(tagsAr[ii].trim() != '')
								tagsAr2.push(tagsAr[ii]);
							
						if($.inArray(tag, tagsAr2) == -1) hideBookmark('#bookmarks a#' + id);
				
					}
					if(pageType == 'notag')
						if(tags != '') hideBookmark('#bookmarks a#' + id);
			
				}
		
			});
	
		});

	};
	
	
	
	/*
	*
	*	DRAG and DROP
	*
	*/
	
	var check_collision = function() {	
		for (var i in coordinates)
			if (mouse_x >= coordinates[i].left && 
				mouse_x <= coordinates[i].right && 
				mouse_y + coordinate_offset >= coordinates[i].top && 
				mouse_y + coordinate_offset <= coordinates[i].bottom
			) coordinates[i].dom.addClass("drop-hover");
			else coordinates[i].dom.removeClass("drop-hover");	
	};

	var coordinates = new Array();
	
	var get_coordinates = function () {
		
		$( "#tags li" ).each(function() { 
		
			var lefttop = $(this).offset();
			coordinates.push({
			    dom: $(this),
			    left: lefttop.left,
			    top: lefttop.top,
			    right: lefttop.left + $(this).width(),
			    bottom: lefttop.top + $(this).height()
			});	
				
		});
	
	};
	get_coordinates();
	

	var coordinate_offset = 0;
	$('#left').on('scroll', function(e) {	

		coordinate_offset = e.currentTarget.scrollTop;
		check_collision();	
	
	});

	var change_offset = function(e, difference) {

		if(mouse_x < 280 || difference == 'wayne') {
	
			var distance_x = mouse_x - e.clientX;
			var distance_y = mouse_y - e.clientY;
	
		} else {
	
			var distance_x = 0;
			var distance_y = 0;
	
		}

		var right_scrollTop = $('#right').scrollTop();

		var right_scrollTop_offset = right_scrollTop - right_scrollTop_prev;
		right_scrollTop_prev = right_scrollTop;


		$('#bookmarks .selected').each(function(){

			if(!$(this).hasClass('dragging'))
				$(this).addClass('dragging');

			var offset = $(this).offset();
			$(this).offset({ top: (offset.top + right_scrollTop_offset - distance_y), left: (offset.left - distance_x) });

		}); 

	};


	$('#right').on('scroll', function(e) {	
	
		if(mode == 'edit') {

			if(dragging == 0)
				right_scrollTop_prev = e.currentTarget.scrollTop;
			else
				change_offset(e);
				
		}
		
	});

	$('body').on('click', '#bookmarks a', function(e) {	

		if(mode == 'edit') {

			e.stopPropagation();
			e.preventDefault();	
		
		}
		
	});

	$('body').on('click', '#right', function(e) {	
			
		if(mode == 'edit') {
		
			mode = 'default';
			$('#bookmarks .selected').each( function() {	
				$(this).removeClass('selected');
			});	
			
		}
		
	});

	var stop_dragging = function(e) {	
	
		dragging = 0;		
		mouse_x = 0;
		mouse_y = 0;

		$('#bookmarks .selected').each(function(){
			$(this).css('position', 'inherit').removeClass('dragging');
		});
		
		$('#tags li.drop-hover').each(function() {
			
			var tag = $(this).children().html().trim();
			
			$('#bookmarks .selected').each( function() {
			
				var id = $(this).attr('id');
				drop_tag(id, tag);
			
			});
		
		});

		for (var i in coordinates) {
			coordinates[i].dom.removeClass("drop-hover");
		}	
		
	};

	var dragging = 0;
	var mouse_x = 0;
	var mouse_y = 0;
	var right_scrollTop_prev = 0;

	var drag_items = function(e) { 

		did_mouse_move = 1;
	
		if(dragging == 0) {
			mouse_x = e.clientX;
			mouse_y = e.clientY;
			dragging = 1;
	 	}
	
		check_collision();
	
		change_offset(e, 'wayne');
	
		mouse_x = e.clientX;
		mouse_y = e.clientY;	
		
	};

	var did_mouse_move = 0;
	var did_add_class = 0;

	$('body').on('mousedown', '#bookmarks a', function(e) {
	
		if(mode == 'edit') {

			e.preventDefault();
	
			if(e.button == 0) {
	
				if(!e.ctrlKey) {
	
					if( !$(this).hasClass('selected') ) {
		
						$('#bookmarks .selected').each(function(){
							$(this).removeClass('selected');
						});
		
						$(this).addClass('selected');
		
					}
		
				} else {
	
					if( !$(this).hasClass('selected') ) {
		
						$(this).addClass('selected');
						did_add_class = 1;
			
					}
		
				}
	
				$('html').on('mousemove', 'body', drag_items);
		
			}
			
		}
	
	});

	$('html').on('mouseup', 'body', function(e) {
	
		if(mode == 'edit') {

			stop_dragging(e);
			$('html').off('mousemove', 'body', drag_items);
			
		}

	});

	$('html').on('mouseup', '#bookmarks a', function(e) {
	
		if(mode == 'edit') {

			if(e.button == 0) {

				if(did_mouse_move == 1) {
	
					stop_dragging(e);
					did_mouse_move = 0;
		
				} else {

					if(!e.ctrlKey) {
	
						$('#bookmarks .selected').each(function(){
							$(this).removeClass('selected');
						});
		
						$(this).addClass('selected');
				
	
					} else {
			
						if(did_add_class == 0)
							$(this).removeClass('selected');
				
						did_add_class = 0;
	
					}
	
				}
	
				$('html').off('mousemove', 'body', drag_items);
	
			}
		
		}
	
	});



	function in_array(needle, haystack)  {
			
		for(var a = 0; a < haystack.length; a++) 
			if(haystack[a] == needle) 
				return 1;
			
	}

	function get_array_key(needle, haystack) {
			
		for(var a = 0; a < haystack.length; a++) 
			if(haystack[a] == needle) 
				return a;
			
	}

	// var pressed_keys = new Array();
	var ctrlDown = false, fDown = false;
    var ctrlKey = 17, vKey = 86, cKey = 67, fKey = 70;

    $(document).keydown(function(e)
    {
        if (e.keyCode === ctrlKey) ctrlDown = true;
        if (e.keyCode === fKey) fDown = true;

        if(ctrlDown && fDown) { // CTRL + f -> focus search
        	
        	// if(!$('#search').is(':focus'))
		    	e.preventDefault();
        	$('#search').focus();
        }
    }).keyup(function(e)
    {
        if (e.keyCode == ctrlKey) ctrlDown = false;
        if (e.keyCode == fKey) fDown = false;
    });

	// Handles the Search live update
	var input = document.getElementById('search');
	input.onkeyup = function() {

		if(loading == 1) return 0;

		var value = $(this).val();
		var outerText;

		if($(this).val() === "") {
			$('#left .wrapper').load("index.php?module=bookmarks&page=menu&wrapping=0", function() {
			});
		}

		// Check if Tags or Bookmark Search
		if(value[0] === "#") {
			// Get Tags
			// var tags = [];
			$('body #tags li').each(function() {
				var matchString = value.replace("#", "");
				outerText = $(this)[0].outerText;

				// If the match string is coming from user input you could do:
				matchString = matchString.toLowerCase();

				if (outerText.toLowerCase().indexOf(matchString) != -1){
					$(this).css('display' , "block");
				} else {
					$(this).css('display' , 'none');	

				}
			});
			$('body .tags a').each(function() {
				var matchString = value.replace("#", "");
				outerText = $(this)[0].outerText;

				// If the match string is coming from user input you could do:
				matchString = matchString.toLowerCase();

				if (outerText.toLowerCase().indexOf(matchString) != -1){
					$(this).css('display' , "inline");
				} else {
					$(this).css('display' , 'none');	

				}
			});
		} else if($(this).val().search("#") === -1) {
			var href = 'index.php?module=bookmarks&ajax=1&page=load&redirect=0&id=search&search=' + encodeURIComponent(value);

			$("#hidden").load( href , function( result ) {
				History.pushState({ target: 'content' }, "RM Internet Suite", result);
			});

		}
		

	};

	$('body').on('focus', '#search', function(e) {

		if($(this).val() === 'Search')
			$(this).val('');	

	});

	$('body').on('focusout', '#search', function(e) {

		if($(this).val() === '')
			$(this).val('Search');

	});

	$('body').on('submit', '#searchform', function(e) {

		e.preventDefault();
		
	});

	var edit_tags = function( ids ) {
	
		var id = ids[ids.length - 1];
		
		$('#hidden').load('index.php?module=bookmarks&ajax=1&page=getTags&id=' + id, function( value ) {
					
			var title = $('#'+id).find('.title').html();
			jPrompt("Tags (separated by #):", value, "Enter Tags for »"+title+"«", function( tags ) {
	
				if(tags != null) {
				
					$('#hidden').load('index.php?module=bookmarks&ajax=1&page=tags&id=' + id + '&tags='+encodeURIComponent(tags), function( result ) { 
				
						if(result != '1') jAlert(result, 'Error');
						else {
							// Check if Tag has to be hidden:
						
							// determine pageType and tag (index.php?type=...)
							var pageType = null;
							var tag = null;
							var params = window.location.search;
							params = params.replace('?', '');
							var paramsAr = params.split('&');
						
							for(var i = 0; i < paramsAr.length; i++) {
								paramsAr2 = paramsAr[i].split('=');
								if(paramsAr2[0] == 'type') pageType = paramsAr2[1];
								if(paramsAr2[0] == 'tag') tag = paramsAr2[1];
							}

							$('#hidden').load('index.php?module=bookmarks&ajax=1&page=getTagName&id=' + tag, function( res ) {
								tag = res;
								tags = tags.trim();
							
								if(pageType == 'tag') {
							
									// clean tags
									var tagsAr = tags.split('#');
									var tagsAr2 = new Array();
									for(var ii = 0; ii < tagsAr.length; ii++) 
										if(tagsAr[ii].trim() != '')
											tagsAr2.push(tagsAr[ii]);

									// console.log("Tag: " + tag);
									// console.log(tagsAr2);

										
									if($.inArray(tag, tagsAr2) == -1) hideBookmark('#bookmarks a#' + id);
							
								}
								if(pageType == 'notag')
									if(tags != '') hideBookmark('#bookmarks a#' + id);
							
								$("#left .wrapper").load('index.php?module=bookmarks&page=menu&wrapping=0', function() {
									get_coordinates();								
								});							
									
								ids.pop();
							
								if(ids.length != 0)
									edit_tags( ids );

							});
					
						}
				
					});
			
				}
		
			});
	
		});
	
	};
	
	var shortenStr = function( string, maxlen ) {
	
		if(string.length > maxlen)
			string = string.substr(0, maxlen)+'…';
		
		return string;
		
	};
	
	var edit_titles = function( ids ) {
	
		var id = ids[ids.length - 1];
		
		$('#hidden').load('index.php?module=bookmarks&ajax=1&page=getTitle&id=' + id, function( title ) {
		
			jPrompt("Name:", title, "Enter New Name", function( newName ) {
	
				if(newName != null) {
			
					if(newName == '') jAlert('Error: Please enter a name', 'Error', function( result ) {
					
						edit_titles(ids)
					
					});
					else {
	
						$('#hidden').load('index.php?module=bookmarks&ajax=1&page=edit&id=' + id + '&title=' + encodeURIComponent(newName), function( result ) {

							// 1#newName for success
							var result_ar = result.split("#");
				
							if(result_ar[0] === '1') { 

								newName = result_ar[1];
						
								newName = shortenStr(newName, 55);
								$('#'+id).find('.title').html( newName );
							
								ids.pop();
								if(ids.length != 0)
									edit_titles( ids );
								
							} else jAlert(result, 'Error');
				
						});
			
					}
			
				}
		
			});
		
		});
	
	};
	
	var edit_urls = function( ids ) {
	
		var id = ids[ids.length-1];
		
		$('#hidden').load('index.php?module=bookmarks&ajax=1&page=changeUrl1&id=' + id, function (url) {
			
			var title = $('#'+id).find('.title').html();
			jPrompt("URL:", url, "Enter New URL for »"+title+"«", function( result ) {
			
				if(result != null) {
		
					if(result == '') jAlert('Error: Please enter a url', 'Error', function( result ) {
					
						edit_urls(ids)
					
					});
					else {
			
						$('#hidden').load('index.php?module=bookmarks&ajax=1&page=changeUrl2&id=' + id + '&url=' + encodeURIComponent(result), function( result ) {
					
							if(result == '1') {
							
								ids.pop();
								if(ids.length != 0)
									edit_urls( ids );
														
							} else jAlert(result, 'Error');
						
						});
				
					}
				} 
			
			});
			
		});
	
	};
	
	var copy_urls = function( ids ) {
	
		var id = ids[ids.length - 1];
		
		$('#hidden').load('index.php?module=bookmarks&ajax=1&page=changeUrl1&id=' + id, function(url) {
		
			var title = $('#'+id).find('.title').html();
			jPrompt("URL:", url, "Copy URL from »"+title+"«", function( result ) {
			
				if(result != null) {
				
					ids.pop();
					if(ids.length != 0)
						copy_urls( ids );
				
				}
			
			});
			
		});
	
	};
	
	var toggle_visibility = function( ids ) {
	
		var id = ids[ids.length - 1];
		
		$('#hidden').load('index.php?module=bookmarks&ajax=1&page=toggleVisibility&id=' + id, function( result ) {
			
			if(result != null) {

	  			if(result == 'hide') hideBookmark('#bookmarks a#' + id); 
	  			else {
	  			
		  			if(result == 'transparent') $('#bookmarks a#' + id ).addClass("clHidden");
		  			else {
		  			
		  				if(result == 'show') $('#bookmarks a#' + id ).removeClass("clHidden");
			  			else jAlert(result, 'Error');
			  			
		  			}
		  			
		  		}
		  		
		  		ids.pop();
		  		if(ids.length != 0)
		  			toggle_visibility( ids );
		  		
			}
		
		});
	
	};
	
	var delete_items = function( ids ) {
	
		var id = ids[ids.length - 1];
		
		$('#hidden').load('index.php?module=bookmarks&ajax=1&page=delete&id=' + id, function(result) {
		
			if(result != null) 
				if(result == '1') {
					
					hideBookmark('#bookmarks a#' + id);
					ids.pop();
					if(ids.length != 0)
						delete_items( ids );
					
				} else jAlert(result);
		
		});
	
	};
	
	var collect_ids = function( dis ) {
	
		var ids = new Array();
		var first = $(dis).attr('id');
		ids.push(first);
	
		$('#bookmarks .selected').each(function() {
		
			var id = $(this).attr('id');
			if(id != first)
				ids.push(id);
		
		});
		ids.reverse();
	
		return ids;
		
	};
	
	$.contextMenu({

		// define which elements trigger this menu
		selector: "navigation a.trash",
		// define the elements of the menu
		items: {
	
			tagsas: {
		
				name: "Empty Trash", 
				icon: "delete", 
				callback: function(key, opt) { 
					$(this).contextMenu("hide");

					$("#hidden").load("index.php?module=bookmarks&ajax=1&page=load&id=empty_trashed", function(result) {
						window.location.reload();
					});
				}
			},
		}
	});
	
	
	/*
	*
	*	CONTEXT MENU
	*
	*/
	$.contextMenu({

		// define which elements trigger this menu
		selector: "#bookmarks a",
		// define the elements of the menu
		items: {
	
			tagsas: {
		
				name: "Tags", 
				icon: "tags", 
				callback: function(key, opt) { 
				
					$(this).contextMenu("hide");
					var ids = new Array();
					ids = collect_ids( this );
					edit_tags( ids );					
				
				}
			
			},
		
			rename: {
		
				name: "Rename", 
				icon: "edit", 
				callback: function(key, opt) { 
			
					$(this).contextMenu("hide");
					var ids = new Array();
					ids = collect_ids( this );
					edit_titles( ids );
					
				}
			
			}, // end items:rename
		
			url: {
		
				name: "Change URL", 
				icon: "edit", 
				callback: function(key, opt) { 
				
					$(this).contextMenu("hide");
					var ids = new Array();
					ids = collect_ids( this );
					edit_urls( ids );					
				
				}
			
			}, //end items:url
		
			copylink: {
		
				name: "Copy URL", 
				icon: "copy", 
				callback: function(key, opt) { 
				
					$(this).contextMenu("hide");
					var ids = new Array();
					ids = collect_ids( this );
					copy_urls( ids );
				
				}
			
			}, //end items:copylink
		
			toggle: {
		
				name: "Toggle Visibility", 
				icon: "quit", 
				callback: function(key, opt) {
				
					$(this).contextMenu("hide");
					var ids = new Array();
					ids = collect_ids( this );
					toggle_visibility( ids );
				
				} 
			
			}, // end items:toggle
		
			del: {
		
				name: "Delete / Restore", 
				icon: "delete", 
				callback: function(key, opt) { 
				
					$(this).contextMenu("hide");
					var ids = new Array();
					ids = collect_ids( this );
					delete_items( ids );
				
				}
			
			} // end items:del
		
		},
		events: {
			show: function(opt){ 
			
				mode = 'edit';
				if(!$(this).hasClass('selected')) {
		        
				   	$('#bookmarks a').each(function() {
				    	$(this).removeClass('selected');
				    });
				    $(this).toggleClass('selected');
				    
				}
			}
		}
	
	}); // end $.contextMenu();

}); // end $(document).ready();
