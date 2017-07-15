<?php
namespace Kattatzu\SBIF\Exception;
use Exception;

class EndpointNotFoundException extends Exception
{
	function __construct($endpoint)
	{
		parent::__construct("Endpoint not found ($endpoint)");
	}
}
