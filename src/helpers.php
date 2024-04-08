<?php

if (!function_exists('sbif_dollar')) {
    /**
     * Retorna el valor del dólar
     * @param $date fecha a consultar
     * @return mixed
     */
    function sbif_dollar($date = null)
    {
        return Sbif::getDollar($date);
    }
}

if (!function_exists('sbif_euro')) {
    /**
     * Retorna el valor del euro
     * @param $date fecha a consultar
     * @return mixed
     */
    function sbif_euro($date = null)
    {
        return Sbif::getEuro($date);
    }
}

if (!function_exists('sbif_utm')) {
    /**
     * Retorna el valor de la UTM
     * @param $date fecha a consultar
     * @return mixed
     */
    function sbif_utm($date = null)
    {
        return Sbif::getUTM($date);
    }
}

if (!function_exists('sbif_uf')) {
    /**
     * Retorna el valor de la UF
     * @param $date fecha a consultar
     * @return mixed
     */
    function sbif_uf($date = null)
    {
        return Sbif::getUF($date);
    }
}

if (!function_exists('sbif_ipc')) {
    /**
     * Retorna el valor del IPC
     * @param $date fecha a consultar
     * @return mixed
     */
    function sbif_ipc($date = null)
    {
        return Sbif::getIPC($date);
    }
}

if (!function_exists('sbif_institution')) {
    /**
     * Retorna la información de una institución
     * @param string $code código de la institución a consultar
     * @param $date fecha a consultar
     * @return mixed
     */
    function sbif_institution($code, $date = null)
    {
        return Sbif::getInstitutionData($code, $date);
    }
}

if (!function_exists('sbif_institutions')) {
    /**
     * Retorna el listado de instituciones registradas en la SBIF
     * @return array
     */
    function sbif_institutions()
    {
        return (new Institution)->getInstitutions();
    }
}
