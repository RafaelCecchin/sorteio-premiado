dataAtual();

function aparecerMensagemPagamentoPremiacao(retorno, registro, premiacao, bilhete, valor) {
    fecharConfirmarPagamento();
    document.getElementById('mensagem-pesquisa').style.display = "block";
    document.getElementById('mensagem-pesquisa').getElementsByTagName('h4')[0].style.display = "flex";
    document.getElementById('mensagem-pesquisa').getElementsByTagName('h5')[0].style.display = "none";

    document.getElementById('mensagem-pesquisa').getElementsByTagName('h4')[0].innerHTML = retorno.descricao;

    if (retorno.status==1) {
        document.getElementById(registro).getElementsByClassName('botao-pagar')[0].style = "background-color: rgb(211, 211, 211); color: grey;";
        document.getElementById(registro).getElementsByClassName('botao-pagar')[0].setAttribute('onclick','cancelar('+registro+','+premiacao+','+bilhete+','+valor+')');
        document.getElementById(registro).getElementsByClassName('status-premiacao')[0].style = "color: green";
        document.getElementById(registro).getElementsByClassName('status-premiacao')[0].innerHTML = "PAGA "+retorno.dh_pagamento;
    }
}

function aparecerMensagemCancelamentoPagamento(retorno, registro, premiacao, bilhete, valor) {
    fecharCancelarPagamento();
    document.getElementById('mensagem-pesquisa').style.display = "block";
    document.getElementById('mensagem-pesquisa').getElementsByTagName('h4')[0].style.display = "flex";
    document.getElementById('mensagem-pesquisa').getElementsByTagName('h5')[0].style.display = "none";

    document.getElementById('mensagem-pesquisa').getElementsByTagName('h4')[0].innerHTML = retorno.descricao;

    if (retorno.status==1) {
        document.getElementById(registro).getElementsByClassName('botao-pagar')[0].style = "background-color:  rgb(0, 194, 0); color: black;";
        document.getElementById(registro).getElementsByClassName('botao-pagar')[0].setAttribute('onclick','pagamento('+registro+','+premiacao+','+bilhete+','+valor+')');
        document.getElementById(registro).getElementsByClassName('status-premiacao')[0].style = "color: red";
        document.getElementById(registro).getElementsByClassName('status-premiacao')[0].innerHTML = "PENDENTE";
    }
}

