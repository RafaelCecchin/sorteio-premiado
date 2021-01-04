verificarDados();

function verificarDados() {
    let login   = document.getElementById("login").value;
    let password   = document.getElementById("password").value;
    let result = document.getElementById("login-alert");
    let xmlreq = criaRequest();
     
    result.innerHTML = 'Carregando...';

    xmlreq.open('POST', '_php/verifica_login.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("login="+login+"&password="+password);
     
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                switch(retorno.status) {
                    case '0':
                        result.innerHTML = retorno.descricao;
                        break;
                    case '1':
                        window.location.href = "../_adm/adm.php";
                        break;
                }
            }else{
                result.innerHTML = "Erro: " + xmlreq.statusText;
            }
        }
    }
}