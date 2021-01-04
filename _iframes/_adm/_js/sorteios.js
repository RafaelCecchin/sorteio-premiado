pesquisarSorteios();

function apresentarSorteios(retorno) {
    switch(retorno.status) {
        case '0':
            limparRetorno();
            document.getElementById('mensagem-pesquisa').style.display = "flex";
            document.getElementById('mensagem-pesquisa').getElementsByTagName('h5')[0].style.display = 'none';
            document.getElementById('mensagem-pesquisa').getElementsByTagName('h4')[0].innerHTML = retorno.descricao;
            break;
        case '1':
            let quantidade = retorno.quantidade;
            let sorteios = retorno.sorteios;
            let divRetorno = document.getElementById('retorno');
            divRetorno.innerHTML = '';
            for (let i = 0; i<quantidade; i++) {
                divRetorno.innerHTML += '<div id="'+i+'"> <div class="conteudo-retorno"> <div class="conteudo-retorno-line1"> <div class="botao-conteudo-adicional" onclick="mostrarMais('+i+')"></div> <h4 class="numero-sorteio">'+sorteios[i].id+'</h4> <h4 class="premiacoes">R$'+real(sorteios[i].premiacoes)+'</h4> </div> <div class="conteudo-retorno-line2"> <hgroup> <h5>Números sorteados: <br/>'+tratarNumerosSorteados(sorteios[i].numeros_sorteados)+'</h5> <h5>Bônus: '+sorteios[i].numero_bonus+'</h5> </hgroup> <hgroup> <h5 class="data-hora">'+sorteios[i].dh_final+'</h5> </hgroup> </div> <div class="linha-preta"></div> </div> </div>';
            }
            break;
    }
}

function real(valor) {	    
    return parseFloat(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2}); 
}

function tratarNumerosSorteados(num) {
    return num.replace(/,/g, ", ");
}

function mostrarMais(id) {
    document.getElementById(id).getElementsByClassName('conteudo-retorno-line2')[0].style.display = "block";
    document.getElementById(id).getElementsByClassName('botao-conteudo-adicional')[0].onclick = function() {mostrarMenos(id)};
    document.getElementById(id).getElementsByClassName('botao-conteudo-adicional')[0].style.transform = "rotate(180deg)";
}

function mostrarMenos(id) {
    document.getElementById(id).getElementsByClassName('conteudo-retorno-line2')[0].style.display = "none";
    document.getElementById(id).getElementsByClassName('botao-conteudo-adicional')[0].onclick = function() {mostrarMais (id)};
    document.getElementById(id).getElementsByClassName('botao-conteudo-adicional')[0].style.transform = "rotate(0deg)";
}

function limparRetorno() {
    document.getElementById('retorno').innerHTML = "";
}

function fecharMensagemPesquisa() {
    document.getElementById('mensagem-pesquisa').style.display = "none";
}

function pesquisarSorteios() {
    aparecerCarregando();

    let numero = document.getElementById('pesquisa').value;
    let pagina = document.getElementById('num-pagina').value;

    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/consultar_sorteios.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("sorteio="+numero+"&pagina="+pagina);
     
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                apresentarSorteios(retorno);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function proximaPagina() {
    document.getElementById('num-pagina').value = parseInt(document.getElementById('num-pagina').value)+1; 
    pesquisarSorteios();
}

function voltarPagina() {
    if (document.getElementById('num-pagina').value>1) {
        document.getElementById('num-pagina').value = parseInt(document.getElementById('num-pagina').value)-1; 
        pesquisarSorteios();
    }
}