function apresentarPremiacoes(retorno) {
    switch(retorno.status) {
        case '0':
            limparRetorno();
            document.getElementById('mensagem-pesquisa').style.display = "flex";
            document.getElementById('mensagem-pesquisa').getElementsByTagName('h5')[0].style.display = 'none';
            document.getElementById('mensagem-pesquisa').getElementsByTagName('h4')[0].innerHTML = retorno.descricao;
            break;
        case '1':
            let premiacoes_pagamento = retorno.premiacoes_pagamento;
            let divRetorno = document.getElementById('retorno');
            divRetorno.innerHTML = '';
            for(let i = 0; i<retorno.quantidade; i++) {
                if (premiacoes_pagamento[i].paga==0) {
                    let mensagem = '<div id="'+i+'"> <div class="conteudo-retorno"> <div class="conteudo-retorno-line1"> <div class="botao-conteudo-adicional" onclick="mostrarMais('+i+')"></div> <h4 class="numero-bilhete">'+premiacoes_pagamento[i].id_bilhete+'</h4> <h4 class="valor-a-pagar">R$'+real(premiacoes_pagamento[i].valor)+'</h4> <input class="botao-pagar" type="button" value="PAGAR" onclick="pagamento('+i+','+premiacoes_pagamento[i].id+','+premiacoes_pagamento[i].id_bilhete+','+premiacoes_pagamento[i].valor+')" style="background-color: rgb(0, 194, 0)"/> </div> <div class="conteudo-retorno-line2">';
                    let premiacoes = premiacoes_pagamento[i].premiacoes;
                    for (let c = 0; c<premiacoes_pagamento[i].quantidade; c++) {
                        let premiacao = premiacoes[c];
                        let multi = premiacao.multiplicador!='0'?'<h5>Multi:<br/>'+premiacao.multiplicador+'x</h5>':'<h5>Bonus:<br/>'+premiacao.bonus+'</h5>';
                        mensagem += '<hgroup> <h5>Sorteio:<br/>'+premiacao.id_sorteio+'</h5> <h5>Valor:<br/>R$'+real(premiacao.valor)+'</h5>'+multi+'</hgroup>';
                    }
                    mensagem += '<hgroup> <h5 class="status-premiacao" style="color: red">PENDENTE</h5> </hgroup> </div> <div class="linha-preta"></div> </div> </div>';
                    divRetorno.innerHTML += mensagem;
                } else {
                    let mensagem = '<div id="'+i+'"> <div class="conteudo-retorno"> <div class="conteudo-retorno-line1"> <div class="botao-conteudo-adicional" onclick="mostrarMais('+i+')"></div> <h4 class="numero-bilhete">'+premiacoes_pagamento[i].id_bilhete+'</h4> <h4 class="valor-a-pagar">R$'+real(premiacoes_pagamento[i].valor)+'</h4> <input class="botao-pagar" type="button" value="PAGAR" onclick="cancelar('+i+','+premiacoes_pagamento[i].id+','+premiacoes_pagamento[i].id_bilhete+','+premiacoes_pagamento[i].valor+')" style="background-color: rgb(211, 211, 211); color: grey;"/> </div> <div class="conteudo-retorno-line2">';
                    let premiacoes = premiacoes_pagamento[i].premiacoes;
                    for (let c = 0; c<premiacoes_pagamento[i].quantidade; c++) {
                        let premiacao = premiacoes[c];
                        let multi = premiacao.multiplicador!='0'?'<h5>Multi:<br/>'+premiacao.multiplicador+'x</h5>':'<h5>Bonus:<br/>'+premiacao.bonus+'</h5>';
                        mensagem += '<hgroup> <h5>Sorteio:<br/>'+premiacao.id_sorteio+'</h5> <h5>Valor:<br/>R$'+real(premiacao.valor)+'</h5>'+multi+'</hgroup>';
                    }
                    mensagem += '<hgroup> <h5 class="status-premiacao" style="color: green">PAGA '+premiacoes_pagamento[i].dh_pagamento+'</h5> </hgroup> </div> <div class="linha-preta"></div> </div> </div>';
                    divRetorno.innerHTML += mensagem;
                }
            }
            break;
    }
}

function real(valor) {	    
    return parseFloat(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2}); 
}

