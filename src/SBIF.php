<?php namespace Kattatzu\Sbif;

use Exception;
use DateTime;
use Carbon\Carbon;
use Psr\Http\Message\RequestInterface;
use Kattatzu\Sbif\Exception\ConnectException;
use Kattatzu\Sbif\Exception\ApikeyNotFoundException;
use Kattatzu\Sbif\Exception\EndpointNotFoundException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;

/**
 * Class Sbif
 * @package Kattatzu\Sbif
 */
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
     *
     * @param null $apiKey El API key de SBIF
     */
    public function __construct($apiKey = null)
    {
        $this->apiKey($apiKey);
    }


    /**
     * Retorna el apikey registrado. Si se define un valor se sobre-escribe el valor actual.
     *
     * @param string $apiKey API key a registrar
     * @return string
     */
    function apiKey($apiKey = null)
    {
        if ($apiKey !== null) {
            $this->apiKey = $apiKey;
        }

        return $this->apiKey;
    }

    /**
     * Retorna el valor del dólar
     *
     * @param DateTime $date fecha a consultar (opcional)
     * @return float
     */
    function getDollar($date = null)
    {
        return $this->getIndicator(self::IND_DOLLAR, $date);
    }

    /**
     * Retorna el valor de un indicador para una fecha en particular
     *
     * @param int $indicator indicador a consultar
     * @param DateTime $date fecha a consultar (opcional)
     * @return float
     */
    function getIndicator($indicator, $date = null)
    {
        $endpoint = $this->getIndicatorEndPoint($indicator, $date);
        $value = $this->get($endpoint);

        return $this->getValueFromResult($value, $indicator);
    }

    /**
     * Retorna el endpoint correspondiente al indicador a consultar
     *
     * @param int $indicator indicador a consultar
     * @param DateTime $date fecha a consultar
     * @return string
     */
    private function getIndicatorEndPoint($indicator, $date = null)
    {
        $date = $this->normalizeDate($date);
        $yearMonthDate = $date->format("Y/m");
        $dayDate = $date->format("d");

        $endpoint = '';
        switch ($indicator) {
            case self::IND_UF:
                $endpoint = '/uf/' . $yearMonthDate . '/dias/' . $dayDate;
                break;
            case self::IND_UTM:
                $endpoint = '/utm/' . $yearMonthDate;
                break;
            case self::IND_DOLLAR:
                $endpoint = '/dolar/' . $yearMonthDate . '/dias/' . $dayDate;
                break;
            case self::IND_EURO:
                $endpoint = '/euro/' . $yearMonthDate . '/dias/' . $dayDate;
                break;
            case self::IND_IPC:
                $endpoint = '/ipc/' . $yearMonthDate;
                break;
        }

        return $endpoint;
    }

    /**
     * Devuelve una instancia Carbon de la fecha consultada. Si la fecha es de un sábado o domingo se devuelve al
     * viernes previo
     *
     * @param mixed $date fecha a normalizar
     * @return Carbon
     */
    private function normalizeDate($date = null)
    {
        if (is_null($date)) {
            $date = Carbon::today();
        }

        $date = ($date instanceof Carbon) ? $date : Carbon::parse($date);

        // Si la fecha es de un sábado o domingo se devuelve al viernes previo
        if (in_array($date->dayOfWeek, array(0, 6))) {
            $date->subDay(($date->dayOfWeek == 6) ? 1 : 2);
        }

        return $date;
    }

    /**
     * Retorna la respuesta de un endpoint
     *
     * @param string $endpoint endpoint a consultar
     * @throws EndpointNotFoundException
     * @throws ConnectException
     * @return object
     */
    public function get($endpoint)
    {
        if ($this->apiKey === null) {
            throw new ApikeyNotFoundException;
        }

        $endpoint = (strpos($endpoint, '/') == 0) ? substr($endpoint, 1) : $endpoint;
        $endpoint = $this->apiBase . $endpoint . '?apikey=' . $this->apiKey . '&formato=json';

        try {
            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', $endpoint);

            if ($res->getStatusCode() == 404) {
                throw new EndpointNotFoundException($endpoint);
            }

            $response = json_decode($res->getBody());
        } catch (GuzzleConnectException $e) {
            throw new ConnectException($endpoint);
        }

        return $response;
    }

    /**
     * Retorna el valor de un indicador desde la respuesta obtenida desde el endpoint consultado.
     *
     * @param object $body respuesta obtenida desde el endpoint
     * @param int $indicator indicador consultado
     * @return mixed
     */
    private function getValueFromResult($body, $indicator)
    {
        $value = 0;
        switch ($indicator) {
            case self::IND_UF:
                $value = isset($body->UFs[0]) ? $body->UFs[0]->Valor : 0;
                break;
            case self::IND_DOLLAR:
                $value = isset($body->Dolares[0]) ? $body->Dolares[0]->Valor : 0;
                break;
            case self::IND_EURO:
                $value = isset($body->Euros[0]) ? $body->Euros[0]->Valor : 0;
                break;
            case self::IND_UTM:
                $value = isset($body->UTMs[0]) ? $body->UTMs[0]->Valor : 0;
                break;
            case self::IND_IPC:
                $value = isset($body->IPCs[0]) ? $body->IPCs[0]->Valor : 0;
                break;
            case self::INF_BANK:
                return new Institution($body->Perfiles[0]);
        }

        return (float)$value;
    }

    /**
     * Retorna el valor del euro
     *
     * @param DateTime $date fecha a consultar (opcional)
     * @return float
     */
    function getEuro($date = null)
    {
        return $this->getIndicator(self::IND_EURO, $date);
    }

    /**
     * Retorna el valor de la UTM
     *
     * @param DateTime $date fecha a consultar (opcional)
     * @return float
     */
    function getUTM($date = null)
    {
        return $this->getIndicator(self::IND_UTM, $date);
    }

    /**
     * Retorna el valor de la UF
     *
     * @param DateTime $date fecha a consultar (opcional)
     * @return float
     */
    function getUF($date = null)
    {
        return $this->getIndicator(self::IND_UF, $date);
    }

    /**
     * Retorna el valor del IPC
     *
     * @param DateTime $date fecha a consultar (opcional)
     * @return float
     */
    function getIPC($date = null)
    {
        return $this->getIndicator(self::IND_IPC, $date);
    }

    /**
     * Retorna la información de una institución bancaria de una fecha en particular
     *
     * @param string $code código de la institución
     * @param DateTime $date fecha a consultar (opcional)
     * @return float
     */
    function getInstitutionData($code, $date = null)
    {
        $endpoint = $this->getInstitutionEndPoint($code, $date);
        $value = $this->get($endpoint);

        return $this->getValueFromResult($value, self::INF_BANK);
    }

    /**
     * Retorna el endpoint correspondiente a la institución financiera a consultar
     *
     * @param string $code código de la institución
     * @param DateTime $date fecha a consultar
     * @return string
     */
    private function getInstitutionEndPoint($code, $date = null)
    {
        $date = $this->normalizeDate($date);
        $yearMonthDate = $date->format("Y/m");
        $endpoint = '/perfil/instituciones/' . $code . '/' . $yearMonthDate;

        return $endpoint;
    }
}
