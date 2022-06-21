# Simple and smart pagination builder
# This libary is part of sprint framework packages

**Version 1.0.0 **

Hey! This is a simple and small single class PHP pagination that handles the pagination based on the http request. All properties and methods are statics.
The codebase is very small and very easy to understand. So you can use it as a boilerplate for a more complex pagination.

---

## Installation including the file
```php
// Require the class
include 'src\SPagination.php';

```

## Installation using Composer
Just run `composer require spagination/sprint-framework-pagination-package`
Than add the autoloader to your project like this:

```php
// Autoload files using composer
require_once __DIR__ . '/vendor/autoload.php';

// Use this namespace
use sprint\spagination\SPagination;

		// Create an instance of the SPagination class and pass the current page retrieved via http request and the number of results per page as parameters to the constructor
		//First parameter is the page the current page number and second is the limit per page
		$spagination = new SPagination(intval($_GET['page']), 5);

// sql query offset, dynamically generated via the number of results to display per page and the current page number
		//echo $spagination::$offset;

// How to use with the database (This example consider the mysqli, but you can use PDO or any other drivers as well)
// First connect to your database
		$conn   = mysqli_connect("localhost","root","password","database");

//Select the table, use $spagination::$limit to get number of result to return and $spagination::$offset to get the offset dynamically
//To get the total filtered rows use SQL_CALC_FOUND_ROWS command in your query this will ignored the limit and run the query as no limit was informed
		$query  = mysqli_query($conn, "SELECT SQL_CALC_FOUND_ROWS * FROM table ORDER BY id ASC LIMIT ".$spagination::$limit." OFFSET ".$spagination::$offset."");
		$results = array();

// Total filtered results from the sql query
		$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT FOUND_ROWS() AS totalFiltered"));
		$spagination::$total = $total["totalFiltered"];

//Now fetch the data and store in array
		while($row = mysqli_fetch_assoc($query)){
			$results[] = $row;
		}

// Either show dots in the pagination or not(Accept boolean value)
		$spagination::$dots = true;//false

//debug the database results with the pagination
		echo "<pre>";
			var_dump($results);
		echo "</pre>";
// Build the pagination
		echo $spagination::page();```

## Custom pagination style
> The SPagination uses bootstrap as default style, but you can create your own custom style by defining the classes to the elements.
*Please refer to the bootstrap documentation for more details, in case you want to use bootstrap for styling your pagination* [Bootstrap Page](https://getbootstrap.com ["Bootstrap Page"])
```php

//This will add custom class to anchor element
//Add general class to the class key and active class to the active key
$spagination::$classes["a"] = array(
	"class" => [""] //default class: ["page-link"]
	"active" => [""] //default class: ["active", "disabled"]
);

//This will add custom class to list item element
//Add general class to the class key and active class to the active key (this is used in case you use list item to style active behaviour)
$spagination::$classes["li"] = array(
	"class" => [""] //default class: ["page-item"]
	"active" => [""]
);

//This will add custom class to unordered list element
//Add general class to the class key
$spagination::$classes["ul"] = array(
	"class" => [""] //default class: ["pagination"]
);

```

## License
This project is licensed under the MIT License. See LICENSE for further information.

## Contributors
- Inacio Agostinho Uassire <inaciowassir@gmail.com>

