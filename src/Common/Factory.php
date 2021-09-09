<?php

namespace NFePHP\NFSeDSF\Common;

/**
 * Class for RPS XML convertion
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
use NFePHP\Common\Certificate;
use NFePHP\Common\DOMImproved as Dom;
use stdClass;
use DOMNode;
use DOMElement;

class Factory
{
    /**
     * @var stdClass
     */
    protected $std;

    /**
     * @var Dom
     */
    protected $dom;

    /**
     * @var DOMNode
     */
    protected $rps;

    /**
     * Constructor
     * @param stdClass $std
     */
    public function __construct(stdClass $std)
    {
        $this->std = $std;
        $this->dom = new Dom('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = false;
        $this->rps = $this->dom->createElement('Rps');
    }

    /**
     * Builder, converts sdtClass Rps in XML Rps
     * NOTE: without Prestador Tag
     * @return string RPS in XML string format
     */
    public function render(Certificate $cert = null): string
    {
        $InfRps = $this->dom->createElement('InfRps');

        $id = $this->dom->createAttribute('id');
        $id->value = $this->std->numero;
        $InfRps->appendChild($id);

        $IdentificacaoRps = $this->dom->createElement('IdentificacaoRps');
        $this->dom->addChild(
            $IdentificacaoRps,
            "Numero",
            $this->std->numero,
            true
        );
        $this->dom->addChild(
            $IdentificacaoRps,
            "Serie",
            $this->std->serie,
            true
        );
        $this->dom->addChild(
            $IdentificacaoRps,
            "Tipo",
            $this->std->tipo,
            true
        );
        $InfRps->appendChild($IdentificacaoRps);

        $this->dom->addChild(
            $InfRps,
            "DataEmissao",
            $this->std->dataEmissao,
            true
        );
        $this->dom->addChild(
            $InfRps,
            "NaturezaOperacao",
            $this->std->naturezaOperacao,
            true
        );
        $this->dom->addChild(
            $InfRps,
            "OptanteSimplesNacional",
            $this->std->optanteSimplesNacional,
            true
        );
        $this->dom->addChild(
            $InfRps,
            "IncentivadorCultural",
            $this->std->incentivadorCultural,
            true
        );
        $this->dom->addChild(
            $InfRps,
            "Status",
            $this->std->status,
            true
        );

        /** Servico **/
        $Servico = $this->dom->createElement('Servico');

        /** Valores **/
        $Valores = $this->dom->createElement('Valores');

        $this->dom->addChild(
            $Valores,
            "ValorServicos",
            $this->std->Servico->ValorServicos,
            true
        );

        $this->dom->addChild(
            $Valores,
            "ValorDeducoes",
            $this->std->Servico->ValorDeducoes,
            true
        );

        $this->dom->addChild(
            $Valores,
            "ValorPis",
            $this->std->Servico->ValorPis,
            true
        );

        $this->dom->addChild(
            $Valores,
            "ValorCofins",
            $this->std->Servico->ValorCofins,
            true
        );

        $this->dom->addChild(
            $Valores,
            "ValorInss",
            $this->std->Servico->ValorInss,
            true
        );

        $this->dom->addChild(
            $Valores,
            "ValorIr",
            $this->std->Servico->ValorIr,
            true
        );

        $this->dom->addChild(
            $Valores,
            "ValorCsll",
            $this->std->Servico->ValorCsll,
            true
        );

        $this->dom->addChild(
            $Valores,
            "IssRetido",
            $this->std->Servico->IssRetido,
            true
        );

        $this->dom->addChild(
            $Valores,
            "ValorIss",
            $this->std->Servico->ValorIss,
            true
        );

        $this->dom->addChild(
            $Valores,
            "OutrasRetencoes",
            $this->std->Servico->OutrasRetencoes,
            true
        );

        $this->dom->addChild(
            $Valores,
            "BaseCalculo",
            $this->std->Servico->BaseCalculo,
            true
        );

        $this->dom->addChild(
            $Valores,
            "Aliquota",
            $this->std->Servico->Aliquota,
            true
        );

        $this->dom->addChild(
            $Valores,
            "ValorLiquidoNfse",
            $this->std->Servico->ValorLiquidoNfse,
            true
        );

        $this->dom->addChild(
            $Valores,
            "DescontoIncondicionado",
            $this->std->Servico->DescontoIncondicionado,
            true
        );

        $this->dom->addChild(
            $Valores,
            "DescontoCondicionado",
            $this->std->Servico->DescontoCondicionado,
            true
        );

        $Servico->appendChild($Valores);

        $this->dom->addChild(
            $Servico,
            "ItemListaServico",
            $this->std->Servico->ItemListaServico,
            true
        );

        $this->dom->addChild(
            $Servico,
            "CodigoCnae",
            $this->std->Servico->CodigoCnae,
            true
        );

        $this->dom->addChild(
            $Servico,
            "Discriminacao",
            $this->std->Servico->Discriminacao,
            true
        );

        $this->dom->addChild(
            $Servico,
            "CodigoMunicipio",
            $this->std->Servico->CodigoMunicipio,
            true
        );

        $InfRps->appendChild($Servico);

        /** Prestador **/
        $Prestador = $this->dom->createElement('Prestador');

        $this->dom->addChild(
            $Prestador,
            "Cnpj",
            $this->std->Prestador->Cnpj,
            true
        );

        $this->dom->addChild(
            $Prestador,
            "InscricaoMunicipal",
            $this->std->Prestador->InscricaoMunicipal,
            true
        );

        $InfRps->appendChild($Prestador);

        /** Tomador **/
        $Tomador = $this->dom->createElement('Tomador');

        $IdentificacaoTomador = $this->dom->createElement('IdentificacaoTomador');

        $CpfCnpjTomador = $this->dom->createElement('CpfCnpj');

        if (!empty($this->std->Tomador->Cpf)) {
            $this->dom->addChild(
                $CpfCnpjTomador,
                "Cpf",
                $this->std->Tomador->Cpf,
                true
            );
        }

        if (!empty($this->std->Tomador->Cnpj)) {
            $this->dom->addChild(
                $CpfCnpjTomador,
                "Cnpj",
                $this->std->Tomador->Cnpj,
                true
            );
        }

        $IdentificacaoTomador->appendChild($CpfCnpjTomador);

        $this->dom->addChild(
            $IdentificacaoTomador,
            "InscricaoMunicipal",
            $this->std->Tomador->InscricaoMunicipal,
            true
        );

        $Tomador->appendChild($IdentificacaoTomador);

        $this->dom->addChild(
            $Tomador,
            "RazaoSocial",
            $this->std->Tomador->RazaoSocial,
            true
        );

        $EnderecoTomador = $this->dom->createElement('Endereco');

        $this->dom->addChild(
            $EnderecoTomador,
            "Endereco",
            $this->std->Tomador->Endereco,
            true
        );

        $this->dom->addChild(
            $EnderecoTomador,
            "Numero",
            $this->std->Tomador->Numero,
            true
        );

        $this->dom->addChild(
            $EnderecoTomador,
            "Complemento",
            $this->std->Tomador->Complemento,
            true
        );

        $this->dom->addChild(
            $EnderecoTomador,
            "Bairro",
            $this->std->Tomador->Bairro,
            true
        );

        $this->dom->addChild(
            $EnderecoTomador,
            "CodigoMunicipio",
            $this->std->Tomador->CodigoMunicipio,
            true
        );

        $this->dom->addChild(
            $EnderecoTomador,
            "Uf",
            $this->std->Tomador->Uf,
            true
        );

        $this->dom->addChild(
            $EnderecoTomador,
            "Cep",
            $this->std->Tomador->Cep,
            true
        );

        $Tomador->appendChild($EnderecoTomador);

        $ContatoTomador = $this->dom->createElement('Contato');

        $this->dom->addChild(
            $ContatoTomador,
            "Telefone",
            $this->std->Tomador->Telefone,
            true
        );

        $this->dom->addChild(
            $ContatoTomador,
            "Email",
            $this->std->Tomador->Email,
            true
        );

        $Tomador->appendChild($ContatoTomador);

        $InfRps->appendChild($Tomador);

        /** IntermediarioServico **/
        if (!empty($this->std->IntermediarioServico)) {
            $IntermediarioServico = $this->dom->createElement('IntermediarioServico');

            $this->dom->addChild(
                $IntermediarioServico,
                "RazaoSocial",
                $this->std->IntermediarioServico->RazaoSocial,
                true
            );

            $CpfCnpjIntermediarioServico = $this->dom->createElement('CpfCnpj');

            if (!empty($this->std->IntermediarioServico->Cpf)) {
                $this->dom->addChild(
                    $CpfCnpjIntermediarioServico,
                    "Cpf",
                    $this->std->IntermediarioServico->Cpf,
                    true
                );
            }

            if (!empty($this->std->IntermediarioServico->Cnpj)) {
                $this->dom->addChild(
                    $CpfCnpjIntermediarioServico,
                    "Cnpj",
                    $this->std->IntermediarioServico->Cnpj,
                    true
                );
            }

            $IntermediarioServico->appendChild($CpfCnpjTomador);

            $this->dom->addChild(
                $IntermediarioServico,
                "InscricaoMunicipal",
                $this->std->IntermediarioServico->InscricaoMunicipal,
                true
            );

            $InfRps->appendChild($IntermediarioServico);
        }

        /** ContrucaoCivil **/
        if (!empty($this->std->ContrucaoCivil)) {
            $ContrucaoCivil = $this->dom->createElement('ContrucaoCivil');

            $this->dom->addChild(
                $ContrucaoCivil,
                "CodigoObra",
                $this->std->ContrucaoCivil->CodigoObra,
                true
            );

            $this->dom->addChild(
                $ContrucaoCivil,
                "Art",
                $this->std->ContrucaoCivil->Art,
                true
            );

            $InfRps->appendChild($ContrucaoCivil);
        }

        /** FIM **/

        $this->rps->appendChild($InfRps);

        $this->dom->appendChild($this->rps);

        return $this->dom->saveXML($this->rps);
    }

    /**
     * Calcula totais
     * @return stdClass
     */
    protected function calcValor(): stdClass
    {
        $std = new stdClass();
        $std->valorFinal = 0;
        $std->valorItens = 0;
        $std->valorDeducao = 0;

        foreach ($this->std->itens as $item) {
            $std->valorItens += $item->valortotal;
        }
        if (!empty($this->std->deducoes)) {
            foreach ($this->std->deducoes as $deducao) {
                $std->valorDeducao += $deducao->valordeduzir;
            }
        }
        $std->valorFinal = $std->valorItens - $std->valorDeducao;
        return $std;
    }
}
