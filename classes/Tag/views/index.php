<?php foreach($this->_['data'] as $key => $item) { ?>
<a href="index.php?page=tag&id=<?php echo $item['id']; ?>"><?php echo $item['name']; ?></a>
<?php } ?>
