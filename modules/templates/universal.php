<!DOCTYPE HTML>
<html class="rm_layout">
	<head>		
		<meta http-equiv="content-type" content="text/html;charset=utf-8" />
		
  		<link rel="stylesheet" type="text/css" media="all" href="modules/styles/reset.style.css" />
  		<link rel="stylesheet" type="text/css" media="all" href="modules/styles/universal.style.css" />
  		<link rel="stylesheet" type="text/css" media="all" href="extensions/jquery.contextMenu/jquery.contextMenu.css" />
  		<link rel="stylesheet" type="text/css" media="all" href="extensions/jquery.alerts/jquery.alerts.css" />	
  		
	    <title><?php echo $this->_['page_title']; ?></title>
  	</head>
  	<body class="rm_layout">
  		
  		
  		<navigation>

  			<ul>
  				<li>
  					<a href="index.php?module=bookmarks&ajax=1&page=load&id=change_module&new_module=bookmarks" target="content" class="url"><img class="home" width="64" height="64" src="modules/images/home.png" alt="Home" Title="Home"></a>
  				</li>
          <li>
            <a href="index.php?module=bookmarks&ajax=1&page=load&id=tagcloud" target="content" class="url"><img width="64" height="64" src="modules/images/tagcloud.png" alt="Tagcloud" Title="Tagcloud"></a>
          </li>

  				<li>
  					<a href="index.php?module=bookmarks&ajax=1&page=load&id=trashed" target="content" class="url trash"><img width="64" height="64" src="modules/images/trash.png" alt="Trash" Title="Trash"></a>
  				</li>
  				<li>
  					<a href="index.php?module=bookmarks&ajax=1&page=random"><img width="64" height="64" src="modules/images/random.png" alt="Random Page" Title="Random Page"></a>
  				</li>
  				<li>
					<a href="javascript:void(window.open('<?php echo $this->_['siteurl'] ?>index.php?module=bookmarks&page=add_bookmark&wrapping=0&url='+encodeURIComponent(location.href)+'&title='+encodeURIComponent(document.title)
			,'TimeTableTimer','status=no,directories=no,location=no,resizable=yes,menubar=no,width=650,height=330,toolbar=no'));"><img width="64" height="64" src="modules/images/favelet.png" alt="+Add as Bookmark" title="+Add as Bookmark"></a>
  				</li>
  				<li>
					<a href="" id="toggleHidden" class="<?php echo (isset($_SESSION['hidden']) ? "hide" : "show"); ?>"><img src="modules/images/hidden.png" width="64" height="64" alt="Toggle Hidden Bookmarks" title="Toggle Hidden Bookmarks"></a>
  				</li>
  				<li>
					<a href="index.php?module=bookmarks&page=get_thumbnails&ajax=1&output=0" target="_blank" id="update_thumbnails"><img width="64" height="64" src="modules/images/update_thumbnails.png" alt="Update Thumbnails" title="Update Thumbnails"></a>
				</li>
  				</li>
					<a href="index.php?module=universal&page=logout&ajax=1"><img width="64" height="64" src="modules/images/logout.png" alt="Logout" title="Logout"></a>
  				</li>
  			</ul>
        
  			<form method="post" action="" id="searchform">
				<input class="data" type="text" id="search" name="search" maxlength="255" value="<?php echo (isset($this->_['search'])) ? $this->_['search'] : 'Search'; ?>" />
			</form>
			<!--<?php echo $this->_['options']; ?>-->
			
		</navigation>  <!-- end navigation -->
  		<div id="hidden"></div>
  		<div id="left">
  		
			<div class="wrapper margin_b">
			
				<?php echo $this->_['menu']; ?>

			</div> <!-- end <div class="wrapper"> -->
			
  		</div> <!-- end <div id="left"> -->

  		<div id="right">

			<div id="container">

				<div id="content">

					<?php echo $this->_['content']; ?>

				</div>  <!-- end <div id="content"> -->
				
				<div class="footer margin_t">More awesome software at <a class="rm_layout" href="http://www.renemichalke.de">renemichalke.de</a></div>
				
			</div>  <!-- end <div id="container"> -->

  		</div>  <!-- end <div id="right"> -->
  		<div id="status">
  			<p></p>
		</div>
  		<script src="extensions/jquery-2.1.4.min.js" type="text/javascript"></script>
  		<script src="extensions/jquery.ui/jquery-ui.js" type="text/javascript"></script>
  		<script src="extensions/jquery.contextMenu/jquery.contextMenu.js" type="text/javascript"></script>
  		<script src="extensions/jquery.alerts/jquery.alerts.js" type="text/javascript"></script>
  		<script src="extensions/native.history.js" type="text/javascript"></script>
  		<script src="extensions/sha256.js" type="text/javascript"></script>
  		<script src="modules/scripts/universal.script.js" type="text/javascript"></script>
  		<script src="modules/scripts/bookmarks.script.js" type="text/javascript"></script>
  	
  		<script type="text/javascript">
  			
   			History.pushState({ target: 'content', origin: getLocation() }, "RM Internet Suite", getLocation() );
  			
  			var module = '<?php echo $this->_['module']; ?>';
  			var prev = getLocation();
  			
  			var load_stuff = function( target, url ) {
  			
  				$("#hidden").load('index.php?module=universal&ajax=1&page=urlHasParams&windowLocation=' + encodeURIComponent( url ), function ( bool ) {	
  					
  					$("#hidden").load('index.php?module=universal&ajax=1&page=changesUrlModule&windowLocation=' + encodeURIComponent( url ) + '&origin=' + encodeURIComponent(prev), function ( result ) {
  					
  						prev = getLocation();
  					
  						var seperator = bool == '1' ? '&' : '?';
  						
  						if(result != '0') $('#left .wrapper').load(result);
  						
  						$(target).load(url + seperator + 'wrapping=0', function ( content ) {	
						
							if(module == 'bookmarks')
								if(! $('#search').is(":focus") ) searchVal(module);					
					
							window.scrollTo(0,0);
							stopLoading(target);
	
						});	
  					
  					});
											
				});	
  			};
  			
  			//load_stuff('#content', getLocation());
  			
	  		History.Adapter.bind(window, 'statechange', function() {
	  		
				if(loading == 1) return 0;
				
				var State = History.getState();
				startLoading('#'+State.data.target);
	
				$('#hidden').load('index.php?module=universal&ajax=1&page=updateSessionUrl&url=' + encodeURIComponent( State.url ), function( result ) {
					
					if(result == 1) {  
					
						load_stuff("#" + State.data.target, State.url);
					
					} else {
					
						jAlert(result, 'Error');
						stopLoading('#'+State.data.target);

					}
											
				});    
			});
		</script>	
  	</body>
</html>
