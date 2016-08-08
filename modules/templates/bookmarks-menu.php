<h2 class="rm_layout"><a href="index.php?module=bookmarks&ajax=1&page=load&id=clear_all_filter" target="content" class="url rm_layout">Clear all Filter</a></h2>

<h2 class="margin rm_layout"><strong>Filter by Tag:</strong></h2>

<ul id="tags" class="margin_l">
	<li class="list-style-n"><a href="index.php?module=bookmarks&ajax=1&page=load&id=filter_not_tagged" target="content" class="url rm_layout">Not Tagged</a></li><br>
	<?php foreach($this->_['folderbytag'] as $key => $item) { ?>	
		<li class="list-style-n<?php if(@$item['clHidden'] == 1) echo ' clHidden';?>">
			<a href="index.php?module=bookmarks&ajax=1&page=load&id=filter_tag&tag=<?php echo urlencode($item['id']); ?>" target="content"  class="url rm_layout">
				<?php echo $item['name']; ?>
			</a>
		</li>
	<?php } ?>
</ul>


<h2 class="margin rm_layout"><strong>Filter by Month:</strong></h2>

<ul class="margin_l">

	<?php foreach($this->_['folderbymonth'] as $key => $item) { 
				$string_split = explode(" - ", $item);
				$year = trim($string_split[0]);
				$month = trim($string_split[1]); ?>
	
		<li>
			<a href="index.php?module=bookmarks&ajax=1&page=load&id=filter_month&year=<?php echo $year; ?>&month=<?php echo $month; ?>" target="content" class="url rm_layout">
				<?php echo $item; ?>
			</a>
		</li>

	<?php } ?>
</ul>

<h2 class="margin rm_layout"><strong>Filter by Year:</strong></h2>

<ul class="margin_l">
	<?php foreach($this->_['folderbyyear'] as $key => $item) { ?>
		<li>
			<a href="index.php?module=bookmarks&ajax=1&page=load&id=filter_year&year=<?php echo $item; ?>" target="content" class="url rm_layout">
				<?php echo $item; ?>
			</a>
		</li>
	<?php } ?>
</ul>
