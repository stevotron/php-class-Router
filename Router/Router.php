<?php

/**
 * Class Router
 * @version 1.2.0
 */
class Router
{
	/**
	 * @var bool Use case insensitive modifier when performing preg match?
	 */
	private $case_sensitive = false;

	/**
	 * @var string The name of the GET key that the route variable is assigned to.
	 */
	private $get_key = '__request_string';

	/**
	 * @var array Strings to add before and/or after the return string.
	 */
	private $return_string_modifier = [null, null];

	/**
	 * @var string The original request string.
	 */
	private $request_string = '';

	/**
	 * @var string The original request string including any GET variables.
	 */
	private $request_with_get = '';

	/**
	 * @var array Each element of the request separated by '/'.
	 */
	private $request_array = [];

	/**
	 * @var array The list of regex request strings and the string they will return.
	 */
	private $route_maps = [];


	function __construct($get_key = null)
	{
		if ($get_key !== null) {
			$this->setGetKey($get_key);
		}
	}

	/**
	 * @param string $get_key
	 */
	public function setGetKey($get_key)
	{
		$this->get_key = $get_key;
	}

	/**
	 * Process the request string, store the details and return the first match.
	 * @return bool|string The first string linked to a matching expression or false
	 */
	public function process()
	{
		// stored cleaned request string, remove it from $_GET
		$this->request_string = isset($_GET[$this->get_key]) ? trim($_GET[$this->get_key], '/') : '';
		unset ($_GET[$this->get_key]);

		// store individual request elements
		$this->request_array = explode('/', $this->request_string);

		// add "/" to the end of $request_string
		$this->request_string .= '/';

		$get_variables = http_build_query($_GET);
		$get_variables = $get_variables ? '?'.$get_variables : '' ;// prefix with ?

		// log full request including $_GET data
		$this->request_with_get = $this->request_string . $get_variables;

		// return result
		return $this->getReturnString();
	}

	/**
	 * Set the case sensitive value
	 * @param bool $bool Will be type cast to boolean when set
	 */
	public function setCaseSensitive($bool)
	{
		$this->case_sensitive = (bool) $bool;
	}

	/**
	 * @param string $pre A string to prepend the return string with.
	 * @param string $post A string to suffix the return string with.
	 */
	public function setReturnStringModifier($pre = null, $post = null)
	{
		$this->return_string_modifier = [$pre, $post];
	}

	/**
	 * @return string
	 */
	public function getRequestString()
	{
		return $this->request_string;
	}

	/**
	 * @return string
	 */
	public function getRequestWithGet()
	{
		return $this->request_with_get;
	}

	/**
	 * @var string $index the index or name of the specific request element to return.
	 * @var bool $strict throw an exception if requested index does not exist or return false.
	 * @return array|string|bool An array of the full request, the requested value or false if requested index does not exist and $strict is false.
	 * @throws Exception if submitted index does not exist.
	 */
	public function getRequest($index = null, $strict = true)
	{
		if ($index === null) {
			return $this->request_array;
		}

		if (array_key_exists($index, $this->request_array)) {
			return $this->request_array[$index];
		}

		if ($strict) {
			throw new Exception ('Request index (' . $index . ') does not exist');
		}

		return false;
	}

	/**
	 * Set or overwrite part of the request array
	 * @param $key string
	 * @param $value string
	 */
	public function setRequest($key, $value)
	{
		$this->request_array[$key] = $value;
	}

	/**
	 * @param $routes_array array Keys are a regex request string and values are the path to return on match
	 * @throws Exception if input is not an array
	 */
	public function setRoutes($routes_array)
	{
		if (!is_array($routes_array)) {
			throw new Exception ('Input must be an array');
		}

		$this->route_maps = $routes_array;
	}

	/**
	 * @return bool|string If a match is found the corresponding string is returned, otherwise false.
	 */
	private function getReturnString()
	{
		foreach ($this->route_maps as $regex_request => $return_string) {

			// escape request string
			$regex_request = str_replace('~', '\~', $regex_request);

			$case_sensitive = $this->case_sensitive ? '' : 'i' ;

			if (preg_match('~^'.$regex_request.'$~'.$case_sensitive, $this->request_string)) {

				return $this->return_string_modifier[0] . $return_string . $this->return_string_modifier[1];
			}
		}

		return false;
	}
}
