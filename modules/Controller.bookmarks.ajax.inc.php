<?php

	require_once('classes/Foo.class.php');
	require_once('classes/Auth.class.php');
	require_once('classes/DB.class.php');
	require_once('classes/Url.class.php');
	require_once('classes/Misc.class.php');
	require_once('classes/Sanitize.class.php');
	
	class Bookmarks extends Foo {
	
		public $request = null; // our request-object; we store here the data from $_POST, $_GET and $_FILES
		
		/**
		* Constructor
		*
		* @param Array $request, merged array of $_GET & $_POST & $_FILES
		*/
		public function __construct($request) {
		
			new Auth();
			$this->request = $request;
			
			// Due to the use of jQuery.serialize() the POST data is being transferred in a plain string, so we have to manually make an array out of it:
			if(@$this->request['post'])	$this->request['post'] = Qualify::StringToPost($this->request['post']);

		} // <!-- end function ’__construct()’ -->
		
		/**
		* Running the actual application
		*/
		public function control() {
		
			switch(@$this->request['page']) {
			
				default:
				
					die('default. This is the place where nothing happens: '. $this->request['page']);
			
				break; // <!-- end ’default’ -->
				
				case 'load':
				
					if(!isset($_SESSION['url_bookmarks'])) {
						$_SESSION['url_bookmarks'] = Url::getCurrentUrl();
					}
						
					$url = html_entity_decode($_SESSION['url_bookmarks']);
					$aUrl = Url::explode($url);

					
				
					switch ($this->request['id']) {

						default:
							die('Error: No $this->request["id"] not set.');
						break;
	
						case 'all_bookmarks':
		
							$url = Url::setParams( $url, array(), array('module', 'search') );
							$url = Url::updateParams( $url, array('type' => 'all') );
		
						break;

						case 'tagcloud':
							
							$url = Url::setParams( $url, array(), array('module', 'bookmarks') );
							$url = Url::updateParams( $url, array('page' => 'tagcloud') );
		
						break;
		
						case 'trashed':
							
							$url = Url::setParams( $url, array(), array('module', 'search') );
							$url = Url::updateParams( $url, array('trashed' => '1') );
		
						break;
		
						case 'clear_all_filter':
			
							$url = Url::removeParams( $url, array('jump', 'type', 'tag', 'month', 'year', 'status') );
							$url = Url::updateParams( $url, array('type' => 'all') );
			
						break;
						
						case 'filter_not_tagged':
							
							$url = Url::removeParams( $url, array('jump', 'tag', 'page', 'status') );
							$url = Url::updateParams( $url, array('type' => 'notag') );
						
						break;
						
						case 'filter_tag':
						
							$this->isInRequest( 'tag' );
						
							$url = Url::removeParams( $url, array('jump', 'month', 'year', 'page', 'status', 'type', 'tag') );
							$url = Url::updateParams( $url, array('type' => 'tag', 'tag' => $this->request['tag']) );

						break;
						
						case 'filter_month':
						
							$this->isInRequest( array('month', 'year') );
						
							$url = Url::removeParams( $url, array('jump', 'tag', 'page', 'status') );
							$url = Url::updateParams( 
								$url, 
								array(
									'type' => 'month', 
									'month' => $this->request['month'], 
									'year' => $this->request['year']
								) 
							);
						
						break;
						
						case 'filter_year':
						
							$this->isInRequest( 'year' );
						
							$url = Url::removeParams( $url, array( 'jump', 'tag', 'page', 'status') );
							$url = Url::updateParams( $url, array('type' => 'year', 'year' => $this->request['year'] ) );
						
						break;
						
						case 'empty_trashed':
						
							/**
							* List of affected bookmarks
							*/
							$query = 'SELECT * FROM bookmark_items WHERE trashed = 1 AND uid = :uid';
							$params = array(':uid' => $_SESSION['uid']);
							$data = DB::get($query, $params);


							/**
							* Delete Thumbnails
							*/
							foreach($data as $item) {
								if ($item['thumbnail'] !== "") {

									$server_path = Url::getCurrentPath();

									$filename = $server_path.'thumbs/'.(int)$_SESSION['uid'].'/'.$item['thumbnail'].'.png';
									@unlink($filename);
								}
							}

							/**
							* Delete bookmarks
							*/	
							$query = 'DELETE FROM bookmark_items WHERE trashed = 1 AND uid = :uid';
							DB::execute($query, $params);

							/**
							* Delete associated tags vom relation table
							*/
							// happens via foreign key constraint


							/**
							* Delete not used tags vom bookmarks_tags
							*/
							$query = 'SELECT * FROM bookmark_tags WHERE uid = :uid';
							$data = DB::get($query, $params);

							foreach($data as $item) {
								$query = 'SELECT COUNT(id) as quantity 
											FROM rel_bookmarks_tags
											WHERE id_b = :id_b LIMIT 1';
								$params = array(
									':id_b' => $item['id']
								);
								$quantity = DB::getOne($query, $params);

								if($quantity['quantity'] === "0") {
									$query = 'DELETE FROM bookmark_tags WHERE id = :id AND uid = :uid';
									$params = array(
										':id' => $item['id'],
										':uid' => $_SESSION['uid']
									);
									DB::execute($query, $params);
								}
							}

						break;
						
						case 'sort_date':
							
							if(@$aUrl['query_params']['jump'] != 'all')
								$url = Url::removeParams( $url, array('jump') );
							$url = Url::updateParams( $url, array('sort' => 'date') );
						
						break;
						
						case 'sort_title':
							
							if(@$aUrl['query_params']['jump'] != 'all')
								$url = Url::removeParams( $url, array('jump') );
							$url = Url::updateParams( $url, array('sort' => 'title' ) );
						
						break;
						
						case 'sort_hits':
							
							if(@$aUrl['query_params']['jump'] != 'all')
								$url = Url::removeParams( $url, array('jump') );
							$url = Url::updateParams( $url, array('sort' => 'hits' ) );
						
						break;
						
						case 'sort_last_hit':
						
							if(@$aUrl['query_params']['jump'] != 'all')
								$url = Url::removeParams( $url, array('jump') );
							$url = Url::updateParams( $url, array('sort' => 'last_hit' ) );
							
						break;
						
						case 'order':
						
							$this->isInRequest( 'order' );
						
							if(@$aUrl['query_params']['jump'] != 'all')
								$url = Url::removeParams( $url, array('jump') );
							$url = Url::updateParams( $url, array('order' => $this->request['order'] ) );
							
						break;
						
						case 'page':
						
							$this->isInRequest( 'jump' );
							
							$url = Url::updateParams( $url, array('jump' => $this->request['jump'] ) );
						
						break;	
						
						case 'search':
						
							$this->isInRequest( 'search' );
							
							if(@$aUrl['query_params']['jump'] != 'all')
								$url = Url::removeParams( $url, array('jump') );
							$url = Url::updateParams( $url, array('search' => urlencode($this->request['search']) ) );
						
						break;
						
						case 'favelet':
						
							$url = Url::setParams( $url, array(), array('module') );
							$url = Url::updateParams( $url, array('page' => 'favelet', 'module' => 'bookmarks') );
						
						break;	
						
						case 'change_module':
						
							$this->isInRequest('new_module');
						
							$url = Url::setParams( $url, array() );
							$url = Url::updateParams( $url, array('module' => $this->request['new_module'] ) );
							
						break;

						case 'get_thumbnails':
						break;
						

					}

					
					$_SESSION['url_bookmarks'] = $url;
					
					if(isset($this->request['redirect']) && $this->request['redirect'] == 0)
						die($_SESSION['url_bookmarks']);
					else
						header('Location: '.$_SESSION['url_bookmarks']);
				
				break;
				
				case 'urlGetSearch':
				
					$this->isInRequest('windowLocation');
					
					$url = Url::explode($this->request['windowLocation']);
					die(@urldecode($url['query_params']['search']));
					
				break;
				
				case 'go':
				
					$this->isInRequest('id');

					// Check if user has right to perform that action
					if(!Misc::HasRight($this->request['id'], "bookmark_items")) {
						die('Error: Permission Denied!');
					}
				
					$query = 'SELECT hits, url FROM bookmark_items WHERE id = :id LIMIT 1';
					$params = array(
						':id' => $this->request['id'],
					);
					$data = DB::getOne($query, $params);
					
                	$options = array(/*'andtoamp', */'htmlspecialchars', 'utf8_decode', 'stripslashes');
					Sanitize::process_array($data, $options);	                    
					
					if(!$data) die('Software Error: query returned no results.');
					
					$data['hits']++;
					
					$query = 'UPDATE bookmark_items SET hits = :hits, last_hit = NOW(), thumbnail_update = 1 WHERE id = :id';
					$params = array(
						':id' => $this->request['id'],
						':hits' => $data['hits']
					);
					DB::execute($query, $params);

					
					header('Location: '.$data['url']);
				
				break; // <!-- end case ’go’ -->
				
				case 'delete':
				
					$this->isInRequest('id');

					// Check if user has right to perform that action
					if(!Misc::HasRight($this->request['id'], "bookmark_items")) {
						die('Error: Permission Denied!');
					}
					
					$query = 'SELECT
								trashed
							  FROM bookmark_items 
							  WHERE id = :id';
					$params = array(':id' => $this->request['id']);
					$data = DB::getOne($query, $params);
					
					if($data['trashed'] == 0) $trasheding = 1;
					elseif($data['trashed'] == 1) $trasheding = 0;
					
					$query = 'UPDATE bookmark_items SET trashed = :trasheding WHERE id = :id';
            		$params = array(
            			':trasheding' => $trasheding,
            			':id'   => $this->request['id']
            		);
            		DB::execute($query, $params);
            		
            		die('1');
            		
				break; // <!-- end case ’delete’ -->
				
				case 'changeUrl1':
				
					$this->isInRequest('id');

					$query = 'SELECT url FROM bookmark_items WHERE id = :id LIMIT 1';
					$params = array(
						':id' => $this->request['id']
					);
					
					$data = DB::get($query, $params);

					$options = array('htmlspecialchars_decode', 'utf8_decode', 'stripslashes');
					Sanitize::process_array($data, $options);	                    
					
					die($data[0]['url']);
					
				break; // <!-- end case ’change_url_1’ -->
				
				case 'changeUrl2':
		
					$this->isInRequest( array('id', 'url') );

					// Check if user has right to perform that action
					if(!Misc::HasRight($this->request['id'], "bookmark_items")) {
						die('Error: Permission Denied!');
					}
					
                	$options = array('htmlspecialchars_decode');
					Sanitize::process_array($this->request, $options);	                    
					
					$query = 'UPDATE bookmark_items SET url = :url WHERE id = :id';
                	$params = array(
                		':url' => $this->request['url'],
                		':id'   => $this->request['id']
                	);
                	DB::execute($query, $params);
                	
                	die('1');
                	
				break; // <!-- end case ’change_url_2’ -->
		
			
				
				case 'toggleVisibility':
					
					$this->isInRequest('id');

					// Check if user has right to perform that action
					if(!Misc::HasRight($this->request['id'], "bookmark_items")) {
						die('Error: Permission Denied!');
					}
					
					$query = 'SELECT
								hidden
							  FROM bookmark_items
							  WHERE id = :id';
					$params = array(
						':id' => $this->request['id']
					);
					$data = DB::getOne($query, $params);

					
					if($data['hidden'] === 0) {
					
						$query = 'UPDATE bookmark_items SET hidden = 1 WHERE id = :id';
                		DB::execute($query, $params);
						
						if(isset($_SESSION['hidden']) AND $_SESSION['hidden'] == 1) 
							echo "transparent";
						else 
							echo "hide";
						
					} elseif($data['hidden'] === 1) {
					
						$query = 'UPDATE bookmark_items SET hidden = 0 WHERE id = :id';
		           		DB::execute($query, $params);
                		
						echo "show";
					}
					
				break; // <!-- end case ’toggleVisibility’ -->
				
				
				case 'edit':

					$this->isInRequest( array('id', 'title') );

					// Check if user has right to perform that action
					if(!Misc::HasRight($this->request['id'], "bookmark_items")) {
						die('Error: Permission Denied!');
					}

                	$options = array('htmlspecialchars_decode');
					Sanitize::process_array($this->request, $options);	                    

					$query = 'UPDATE bookmark_items SET title = :title WHERE id = :id';
                	$params = array(
                		':title' => $this->request['title'],
                		':id'   => $this->request['id']
                	);
                	DB::execute($query, $params);


                	// get masked new title
                	$query = 'SELECT title FROM bookmark_items WHERE id = :id';
                	$params = array(
                		':id' => $this->request['id']
                	);
                	$data = DB::get($query, $params);

                	$options = array('htmlspecialchars', 'utf8_decode', 'stripslashes', 'no_js_injection');	
					Sanitize::process_array($data, $options);	                    
                	
                	die('1#' . $data[0]['title']);
                	
				break; // <!-- end case ’edit’ -->

				case 'getTags':
					
					$this->isInRequest('id');
				
					// Check if user has right to perform that action
					if(!Misc::HasRight($this->request['id'], "bookmark_items")) {
						die('Error: Permission Denied!');
					}

					$query = 'SELECT * FROM rel_bookmarks_tags JOIN bookmark_tags ON rel_bookmarks_tags.id_a = :id_a AND bookmark_tags.id = rel_bookmarks_tags.id_b ORDER BY bookmark_tags.name ASC';
					$params = array(
						':id_a' => $this->request['id']
					);
					
					$data = DB::get($query, $params);


					$tags = '';
					$run = 0;
					foreach($data as $item)
						$tags .= ($run++ != 0 ? '#' : '').$item['name'];
						
					die($tags);
					
				break; // <!-- end case ’get_tags’ -->
				
				case 'getTagName':
					
					$this->isInRequest('id');

					// Check if user has right to perform that action
					if(!Misc::HasRight($this->request['id'], "bookmark_tags")) {
						die('Error: Permission Denied!');
					}
				
					$query = 'SELECT name FROM bookmark_tags WHERE id = :id LIMIT 1';
					$params = array(
						':id' => $this->request['id']
					);
					
					$data = DB::getOne($query, $params);

					die($data['name']);
					
				break; // <!-- end case ’get_tags’ -->
				
				case 'getTitle':
					
					$this->isInRequest('id');

					// Check if user has right to perform that action
					if(!Misc::HasRight($this->request['id'], "bookmark_items")) {
						die('Error: Permission Denied!');
					}
				
					$query = 'SELECT title FROM bookmark_items WHERE id = :id LIMIT 1';
					$params = array(
						':id' => $this->request['id']
					);

					$data = DB::get($query, $params);

                	$options = array('htmlspecialchars_decode', 'utf8_decode', 'stripslashes', 'no_js_injection');
					Sanitize::process_array($data, $options);	                    
					
					die($data[0]['title']);
					
				break; // <!-- end case ’get_tags’ -->

				case 'tags':
					
					$this->isInRequest( array( 'tags', 'id' ) );

					// Check if user has right to perform that action
					if(!Misc::HasRight($this->request['id'], "bookmark_items")) {
						die('Error: Permission Denied!');
					}
					
					$tags = explode("#", $this->request['tags']);


					// Remove empty tags in case the user used more than one #
					foreach($tags as $key => $item) {
					
						$tags[$key] = trim($item);
						if(!strcmp($item, ''))
							unset($tags[$key]);
							
					}
					
					$tags = array_unique($tags);

					if(sizeof($tags) == 0) {
						$tags[0] = 'Not Tagged';
					}

					// Check if tag has to be added
					foreach($tags as $item) {
					
						if($item === "Not Tagged") { 

						} else {

							$query = 'SELECT COUNT(*) as quantity FROM bookmark_tags WHERE uid = :uid AND name = :name LIMIT 1';
							$params = array(
								':uid' => $_SESSION['uid'],
								':name' => $item
							); $data = DB::getOne($query, $params);

						
							if($data['quantity'] == 0) { // tag has to be added to both table tags and table relation_bookmarks_tags
							
								// add it to table tags
								$query = 'INSERT INTO bookmark_tags (uid, name) VALUES (:uid, :name)';
								$params = array(
									':uid' => $_SESSION['uid'],
									':name' => $item
								); DB::execute($query, $params);
								
							} 

						}

						// check if tag has to be added to table relation_bookmarks_tags
						
						$query = 'SELECT id FROM bookmark_tags WHERE (uid = :uid OR uid = 0) AND name = :name LIMIT 1';
						$params = array(
							':uid' => $_SESSION['uid'],
							':name' => $item
						); $id_b = DB::getOne($query, $params);
					
						$query = 'SELECT 
									COUNT(*) as quantity
								  FROM 
								  	rel_bookmarks_tags 
								  JOIN 
								  	bookmark_tags 
								  ON 
								  	rel_bookmarks_tags.id_a = :id_a
								  	AND rel_bookmarks_tags.id_b = :id_b 
								  	AND bookmark_tags.uid = :uid
								  LIMIT 1';

						$params = array(
							':id_a' => $this->request['id'],
							':id_b' => $id_b['id'],
							':uid' => $_SESSION['uid']
						); 

						$data = DB::getOne($query, $params);

						if($data['quantity'] == 0) { // tag has to be added to table relation_bookmark_tags
						
							$query = 'INSERT INTO rel_bookmarks_tags (id_a, id_b) VALUES (:id_a, :id_b)';
							unset($params[':uid']);
							DB::execute($query, $params);
													
						}
						
					}

					// Check if tag has to be deleted from table tags
					$query = 'SELECT * FROM rel_bookmarks_tags JOIN bookmark_tags ON rel_bookmarks_tags.id_a = :id AND bookmark_tags.id = rel_bookmarks_tags.id_b AND (bookmark_tags.uid = :uid OR bookmark_tags.uid = 0)';
					$params = array(
						':id' => $this->request['id'],
						':uid' => $_SESSION['uid']
					); 
					$tags_old = DB::get($query, $params);
					
					foreach($tags_old as $item) {
					
						/*echo '!in_array('.$item['title'].', Array: ';
						Misc::pre($tags);
						echo '):<br>';*/
						
						if(!Misc::in_arrayi($item['name'], $tags)) { // tag has been removed from current bookmark

							// delete it from table relation_bookmarks_tags
							$query = 'SELECT id FROM bookmark_tags WHERE (uid = :uid OR uid = 0) AND name = :name LIMIT 1';
							$params = array(
								':uid' => $_SESSION['uid'],
								':name' => $item['name']
							); 
							$id_b = DB::getOne($query, $params);

							$query = 'DELETE FROM rel_bookmarks_tags WHERE id_b = :id_b AND id_a = :id_a';
							$params = array(
								':id_a' => $this->request['id'],
								':id_b' => $id_b['id']
							); 

							DB::execute($query, $params);

							// Check if tag is used by other bookmarks
							$query = 'SELECT 
										COUNT(*) as quantity 
									  FROM 
									  	rel_bookmarks_tags 
									  WHERE
									  	id_a != :id_a AND 
									  	id_b = :id_b 
									  LIMIT 1';
							$params = array(
								':id_a' => $this->request['id'],
								':id_b' => $id_b['id']
							); $data = DB::getOne($query, $params);
							
							if($data['quantity'] == 0) { // tag isn't being used by other bookmarks, therefore it can be deleted from table tags
							
								$query = 'DELETE FROM bookmark_tags WHERE uid = :uid AND name = :name AND id != 0';
								$params = array(
									':uid' => $_SESSION['uid'],
									':name' => $item['name']
								); DB::execute($query, $params);
								
							} 
						}
					}
				
                	die('1');
                	
				break; // <!-- end case ’tags’ -->
				
				
				case 'showHidden':
					
					$this->isInRequest('password');
					
					$query = 'SELECT password FROM users WHERE id = :uid LIMIT 1';
					$params = array(
						':uid' => $_SESSION['uid']
					);
					$data = DB::getOne($query, $params);
				
					if(strcmp($this->request['password'], $data['password']))
						die('0');
					else  {
					
						$_SESSION['hidden'] = 1;
						die('1');
						
					}
					
				break; // <!-- end case ’show_hidden’ -->

				case 'hideHidden':
				
					unset($_SESSION['hidden']);
					
				break; // <!-- end case ’hide_hidden’ -->
				
				case 'random':

					$query = 'SELECT id, hidden FROM bookmark_items WHERE uid = :uid';
					$params = array(':uid' => $_SESSION['uid']);
					$data = DB::get($query, $params);

					$rand = rand(0, sizeof($data)-1); 
					if(!isset($_SESSION['hidden']))
						while($data[$rand]['hidden'] == 1) $rand = rand(0, sizeof($data)-1);
					
					header('Location: index.php?module=bookmarks&ajax=1&page=go&id='.$data[$rand]['id']);
				
				break; // <!-- end case ’random’ -->

				case 'get_thumbnails':

					// disables script slowing down other php jobs
					session_write_close();

					// Get the thumbnails of ALL bookmarks of EVERY user
					$output = @$this->request['output'] === 1 ? true : false;

					if(isset($this->request['id'])) {

						// Check if user has right to perform that action
						if(!Misc::HasRight($this->request['id'], "bookmark_items")) {
							die('Error: Permission Denied!');
						}

						$query = 'SELECT * FROM bookmark_items WHERE id = :id';
						$params = array(
							':id' => $this->request['id']
						);
						$data = DB::get($query, $params);
					} else {
						$query = 'SELECT * FROM bookmark_items WHERE uid = :uid AND hidden = 0 AND trashed = 0 AND thumbnail_update = 1 ORDER BY date';
						$params = array(
							':uid' => $_SESSION['uid']
						);
						$data = DB::get($query, $params);
					}
					
					foreach($data as $key => $item) {						

						// Check if user has right to perform that action
						if(!Misc::HasRight($item['id'], "bookmark_items")) {
							die('Error: Permission Denied!');
						}

						if($output)
							echo '<hr/><hr/><hr/>Processing '.$item['id'].' – '.$item['title'].' – '.$item['url'].' …<br>';
						
						$curl = curl_init();
						curl_setopt($curl, CURLOPT_URL, $item['url']);
						curl_setopt($curl, CURLOPT_HEADER, true);
						curl_setopt($curl, CURLOPT_TIMEOUT, 5);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
						curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.57 Safari/537.36');
					
						$data = curl_exec($curl);
						$new_url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
						curl_close($curl);
							
						if($output)
						if($item['url'] != $new_url)					
							echo '<br>Redirecting to '.$new_url.' … <br>';
						
						$url_valid = filter_var($new_url, FILTER_VALIDATE_URL);		
						if($url_valid == '') // Invalid URL 
							continue;
						
						$a_url = Url::explode($url_valid);

						// Only get Images from http:// or https://
						if(strcasecmp($a_url['scheme'], 'http') && strcasecmp($a_url['scheme'], 'https')) 
							continue;

						// Don't try to load Images from localhost, obv won't work
						// if(Url::is_local_host($url_valid)) 
						// 	continue;


						$server_path = Url::getCurrentServerPath();
						$folder = $server_path.'thumbs/'.$item['uid'].'/';
						$file_old =  $folder.$item['thumbnail'].'.png';
					
						$hash = hash("sha256", $item['url'].time());
						$file_new =  $folder.$hash.'.png';


						// check if url points to a image instead of webpage
						$ending = explode(".", $url_valid);
						if(strcasecmp($ending[sizeof($ending)-1], "jpg") === 0
							|| strcasecmp($ending[sizeof($ending)-1], "png") === 0
							|| strcasecmp($ending[sizeof($ending)-1], "gif") === 0
							|| strcasecmp($ending[sizeof($ending)-1], "jpeg") === 0
							) {
							copy($url_valid, $file_new);
						} else {
							Misc::getImage($url_valid, $hash, $folder, $output);
						}
						
						if(file_exists($file_new)) {
						
							if(file_exists($file_old))
								unlink($file_old);
							
							$query = 'UPDATE bookmark_items SET thumbnail = :thumbnail WHERE id = :id';
							$params = array(
								':thumbnail' => $hash,
								':id' => $item['id']
							); DB::execute($query, $params);
							
							if($output)
							echo '<br><br>Success.<br>';
							
						} else 
						if($output)
						echo '<br>Error: Could not save image.<br>';
						
						$query = 'UPDATE bookmark_items SET thumbnail_update = 0 WHERE id = :id';
						$params = array(
							':id' => $item['id']
						); DB::execute($query, $params);
						
						flush();						
					}
				
					die('Script Finished');
					
				break; // <!-- end case ’get_thumbnails’ -->
				
			} // <!-- end ’switch(@$this->request['page'])’ -->
		
		} // <!-- end function ’control()’ -->
		
		/**
		* Displaying the content
		*
		* @return String, the generated html code
		*/
		public function display() {
		} // <!-- end function ’display()’ -->
		
	} // <!-- end class ’Controller’ -->   
?>
