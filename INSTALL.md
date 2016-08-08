## Installation

(1) Insert your Database credentials and E-Mail in protected/Config.inc.php .
(2) Protect the directory "protected/" via file and group permissions (see 2.1) or .htaccess.
	(2.1) Run the following commands (www-data is the user under which your webserver runs):
		- sudo chmod 700 -R protected/
		- sudo chown www-data:www-data protected
		- sudo chown www-data:www-data thumbs
(3) Create a database (preferrable collation: utf8mb4_general_ci) .
(4) Run db_tables.sql in your database.
(5) Make sure that the folder "thumbs" is writable
(4) Open the application in your browser and register a new user.
(5) Login and have fun.

## Server Requirements

### for general execution
* php5, php5-mysql

### for creating thumbnails

* wkhtmltoimage (installed in /usr/local/bin)
	** download from http://wkhtmltopdf.org/downloads.html
* xvfb
	** install via packet manager
* php5-gd
	** install via packet manager

* also make sure that the php command system is allowed, otherwise creating thumbnails won't work

