<?php
namespace Kattatzu\SBIF;

use Exception;
use DateTime;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;

class SBIF
{
	public const IND_UF = 100;
	public const IND_UTM = 200;
	public const IND_DOLLAR = 300;
	public const IND_EURO = 400;
	public const IND_IPC = 500;
	protected $apiKey = null;
	protected $apiBase = 'http://api.sbif.cl/api-sbifv3/recursos_api/';

	public function __construct($apiKey){
		$this->apiKey = $apiKey;
	}

	function getDollar($date = null){
		return $this->getIndicator(self::IND_DOLLAR, $date);
	}

	function getIndicator($indicator, $date = null){
		$endpoint = $this->getEndPoint($indicator, $date);
		$value = $this->get($endpoint);

		return $this->getValueOfIndicator($value, $indicator);
	}
	/**
	 * Retorna el valor de un indicador para la fecha especificada
	 * @var integer $indicator Indicador a consultar
	 * @var mixed $date Fecha a consultar
	 * @throws GuzzleHttp\Exception\RequestException
	 * @return array
	 */
	public function get($endpoint){
		$endpoint = (strpos($endpoint, '/') == 0) ? substr($endpoint, 1) : $endpoint;
		$endpoint = $this->apiBase . $endpoint . '?apikey=' . $this->apiKey . '&formato=json';

		$client = new \GuzzleHttp\Client();
		$res = $client->request('GET', $endpoint);
		if ($res->getStatusCode() == 404) {
			return new Exception("Endpoint not found ({$endpoint})");
		}

		$response = json_decode($res->getBody());

		return $response;
	}

	/**
	 *
	 */
	private function getEndPoint($indicator, $date = null)
	{
		$date = $this->normalizeDate($date);
		$yearMonthDate = $date->format("Y/m");
		$dayDate = $date->format("d");

		$endpoint = '';
		switch($indicator){
			case self::IND_UF:
				$endpoint = '/uf/'.$yearMonthDate.'/dias/'.$dayDate;
				break;
			case self::IND_UTM:
				$endpoint = '/utm/'.$yearMonthDate;
				break;
			case self::IND_DOLLAR:
				$endpoint = '/dolar/'.$yearMonthDate.'/dias/'.$dayDate;
				break;
			case self::IND_EURO:
				$endpoint = '/euro/'.$yearMonthDate.'/dias/'.$dayDate;
				break;
			case self::IND_IPC:
				$endpoint = '/ipc/'.$yearMonthDate;
				break;
		}

		return $endpoint;
	}

	/**
	 *
	 *	@return DateTime
	 */
	private function normalizeDate($date = null){
		if (is_null($date)){
			return Carbon::today();
		}

		return ($date instanceof DateTime) ? $date : Carbon::parse($date);
	}

	/**
	 *
	 */
	private function getValueOfIndicator($body, $indicator)
	{
		switch ($indicator) {
			case self::IND_UF:
				return isset($body->UFs[0]) ? $body->UFs[0]->Valor : null;
			case self::IND_DOLLAR:
				return isset($body->Dolares[0]) ? $body->Dolares[0]->Valor : null;
			case self::IND_EURO:
				return isset($body->Euros[0]) ? $body->Euros[0]->Valor : null;
			case self::IND_UTM:
				return isset($body->UTMs[0]) ? $body->UTMs[0]->Valor : null;
			case self::IND_IPC:
				return isset($body->IPCs[0]) ? $body->IPCs[0]->Valor : null;
		}

		return null;
	}

	/**
	 *
	 */
	function apiKey($apiKey = null)
	{
		if($apiKey === null) {
			return $this->apiKey;
		}

		$this->apiKey = $apiKey;
	}
}
