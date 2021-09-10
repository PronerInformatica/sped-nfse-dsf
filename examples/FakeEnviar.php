<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeDSF\Tools;
use NFePHP\NFSeDSF\Rps;
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

    $arps = [];

    // RPS
    $std = new \stdClass();
    $std->tipo = 2;
    $std->serie = 'NF';
    $std->numero = 140521135457991;
    $std->dataEmissao = '2021-08-30T16:00:00.734375-03:00';
    $std->naturezaOperacao = 52;
    $std->optanteSimplesNacional = 2;
    $std->incentivadorCultural = 2;
    $std->status = 1;

    // SERVICO
    $stdServico = new stdClass();
    $stdServico->ValorServicos = 1.00;
    $stdServico->ValorDeducoes = 1.00;
    $stdServico->ValorPis = 1.00;
    $stdServico->ValorCofins = 1.00;
    $stdServico->ValorInss = 1.00;
    $stdServico->ValorIr = 1.00;
    $stdServico->ValorCsll = 1.00;
    $stdServico->IssRetido = 2;
    $stdServico->ValorIss = 1.00;
    $stdServico->ValorIssRetido = 1.00;
    $stdServico->OutrasRetencoes = 1.00;
    $stdServico->BaseCalculo = 1.00;
    $stdServico->Aliquota = 1.00;
    $stdServico->ValorLiquidoNfse = 1.00;
    $stdServico->DescontoIncondicionado = 1.00;
    $stdServico->DescontoCondicionado = 1.00;

    $stdServico->ItemListaServico = 102;
    $stdServico->CodigoCnae = 111302;
    $stdServico->Discriminacao = 'teste';
    $stdServico->CodigoMunicipio = 4317608;

    $std->Servico = $stdServico;

    // PRESTADOR
    $stdPrestador = new stdClass();
    $stdPrestador->Cnpj = "10015889000131";
    $stdPrestador->InscricaoMunicipal = "41366";

    $std->Prestador = $stdPrestador;

    // TOMADOR
    $stdTomador = new stdClass();
//    $stdTomador->Cpf = "00123456000";
    $stdTomador->Cnpj = "10015889000131";
    $stdTomador->InscricaoMunicipal = "41366";
    $stdTomador->RazaoSocial = "EMPRESA TESTE";
    $stdTomador->Endereco = "rua teste";
    $stdTomador->Numero = "123";
    $stdTomador->Complemento = "000000000000123";
    $stdTomador->Bairro = "000000000000123";
    $stdTomador->CodigoMunicipio = "4317608";
    $stdTomador->Uf = "RS";
    $stdTomador->Cep = "000000000000123";
    $stdTomador->Telefone = "51996537763";
    $stdTomador->Email = "000000000000123";

    $std->Tomador = $stdTomador;

    $stdIntermediarioServico = new stdClass();
    $stdIntermediarioServico->RazaoSocial = "000000000000123";
//    $stdIntermediarioServico->Cpf = "00123456000";
    $stdIntermediarioServico->Cnpj = "00123456000123";
    $stdIntermediarioServico->InscricaoMunicipal = "000000000000123";

//    $std->IntermediarioServico = $stdIntermediarioServico;

    $stdContrucaoCivil = new stdClass();
    $stdContrucaoCivil->CodigoObra = "000000000000123";
    $stdContrucaoCivil->Art = "00123456000123";

//    $std->ContrucaoCivil = $stdContrucaoCivil;

    echo json_encode($std);

    $rps = new Rps($std);

    $arps[] = $rps;

    $lote = '14052113560689';

    $response = $tools->enviar($arps, $lote);

    header('Content-Type: application/xml; charset=iso-8859-1');
    echo $response['response'];
} catch (\Exception $e) {
    echo $e->getMessage();
}
