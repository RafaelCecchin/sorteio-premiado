<?php 
    include '../../../_bd/conexao_bd.php';

    retornaSorteio();
 
    function retornaSorteio() {
        $conn = getConnection();
        $sql = 'SELECT id, numeros_sorteados, numero_bonus FROM sorteios WHERE id=(SELECT max(id) FROM sorteios WHERE fechado=1)';
        $stmt = $conn-> prepare($sql);
        $stmt-> execute();
        $retorno = $stmt -> fetch(PDO::FETCH_OBJ);
        
        $numero_sorteio = $retorno->id;
        $numeros_sorteados = $retorno->numeros_sorteados;
        $numero_bonus = $retorno->numero_bonus;
        $bilhetes_premiados = bilhetesPremiados();

        retornar($numero_sorteio, $numeros_sorteados, $numero_bonus, $bilhetes_premiados);
    }

    function bilhetesPremiados() {
        $conn = getConnection();
        $sql = 'SELECT id_bilhete AS bilhete FROM premiacoes AS p LEFT JOIN apostas AS a ON p.id_aposta = a.id WHERE a.id_sorteio = (SELECT MAX(id) FROM sorteios WHERE fechado = 1)';
        $stmt = $conn-> prepare($sql);
        $stmt-> execute();
        $bilhetes_premiados = $stmt -> fetchAll(PDO::FETCH_OBJ);

        return $bilhetes_premiados;
    }

    function retornar($numero_sorteio, $numeros_sorteados, $numero_bonus, $bilhetes_premiados) {
        $arquivo = file_get_contents('../_json/retorna_sorteio.json');
        $retorno = json_decode($arquivo);
        $retorno->numero_sorteio=$numero_sorteio;
        $retorno->numeros_sorteados=$numeros_sorteados;
        $retorno->numero_bonus=$numero_bonus;
        $retorno->bilhetes_premiados=$bilhetes_premiados;
        echo json_encode($retorno);
    }