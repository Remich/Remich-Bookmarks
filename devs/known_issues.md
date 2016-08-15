## KNOWN ISSUES
	* Multi-User Support is not completely tested, USE AT YOUR OWN RISK
	* Fix Problem of ' in URLs e.g. Carl's Life
		* Go doesn't work
		* Renaming Title doesn't work with AJAX
	* Fix problem, when session has expired and you click on toggle hide items or probably any link
		* Also affects the form destination of login, after clicking on link then logging out 
	* Move all Session stored data to URL stored data, otherwise you can't have two tabs with different sorting types and the navigating those two tabs
	* Fix, when Bookmark is not tagged and then dragged over a tagged, the "Not Tagged"-Tag doesn't get removed
	* Fix, When selecting multiple items and then editing their tags, you get to edit last selected item first... 
	* When editing Tags and inserting "Tagname#F" -> search gets activated ( at least sometimes )
	* TRIM all ajax results!!! OR search for spaces being delivered!!
	* Firefox *
		** Search By Tag not working
	* Wenn #left nach unten gescrollt und $('#left .wrapper').load() oder $('#left .wrapper').html() gecallt wird, dann funktionieren droppen nicht mehr
			weil: coordinates muss neu initialisert werden
			weil: scrolltop neu ermittelt werden muss, für das offset
	* when emptying trash, something throws an error