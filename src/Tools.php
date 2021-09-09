<?php

namespace NFePHP\NFSeDSF;

/**
 * Class for comunications with NFSe webserver in Nacional Standard
 *
 * @category  NFePHP
 * @package   NFePHP\NFSeDSF
 * @copyright NFePHP Copyright (c) 2008-2018
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse-dsf for the canonical source repository
 */

use Exception;
use NFePHP\NFSeDSF\Common\Tools as BaseTools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Validator;
use Safe\Exceptions\FilesystemException;
use stdClass;
use DateTime;

class Tools extends BaseTools
{

    protected $xsdpath;
    protected $config;

    /**
     * Construtor
     * @param array $config
     * @param Certificate $cert
     * @throws FilesystemException
     */
    public function __construct($config, Certificate $cert)
    {
        parent::__construct(json_encode($config), $cert);
        $path = \Safe\realpath(__DIR__ . '/../storage/schemes');
        $this->xsdpath = $path;
        $this->config = $config;
    }

    /**
     * Cancela Nota
     * @param string $numero
     * @param string $codigoCancelamento
     * @return array
     */
    public function cancelar(string $numero, string $codigoCancelamento): array
    {
        $operation = "cancelarNfse";
        $lote = date('ymdHis');
        $content = "<Pedido xmlns=\"http://www.abrasf.org.br/ABRASF/arquivos/nfse.xsd\">"
            . "<InfPedidoCancelamento id=\"$lote\">"
            . "<IdentificacaoNfse>"
            . "<Numero>$numero</Numero>"
            . "<Cnpj>{$this->config['cnpj']}</Cnpj>"
            . "<InscricaoMunicipal>{$this->config['im']}</InscricaoMunicipal>"
            . "<CodigoMunicipio>{$this->wsobj->siaf}</CodigoMunicipio>"
            . "</IdentificacaoNfse>"
            . "<CodigoCancelamento>$codigoCancelamento</CodigoCancelamento>"
            . "</InfPedidoCancelamento>"
            . "</Pedido>";

        if ($this->wsobj->sign->$operation) {
            $content = $this->sign($content, 'InfPedidoCancelamento', 'id');
        }

        $content = "<CancelarNfseEnvio xmlns=\"http://www.abrasf.org.br/ABRASF/arquivos/nfse.xsd\">"
            . $content
            . "</CancelarNfseEnvio>";

        Validator::isValid($content, $this->xsdpath."/ReqCancelamentoNFSe.xsd");

        return ['content'=> $content, 'response'=>$this->send($content, $operation)];
    }

    /**
     * Consulta notas pelo número de RPS
     * @param string $numero
     * @param string $serie
     * @param string $tipo
     * @return array
     */
    public function consultarNFSeRps(string $numero, string $serie, string $tipo): array
    {
        $operation = "consultarNfsePorRps";

        $content  = "<ConsultarNfseRpsEnvio xmlns=\"http://www.abrasf.org.br/ABRASF/arquivos/nfse.xsd\">";
        $content .= "<IdentificacaoRps>";
        $content .= "<Numero>$numero</Numero>";
        $content .= "<Serie>$serie</Serie>";
        $content .= "<Tipo>$tipo</Tipo>";
        $content .= "</IdentificacaoRps>";
        $content .= "<Prestador>";
        $content .= "<Cnpj>{$this->config['cnpj']}</Cnpj>";
        $content .= "<InscricaoMunicipal>{$this->config['im']}</InscricaoMunicipal>";
        $content .= "</Prestador>";
        $content .= "</ConsultarNfseRpsEnvio>";

        Validator::isValid($content, $this->xsdpath."/ReqConsultaNFSeRPS.xsd");

        return ['content'=> $content, 'response'=>$this->send($content, $operation)];
    }

    /**
     * Consulta Notas pelo número do lote
     * @param string $lote
     * @return array
     */
    public function consultarLote(string $lote): array
    {
        $operation = "consultarLoteRps";

        $content  = "<ConsultarLoteRpsEnvio xmlns=\"http://www.abrasf.org.br/ABRASF/arquivos/nfse.xsd\">";
        $content .= "<Prestador>";
        $content .= "<Cnpj>{$this->config['cnpj']}</Cnpj>";
        $content .= "<InscricaoMunicipal>{$this->config['im']}</InscricaoMunicipal>";
        $content .= "</Prestador>";
        $content .= "<Protocolo>$lote</Protocolo>";
        $content .= "</ConsultarLoteRpsEnvio>";

        Validator::isValid($content, $this->xsdpath."/ReqConsultaLote.xsd");

        return ['content'=> $content, 'response'=>$this->send($content, $operation)];
    }

