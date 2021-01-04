<?php
    include '../../../_bd/conexao_bd.php';
    session_start();

    define('TENTATIVAS_ACEITAS', 20);
    define('MINUTOS_BLOQUEIO', 30); 

    
    if (isset($_SESSION['logado'])) {
        retornar("1", "Usuário já está logado");
    } else {
        if (isset($_POST['login']) && isset($_POST['password'])) {
            if ($_POST['login']!="" && $_POST['password']!="") {

                $usuario = $_POST['login'];
                $senha = $_POST['password'];

                salvarLogTentativa($usuario, $senha);
                verificarLoginSenha($usuario, $senha);
                
            } else {
                retornar("0", "Informe o usuário e senha...");
            }
        } else {
            retornar("0", "Usuário e senha não informados. Acesse a tela inicial do sistema para informar o usuário e a senha.");
        }
    }

    function salvarLogTentativa($usuario, $senha) {
        $login = $usuario;
        $password = $senha;
        $bloqueado = "NAO";

        $conn = getConnection();

        $sql = 'INSERT INTO log_tentativas (ip, email, senha, origem, bloqueado) VALUES (?, ?, ?, ?, ?)';
        
        $stmt = $conn->prepare($sql);
        $stmt-> bindValue(1, $_SERVER['SERVER_ADDR']);
        $stmt-> bindValue(2, $login);
        $stmt-> bindValue(3, $password);
        $stmt-> bindValue(4, $_SERVER['HTTP_REFERER']);
        $stmt-> bindValue(5, $bloqueado);

        $stmt->execute();
    }

    function tratarLoginSenha($login, $password) {
        $cond_login = ((filter_var($login, FILTER_VALIDATE_EMAIL)) && (strlen($login)<=100)); 
        $cond_pass = (strlen($password)<=20);

        if ($cond_login && $cond_pass) {
            return true;
        }

        return false;
    }

    function verificarLoginSenha($login, $password) {
        if (tratarLoginSenha($login, $password)) {
            $conn = getConnection();

            $sql = 'SELECT id, nome, email, senha FROM usuarios WHERE email = ? LIMIT 1';
            $stmt = $conn-> prepare($sql);
            $stmt -> bindValue(1, $login);
            $stmt-> execute();
            $retorno = $stmt -> fetch(PDO::FETCH_OBJ);

            if(!empty($retorno) && password_verify($password, $retorno->senha)){
                $_SESSION['id'] = $retorno->id;
                $_SESSION['nome'] = $retorno->nome;
                $_SESSION['email'] = $retorno->email;
                $_SESSION['tentativas'] = 0;
                $_SESSION['logado'] = 'SIM';


                retornar("1", "Login e senha corretos");
                
            } else {
                retornar("0", "Usuário ou senha incorretos!");
            }
        } else {
            retornar("0", "Usuário ou senha fora do padrão permitido!");
        }
    }

    function retornar($status, $descricao) {
        $arquivo = file_get_contents('../_json/login.json');
        $retorno = json_decode($arquivo);
        $retorno->status=$status;
        $retorno->descricao=$descricao;
        echo json_encode($retorno);
    }