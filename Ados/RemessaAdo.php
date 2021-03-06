<?php

require_once 'ADO.php';
require_once '../Classes/datasehoras.php';

class RemessaAdo extends ADO {

    public function consultaBoletosParaRemessa() {
        $query = "select * from Boletos where boletoRemetido = '0' ";

        $resultado = parent::executaQuery($query);

        if ($resultado) {
            //consulta Ok. Continua.s
        } else {
            parent::setMensagem("Erro no select de consultaBoletosParaRemessa: " . parent::getBdError());
            return false;
        }
        $BoletosModel = null;
        $DatasEHoras = new DatasEHoras();

        while ($boleto = parent::leTabelaBD()) {
            $boletoDataVencimento = $DatasEHoras->getDataEHorasDesinvertidaComBarras($boleto['boletoDataVencimento']);
            $boletoDataEmissao = $DatasEHoras->getDataEHorasDesinvertidaComBarras($boleto['boletoDataEmissao']);
            $BoletoModel = array($boleto['boletoId'], $boleto['boletoNumeroDocumento'], $boleto['boletoNossoNumero'], $boleto['boletoSacado'], $boleto['boletoRemetido'], $boletoDataVencimento, $boletoDataEmissao, $boleto['boletoNumeroParcela'], $boleto['boletoValor'], $boleto['boletoProdutoId']);
            $BoletosModel[] = $BoletoModel;
        }

        return $BoletosModel;
    }

    public function consultaBoletosParaRemessaAnterior() {
        $query = "select * from Boletos where boletoRemetido = '1' ";

        $resultado = parent::executaQuery($query);

        if ($resultado) {
            //consulta Ok. Continua.s
        } else {
            parent::setMensagem("Erro no select de consultaBoletosParaRemessaAnterior: " . parent::getBdError());
            return false;
        }
        $BoletosModel = null;
        $DatasEHoras = new DatasEHoras();

        while ($boleto = parent::leTabelaBD()) {
            $boletoDataVencimento = $DatasEHoras->getDataEHorasDesinvertidaComBarras($boleto['boletoDataVencimento']);
            $BoletoModel = array($boleto['boletoId'], $boleto['boletoNumeroDocumento'], $boleto['boletoNossoNumero'], $boleto['boletoSacado'], $boleto['boletoRemetido'], $boletoDataVencimento, $boletoDataEmissao, $boleto['boletoNumeroParcela'], $boleto['boletoValor'], $boleto['boletoProdutoId']);
            $BoletosModel[] = $BoletoModel;
        }

        return $BoletosModel;
    }

    public function alteraRemetido() {
        $query = "update Boletos set boletoRemetido = '1' ";

        $resultado = parent::executaQuery($query);

        if ($resultado) {
            return true;
        } else {
            parent::setMensagem("Erro no update de alteraRemetido: " . parent::getBdError());
            return false;
        }
    }

    public function alteraObjeto(\Model $objetoModelo) {
        
    }

    public function consultaArrayDeObjeto() {
        
    }

    public function consultaObjetoPeloId($id) {
        
    }

    public function excluiObjeto(\Model $objetoModelo) {
        
    }

    public function insereObjeto(Model $boletoModel) {
        $boletoId = $boletoModel->getboletoId();
        $boletoNumeroDocumento = $boletoModel->getboletoNumeroDocumento();
        $boletoNossoNumero = $boletoModel->getboletoNossoNumero();
        $boletoRemetido = $boletoModel->getboletoRemetido();
        $boletoSacado = $boletoModel->getboletoSacado();
        $boletoDataVencimento = $boletoModel->getboletoDataVencimento();
        $boletoDataEmissao = $boletoModel->getboletoDataEmissao();
        $boletoValor = number_format($boletoModel->getboletoValor(), 2, ".", "");
        $boletoProdutoId = $boletoModel->getboletoProdutoId();

        $query = "insert into Boletos (boletoId, boletoNumeroDocumento, boletoNossoNumero, boletoSacado, boletoRemetido, boletoDataVencimento, boletoDataEmissao, boletoNumeroParcela, boletoValor, boletoProdutoId) values (null, '$boletoNumeroDocumento', '$boletoNossoNumero','$boletoSacado', '$boletoRemetido', '$boletoDataVencimento', '$boletoDataEmissao', '$boletoNumeroDocumento', '$boletoValor', '$boletoProdutoId')";

        $resultado = parent::executaQuery($query);
        if ($resultado) {
            return true;
        } else {
            parent::setMensagem("Erro no insert de insereObjeto: " . parent::getBdError());
            return false;
        }
    }

}
