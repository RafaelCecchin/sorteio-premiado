<?php
    include '../../../_bd/conexao_bd.php';
        
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['inicio']) && isset($_POST['fim']) && isset($_POST['caixa']) && isset($_POST['tipo']) && isset($_POST['operacoes'])) {
            $inicio = tratarData($_POST['inicio']);
            $fim = tratarData($_POST['fim']);
            $caixa = tratarCaixa($_POST['caixa']);
            $tipo = tratarTipo($_POST['tipo']);
            $operacoes = tratarOperacoes($_POST['operacoes']);

            if (validarData($inicio) && validarData($fim) && validarCaixa($caixa) && validarTipo($tipo) && validarOperacoes($operacoes)) {
                retornaExtrato($inicio, $fim, $caixa, $tipo, $operacoes);
            } else {
                retornar("0","DADOS INVÁLIDOS!", null, null, null, null, null, null, null);
            }
        }
    } else {
        header('Location: ../_login/login.html');
    }
    
    function retornaExtrato($inicio, $fim, $caixa, $tipo, $operacoes) {
        $caixa = $caixa==0?'1,2':$caixa;
        $conn = getConnection();
        
        
        $sql = "SELECT cm.id, COALESCE(cm.id_origem, '###') AS id_origem, tm.descricao AS tipo_movimentacao, cm.valor, c.id AS caixa, tm.direcao, cm.dh_insert FROM `caixas_movimentacoes` AS cm LEFT JOIN tipos_movimentacoes AS tm ON cm.id_tipo_movimentacao = tm.id LEFT JOIN caixas AS c ON cm.id_caixa = c.id WHERE (DATE(cm.dh_insert) BETWEEN ? AND ?) AND cm.id_caixa IN (".$caixa.") AND cm.id_tipo_movimentacao IN (".$operacoes.")";
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $inicio);
        $stmt-> bindValue(2, $fim);
        $stmt-> execute();
        $movimentacoes = $stmt -> fetchAll(PDO::FETCH_OBJ);

        $quantidade = count($movimentacoes);
        $saldo_inicial = retornaSaldoInicial($inicio, $caixa);
        $saldo_final = retornaSaldoFinal($fim, $caixa);
        $outras_operacoes = retornaOutrasOperacoes($inicio, $fim, $caixa, $operacoes);
        $total = retornaTotal($inicio, $fim, $caixa, $operacoes);

        for ($i = 0; $i<$quantidade; $i++) {
            $movimentacoes[$i]->dh_insert = date('d/m/Y H:i:s', strtotime($movimentacoes[$i]->dh_insert));
        }

        if ($tipo==1)  {
            retornar("1", "REGISTROS ENCONTRADOS COM SUCESSO!", 1, $quantidade, $saldo_inicial, $saldo_final, $outras_operacoes, $total, $movimentacoes);
        } else {
            retornar("1", "REGISTROS ENCONTRADOS COM SUCESSO!", 2, 0, $saldo_inicial, $saldo_final, $outras_operacoes, $total, null);
        }
        
        
    }

    function retornaTotal($inicio, $fim, $caixa, $operacoes) {
        $conn = getConnection();
        $sql = "SELECT COALESCE(SUM(CASE tm.direcao WHEN 0 THEN cm.valor ELSE cm.valor*-1 END), 0) AS total FROM `caixas_movimentacoes` AS cm LEFT JOIN tipos_movimentacoes AS tm ON cm.id_tipo_movimentacao = tm.id LEFT JOIN caixas AS c ON cm.id_caixa = c.id WHERE (DATE(cm.dh_insert) BETWEEN ? AND ?) AND cm.id_caixa IN (".$caixa.") AND cm.id_tipo_movimentacao IN (".$operacoes.")";
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $inicio);
        $stmt-> bindValue(2, $fim);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $total = $retorno->total;
        return $total;
    }

    function retornaSaldoInicial($inicio, $caixa) {
        $conn = getConnection();
        $sql = "SELECT COALESCE(SUM(CASE tm.direcao WHEN 0 THEN cm.valor ELSE cm.valor*-1 END), 0) AS saldo_inicial FROM `caixas_movimentacoes` AS cm LEFT JOIN tipos_movimentacoes AS tm ON cm.id_tipo_movimentacao = tm.id LEFT JOIN caixas AS c ON cm.id_caixa = c.id WHERE DATE(cm.dh_insert) < ? AND cm.id_caixa IN (".$caixa.")";
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $inicio);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $saldo_inicial = $retorno->saldo_inicial;
        return $saldo_inicial;
    }

    function retornaSaldoFinal($fim, $caixa) {
        $conn = getConnection();
        $sql = "SELECT COALESCE(SUM(CASE tm.direcao WHEN 0 THEN cm.valor ELSE cm.valor*-1 END), 0) AS saldo_final FROM `caixas_movimentacoes` AS cm LEFT JOIN tipos_movimentacoes AS tm ON cm.id_tipo_movimentacao = tm.id LEFT JOIN caixas AS c ON cm.id_caixa = c.id WHERE DATE(cm.dh_insert) <= ? AND cm.id_caixa IN (".$caixa.")";
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $fim);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $saldo_final = $retorno->saldo_final;
        return $saldo_final;
    }

    function retornaOutrasOperacoes($inicio, $fim, $caixa, $operacoes) {
        $conn = getConnection();
        $sql = "SELECT COALESCE(SUM(CASE tm.direcao WHEN 0 THEN cm.valor ELSE cm.valor*-1 END), 0) AS outras_operacoes FROM `caixas_movimentacoes` AS cm LEFT JOIN tipos_movimentacoes AS tm ON cm.id_tipo_movimentacao = tm.id LEFT JOIN caixas AS c ON cm.id_caixa = c.id WHERE (DATE(cm.dh_insert) BETWEEN ? AND ?) AND cm.id_caixa IN (".$caixa.") AND cm.id_tipo_movimentacao NOT IN (".$operacoes.")";
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $inicio);
        $stmt-> bindValue(2, $fim);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $outras_operacoes = $retorno->outras_operacoes;
        return $outras_operacoes;
    }

    function tratarData($date, $format = 'Y-m-d') {

        $d = DateTime::createFromFormat($format, $date);
    
        if ($d && $d->format($format) != $date) {
            return date('Y-m-d');
        }
    
        return $d->format('Y-m-d');
    }

    function tratarTipo($num) {
        if ($num!=1 && $num!=2) {
            return null;
        }
        return $num;
    }

    function tratarCaixa($num) {
        if ($num!=0 && $num!=1 && $num!=2) {
            return null;
        }
        return $num;
    }

    function tratarOperacoes($operacoes) {
        $array_operacoes = explode(",", $operacoes);
        $retorno = "";
        for ($i = 0; $i<count($array_operacoes); $i++) {
            if ($array_operacoes[$i]=='1' || $array_operacoes[$i]=='2' || $array_operacoes[$i]=='3' || $array_operacoes[$i]=='4' || $array_operacoes[$i]=='5' || $array_operacoes[$i]=='6' || $array_operacoes[$i]=='7' || $array_operacoes[$i]=='8' || $array_operacoes[$i]=='9' || $array_operacoes[$i]=='10' || $array_operacoes[$i]=='11' || $array_operacoes[$i]=='12') {
                if ($retorno=="") {
                    $retorno = $retorno.$array_operacoes[$i];
                } else {
                    $retorno = $retorno.",".$array_operacoes[$i];
                }
            }
        }
        return $retorno;
    }

    function validarData($date, $format = 'Y-m-d') {

        $d = DateTime::createFromFormat($format, $date);
    
        if ($d && $d->format($format) == $date) {
            return true;
        }
    
        return false;
    }

    function validarCaixa($caixa) {
        if ($caixa==0 || $caixa==1 || $caixa==2) {
            return true;
        }

        return false;
    }

    function validarTipo($tipo) {
        if ($tipo==1 || $tipo==2) {
            return true;
        }

        return false;
    }

    function validarOperacoes($operacoes) {
        $array_operacoes = explode(",", $operacoes);
        for ($i = 0; $i<count($array_operacoes); $i++) {
            if ($array_operacoes[$i]!='1' && $array_operacoes[$i]!='2' && $array_operacoes[$i]!='3' && $array_operacoes[$i]!='4' && $array_operacoes[$i]!='5' && $array_operacoes[$i]!='6' && $array_operacoes[$i]!='7' && $array_operacoes[$i]!='8' && $array_operacoes[$i]!='9' && $array_operacoes[$i]!='10' && $array_operacoes[$i]!='11' && $array_operacoes[$i]!='12') {
                return false;
            }
        }
        return true;
    }

    function retornar($status, $descricao, $tipo, $quantidade, $saldo_inicial, $saldo_final, $outras_operacoes, $total, $movimentacoes) {
        $arquivo = file_get_contents('../_json/consultar_extrato.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        $retorno->tipo=$tipo;
        $retorno->quantidade=$quantidade;
        $retorno->saldo_inicial=$saldo_inicial;
        $retorno->saldo_final=$saldo_final;
        $retorno->outras_operacoes=$outras_operacoes;
        $retorno->total=$total;
        $retorno->movimentacoes=$movimentacoes;
        echo json_encode($retorno);
    }