# SBIF API
Libreria que permite consultar el valor de indicadores al API de la superintendencia de bancos e instituciones financieras (SBIF) de Chile. Pueden acceder a ls siguientes indicadores:

- Dólar
- Euro
- UF
- UTM
- IPC

Además se puede acceder a la información de los bancos de Chile.

## Obtener API key
Para usar el API de la SBIF debes obtener tu APIKEY en la siguiente página:
http://api.sbif.cl/api/contactanos.jsp

## Instalación
Para instalar la librería ejecuta el siguiente comando en la consola:

```bash
composer require kattatzu/sbif
```

## Uso de forma Standalone
```php
use Kattatzu/Sbif/Sbif;

$sbif = new Sbif('SBIF API KEY');
echo $sbif->getDollar('2017-04-30');
//664.0
```

### Indicadores disponibles
```php
$date = Carbon::today();

$sbif->getDollar($date);
$sbif->getEuro($date);
$sbif->getUTM($date);
$sbif->getUF($date);
$sbif->getIPC($date);

// Si no envias una fecha se toma la fecha actual
$sbif->getDollar();
```

También puedes consultar de forma dinámica
```php
$sbif->getIndicator(Sbif::IND_EURO, $date);
```
Constantes disponibles
```php
Sbif::IND_UF
Sbif::IND_UTM
Sbif::IND_DOLLAR
Sbif::IND_EURO
Sbif::IND_IPC
```





