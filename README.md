# SBIF API
Libreria que permite consultar el valor de indicadores al API de la superintendencia de bancos e instituciones financieras (SBIF) de Chile. Pueden acceder a ls siguientes indicadores:

- Dólar
- Euro
- UF
- UTM
- IPC

Además se puede acceder a la información los bancos de Chile.

## Instalación
Para instalar la librería ejecuta el siguiente comando en la consola:

```shell
composer require kattatzu/sbif
```

## Uso de forma Standalone

```php
use Kattatzu/SBIF/SBIF;
$sbif = new SBIF('SBIF API KEY');
echo $sbif->getDollar('2017-04-30');
//664.0
```



