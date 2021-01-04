<?php

    include '../../../_bd/conexao_bd.php';
    
    session_start();

    if (isset($_SESSION['logado'])) {
        if (isset($_POST['numeros']) && isset($_POST['valor']) && isset($_POST['rodadas'])) {
            $numeros = tratarNumeros($_POST['numeros']);
            $valor = tratarNumeros($_POST['valor'])[0];
            $rodadas = tratarNumeros($_POST['rodadas'])[0];
            
            if (verificarNumeros($numeros) && verificaValor($valor) && verificarRodadas($rodadas)) {
                salvarBilhete($numeros, $valor, $rodadas);
            } else {
                retornar("0", "DADOS INVÁLIDOS!", null, null, null, null, null, null, null, null);
            }
        }
    } else {
        header('Location: ../_login/login.html');
    }

    function sorteiosAbertos($rodadas) {
        $conn = getConnection();

        $sql = 'SELECT min(id) AS id FROM sorteios WHERE fechado=0';
        $stmt = $conn-> prepare($sql);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $primeiroSorteioAberto = $retorno->id;

        $sql = 'SELECT max(id) AS id FROM sorteios WHERE fechado=0';
        $stmt = $conn-> prepare($sql);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        $ultimoSorteioAberto = $retorno->id;

        if ((($primeiroSorteioAberto-1)+$rodadas)<=$ultimoSorteioAberto) {
            return true;
        }

        return false;
    }

    function salvarBilhete($numeros, $valor, $rodadas) {
        if (sorteiosAbertos($rodadas)) {
            
            $conn = getConnection();
            $sql = 'INSERT INTO bilhetes (numeros_apostados, numero_bonus, rodadas, valor, id_usuario) VALUES (?, ?, ?, ?, ?)';
            $stmt = $conn->prepare($sql);

            $stmt-> bindValue(1, count($numeros)>1?implode(",", $numeros):$numeros[0]);
            $stmt-> bindValue(2, gerarBonus());
            $stmt-> bindValue(3, $rodadas);
            $stmt-> bindValue(4, $valor);
            $stmt-> bindValue(5, $_SESSION['id']);

            if ($stmt->execute()) {

                $sql = 'SELECT * FROM bilhetes WHERE id = (SELECT MAX(id) FROM bilhetes)';
                $stmt = $conn-> prepare($sql);
                $stmt-> execute();
                $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
                
                if (isset($retorno->id)) {
                    $bilhete = $retorno->id;
                    $numeros_apostados = str_replace(",", ", ",$retorno->numeros_apostados);
                    $numero_bonus = $retorno->numero_bonus;
                    $rodadas = $retorno->rodadas;
                    $valor = $retorno->valor;
                    $dh_insert =  date('d/m/Y H:i:s', strtotime($retorno->dh_insert));
                    $quantidade_apostada = retornaQuantidade($numeros_apostados);
                    $premiacao = retornaPremiacao($quantidade_apostada);

                    retornar("1","BILHETE SALVO COM SUCESSO!", $bilhete, $numeros_apostados, $numero_bonus, $rodadas, $valor, $dh_insert, $quantidade_apostada, $premiacao);
                } else {
                    retornar("0","BILHETE SALVO, MAS OCORREU UM ERRO AO RETORNAR O BILHETE!", null, null, null, null, null, null, null, null);
                }

            } else {
                retornar("0", "ERRO AO SALVAR O BILHETE!", null, null, null, null, null, null, null, null);
            }
        } else {
            retornar("0", "NÃO HÁ QUANTIDADE SUFICIENTE DE SORTEIOS EM ABERTO!", null, null, null, null, null, null, null, null);
        }
    }
    
    function retornaQuantidade($numeros_apostados) {
        return count(explode(",", $numeros_apostados));
    }

    function retornaPremiacao($quantidade) {
        $conn = getConnection();
        $sql = "SELECT id, quantidade_apostada, quantidade_acertos, multiplicador FROM premiacao WHERE quantidade_apostada = ?";
        $stmt = $conn-> prepare($sql);
        $stmt-> bindValue(1, $quantidade);
        $stmt-> execute();
        $retorno = $stmt -> fetchAll(PDO::FETCH_OBJ);

        return $retorno;
    }

    function gerarBonus() {
        $bonus = array();
        for ($i=0; $i<5; $i++) {
            array_push($bonus, rand(0, 9));
        }
        return implode('', $bonus);
    }

    function tratarNumeros($num) {
        $num = explode(",", $num);

        if (count($num)>=1) {
            for ($i=0; $i<count($num); $i++) {
                $num[$i] = (int)$num[$i];
            }
            return $num;
        } 
    }

    function verificarNumeros($num) {
        if (count($num)>=1 && count($num)<=10) {
            foreach ($num as $value) { 
                if ($value<1 || $value>80) {
                    return false;
                }
                if(array_count_values($num)[$value]!=1) {
                    return false; 
                }
            }
        } else {
            return false;
        }

        return true;
    }

    function verificarBonus($num) {
        if ($num<0 || $num>99999) {
            return false;
        }

        return true;
    }

    function verificaValor($num) {
        if ($num<1) {
            return false;
        }

        return true;
    }

    function verificarRodadas($num) {
        if ($num!=1 && $num!=2 && $num!=3 && $num!=4 && $num!=5 && $num!=6 && $num!=7 && $num!=8 && $num!=9 && $num!=10) {
            return false;
        }

        return true;
    }

    function retornar($status, $descricao, $bilhete, $numeros_apostados, $numero_bonus, $rodadas, $valor, $dh_insert, $quantidade_apostada, $premiacao) {
        $arquivo = file_get_contents('../_json/realizar_aposta.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        $retorno->bilhete=$bilhete;
        $retorno->numeros_apostados=$numeros_apostados;
        $retorno->numero_bonus=$numero_bonus;
        $retorno->rodadas=$rodadas;
        $retorno->valor=$valor;
        $retorno->dh_insert=$dh_insert;
        $retorno->quantidade_apostada=$quantidade_apostada;
        $retorno->premiacao=$premiacao;
        echo json_encode($retorno);
    }