retornaConfiguracoesGerais();
retornaConfiguracoesUsuarios();

function telaGerais() {
    document.getElementById('gerais').style.display = "table";
    document.getElementById('usuario').style.display = "none";
    document.getElementById('aba-gerais').style = "background-color: white; transform: scale(1.1); z-index: 2;";
    document.getElementById('aba-usuario').style = "background-color: transparent; transform: scale(1); z-index: 1;";
}

function telaUsuario() {
    document.getElementById('gerais').style.display = "none";
    document.getElementById('usuario').style.display = "table";
    document.getElementById('aba-usuario').style = "background-color: white; transform: scale(1.1); z-index: 2;";
    document.getElementById('aba-gerais').style = "background-color: transparent; transform: scale(1); z-index: 1;";
}


function aparecerMensagemConfiguracao(retorno) {
    document.getElementById('mensagem-configuracao').style.display = "flex";
    document.getElementsByTagName('h5')[0].style.display = 'none';
    document.getElementsByTagName('h4')[0].innerHTML = retorno.descricao;
}

function fecharMensagemConfiguracao() {
    document.getElementById('mensagem-configuracao').style.display = "none";
}

function alterarLucro() {
    let lucro = document.getElementById('lucro').value;
    document.getElementById('lucro-casa').value = lucro;
    document.getElementById('lucro-apostadores').value = 100-lucro;
}

function alterarRazao() {
    let razao = document.getElementById('razao').value;
    document.getElementById('razao-selecionada').value = razao/100;
}

function recebeConfiguracoesGerais(retorno) {
    switch(retorno.status) {
        case '0':
            aparecerMensagemConfiguracao(retorno);
            break;
        case '1':
            document.getElementById('razao').value = retorno.razao_selecionada*100;
            alterarRazao();
            document.getElementById('razao-atual').value = retorno.razao_atual;
            break;
    }
}

function recebeConfiguracoesUsuarios(retorno) {
    switch(retorno.status) {
        case '0':
            aparecerMensagemConfiguracao(retorno);
            break;
        case '1':
            document.getElementById('bilhete_app_font').value = retorno.bilhete_app_font;
            document.getElementById('bilhete_print_font').value = retorno.bilhete_print_font;
            document.getElementById('extrato_app_font').value = retorno.extrato_app_font;
            document.getElementById('extrato_print_font').value = retorno.extrato_print_font;
            document.getElementById('h4_font').value = retorno.h4_font;
            document.getElementById('h5_font').value = retorno.h5_font;
            break;
    }
}

function salvarConfiguracoesGerais() {
    aparecerCarregando();


    let razao_atual = document.getElementById('razao-atual').value;
    let razao_selecionada = document.getElementById('razao-selecionada').value;
    

    let xmlreq = criaRequest();
    xmlreq.open('POST', '_php/salvar_configuracoes_gerais.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("razao_atual="+razao_atual+"&razao_selecionada="+razao_selecionada);
    
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                aparecerMensagemConfiguracao(retorno);
                retornaConfiguracoesGerais();
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function salvarConfiguracoesUsuarios() {
    aparecerCarregando();

    let bilhete_app_font = document.getElementById('bilhete_app_font').value;
    let bilhete_print_font = document.getElementById('bilhete_print_font').value;
    let extrato_app_font = document.getElementById('extrato_app_font').value;
    let extrato_print_font = document.getElementById('extrato_print_font').value;
    let h4_font = document.getElementById('h4_font').value;
    let h5_font = document.getElementById('h5_font').value;

    let xmlreq = criaRequest();
    xmlreq.open('POST', '_php/salvar_configuracoes_usuarios.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("bilhete_app_font="+bilhete_app_font+"&bilhete_print_font="+bilhete_print_font+"&extrato_app_font="+extrato_app_font+"&extrato_print_font="+extrato_print_font+"&h4_font="+h4_font+"&h5_font="+h5_font);
    
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                aparecerMensagemConfiguracao(retorno);
                retornaConfiguracoesUsuarios();
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function retornaConfiguracoesGerais() {
    aparecerCarregando();

    let xmlreq = criaRequest();
    xmlreq.open('POST', '_php/consultar_configuracoes_gerais.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlreq.send();
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                recebeConfiguracoesGerais(retorno);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function retornaConfiguracoesUsuarios() {
    aparecerCarregando();

    let xmlreq = criaRequest();
    xmlreq.open('POST', '_php/consultar_configuracoes_usuarios.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlreq.send();
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                recebeConfiguracoesUsuarios(retorno);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

