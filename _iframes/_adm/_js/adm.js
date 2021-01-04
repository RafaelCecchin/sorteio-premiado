function mudarPagina(pagina) {
    if (pagina!=null) {
        window.location.href = "./adm.php?page="+pagina;
    } else {
        window.location.href = "./adm.php";
    }
    
}

function finalizaSessao() {
    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/finaliza_sessao.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send();
     
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                window.location.href = "../../_iframes/_login/login.html";
            }else{
                result.innerHTML = "Erro: " + xmlreq.statusText;
            }
        }
    }
}

function voltar() {
    history.go(-1);
}


function aparecerCarregando() {
    document.getElementById('carregando').style.display = "flex";
}

function sumirCarregando() {
    document.getElementById('carregando').style.display = "none";
}
