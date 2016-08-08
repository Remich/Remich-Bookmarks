# Remich Bookmarks

Remich Bookmarks is a website bookmarking tool which runs as a webapplication. Therefore it works across browsers and you are not bound to one single webbrowser when accessing and managing your bookmarks.

## FEATURES:
	* bookmarks are added via a bookmarklet / favelet
	* hit counter
	* extensive filter and sorting capabilities
	* open random bookmark
	* live search
	* tag support
	* thumbnail previews

## INSTALLATION
	see INSTALL.md

## KNOWN ISSUES
	* Multi-User Support is not completely tested, USE AT YOUR OWN RISK
	* URGENT: Make sure that all SQL-Queries get executed with the correct Userhash
	* URGENT: Mask parameters in filelocations
		* e.g. in bookmarks.ajax.inc.php in empty_trash...
	* Fix Javascript Injection by not masking the data before the output
		only for text input fields, i think
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

### TODO
	* Find a better name
	* Implement classes/Url.class.php in Javascript, in order to save Ajax-Calls
	* Remove unused code from classes/Navigation.class.php
	* Check in which Browsers this software works
	* Write a Manual / Tutorial
	* Add User Preferences
		** Change Password / Email
		** Delete Account	
	* Implement shift selection
	* Import / Export Bookmarks
	* Add RSS Reader
	* dynamic page title in <title>
	* Responsives
	* Security Feature: Bouncer = Class which acts like Auth and permanently checks if the $_SESSION data has the expected type / value
		* by this way you can avoid manually escaping $_SESSION data
	* Brute Force Protection on Login Page
	* Implement Auto Scrolling
	* Remove Current Clumsy Mousewheel Scrolling
	* Implement Child / Parent Tags
	* Improve Performance with ToggleVisibility & Delete/Restore Items
	* Lock Mouse Down & Mouse Up to same item
	* Toggle mode if item is being hidden/tagged and removed/deleted