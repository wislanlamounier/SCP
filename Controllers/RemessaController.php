<?php

require_once '../Views/RemessaView.php';
require_once '../Classes/cpf.php';
require_once '../Classes/datasehoras.php';
require_once '../Ados/RemessaAdo.php';
require_once '../Ados/ClienteAdo.php';
require_once '../Ados/RemessaAdo.php';
require_once '../Ados/BoletoAdo.php';
require_once '../Models/RemessaModel.php';

class RemessaController {

    private $RemessaView = null;
    private $RemessaAdo = null;
    private $RemessaModel = null;

    public function __construct() {
        $this->RemessaView = new RemessaView();
        $this->RemessaAdo = new RemessaAdo();
        $this->RemessaModel = new RemessaModel();

        $acao = $this->RemessaView->getAcao();

        switch ($acao) {
            case 'gbo':
                $this->gerarBoleto();
                break;
            
            case 'grm':
                $this->gerarRemessas();
                break;

            case 'grma':
                $this->gerarRemessasAnteriores();
                break;

            default:
                $this->RemessaView = new RemessaView();
                break;
        }

        $this->RemessaView->displayInterface(NULL);
    }

function gerarRemessas() {
        $arrayBoletoRemessa = $this->RemessaAdo->consultaBoletosParaRemessa();
        $ClienteAdo = new ClienteAdo();
        $data = date("dmy");
        //$narquivo = date("dmy");
        date_default_timezone_set('America/Sao_Paulo');
        $codArquivo = date("dmy");
        $numeroArquivo = '00';
        $extensaoArquivo = ".seq";
        $textCompleto = $text = null;

        $nomeDoArquivo = $numeroArquivo . $codArquivo . $extensaoArquivo;

        for ($numeroArquivo; is_readable("C:\Remessas\\" . $nomeDoArquivo); $numeroArquivo++) {
            $numeroArquivo = str_pad($numeroArquivo, 2, "0", STR_PAD_LEFT);
            $nomeDoArquivo = $numeroArquivo . $codArquivo . $extensaoArquivo;
        }

        $fp = fopen("C:\Remessas\\" . $nomeDoArquivo, "w");
        //$data1 = explode("-", $data);
        $text .= "0"
                . "1"
                . "REMESSA"
                . "01"
                . str_pad("COBRANCA", 15, " ", STR_PAD_RIGHT)
                . "0"
                . "0501"
                . "55"
                . "05010030310"
                . "  "
                . "PARK VILLE INCORPORACAO S LTDA"
                . "399"
                . str_pad("HSBC", 15, " ", STR_PAD_RIGHT)
                . $data
                . "01600"
                . "BPI" .
                "  "
                . "LANCV08"
                . str_pad("", 277, " ", STR_PAD_RIGHT)
                . "000001"
                . "\r\n";
        $i = 2;
        if (is_array($arrayBoletoRemessa)) {
            foreach ($arrayBoletoRemessa as $BoletoModel) {

                $boletoId = $BoletoModel['0'];
                $boletoNumeroDocumento = $BoletoModel['1'];
                $boletoNossoNumero = $BoletoModel['2'];
                $CPF = new CPF;
                $DatasEHoras = new DatasEHoras;
                $boletoSacado = $BoletoModel['3'];
                $Cliente = $ClienteAdo->consultaObjetoPeloId($boletoSacado);
                $clienteNome = $Cliente->getClienteNome();
                $clienteCPF = $Cliente->getClienteCPF();
                $clienteEndereco = $Cliente->getClienteEndereco();
                $clienteEstado = $Cliente->getClienteEstado();
                $clienteCidade = $Cliente->getClienteCidade();
                $clienteCEP = $CPF::retiraMascaraCPF($Cliente->getClienteCEP());

                $Sacado = $clienteNome . " - " . $clienteCPF;

                $boletoRemetido = $BoletoModel['4'];
                $boletoDataVencimento = $DatasEHoras->getDataInvertidaComTracos($BoletoModel['5']);
                $boletoDataEmissao = $DatasEHoras->getDataInvertidaComTracos($BoletoModel['6']);
                $dataemissao = date("dmy", strtotime($boletoDataVencimento));
                $datavenc = date("dmy", strtotime($boletoDataVencimento));
                //$boletoNumeroParcela = $BoletoModel['7'];
                $boletoValor = number_format($BoletoModel['8'], 2, "", "");


                $ncpf = $CPF::retiraMascaraCPF($clienteCPF);
                if (is_null($ncpf)) {
                    $cod = "98";
                } else {
                    if (strlen($ncpf) == 11) {
                        $cod = "01";
                    } elseif (strlen($ncpf) > 11) {
                        $cod = "02";
                    } else {
                        $cod = "99";
                    }
                }
                // 01 CPF // 02 CNPJ // 98 NÃƒO TEM // 99 OUTROS                                                                                   DESCONTO_DATA              VALOR                                                                                                  CARTEIRA.OC
                $text .= "1"                                                        //POSIÃ‡ÃƒO 01 DE 01
                        . "02"                                                      //
                        . "23501469000100"                                          //
                        . "0"                                                       //
                        . "0501"                                                    //
                        . "55"                                                      //
                        . "05010030310"                                             //
                        . "  "                                                      //
                        . str_pad("", 25, " ", STR_PAD_RIGHT)                       //
                        . $boletoNossoNumero                                        //
                        . str_pad("", 6, " ", STR_PAD_RIGHT)                        //
                        . str_pad("", 11, " ", STR_PAD_RIGHT)                       //
                        . str_pad("", 6, " ", STR_PAD_RIGHT)                        //
                        . str_pad("", 11, " ", STR_PAD_RIGHT)                       //
                        . "1"                                                       // POSIÃ‡ÃƒO 108 DE 108 - TIPO CARTEIRA 1 - C0BRANÃ‡A SIMPLES
                        . "01"                                                      // POSIÃ‡ÃƒO 109 DE 110 OCORRÃŠNCIA - REMESSA 01 
                        . str_pad($boletoNumeroDocumento, 10, " ", STR_PAD_RIGHT)  //
                        . $datavenc
                        . str_pad($boletoValor, 13, "0", STR_PAD_LEFT)
                        . "399"
                        . "00000"
                        . "98"
                        . "N"
                        . $dataemissao
                        . "15"                                                                                  // POSICAO 157 A 158 * INSTRUCAO 01
                        . "00"
                        . str_pad("", 8, " ", STR_PAD_LEFT) . "T" . "0003" //POSIÃ‡ÃƒO 161 A 173 JUROS DE MORA
                        . "000000"                                     //POSIÃ‡ÃƒO 174 A 179 DATA DESCONTO
                        . str_pad("", 13, "0", STR_PAD_LEFT)           //POSIÃ‡ÃƒO 180 A 192 VALOR DO DESCONTO
                        . str_pad("", 13, "0", STR_PAD_LEFT)           //POSIÃ‡ÃƒO 193 A 205 VALOR DO IOF
                        . $datavenc . "1000" . str_pad("", 3, " ", STR_PAD_LEFT)           //POSIÃ‡ÃƒO 206 A 218 VALOR DA MULTA
                        . $cod
                        . str_pad($ncpf, 14, "0", STR_PAD_LEFT)
                        . strtoupper(str_pad($CPF->retiraAcentos($clienteNome), 40, " ", STR_PAD_RIGHT))
                        . strtoupper(str_pad($CPF->retiraAcentos($clienteEndereco), 38, " ", STR_PAD_RIGHT))
                        . "  "
                        . str_pad("0", 12, " ", STR_PAD_RIGHT)
                        . $clienteCEP
                        . strtoupper(str_pad($CPF->retiraAcentos($clienteCidade), 15, " ", STR_PAD_RIGHT))
                        . strtoupper($CPF->retiraAcentos($clienteEstado))
                        . str_pad("", 39, " ", STR_PAD_RIGHT)
                        . " "
                        . "  "
                        . "9"
                        . str_pad($i, 6, "0", STR_PAD_LEFT)
                        . "\r\n";
                $i++;
            }
        }
        if (!isset($i)) {
            $i = 2;
        }
        $text .= "9"
                . str_pad("", 393, " ", STR_PAD_RIGHT) .
                str_pad($i, 6, "0", STR_PAD_LEFT);
        $escreve = fwrite($fp, $text);

        if ($fp == false)
            die('NÃ£o foi possÃ­vel abrir o arquivo.');

        if ($fp == true) {
            $RemessaAdo = new RemessaAdo();
            $RemessaAdo->alteraRemetido();
            fclose($fp);
        }
    }

