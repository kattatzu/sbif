<?php
namespace Kattatzu\Sbif;

use Exception;
use DateTime;
use Carbon\Carbon;
use Psr\Http\Message\RequestInterface;
use Kattatzu\Sbif\Exception\ConnectException;
use Kattatzu\Sbif\Exception\EndpointNotFoundException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;

class Sbif
{
	const IND_UF = 100;
	const IND_UTM = 200;
	const IND_DOLLAR = 300;
	const IND_EURO = 400;
	const IND_IPC = 500;
	const INF_BANK = 600;

	protected $apiKey = null;
	protected $apiBase = 'http://api.sbif.cl/api-sbifv3/recursos_api/';

	/**
	 * Constructor de la clase
	 * @var string $apiKey El API key de SBIF
	 */
	public function __construct($apiKey){
		$this->apiKey = $apiKey;
	}

	/**
	 * Retorna el valor del dólar
	 * @var DateTime $date fecha a consultar (opcional)
	 * @return double
	 */
	function getDollar($date = null){
		return $this->getIndicator(self::IND_DOLLAR, $date);
	}

	/**
	 * Retorna el valor del euro
	 * @var DateTime $date fecha a consultar (opcional)
	 * @return double
	 */
	function getEuro($date = null){
		return $this->getIndicator(self::IND_EURO, $date);
	}

	/**
	 * Retorna el valor de la UTM
	 * @var DateTime $date fecha a consultar (opcional)
	 * @return double
	 */
	function getUTM($date = null){
		return $this->getIndicator(self::IND_UTM, $date);
	}

	/**
	 * Retorna el valor de la UF
	 * @var DateTime $date fecha a consultar (opcional)
	 * @return double
	 */
	function getUF($date = null){
		return $this->getIndicator(self::IND_UF, $date);
	}

	/**
	 * Retorna el valor del IPC
	 * @var DateTime $date fecha a consultar (opcional)
	 * @return double
	 */
	function getIPC($date = null){
		return $this->getIndicator(self::IND_IPC, $date);
	}

	/**
	 * Retorna el valor del IPC
	 * @var DateTime $date fecha a consultar (opcional)
	 * @return double
	 */
	function getIPC($date = null){
		return $this->getIndicator(self::IND_IPC, $date);
	}

	/**
	 * Retorna el valor de un indicador para una fecha en
	 * particular
	 * @var int $indicator indicador a consultar
	 * @var DateTime $date fecha a consultar (opcional)
	 * @return object
	 */
	function getIndicator($indicator, $date = null){
		$endpoint = $this->getIndicatorEndPoint($indicator, $date);
		$value = $this->get($endpoint);

		return $this->getValueFromResult($value, $indicator);
	}

	/**
	 * Retorna la información de una institución bancaria de una
	 * fecha en particular
	 * @var string $code código de la institución
	 * @var DateTime $date fecha a consultar (opcional)
	 * @return object
	 */
	function getInstitutionData($code, $date = null){
		$endpoint = $this->getInstitutionEndPoint($code, $date);
		$value = $this->get($endpoint);

		return $this->getValueFromResult($value, self::INF_BANK);
	}

	/**
	 * Retorna la respuesta de un endpoint
	 * @var string $endpoint endpoint a consultar
	 * @throws Kattatzu\Sbif\Exception\EndpointNotFoundException
	 * @throws Kattatzu\Sbif\Exception\ConnectException
	 * @return object
	 */
	public function get($endpoint){
		$endpoint = (strpos($endpoint, '/') == 0) ? substr($endpoint, 1) : $endpoint;
		$endpoint = $this->apiBase . $endpoint . '?apikey=' . $this->apiKey . '&formato=json';

		try{
			$client = new \GuzzleHttp\Client();
			$res = $client->request('GET', $endpoint);
			if ($res->getStatusCode() == 404) {
				throw new EndpointNotFoundException($endpoint);
			}

			$response = json_decode($res->getBody());
		}catch(GuzzleConnectException $e){
			throw new ConnectException($endpoint);
		}

		return $response;
	}

	/**
	 * Retorna el endpoint correspondiente al indicador
	 * a consultar
	 * @var int $indicator indicador a consultar
	 * @var DateTime $date fecha a consultar
	 * @return string
	 */
	private function getIndicatorEndPoint($indicator, $date = null)
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
	 * Retorna el endpoint correspondiente a la institución
	 * financiera a consultar
	 * @var string $code código de la institución
	 * @var DateTime $date fecha a consultar
	 * @return string
	 */
	private function getIntitutionEndPoint($code, $date = null)
	{
		$date = $this->normalizeDate($date);
		$yearMonthDate = $date->format("Y/m");
		$endpoint = '/perfil/instituciones/' . $code . '/' . $yearMonthDate;

		return $endpoint;
	}

	/**
	 * Devuelve una instancia Carbon de la fecha consultada.
	 * Si la fecha es de un sábado o domingo se devuelve al
	 * viernes previo
	 * @var mixed $date fecha a normalizar
	 * @return Carbon\Carbon
	 */
	private function normalizeDate($date = null){
		if (is_null($date)){
			$date = Carbon::today();
		}

		$date = ($date instanceof Carbon) ? $date : Carbon::parse($date);

		// Si la fecha es de un sábado o domingo se devuelve al viernes previo
		if ( in_array($date->dayOfWeek, array(0, 6)) ){
			$date->subDay(($date->dayOfWeek == 6) ? 1 : 2);
		}

		return $date;
	}

	/**
	 * Retorna el valor de un indicador desde la respuesta
	 * obtenida desde el endpoint consultado.
	 * @var object respuesta obtenida desde el endpoint
	 * @var int $indicator indicador consultado
	 * @return double
	 */
	private function getValueFromResult($body, $indicator)
	{
		$value = 0;
		switch ($indicator) {
			case self::IND_UF:
				$value = isset($body->UFs[0]) ? $body->UFs[0]->Valor : 0; break;
			case self::IND_DOLLAR:
				$value = isset($body->Dolares[0]) ? $body->Dolares[0]->Valor : 0; break;
			case self::IND_EURO:
				$value = isset($body->Euros[0]) ? $body->Euros[0]->Valor : 0; break;
			case self::IND_UTM:
				$value = isset($body->UTMs[0]) ? $body->UTMs[0]->Valor : 0; break;
			case self::IND_IPC:
				$value = isset($body->IPCs[0]) ? $body->IPCs[0]->Valor : 0; break;
			case self::self::INF_BANK:
				$value = isset($body->Perfiles[0]) ? $body->Perfiles[0] : null; break;
		}

		return (double) $value;
	}

	/**
	 * Retorna el apikey registrado. Si se define un valor
	 * se sobre escribe el valor actual.
	 * @val string $apiKey API key a registrar
	 * @return string
	 */
	function apiKey($apiKey = null)
	{
		if($apiKey === null) {
			return $this->apiKey;
		}

		$this->apiKey = $apiKey;
	}
}
