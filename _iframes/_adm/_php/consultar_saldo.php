<?php

    include '../../../_bd/conexao_bd.php';
    
    session_start();

    if (isset($_SESSION['logado'])) {
        retornaSaldo();
    } else {
        header('Location: ../_login/login.html');
    }

    function retornaSaldo() {
        $conn = getConnection();
        $sql = "SELECT COALESCE(SUM(CASE cm.id_caixa WHEN 1 THEN (CASE tm.direcao WHEN 0 THEN cm.valor ELSE cm.valor*-1 END) END), 0) AS saldo_principal, COALESCE(SUM(CASE cm.id_caixa WHEN 2 THEN (CASE tm.direcao WHEN 0 THEN cm.valor ELSE cm.valor*-1 END) END), 0) AS saldo_premio, (SELECT COALESCE(SUM(valor*-1), 0) FROM premiacoes_pagamentos WHERE paga = 0) as saldo_premiacoes_pendentes FROM `caixas_movimentacoes` AS cm LEFT JOIN tipos_movimentacoes AS tm ON cm.id_tipo_movimentacao = tm.id LEFT JOIN caixas AS c ON cm.id_caixa = c.id";
        $stmt = $conn-> prepare($sql);
        
        if ($stmt-> execute()) {
            $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
            $saldo_principal = $retorno->saldo_principal;
            $saldo_premio = $retorno->saldo_premio;
            $saldo_premiacoes_pendentes = $retorno->saldo_premiacoes_pendentes;

            retornar("1", "CONSULTA REALIZADA COM SUCESSO!", $saldo_principal, $saldo_premio, $saldo_premiacoes_pendentes);
        } else {
            retornar("0", "ERRO AO REALIZAR CONSULTA!", null, null, null);
        }
    }

    function retornar($status, $descricao, $saldo_principal, $saldo_premio, $saldo_premiacoes_pendentes) {
        $arquivo = file_get_contents('../_json/consultar_saldo.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        $retorno->saldo_principal=$saldo_principal;
        $retorno->saldo_premio=$saldo_premio;
        $retorno->saldo_premiacoes_pendentes=$saldo_premiacoes_pendentes;
        echo json_encode($retorno);
    }