    function gerarRemessasAnteriores() {
        $arrayBoletoRemessa = $this->RemessaAdo->consultaBoletosParaRemessaAnterior();
        $ClienteAdo = new ClienteAdo();
        $data = date("dmy");
        //$narquivo = date("dmy");
        date_default_timezone_set('America/Sao_Paulo');
        $codArquivo = date("dmy");
        $numeroArquivo = '00';
        $extensaoArquivo = ".seq";
        $textCompleto = $text = null;

        $nomeDoArquivo = $numeroArquivo . $codArquivo . $extensaoArquivo;

        for ($numeroArquivo; is_readable("C:\Remessas-Anteriores\\" . $nomeDoArquivo); $numeroArquivo++) {
            $numeroArquivo = str_pad($numeroArquivo, 2, "0", STR_PAD_LEFT);
            $nomeDoArquivo = $numeroArquivo . $codArquivo . $extensaoArquivo;
        }

        $fp = fopen("C:\Remessas-Anteriores\\" . $nomeDoArquivo, "w");
        //$data1 = explode("-", $data);
        $text .= "0"
                . "1"
                . "REMESSA"
                . "01"
                . str_pad("COBRANCA", 15, " ", STR_PAD_RIGHT)
                . "0"
                . "0501"
                . "55"
                . "05010030310"
                . "  "
                . "PARK VILLE INCORPORACAO S LTDA"
                . "399"
                . str_pad("HSBC", 15, " ", STR_PAD_RIGHT)
                . $data
                . "01600"
                . "BPI" .
                "  "
                . "LANCV08"
                . str_pad("", 277, " ", STR_PAD_RIGHT)
                . "000001"
                . "\r\n";
        $i = 2;
        if (is_array($arrayBoletoRemessa)) {
            foreach ($arrayBoletoRemessa as $BoletoModel) {

                $boletoId = $BoletoModel['0'];
                $boletoNumeroDocumento = $BoletoModel['1'];
                $boletoNossoNumero = $BoletoModel['2'];
                $CPF = new CPF;
                $DatasEHoras = new DatasEHoras;
                $boletoSacado = $BoletoModel['3'];
                $Cliente = $ClienteAdo->consultaObjetoPeloId($boletoSacado);
                $clienteNome = $Cliente->getClienteNome();
                $clienteCPF = $Cliente->getClienteCPF();
                $clienteEndereco = $Cliente->getClienteEndereco();
                $clienteEstado = $Cliente->getClienteEstado();
                $clienteCidade = $Cliente->getClienteCidade();
                $clienteCEP = $CPF::retiraMascaraCPF($Cliente->getClienteCEP());

                $Sacado = $clienteNome . " - " . $clienteCPF;

                $boletoRemetido = $BoletoModel['4'];
                $boletoDataVencimento = $DatasEHoras->getDataInvertidaComTracos($BoletoModel['5']);
                $datavenc = date("dmy", strtotime($boletoDataVencimento));
                $boletoNumeroParcela = $BoletoModel['6'];
                $boletoValor = number_format($BoletoModel['7'], 2, "", "");

                $ncpf = $CPF::retiraMascaraCPF($clienteCPF);
                if (is_null($ncpf)) {
                    $cod = "98";
                } else {
                    if (strlen($ncpf) == 11) {
                        $cod = "01";
                    } elseif (strlen($ncpf) > 11) {
                        $cod = "02";
                    } else {
                        $cod = "99";
                    }
                }
                // 01 CPF // 02 CNPJ // 98 NÃƒO TEM // 99 OUTROS                                                                                   DESCONTO_DATA              VALOR                                                                                                  CARTEIRA.OC
                $text .= "1"
                        . "02"
                        . "23501469000100"
                        . "0"
                        . "0501"
                        . "55"
                        . "05010030310"
                        . "  "
                        . str_pad("", 25, " ", STR_PAD_RIGHT)
                        . $boletoNossoNumero
                        . str_pad("", 6, " ", STR_PAD_RIGHT)
                        . str_pad("", 11, " ", STR_PAD_RIGHT)
                        . str_pad("", 6, " ", STR_PAD_RIGHT)
                        . str_pad("", 11, " ", STR_PAD_RIGHT)
                        . "0"
                        . "00"
                        . str_pad($boletoNumeroDocumento, 10, " ", STR_PAD_RIGHT)
                        . $datavenc
                        . str_pad($boletoValor, 13, "0", STR_PAD_LEFT)
                        . "399"
                        . "00000"
                        . "98"
                        . "N"
                        . "100416"
                        . "00"
                        . "00"
                        . str_pad("", 13, " ", STR_PAD_RIGHT)
                        . "000000"
                        . str_pad("", 13, "0", STR_PAD_LEFT)
                        . str_pad("", 13, "0", STR_PAD_LEFT)
                        . str_pad("", 13, "0", STR_PAD_LEFT)
                        . $cod
                        . str_pad($ncpf, 14, "0", STR_PAD_LEFT)
                        . strtoupper(str_pad($CPF->retiraAcentos($clienteNome), 40, " ", STR_PAD_RIGHT))
                        . strtoupper(str_pad($CPF->retiraAcentos($clienteEndereco), 38, " ", STR_PAD_RIGHT))
                        . "  "
                        . str_pad("0", 12, " ", STR_PAD_RIGHT)
                        . $clienteCEP
                        . strtoupper(str_pad($CPF->retiraAcentos($clienteCidade), 15, " ", STR_PAD_RIGHT))
                        . strtoupper($CPF->retiraAcentos($clienteEstado))
                        . str_pad("", 39, " ", STR_PAD_RIGHT)
                        . " "
                        . "  "
                        . "9"
                        . str_pad($i, 6, "0", STR_PAD_LEFT)
                        . "\r\n";
                $i++;
            }
        }
        if (!isset($i)) {
            $i = 2;
        }
        $text .= "9"
                . str_pad("", 393, " ", STR_PAD_RIGHT) .
                str_pad($i, 6, "0", STR_PAD_LEFT);
        $escreve = fwrite($fp, $text);

        if ($fp == false)
            die('NÃ£o foi possÃ­vel abrir o arquivo.');

        if ($fp == true) {
            $RemessaAdo = new RemessaAdo();
            $RemessaAdo->alteraRemetido();
            fclose($fp);
        }
    }
    
