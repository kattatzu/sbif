# SBIF API
Libreria que permite consultar el valor de indicadores al API de la superintendencia de bancos e instituciones financieras (SBIF) de Chile. Pueden acceder a ls siguientes indicadores:

- Dólar
- Euro
- UF
- UTM
- IPC

Además se puede acceder a la información los bancos de Chile.

Para usar el API de la SBIF debes obtener tu APIKEY en la página:

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



