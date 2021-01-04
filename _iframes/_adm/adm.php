<?php
    session_start();
    
    if (isset($_SESSION['logado'])) {
        if ($_SESSION['logado'] == 'SIM') {
            if (isset($_GET['page'])) {
                $conteudo = file_get_contents('./_telas/'.$_GET['page'].'.html');
                echo $conteudo;
            } else {
                $conteudo = file_get_contents('./_telas/menu.html');
                echo $conteudo;
            }
        }
    } else {
        header('Location: ../_login/login.html');
    }