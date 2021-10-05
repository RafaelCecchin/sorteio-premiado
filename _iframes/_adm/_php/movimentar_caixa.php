<?php
    include '../../../_bd/conexao_bd.php';
        
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['tipo']) && isset($_POST['caixa']) && isset($_POST['valor'])) { 

            $tipo = tratarNumeros($_POST['tipo']);
            $caixa = tratarNumeros($_POST['caixa']);
            $valor = tratarNumeros($_POST['valor']);

            if (validarTipo($tipo) && validarCaixa($caixa) && validarValor($valor)) {
                movimentarCaixa($tipo, $caixa, $valor);
            } else {
                retornar("0","DADOS INVÁLIDOS!");
            }

        }
    } else {
        header('Location: ../_login/login.html');
    }

    function tratarNumeros($num) {
        try {
            return (int)$num;
        } catch (Exception $e) {
            return null;
        }
    }


    function validarValor($valor) {
        if ($valor>0) {
            return true;
        } 

        return false;
    }

    function validarCaixa($caixa) {
        if ($caixa==1 || $caixa==2) {
            return true;
        }

        return false;
    }

    function validarTipo($tipo) {
        if ($tipo==1 || $tipo==2 || $tipo==3) {
            return true;
        } 

        return false;
    }

    function movimentarCaixa($tipo, $caixa, $valor) {

        $saldo = retornaSaldo();
        $saldo_principal = $saldo[0];
        $saldo_premio = $saldo[1];
        $saldo_caixa = $caixa==1?$saldo_principal:$saldo_premio;
        
        $conn = getConnection();
        
        switch ($tipo) {
            case 1: 
                $sql = 'INSERT INTO caixas_movimentacoes (id_tipo_movimentacao, id_caixa, valor) values (3, ?, ?)';
                $stmt = $conn-> prepare($sql);
                $stmt-> bindValue(1, $caixa);
                $stmt-> bindValue(2, $valor);
                if ($stmt-> execute()) {
                    retornar("1", "ENTRADA REALIZADA COM SUCESSO!");
                } else {
                    retornar("0", "ERRO AO REALIZAR ENTRADA!");
                }
                break;
            case 2:
                if ($saldo_caixa>=$valor) {
                    $sql = 'INSERT INTO caixas_movimentacoes (id_tipo_movimentacao, id_caixa, valor) values (9, ?, ?)';
                    $stmt = $conn-> prepare($sql);
                    $stmt-> bindValue(1, $caixa);
                    $stmt-> bindValue(2, $valor);
                    if ($stmt-> execute()) {
                        retornar("1", "SANGRIA REALIZADA COM SUCESSO!");
                    } else {
                        retornar("0", "ERRO AO REALIZAR SANGRIA!");
                    }
                } else {
                    retornar("0", "SALDO EM CAIXA INSUFICIENTE!");
                }
                break;
            case 3:
                if ($saldo_caixa>=$valor) {
                    $sql = 'INSERT INTO caixas_movimentacoes (id_tipo_movimentacao, id_caixa, valor) values (11, ?, ?), (5, ?, ?)';
                    $stmt = $conn-> prepare($sql);

                    $stmt-> bindValue(1, $caixa);
                    $stmt-> bindValue(2, $valor);
                    $stmt-> bindValue(3, $caixa==1?2:1);
                    $stmt-> bindValue(4, $valor);

                    if ($stmt-> execute()) {
                        retornar("1", "TRANSFERÊNCIA REALIZADA COM SUCESSO!");
                    } else {
                        retornar("0", "ERRO AO REALIZAR TRANSFERÊNCIA!");
                    }
                } else {
                    retornar("0", "SALDO EM CAIXA INSUFICIENTE!");
                }
                break;
        }
    }

    function retornaSaldo () {
        $conn = getConnection();
        $sql = "SELECT COALESCE(SUM(CASE cm.id_caixa WHEN 1 THEN (CASE tm.direcao WHEN 0 THEN cm.valor ELSE cm.valor*-1 END) END), 0) AS saldo_principal, COALESCE(SUM(CASE cm.id_caixa WHEN 2 THEN (CASE tm.direcao WHEN 0 THEN cm.valor ELSE cm.valor*-1 END) END), 0)-(SELECT COALESCE(SUM(valor), 0) FROM premiacoes_pagamentos WHERE paga = 0) AS saldo_premio FROM `caixas_movimentacoes` AS cm LEFT JOIN tipos_movimentacoes AS tm ON cm.id_tipo_movimentacao = tm.id LEFT JOIN caixas AS c ON cm.id_caixa = c.id";
        $stmt = $conn-> prepare($sql);
        
        if ($stmt-> execute()) {
            $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
            $saldo_principal = $retorno->saldo_principal;
            $saldo_premio = $retorno->saldo_premio;

            return [$saldo_principal, $saldo_premio];
        } else {
            return [0,0];
        }
    }
    
    function retornar($status, $descricao) {
        $arquivo = file_get_contents('../_json/movimentar_caixa.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        echo json_encode($retorno);
    }