     function incluiBoleto() {
        $this->RemessaModel = $this->RemessaView->getDadosEntrada();

        $boletoId = $this->RemessaModel->getboletoId();
        $boletoNumeroDocumento = $this->RemessaModel->getboletoNumeroDocumento();
        $boletoNossoNumero = $this->RemessaModel->getboletoNossoNumero();
        $boletoRemetido = $this->RemessaModel->getboletoRemetido();
        $boletoSacado = $this->RemessaModel->getboletoSacado();
        $boletoDataVencimento = $this->RemessaModel->getboletoDataVencimento();
        $boletoDataEmissao = $this->RemessaModel->getboletoDataEmissao();
        $boletoValor = $this->RemessaModel->getboletoValor();
        $boletoProdutoId = $this->RemessaModel->getboletoProdutoId();

        if ($this->RemessaModel->checaAtributos()) {
            if ($this->RemessaAdo->insereObjeto($this->RemessaModel)) {
                $this->RemessaView->adicionaMensagemSucesso("Venda do apartamento " . $this->RemessaModel->getboletoNumeroDocumento() . " realizada com sucesso! ");

                $this->RemessaModel = new RemessaModel();
            } else {
                $this->RemessaView->adicionaMensagemErro("Erro ao realizar a venda do apartamento " . $this->RemessaModel->getboletoNumeroDocumento() . "!");
                $this->RemessaView->adicionaMensagemErro($this->RemessaAdo->getMensagem());
            }
        } else {
            $this->ProdutoView->adicionaMensagemAlerta($this->RemessaModel->getMensagem(), "Erro");
        }
    }
        }