function fecharMensagemPesquisa() {
    document.getElementById('mensagem-pesquisa').style.display = "none";
    document.getElementById('mensagem-pesquisa').getElementsByTagName('h4')[0].innerHTML = "";
    document.getElementById('mensagem-pesquisa').getElementsByTagName('h5')[0].innerHTML = "";
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

function dataAtual() {
    let now = new Date;
    let d = now.getUTCDate()
    let m = now.getUTCMonth()+1;
    let y = now.getUTCFullYear();

    document.getElementById('data-inicio').value = y+"-"+zeroEsquerda(m, 2)+"-"+zeroEsquerda(d, 2);
    document.getElementById('data-fim').value = y+"-"+zeroEsquerda(m, 2)+"-"+zeroEsquerda(d, 2);
}

function zeroEsquerda(n, width, z) {
    z = z || '0';
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function limparRetorno() {
    document.getElementById('retorno').innerHTML = "";
}

function pesquisarPremiacoes() {
    aparecerCarregando();

    let pesquisa = document.getElementById('pesquisa').value;
    let inicio = document.getElementById('data-inicio').value;
    let fim = document.getElementById('data-fim').value;
    let status = conferirRadio('status-pesquisa', 3);
    let pagina = document.getElementById('num-pagina').value;

    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/consultar_premiacoes.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("pesquisa="+pesquisa+"&inicio="+inicio+"&fim="+fim+"&status="+status+"&pagina="+pagina);
     
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                apresentarPremiacoes(retorno);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function conferirRadio(classname, qtd) {
    for (let i = 0; i<qtd; i++) {
        if (document.getElementsByClassName(classname)[i].checked) {
            return document.getElementsByClassName(classname)[i].value;
        }
    }
}

function confirmarPagamento(registro, premiacao, bilhete, sorteio, valor) {
    aparecerCarregando();


    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/realizar_pagamento.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("premiacao="+premiacao);
     
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                aparecerMensagemPagamentoPremiacao(retorno, registro, premiacao, bilhete, sorteio, valor);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function cancelarPagamento(registro, premiacao, bilhete, sorteio, valor) {
    aparecerCarregando();

    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/cancelar_pagamento.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("premiacao="+premiacao);
     
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                aparecerMensagemCancelamentoPagamento(retorno, registro, premiacao, bilhete, sorteio, valor);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function pagamento(registro, premiacao, bilhete, valor) {
    document.getElementById('confirmar-pagamento').style.display = 'block';
    document.getElementById('confirmar-pagamento').getElementsByTagName('h4')[0].style.display = "flex";
    document.getElementById('confirmar-pagamento').getElementsByTagName('h5')[0].style.display = "flex";
    document.getElementById('confirmar-pagamento').getElementsByTagName('h4')[0].innerHTML = "DESEJA MESMO REALIZAR O PAGAMENTO?";
    document.getElementById('confirmar-pagamento').getElementsByTagName('h5')[0].innerHTML = "BILHETE: "+bilhete+"<br/>VALOR: R$"+real(valor);
    document.getElementById('botao-confirmar-pagamento-sim').setAttribute('onclick','confirmarPagamento('+registro+','+premiacao+','+bilhete+','+valor+')');
}

function fecharConfirmarPagamento() {
    document.getElementById('confirmar-pagamento').style.display = 'none';
    document.getElementById('confirmar-pagamento').getElementsByTagName('h4')[0].style.display = "none";
    document.getElementById('confirmar-pagamento').getElementsByTagName('h5')[0].style.display = "none";
    document.getElementById('confirmar-pagamento').getElementsByTagName('h4')[0].innerHTML = "";
    document.getElementById('confirmar-pagamento').getElementsByTagName('h5')[0].innerHTML = "";
    document.getElementById('botao-confirmar-pagamento-sim').setAttribute('onclick','');
}

function cancelar(registro, premiacao, bilhete, valor) {
    document.getElementById('cancelar-pagamento').style.display = 'block';
    document.getElementById('cancelar-pagamento').getElementsByTagName('h4')[0].style.display = "flex";
    document.getElementById('cancelar-pagamento').getElementsByTagName('h5')[0].style.display = "flex";
    document.getElementById('cancelar-pagamento').getElementsByTagName('h4')[0].innerHTML = "DESEJA MESMO CANCELAR O PAGAMENTO?";
    document.getElementById('cancelar-pagamento').getElementsByTagName('h5')[0].innerHTML = "BILHETE: "+bilhete+"<br/>VALOR: R$"+real(valor);
    document.getElementById('botao-cancelar-pagamento-sim').setAttribute('onclick','cancelarPagamento('+registro+','+premiacao+','+bilhete+','+valor+')');
}

function fecharCancelarPagamento() {
    document.getElementById('cancelar-pagamento').style.display = 'none';
    document.getElementById('cancelar-pagamento').getElementsByTagName('h4')[0].style.display = "none";
    document.getElementById('cancelar-pagamento').getElementsByTagName('h5')[0].style.display = "none";
    document.getElementById('cancelar-pagamento').getElementsByTagName('h4')[0].innerHTML = "";
    document.getElementById('cancelar-pagamento').getElementsByTagName('h5')[0].innerHTML = "";
    document.getElementById('botao-cancelar-pagamento-sim').setAttribute('onclick','');
}

function proximaPagina() {
    document.getElementById('num-pagina').value = parseInt(document.getElementById('num-pagina').value)+1; 
    pesquisarPremiacoes();
}

function voltarPagina() {
    if (document.getElementById('num-pagina').value>1) {
        document.getElementById('num-pagina').value = parseInt(document.getElementById('num-pagina').value)-1; 
        pesquisarPremiacoes();
    }
}