    /**
     * Consulta Notas no intervalo das datas e número
     * @param string $dataInicial
     * @param string $dataFinal
     * @param string $numeroNfse
     * @return array
     */
    public function consultarNota(string $dataInicial, string $dataFinal, string $numeroNfse = ""): array
    {
        $operation = "consultarNfse";

        $content  = "<ConsultarNfseEnvio xmlns=\"http://www.abrasf.org.br/ABRASF/arquivos/nfse.xsd\">";
        $content .= "<Prestador>";
        $content .= "<Cnpj>{$this->config['cnpj']}</Cnpj>";
        $content .= "<InscricaoMunicipal>{$this->config['im']}</InscricaoMunicipal>";
        $content .= "</Prestador>";
        if (!empty($NumeroNfse)) {
            $content .= "<NumeroNfse>$numeroNfse</NumeroNfse>";
        }
        $content .= "<PeriodoEmissao>";
        $content .= "<DataInicial>$dataInicial</DataInicial>";
        $content .= "<DataFinal>$dataFinal</DataFinal>";
        $content .= "</PeriodoEmissao>";
        $content .= "</ConsultarNfseEnvio>";

        Validator::isValid($content, $this->xsdpath."/ReqConsultaNotas.xsd");

        return ['content'=> $content, 'response'=>$this->send($content, $operation)];
    }

    /**
     * Consulta Dados Cadastrais
     * @param string $cnpjCpf
     * @param string $cpf
     * @return array
     */
    public function consultarDadosCadastrais(string $cnpjCpf = "", string $cpf = ""): array
    {
        $operation = "retornarDadosCadastrais";

        $content  = "<retornarDadosCadastrais xmlns=\"http://server.nfse.thema.inf.br\">";
        if (!empty($cnpjCpf)) {
            $content .= "<cnpjCpf>$cnpjCpf</cnpjCpf>";
        }
        if (!empty($cpf)) {
            $content .= "<cpf>$cpf</cpf>";
        }
        $content .= "</retornarDadosCadastrais>";

        return ['content'=> $content, 'response'=>$this->send($content, $operation)];
    }

    /**
     * Envia RSP em modo assincrono
     * @param array $aRps
     * @param string $lote
     * @return array
     * @throws Exception
     */
    public function enviar(array $aRps, string $lote): array
    {
        $operation = "recepcionarLoteRps";
        $quantidade = count($aRps);

        $std = new \stdClass();
        $std->dtInicial = '';
        $std->dtFinal = '';

        $rpsxmls = $this->buildRpsXml($aRps, $std);

        $content  = "<EnviarLoteRpsEnvio xmlns=\"http://www.abrasf.org.br/ABRASF/arquivos/nfse.xsd\">";
        $content .= "<LoteRps id=\"$lote\">";
        $content .= "<NumeroLote>{$lote}</NumeroLote>";
        $content .= "<Cnpj>{$this->config['cnpj']}</Cnpj>";
        $content .= "<InscricaoMunicipal>{$this->config['im']}</InscricaoMunicipal>";
        $content .= "<QuantidadeRps>{$quantidade}</QuantidadeRps>";
        $content .= "<ListaRps>" . implode('', $rpsxmls) . "</ListaRps>";
        $content .= "</LoteRps>";
        $content .= "</EnviarLoteRpsEnvio>";

        if ($this->wsobj->sign->$operation) {
            $content = $this->sign($content, 'LoteRps', 'id');
        }

        Validator::isValid($content, $this->xsdpath."/ReqEnvioLoteRPS.xsd");

        return ['content'=> $content, 'response'=>$this->send($content, $operation)];
    }

    /**
     * Constroi os RPS em XML e retorna em um array
     * os valores e datas dos RPS são retornados em
     * um stdClass passado por referencia
     * @param array $arps
     * @param stdClass $std
     * @return array
     * @throws Exception
     */
    protected function buildRpsXml(array $arps, \stdClass &$std): array
    {
        $std->dtInicial = '';
        $std->dtFinal = '';
        $dtIni = null;
        $dtFim = null;
        $rpsxmls = [];
        foreach ($arps as $rps) {
            if (empty($dtIni)) {
                $dtIni = new Datetime($rps->std->dataEmissao);
                $dtFim = new Datetime($rps->std->dataEmissao);
            } else {
                $dtIni = $this->smaller($dtIni, new Datetime($rps->std->dataemissaorps));
                $dtFim = $this->bigger($dtFim, new Datetime($rps->std->dataemissaorps));
            }
            //complementar a estrutura do RPS
            //montar e inserir a assinatura
            //carregar o array de XML dos RPS
            $rpsxml = $rps->render($this->certificate);
            $rpsxml = $this->sign($rpsxml, 'InfRps', 'id');

            $rpsxmls[] = $rpsxml;
        }
        $std->dtInicial = $dtIni->format('Y-m-d');
        $std->dtFinal = $dtFim->format('Y-m-d');
        return $rpsxmls;
    }

    /**
     * Verifica qual é a maior data entre as passadas como parâmetro
     * @param DateTime $dt1
     * @param DateTime $dt2
     * @return DateTime
     */
    protected function bigger(DateTime $dt1, DateTime $dt2): Datetime
    {
        if ($dt1 > $dt2) {
            return $dt1;
        } else {
            return $dt2;
        }
    }

    /**
     * Verifica qual é a menor data entre as passadas como parâmetro
     * @param DateTime $dt1
     * @param DateTime $dt2
     * @return DateTime
     */
    protected function smaller(DateTime $dt1, DateTime $dt2): Datetime
    {
        if ($dt1 < $dt2) {
            return $dt1;
        } else {
            return $dt2;
        }
    }
}
