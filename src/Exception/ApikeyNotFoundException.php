<?php
namespace Kattatzu\SBIF\Exception;
use Exception;

class ApikeyNotFoundException extends Exception{
	function __construct($endpoint)
	{
		parent::__construct("Api key not found");
	}
}
