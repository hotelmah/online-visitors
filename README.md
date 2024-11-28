## About
This package returns a PHP one-dimensional indexed array of GeoIP data with Remote IP Addresses and sorted DateTime differences for you to use as you wish like rendering to a view.

## Files Not Included in Packagist Package
- .gitattributes
- OnlineVisitors-Test-1.php


## Test File
- *OnlineVisitors-Test-1.php*
- This is a sample of how the package works.


## In your Script
In each page that you want to record an online visitor, add the following (See the test file for an example):
`
require_once('src/functions.onlinevisitors.php');
`

`
use function OnlineVisitors\{executeOnlineVisitorsInsertRow, executeGetOnlineVisitorsLatestIPAddressesWithTime};
`

Call this function to insert a row into the database:

`
executeOnlineVisitorsInsertRow();
`

Get the latest Online Visitors returned in an indexed array:

`
$SelectOptionsAry = executeGetOnlineVisitorsLatestIPAddressesWithTime();
`

Process this returned array as you wish.


## Installation - Composer


## Installation - Manual
- Copy the src directory contents to an appropriate directory like includes/ in your LAMP web hosting provider.
- Refer to the 'In your Script' heading above.


## Notes
- The script waits 10 minutes before updating the same Remote IP Address in the database. Thus, the visitor would need to be on your site for more than 10 minutes to see an update.
- 20 records are retrieved and sorted by most recent on top.


## Database
- A sample SQLite3 database is not included in this repository because the script automatically creates the SQLite3 database if not found. If found, it simply updates the tables.
- The script creates/updates the SQLite3 database in your root web folder (html_public).
- SQLite3 does not require a username or password. Thus, there is no authentication to use the database.
- The database is a single file.
- The script must have read/write access to the folder the database is in.
- You can delete the SQLite3 database on your server at will to start a new database, or rename the database to start a new database.
- The database default name is OnlineVisitors.db
- On your server, and in your PHP.ini ensure that SQLite3 is enabled and working.


## Third-Party Service
- This script uses Curl to get GeoIP data for the Remote IP Address received.
- The GeoIP service is a free and open source service.


## Feedback
- Forks and Pull Requests are welcomed.
- Suggestions and comments for improvement are requested.
- Thank you for reading!


## Future Updates
- The latest 20 records are retrieved. This limit is not yet passed in as a parameter.
