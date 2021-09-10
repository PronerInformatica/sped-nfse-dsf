<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeDSF\Tools;
use NFePHP\NFSeDSF\Common\Soap\SoapCurl;

try {
    $config = [
        'cnpj' => '99999999000191',
        'im' => '1733160024',
        'cmun' => '3170206', //ira determinar as urls e outros dados
        'razao' => 'Empresa Test Ltda',
        'tpamb' => 2 //1-producao, 2-homologacao
    ];

    $content = file_get_contents('expired_certificate.pfx');
    $password = 'associacao';
    $cert = Certificate::readPfx($content, $password);

    $soap = new SoapCurl($cert);
    $soap->setDebugMode(true);

    $tools = new Tools($config, $cert);
    $tools->loadSoapClass($soap);

    $numero = '14052113560689';
    $codigoverificacao = 'E506';

    $response = $tools->cancelar($numero, $codigoverificacao);

    header('Content-Type: application/xml; charset=iso-8859-1');
    echo $response['response'];
} catch (\Exception $e) {
    echo $e->getMessage();
}
