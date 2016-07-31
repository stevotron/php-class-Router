<?php

require __DIR__ . '/../Router/Router.php';
$Router = new Router();


/*
 * This is the default key that is searched for but the method is included below so you can change it if needed.
 */
$Router->setGetKey('__request_string');


/*
 * This is set to false by default but the method is included below so you can change it if needed.
 */
$Router->setCaseSensitive(false);


/*
 * Array key is regex string and value is string to return if there is a match.
 */
$Router->setRoutes([
	''                   => 'home',
	'secret'             => 'secret',
	'add/[\d]+/[\d]+'    => 'adder',
	'any-string/[\w\-]+' => 'any-string',
	'any-string/never'   => 'never-reached' // this will never be reached as the regex above will be matched first
]);


/*
 * Set prefix and suffix for returned string, in this case we're creating a path to the file we want to run.
 */
$Router->setReturnStringModifier(__DIR__ . '/processors/', '.php');


/*
 * Search for a match and get string if there is one.
 */
$returned_string = $Router->process();


/*
 * Check to see if a match was returned.
 */
if ($returned_string === false) {
	// You could use a 404 redirect here...
	exit('Router class returned no match for "' . $Router->getRequestString() . '"');
}
/*
 * Check returned string is a valid path and load it or exit with an error.
 */
else if (file_exists($returned_string)) {
	require $returned_string;
}
else {
	exit ('Returned path (' . $returned_string . ') does not exist');
}
