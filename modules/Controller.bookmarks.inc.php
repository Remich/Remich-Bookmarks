<?php

	require_once('classes/Foo.class.php');
	require_once('classes/View.class.php');
	require_once('classes/Auth.class.php');
	require_once('classes/DB.class.php');
	require_once('classes/Url.class.php');
	require_once('classes/Misc.class.php');
	require_once('classes/Pages.class.php');
	require_once('classes/Navigation.class.php');
	require_once('classes/Sanitize.class.php');

	
	class Bookmarks extends Foo {
	
		public $request = null; // our request-object; we store here the data from $_POST, $_GET and $_FILES
		private $view = null; // our view object
		
		private $trashed = 0; // set to true in order to display only files, which have been moved to trashed
		private $hidden = 0; // set to true in order to display also files, which have been hidden
		private $search_word = NULL;
		
		/**
		* Constructor
		*
		* @param Array $request, merged array of $_GET & $_POST & $_FILES
		*/
		public function __construct($request) {
		
			new Auth();
			$this->request = $request;
			$this->view = new View();
			
			/**
			* Checking Session Variables on their status
			*/
			Url::initAndFixPath();

			/**
			* Checking Request Variables on their status
			*/
			if(!isset($this->request['page'])) $this->request['page'] = 'content';
			if(!isset($this->request['wrapping'])) $this->request['wrapping'] = 1;
			$this->trashed = !isset($this->request['trashed']) ? 0 : 1;
			$this->hidden = !isset($_SESSION['hidden']) ? 0 : 1;
			
			if(isset($this->request['search'])) {
			
				$this->request['search'] = urldecode($this->request['search']);
				if(!strcmp(trim($this->request['search']), '')) unset($this->request['search']);
				else {
					$arr = explode(' ', $this->request['search']);

					$this->search_word = '%';
					foreach($arr as $item) 
						$this->search_word .= $item.'%';

					$this->view->assign('search', htmlspecialchars(stripslashes($this->request['search'])));
				}
				
			}
			
			$this->view->assign('trashed' , $this->trashed);
			$this->view->assign('page_title', Config::getOption('title'));
			$this->view->assign('siteurl', Url::getCurrentPath());
			$this->view->assign('page', $this->request['page']);
			$this->view->assign('module', $this->request['module']);
			$this->view->assign('hidden', $this->hidden);

		} // <!-- end function ’__construct()’ -->
		
		/**
		* Running the actual application
		*/
		public function control() {
		
			if($this->request['wrapping'] == 1) {
			
				$_SESSION['url_bookmarks'] = Url::getCurrentUrl();				
				
				$this->request['wrapping'] = 0;
				
				if(!isset($this->request['page']))
					$this->request['page'] = 'content';
				$content = new self($this->request);
					$this->view->assign('content', $content->control());
				
				$this->request['page'] = 'menu';
				$menu = new self($this->request);
				$this->view->assign('menu', $menu->control());	
				
				$this->view->setTemplate('universal');
				
			} else {
			
				switch(@$this->request['page']) {
			
					default:
				
						die('This is the place where nothing happens.');
					
					break; // <!-- end case ’default’ --> 
					
					case 'favelet':
						
						$this->view->setTemplate('bookmarks-favelet');
						return $this->view->loadTemplate();
						
					break;
				
					case 'menu':
				
						/**
						* Menu
						*/						
						
						$query = 'SELECT 
									name, id
								  FROM bookmark_tags
								  WHERE uid = :uid
								  ORDER BY name ASC';
						$params = array(
							':uid' => $_SESSION['uid']
						);
						$data = DB::get($query, $params);
			
						// Don't display tags with only hidden files
						foreach($data as $key => $item)
							if(Misc::hasOnlyHiddenItems($item['id']))
								if($this->hidden === 0)
									unset($data[$key]);
								else
									$data[$key]['clHidden'] = 1;
								
					
						$this->view->assign('folderbytag', $data);
				
						$query = 'SELECT 
									id, 
									MONTHNAME(date) as month, 
									YEAR(date) as year 
						  		  FROM bookmark_items  
						  		  WHERE uid = :uid
						  		  ORDER BY date DESC';
						$data = DB::get($query, $params);
					
						$monthlist = array();
						$yearlist = array();
					
						foreach($data as $key => $item) {
					
							$stringi = $item['year']." - ".$item['month'];
						
							if(!in_array($stringi, $monthlist)) $monthlist[] = $item['year']." - ".$item['month'];
						
							$stringoi = $item['year'];
							if(!in_array($stringoi, $yearlist)) $yearlist[] = $item['year'];
						
						}
						 
						$this->view->assign('folderbymonth', $monthlist);
						$this->view->assign('folderbyyear', $yearlist);
						$this->view->setTemplate('bookmarks-menu');

						if(@$this->request['die'] === "1")
							die($this->view->loadTemplate());
						else
							return $this->view->loadTemplate();
					
					break; // <!-- end case ’menu’ -->

					case 'menu_die':

						break;

					/**
					* Content
					*/
					
					case 'content':
				
						/**
						* Checking Request Variables on their status
						*/

						if(!isset($this->request['sort'])) $this->request['sort'] = 'date';
						if(!isset($this->request['order'])) $this->request['order'] = 'DESC';
			
						$this->view->assign('sort', $this->request['sort']);
						$this->view->assign('order', $this->request['order']);
				
						/**
						* Creating the demanded SELECT Query and associated Title
						*/
					
						$title = '';
					
						if(isset($this->request['search']))	
							$title = 'Search results for &raquo;<span>'.stripslashes($this->request['search']).'</span>&laquo; in ';
						
						$title .= ( isset($this->request['search']) ? '' : '' ).'&raquo;<span>'. ( $this->trashed ? 'Trash' : 'All Bookmarks' ).'</span>&laquo;';
					
						switch(@$this->request['type']) {
					
							default:
							case 'all':
						
								$query = 'SELECT * 
										FROM bookmark_items 
										WHERE 1';
										   
							break; // <!-- end ’default’ -->
							
							case 'notag':
							
								$query = 'SELECT
											DISTINCT bookmark_items.*
										FROM
										  	bookmark_items
										JOIN
										  	rel_bookmarks_tags
										ON 
										  	bookmark_items.id = rel_bookmarks_tags.id_a AND
										  	rel_bookmarks_tags.id_b = "0"
										WHERE 1';
							
								$title .= ' <a href="index.php?module=bookmarks&ajax=1&page=load&id=clear_all_filter" target="content" class="url rm_layout">filtered by</a> &raquo;<span>Not Tagged</span>&laquo;';
							
							break; // <!-- end case ’notag’ -->

							case 'tag':

								if(Misc::hasOnlyHiddenItems($this->request['tag']) && $this->hidden === 0) {
									$query = "SELECT * FROM bookmark_items WHERE 0=1";
									$title = 'Error: Forbidden';
								} else {
							
									$query = 'SELECT
												bookmark_items.id, bookmark_items.uid, bookmark_items.hits, bookmark_items.hidden, bookmark_items.thumbnail, bookmark_items.title
											FROM
											  	rel_bookmarks_tags
											JOIN
											    bookmark_items
											ON
												rel_bookmarks_tags.id_a = bookmark_items.id AND
												rel_bookmarks_tags.id_b = '.$this->request['tag'];
											  
									$select = 'SELECT name FROM bookmark_tags WHERE id = :tid LIMIT 1';
									$params = array(
										':tid' => $this->request['tag']
									); 
									$tag_title = DB::getOne($select, $params);

								
									if(sizeof($tag_title)  === 0)
										$title = 'Error: Tag Not Found';
									else
										$title .= ' <a href="index.php?module=bookmarks&ajax=1&page=load&id=clear_all_filter" target="content" class="url rm_layout clear_filter" title="Clear Filter"> filtered by</a> &raquo;<span>Tag &rsaquo; '. $tag_title['name'] .'</span>&laquo;';

								}
							
							break; // <!-- end case ’tag’ -->
						
							case 'month':
						
								$months = array('January' => 1, 
												'February' => 2, 
												'March' => 3,
												'April' => 4, 
												'May' => 5, 
												'June' => 6, 
												'July' => 7, 
												'August' => 8, 
												'September' => 9, 
												'October' => 10, 
												'November' => 11, 
												'December' => 12);
						
								$query = 'SELECT *,
											MONTHNAME(date) as month,
											YEAR(date) as year
										FROM bookmark_items
										WHERE YEAR(date) = :year
											AND MONTH(date) = :month';
							
								$title .= ' filtered by &raquo;<span>Month &rsaquo; '. $this->request['month'] .' '. $this->request['year'].'</span>&laquo;';
							
							break; // <!-- end case ’month’ -->
						
							case 'year':
						
								$query = 'SELECT *,
											YEAR(date) as year
										FROM bookmark_items
										WHERE
									   		YEAR(date) = :year';
										   
								$title .= ' filtered by &raquo;<span>Year &rsaquo; '.$this->request['year'].'</span>&laquo;';
							
							break; // <!-- end case ’year’ -->
						
						} // <!-- end ’switch(@SESSION['type'])’ -->
					


						/**
						* Modify Query
						*/

						// userid 
						$query .= ' AND uid = :uid';

						// trash, hidden, search
						$query .= ' AND trashed = :trashed'.
						  	   		( $this->hidden === 0 ? ' AND hidden = 0' : '').
						  	   		( $this->search_word !== NULL ? ' AND title LIKE :search_word' : '');

						// order attribute
						switch($this->request['sort']) {
							case 'date':
								$query .= ' ORDER BY date';
							break;
							case 'title':
								$query .= ' ORDER BY title';
							break;
							case 'hits':
								$query .= ' ORDER BY hits';
							break;
							case 'last_hit':
								$query .= ' ORDER BY last_hit';
							break;
						}

						switch($this->request['order']) {
							case 'DESC':
								$query .= ' DESC';
								break;
							case 'ASC':
								$query .= ' ASC';
								break;
						}

						/**
						* Create Paramaters for SQL Query 
						*/
						$params = array(
							':uid' => $_SESSION['uid'],
							':trashed' => $this->trashed,
						);
						if($this->search_word !== NULL) {
							$params[':search_word'] = $this->search_word;
						}
						if(@$this->request['type'] === 'month') {
							$params[':month'] = $months[$this->request['month']];
							$params[':year'] = $this->request['year'];
						}
						if(@$this->request['type'] === 'year') {
							$params[':year'] = $this->request['year'];
						}

						/**
						* Divide bookmarks into pages, which can be navigated
						*/
						$query_count = "SELECT COUNT(*) as no_of_bookmarks FROM (" . $query . ") as Results";
						$data = DB::getOne($query_count, $params);

						$pages = new Pages($data['no_of_bookmarks'], Config::getOption('items_per_page'));

						if(@!isset($this->request['jump']) OR $this->request['jump'] == "" OR $pages->getStart($this->request['jump']) < 0 )
							$this->request['jump'] = $pages->getPages();
					
						if($this->request['jump'] !== "all") {
							$query .= ' LIMIT '.$pages->getStart($this->request['jump']).', '.Config::getOption('items_per_page');
						}

						$data = DB::get($query, $params);
					
						// Display the bookmarks
						foreach($data as $key => $item) { 
							$data[$key]['title'] = Misc::shortenStr($item['title'], 55, 0);

							if ($item['thumbnail'] !== "")
								$data[$key]['image_file'] = 'thumbs/'.(int)$_SESSION['uid'].'/'.$item['thumbnail'].'.png';
							else
								$data[$key]['image_file'] = '';
						}
						
	                	$options = array('htmlspecialchars', 'utf8_decode', 'stripslashes');
						Sanitize::process_array($data, $options);

						$this->view->assign('pages', $data);
					
						// Display flipping pages
						if($pages->getPages() > 0)
							$this->view->assign('flipping', $pages->getHtml($this->request['jump'], $pages->getPages()));
						else
							$this->view->assign('flipping', '');


						$this->view->assign('title', $title);
					
						$this->view->setTemplate('bookmarks-default');
						
						return $this->view->loadTemplate();
					
					break; // <!-- end case ’content’ -->

					case 'tagcloud':
						require_once("classes/TagCloud/TagCloud.class.php");
						$tags = new TagCloud(/*@$this->_request['tag_id']*/);
						$tags->setUID($_SESSION['uid']);
						$tags->setTableTags("bookmark_tags");
						$tags->setTableRelation("rel_bookmarks_tags");
						$tags->setFontMax(64);
						$tags->setFontMin(9);
						if(isset($this->_request['page']))
							$tags->setPage($this->_request['page']);
						$tags->generate();
						$this->view->assign('content', $tags->display());
						$this->view->setTemplate('bookmarks-tagcloud');
						return $this->view->loadTemplate();
					break;
				
					case 'add_bookmark':
				
						$this->isInRequest(array('title', 'url'));

						DB::execute("START TRANSACTION;");
						
	                	$options = array('htmlspecialchars_decode');
						Sanitize::process_array($this->request, $options);	                    
				
						$query = 'INSERT INTO 
									bookmark_items (uid, title, url, date) 
								  VALUES 
								  	(:uid, :title, :url, NOW())';
		                $params = array(
		                	':uid' => $_SESSION['uid'],
		        			':title' => $this->request['title'],
		        			':url'	 => $this->request['url']
		        		);                                  
		                $res =  DB::execute($query, $params);

		                if($res === NULL) {
		                	DB::execute("ROLLBACK;");
		               		die('Error: Could not Save Bookmark. Please Contact: <a href="mailto:' . Config::getOption("contact") . '">' . Config::getOption("contact") . '</a>');
		                }

		                // Entry immediately as Not-Tagged taggen
		                $query = 'SELECT 
		                			id
		                		  FROM
		                		    bookmark_items
		                		  WHERE
		                		  	id = LAST_INSERT_ID()
		                		  	AND uid = :uid
		                		  LIMIT 1';
		                $params = array(
		                	':uid' => $_SESSION['uid']
		                );
		               	$data = DB::GetOne($query, $params);

		               	if($data === NULL) {
		               		DB::execute("ROLLBACK;");
		               		die('Error: Could not save bookmark. Please contact: <a href="mailto:' . Config::getOption("contact") . '">' . Config::getOption("contact") . '</a>');
		               	}

		                
		                $query = 'INSERT INTO 
									rel_bookmarks_tags (id_a, id_b) 
								  VALUES 
								  	(:bid, 0)';
		                $params = array(
		                	':bid' => $data['id']
		        		);                                  
			            $res = DB::execute($query, $params);

		                if($res === NULL) {
		                	DB::execute("ROLLBACK;");
		               		die('Error: Could not Save Bookmark. Please Contact: <a href="mailto:' . Config::getOption("contact") . '">' . Config::getOption("contact") . '</a>');
		                } else {
			                DB::execute("COMMIT;");
		                }

						return $this->view->setTemplate('bookmarks-add');
					
					break; // <!-- end case ’add_bookmark’ -->
					/*case 'import_html':
				
						$this->isInRequest(array('title', 'url', 'date', 'last'));
				

						$query = 'INSERT INTO bookmark_items (title, url, date, last_hit) VALUES (:title,:url,FROM_UNIXTIME(:date), FROM_UNIXTIME(:last) )';
		                $params = array(
		        			':title' => $this->request['title'],
		        			':url'	 => $this->request['url'],

		        			':date'  => $this->request['date'],
		        			':last'  => $this->request['last']
		        		);                                  
		                DB::execute($query, $params);
						$this->view->setTemplate('add-bookmark');

					
					break; // <!-- end case ’import_html’ -->
				
					case 'delete_duplicates':

				
						$query = 'SELECT * FROM bookmark_items ORDER BY title DESC';
						$data = DB::get($query);
					
						foreach($data as $key => $item) {

					
							echo "<br>Title: ". $item['title'];
						
							$query = 'DELETE FROM bookmark_items WHERE title = :title AND url = :url AND id != :id';
							$params = array(

								':id' => $item['id'],
								':title' => $item['title'],
								':url' => $item['url']
							);
							DB::execute($query, $params);

						
						}
						$this->view->setTemplate('blank');
					
					break; // <!-- end case ’delete_duplicates’ -->*/
				
				} // <!-- end ’switch(@$this->request['page'])’ -->
			
			}
		
		} // <!-- end function ’control()’ -->
		
		/**
		* Displaying the content
		*
		* @return String, the generated html code
		*/
		public function display() {
		
			return $this->view->loadTemplate();
			
		} // <!-- end function ’display()’ -->
		
	} // <!-- end class ’Controller’ -->   
?>
