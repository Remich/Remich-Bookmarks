<h1 class="rm_layout"><?php echo $this->_['title']; ?></h1>
 
<div id="sorting-dock">

	<span id="styling">

		Sort by: <a href="index.php?module=bookmarks&ajax=1&page=load&id=sort_date" target="content" class="url rm_layout"><?php if($this->_['sort'] == 'date') { ?><strong><?php } ?>Date<?php if($this->_['sort'] == 'date') { ?></strong><?php } ?></a> | <a href="index.php?module=bookmarks&ajax=1&page=load&id=sort_title" target="content" class="url rm_layout"><?php if($this->_['sort'] == 'title') { ?><strong><?php } ?>Name<?php if($this->_['sort'] == 'title') { ?></strong><?php } ?></a> | <a href="index.php?module=bookmarks&ajax=1&page=load&id=sort_hits" target="content" class="url rm_layout"><?php if($this->_['sort'] == 'hits') { ?><strong><?php } ?>Hits<?php if($this->_['sort'] == 'hits') { ?></strong><?php } ?></a> | <a href="index.php?module=bookmarks&ajax=1&page=load&id=sort_last_hit" target="content" class="url rm_layout"><?php if($this->_['sort'] == 'last_hit') { ?><strong><?php } ?>Recent Hits<?php if($this->_['sort'] == 'last_hit') { ?></strong><?php } ?></a> – <a href="index.php?module=bookmarks&ajax=1&page=load&id=order&order=DESC" target="content" class="url rm_layout"><?php if($this->_['order'] == 'DESC') { ?><strong><?php } ?>⇣ Descending ⇣<?php if($this->_['order'] == 'DESC') { ?></strong><?php } ?></a> | <a href="index.php?module=bookmarks&ajax=1&page=load&id=order&order=ASC" target="content" class="url rm_layout"><?php if($this->_['order'] == 'ASC') { ?><strong><?php } ?>⇡ Ascending ⇡ <?php if($this->_['order'] == 'ASC') { ?></strong><?php } ?></a>

	</span>

</div> <!-- end <div id="sorting-dock"> -->

<div id="bookmarks">

	<?php if(count($this->_['pages']) > 0) foreach($this->_['pages'] as $key => $item) {  
		if($item['title'] === '') { $item['title'] = 'Unnamed'; } 
	?>
		<a href="index.php?module=bookmarks&ajax=1&page=go&id=<?php echo $item['id']; ?>" id="<?php echo $item['id']; ?>" class="rm_layout <?php if($item['hidden']) echo 'clHidden'; ?>">
			<table>
				<tr>
					
					
						<td class="hits<?php if($item['hits'] == 0) echo ' zero'; ?>">
							
							<span class="em">
								<em>*</em>
								<span class="frame">
									<span class="frame_2">	
										<?php
											if(file_exists($item['image_file'])) 
												echo '<div><img src="'.$item['image_file'].'" width="160" height="100"></div>';
											else {
												echo '<div style="background: url(modules/images/page.png) center center no-repeat;"></div>';
											}
										?>
										<span class="hit">
											<?php echo $item['hits']; ?>
										</span>					
									</span>
								</span>
							</span>
						</td>
				
						
				</tr>
				<tr><td class="title"><?php echo $item['title']; ?></td></tr>
			</table>
			
		  </a>
	<?php } else echo '<p class="noitems">No Items Found</p>'; ?>

</div> <!-- end <div id="page"> -->

<div class="footer">

	<?php if($this->_['flipping'] != '') echo '<br><br>'.$this->_['flipping'].'<br>'; ?>
	
</div> <!-- end <div class="footer"> --> 
	
