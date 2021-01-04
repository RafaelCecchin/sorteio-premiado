<?php
    include '../../../_bd/conexao_bd.php';
        
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['pesquisa']) && isset($_POST['inicio']) && isset($_POST['fim']) && isset($_POST['status']) && isset($_POST['pagina'])) { 
            
            $pesquisa = tratarNumeros($_POST['pesquisa']);
            $inicio = tratarData($_POST['inicio']);
            $fim =  tratarData($_POST['fim']);
            $status = tratarNumeros($_POST['status']);
            $pagina = tratarNumeros($_POST['pagina']);
            
            if (validarPesquisa($pesquisa) && validarData($inicio) && validarData($fim) && validarStatus($status) && validarPagina($pagina)) {
                retornaPremiacoes($pesquisa, $inicio, $fim, $status, $pagina);
            } else {
                retornar("0", "DADOS INVÁLIDOS!", null, null);
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

    function tratarData($date, $format = 'Y-m-d') {

        $d = DateTime::createFromFormat($format, $date);
    
        if ($d && $d->format($format) != $date) {
            return date('Y-m-d');
        }
    
        return $d->format('Y-m-d');
    }
    
    function validarData($date, $format = 'Y-m-d') {

        $d = DateTime::createFromFormat($format, $date);
    
        if ($d && $d->format($format) == $date) {
            return true;
        }
    
        return false;
    }

    function validarPagina($num) {
        if ($num>0) {
            return true;
        }

        return false;
    }

    function validarPesquisa($num) {
        if ($num>=0) {
            return true;
        }

        return false;
    }

    function validarStatus($status) {
        if ($status==0 || $status==1 || $status==2) {
            return true;
        }

        return false;
    }

    function getPremiacoes($id_bilhete) {
        $conn = getConnection();

        $sql = "SELECT a.id_sorteio, p.valor, pr.multiplicador, pb.quantidade_acertos AS bonus FROM premiacoes AS p LEFT JOIN apostas AS a ON p.id_aposta = a.id LEFT JOIN premiacao AS pr ON p.id_premiacao = pr.id LEFT JOIN premiacao_bonus AS pb ON p.id_premiacao_bonus = pb.id WHERE a.id_bilhete = ?";
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $id_bilhete);
        $stmt-> execute();
        $retorno = $stmt -> fetchAll(PDO::FETCH_OBJ);

        return $retorno;
    }

    function retornaPremiacoes($pesquisa, $inicio, $fim, $status, $pagina) {
        $conn = getConnection();

        if ($pesquisa!='0') {
            $sql = "SELECT pp.id, pp.id_bilhete, pp.valor, pp.paga, pp.dh_pagamento, pp.dh_insert FROM premiacoes_pagamentos AS pp WHERE (DATE(pp.dh_insert) BETWEEN ? AND ?) AND (pp.id_bilhete = ?) AND (pp.paga IN (?, ?)) ORDER BY pp.id_bilhete LIMIT ?, 25";
            $stmt = $conn-> prepare($sql);
            $stmt-> bindValue(1, $inicio);
            $stmt-> bindValue(2, $fim);
            $stmt-> bindValue(3, $pesquisa);
            $stmt-> bindValue(4, ($status=='0')?'0':(($status=='1')?'1':'0'));
            $stmt-> bindValue(5, ($status=='0')?'0':(($status=='1')?'1':'1'));
            $stmt-> bindValue(6, (($pagina-1)*25), PDO::PARAM_INT);
        } else {
            $sql = "SELECT pp.id, pp.id_bilhete, pp.valor, pp.paga, pp.dh_pagamento, pp.dh_insert FROM premiacoes_pagamentos AS pp WHERE (DATE(pp.dh_insert) BETWEEN ? AND ?) AND (pp.paga IN (?, ?)) ORDER BY pp.id_bilhete LIMIT ?, 25";
            $stmt = $conn-> prepare($sql);
            $stmt-> bindValue(1, $inicio);
            $stmt-> bindValue(2, $fim);
            $stmt-> bindValue(3, ($status=='0')?'0':(($status=='1')?'1':'0'));
            $stmt-> bindValue(4, ($status=='0')?'0':(($status=='1')?'1':'1'));
            $stmt-> bindValue(5, (($pagina-1)*25), PDO::PARAM_INT);            
        }

        $stmt-> execute();
        $retorno = $stmt -> fetchAll(PDO::FETCH_OBJ);

        if (!empty($retorno)) {
            $quantidade = count($retorno);
            for ($i = 0; $i<$quantidade; $i++) {

                $retorno[$i]->dh_insert = date('d/m/Y H:i:s', strtotime($retorno[$i]->dh_insert));
                if ($retorno[$i]->dh_pagamento!=null) {
                    $retorno[$i]->dh_pagamento = date('d/m/Y H:i:s', strtotime($retorno[$i]->dh_pagamento));
                }

                $retorno[$i]->premiacoes = getPremiacoes($retorno[$i]->id_bilhete);
                $retorno[$i]->quantidade = count($retorno[$i]->premiacoes);
            }
            retornar("1", "REGISTROS ENCONTRADOS COM SUCESSO!", $quantidade, $retorno);
        } else {
            retornar("0", "NENHUM REGISTRO ENCONTRADO!", null, null);
        }
    }

    function retornar($status, $descricao, $quantidade, $premiacoes_pagamento) {
        $arquivo = file_get_contents('../_json/consultar_premiacoes.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        $retorno->quantidade=$quantidade;
        $retorno->premiacoes_pagamento=$premiacoes_pagamento;
        echo json_encode($retorno);
    }