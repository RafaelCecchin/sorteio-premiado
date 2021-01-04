<?php
    ini_set('xdebug.max_nesting_level', 1000);

    include '/var/www/html/_bd/conexao_bd.php';
    /*include '../../../_bd/conexao_bd.php';*/

    $sorteio = numeroSorteio();
    $bilhetes = getBilhetes();
    $qtd_bilhetes = count($bilhetes);
    $premiacao = getPremiacao();
    $premiacao_bonus = getPremiacaoBonus();
    $total_caixa = getTotalCaixa();
    $razao_atual = getRazaoAtual();
    $razao_selecionada = getRazaoSelecionada();

    adicionarSorteioAberto();
    realizarSorteio();
        
    function adicionarSorteioAberto() {
        $conn = getConnection();
        $sql = 'INSERT INTO sorteios () VALUES ()';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        

        if (getSorteiosAbertos()<11) {
            adicionarSorteioAberto();
        }
    }

    function getSorteiosAbertos() {
        $conn = getConnection();
        $sql = 'SELECT (MAX(id)-MIN(id)+1) AS sorteios_abertos FROM sorteios WHERE fechado = 0';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $sorteios_abertos = $retorno->sorteios_abertos;

        return $sorteios_abertos;
    }

    function realizarSorteio() {
        $sorteio_atual = 0;
        $numeros_sorteados = array();
        $bonus_sorteado = array();

        for ($cont = 1; $cont<=20; $cont++) {
            do {
                $sorteio_atual = rand(1,80);
            } while(in_array($sorteio_atual,$numeros_sorteados));
            array_push($numeros_sorteados,$sorteio_atual);
        }

        for ($cont = 1; $cont<=5; $cont++) {
            $sorteio_atual = rand(0,9);
            array_push($bonus_sorteado,$sorteio_atual);
        }

        conferirBilhetes($numeros_sorteados, $bonus_sorteado);
    }


    function conferirBilhetes($numeros_sorteados, $bonus_sorteado) {
        $bilhetes_premiados = array();

        for ($i = 0; $i<$GLOBALS["qtd_bilhetes"]; $i++) {
            $bilhete = $GLOBALS["bilhetes"][$i];

            $valor = $bilhete->valor;
            $id_aposta = $bilhete->id;
            $numeros_apostados = explode(",", $bilhete->numeros_apostados);
            $bonus_apostado = str_split($bilhete->numero_bonus);
            
            $quantidade_apostada = count($numeros_apostados);
            $acertos_numeros = conferirNumerosApostados($numeros_sorteados, $numeros_apostados);
            $acertos_bonus = conferirNumeroBonus($bonus_sorteado, $bonus_apostado);

            $id_premiacao_bonus = conferirPremiacaoBonus($acertos_bonus);
            $id_premiacao = conferirPremiacao($quantidade_apostada, $acertos_numeros);
            

            if ($id_premiacao) {
                array_push($bilhetes_premiados, ["aposta"=>$id_aposta, "premiacao"=>$id_premiacao, "premiacao_bonus"=>'0', "total"=>getTotalPremiacao($valor, $id_premiacao)]);
            }

            if ($id_premiacao_bonus) {
                array_push($bilhetes_premiados, ["aposta"=>$id_aposta, "premiacao"=>'0', "premiacao_bonus"=>$id_premiacao_bonus, "total"=>getTotalPremiacaoBonus($id_premiacao_bonus)]);
            }
        }

        $total_premiacao = totalPremiacao($bilhetes_premiados);
        
        if (($total_premiacao<=$GLOBALS["total_caixa"] && $total_premiacao<=400) || ($total_premiacao<=400 && ($GLOBALS["razao_atual"]<$GLOBALS["razao_selecionada"] || $total_premiacao==0))) {
                salvarPremiacoes($bilhetes_premiados);
                salvarSorteio($numeros_sorteados, $bonus_sorteado);
        } else {
            realizarSorteio();
        }
    }

    function totalPremiacao($bilhetes_premiados) {
        $qtd_premiacoes = count($bilhetes_premiados);
        $valor_total = 0;

        for ($i = 0; $i<$qtd_premiacoes; $i++) {
            $bilhete = $bilhetes_premiados[$i];
            $valor_total += $bilhete['total'];
        }

        return $valor_total;
    }

    function getTotalCaixa() {
        $conn = getConnection();
        $sql = "SELECT COALESCE(SUM(CASE cm.id_caixa WHEN 2 THEN (CASE tm.direcao WHEN 0 THEN cm.valor ELSE cm.valor*-1 END) END), 0)-(SELECT COALESCE(SUM(valor), 0) FROM premiacoes_pagamentos WHERE paga = 0) AS saldo FROM `caixas_movimentacoes` AS cm LEFT JOIN tipos_movimentacoes AS tm ON cm.id_tipo_movimentacao = tm.id LEFT JOIN caixas AS c ON cm.id_caixa = c.id";
        $stmt = $conn-> prepare($sql);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $total_caixa = $retorno->saldo;

        return $total_caixa;
    }

    function getBilhetes() {
        $conn = getConnection();
        $sql = 'SELECT a.id, b.numeros_apostados, b.numero_bonus, b.valor FROM apostas AS a LEFT JOIN bilhetes AS b ON b.id = a.id_bilhete WHERE a.id_sorteio = (SELECT MIN(id) FROM sorteios WHERE fechado = 0)';
        $stmt = $conn-> prepare($sql);
        $stmt-> execute();
        $bilhetes = $stmt -> fetchAll(PDO::FETCH_OBJ);

        return $bilhetes;
    }

    function getPremiacao() {
        $conn = getConnection();
        $sql = 'SELECT * FROM premiacao';
        $stmt = $conn-> prepare($sql);
        $stmt-> execute();
        $premiacao = $stmt -> fetchAll(PDO::FETCH_OBJ);

        return $premiacao;
    }

    function getPremiacaoBonus() {
        $conn = getConnection();
        $sql = 'SELECT * FROM premiacao_bonus';
        $stmt = $conn-> prepare($sql);
        $stmt-> execute();
        $premiacao_bonus = $stmt -> fetchAll(PDO::FETCH_OBJ);

        return $premiacao_bonus;
    }

    function conferirNumerosApostados($numeros_sorteados, $numeros_apostados) {
        $acertos = array_intersect($numeros_sorteados, $numeros_apostados);
        return count($acertos);
    }

    function conferirNumeroBonus($bonus_sorteado, $bonus_apostado) {

        $acertos = 0;
        for ($i = 4; $i>=0; $i--) {
            if ($bonus_sorteado[$i]==$bonus_apostado[$i]) {
                $acertos++;
            } else {
                break;
            }
        }

        return $acertos;
    }

    function conferirPremiacao($quantidade_apostada, $quantidade_acertos) {
        $premiacao = $GLOBALS["premiacao"];
        for ($i = 0; $i < count($premiacao); $i++) {
            $premio = $premiacao[$i];
            if ($premio->quantidade_apostada==$quantidade_apostada && $premio->quantidade_acertos==$quantidade_acertos) {
                return $premio->id;
            }
        }
        return 0;
    }
    
    function conferirPremiacaoBonus($quantidade_acertos) {
        $premiacao_bonus = $GLOBALS["premiacao_bonus"];
        for ($i = 0; $i < count($premiacao_bonus); $i++) {
            $premio = $premiacao_bonus[$i];
            if ($premio->quantidade_acertos==$quantidade_acertos) {
                return $premio->id;
            }
        }
        return 0;
    }

    function getTotalPremiacao($valor, $id_premiacao) {
        $premiacao = $GLOBALS["premiacao"];
        for ($i = 0; $i < count($premiacao); $i++) {
            $premio = $premiacao[$i];
            if ($premio->id == $id_premiacao) {
                return ($premio->multiplicador)*$valor;
            }
        }
    }

    function getTotalPremiacaoBonus($id_premiacao_bonus) {
        $premiacao = $GLOBALS["premiacao_bonus"];
        for ($i = 0; $i < count($premiacao); $i++) {
            $premio = $premiacao[$i];
            if ($premio->id == $id_premiacao_bonus) {
                return $premio->valor;
            }
        }
    }

    function getValorApostado($aposta) {
        $conn = getConnection();

        $sql = 'SELECT b.valor FROM apostas AS a LEFT JOIN bilhetes AS b ON a.id_bilhete = b.id WHERE a.id = ?';
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $aposta);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $valor = $retorno->valor;

        return $valor;
    }

    function getMultiplicadorPremiacao($premiacao) {
        $conn = getConnection();

        $sql = 'SELECT multiplicador FROM premiacao WHERE id = ?';
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $premiacao);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $multiplicador = $retorno->multiplicador;

        return $multiplicador;
    }

    function salvarSorteio($numeros_sorteados, $bonus_sorteado) {
	    $conn = getConnection();
        $sql = 'UPDATE sorteios SET numeros_sorteados=?, numero_bonus=?, fechado=1 WHERE id=?';
        $stmt = $conn->prepare($sql);
        $stmt-> bindValue(1, implode(",", $numeros_sorteados));
        $stmt-> bindValue(2, implode("", $bonus_sorteado));
        $stmt-> bindValue(3, numeroSorteio());
        $stmt->execute();
    }

    function salvarPremiacoes($bilhetes_premiados) {
        $qtd_premiacoes = count($bilhetes_premiados);
        $conn = getConnection();

        for ($i = 0; $i<$qtd_premiacoes; $i++) {
            $bilhete = $bilhetes_premiados[$i];
            $sql = 'INSERT INTO premiacoes (id_aposta, id_premiacao, id_premiacao_bonus, valor) VALUES (?, ?, ?, ?)';
            $stmt = $conn-> prepare($sql);
            $stmt-> bindValue(1, $bilhete['aposta']);
            $stmt-> bindValue(2, $bilhete['premiacao']);
            $stmt-> bindValue(3, $bilhete['premiacao_bonus']);
            $stmt-> bindValue(4, $bilhete['total']);
            $stmt-> execute();
        }
    }
    
    function numeroSorteio() {
        $conn = getConnection();
        $sql = 'SELECT MIN(id) AS id FROM sorteios WHERE fechado=0';
        $stmt = $conn->prepare($sql);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $numero_sorteio = $retorno->id;

        return $numero_sorteio;
    }

    function getRazaoSelecionada() {
        $conn = getConnection();
        $sql = 'SELECT COALESCE(razao_selecionada, 0) AS razao_selecionada FROM configuracoes';
        $stmt = $conn->prepare($sql);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $razao_selecionada = $retorno->razao_selecionada;

        return $razao_selecionada;
    }

    function getRazaoAtual() {
        $conn = getConnection();
        $sql = 'SELECT COALESCE(razao_atual, 0) AS razao_atual FROM configuracoes';
        $stmt = $conn->prepare($sql);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $razao_atual = $retorno->razao_atual;

        return $razao_atual;
    }
