<?php
use Kattatzu\Sbif\Institution;

if (! function_exists('sbif_dollar'))
{
    function sbif_dollar($date = null)
    {
    	return Sbif::getDollar($date);
    }
}

if (! function_exists('sbif_euro'))
{
    function sbif_euro($date = null)
    {
    	return Sbif::getEuro($date);
    }
}

if (! function_exists('sbif_utm'))
{
    function sbif_utm($date = null)
    {
    	return Sbif::getUTM($date);
    }
}

if (! function_exists('sbif_uf'))
{
    function sbif_uf($date = null)
    {
    	return Sbif::getUF($date);
    }
}

if (! function_exists('sbif_ipc'))
{
    function sbif_ipc($date = null)
    {
    	return Sbif::getIPC($date);
    }
}

if (! function_exists('sbif_institution'))
{
    function sbif_institution($code, $date = null)
    {
        return Sbif::getInstitutionData($code, $date);
    }
}

if (! function_exists('sbif_institutions'))
{
    function sbif_institutions()
    {
        return (new Institution)->getInstitutions();
    }
}
