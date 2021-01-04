dataAtual();
retornaSaldo()

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

function fecharRetorno() {
    document.getElementById('retorno').style.display = "none";
    document.getElementById('retorno').getElementsByTagName('h4')[0].innerHTML = "";
    document.getElementById('retorno').getElementsByTagName('h5')[0].innerHTML = "";
}

function imprimirRetorno() {
    Website2APK.printPage();
}

function pesquisarExtrato() {
    aparecerCarregando();

    let inicio = document.getElementById('data-inicio').value;
    let fim = document.getElementById('data-fim').value;
    let caixa = returnSelected('caixa');
    let tipo = conferirRadio('tipo-pesquisa', 2);
    let operacoes = conferirCheckbox('operacoes', 12);
    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/consultar_extrato.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("inicio="+inicio+"&fim="+fim+"&caixa="+caixa+"&tipo="+tipo+"&operacoes="+operacoes);
     
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                apresentarExtrato(retorno);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function conferirCheckbox(classname, qtd) {
    let retorno = "";
    for (let i = 0; i<qtd; i++) {
        if (document.getElementsByClassName(classname)[i].checked) {
            if (retorno == "") {
                retorno += document.getElementsByClassName(classname)[i].value;
            } else {
                retorno += ","+document.getElementsByClassName(classname)[i].value;
            }
        }
    }
    return retorno;
}

function entradasCheckbox() {
    for (let i = 0; i<6; i++) {
        document.getElementsByClassName("operacoes")[i].setAttribute("checked", "true");
    }
    for (let i = 6; i<12; i++) {
        document.getElementsByClassName("operacoes")[i].removeAttribute("checked");
    }
}

function saidasCheckbox() {
    for (let i = 0; i<6; i++) {
        document.getElementsByClassName("operacoes")[i].removeAttribute("checked");
    }
    for (let i = 6; i<12; i++) {
        document.getElementsByClassName("operacoes")[i].setAttribute("checked", "true");
    }
}

function conferirRadio(classname, qtd) {
    for (let i = 0; i<qtd; i++) {
        if (document.getElementsByClassName(classname)[i].checked) {
            return document.getElementsByClassName(classname)[i].value;
        }
    }
}

function returnSelected(className) {
    for (i in document.getElementsByClassName(className)) {
        if (document.getElementsByClassName(className)[i].selected) {
            return document.getElementsByClassName(className)[i].value;
        }
    }
}

function apresentarExtrato(retorno) {
    switch(retorno.status) {
        case '0':
            aparecerRetorno(retorno.descricao, null, false);
            break;
        case '1':
            let separador = '<tr><td colspan="3" class="separador">--------------------------------</td></tr>';

            let titulo = "EXTRATO DE CAIXA";
            let mensagem = '<table id="retorno-extrato"><tbody>'+separador;    

            let movimentacoes = retorno.movimentacoes;

            for (let i = 0; i<retorno.quantidade; i++) {
                let movimentacao = movimentacoes[i];
                mensagem += '<tr><td>'+movimentacao.tipo_movimentacao+'</td><td>'+movimentacao.caixa+'</td><td class="extrato-direita">ORIG.:'+movimentacao.id_origem+'</td></tr><tr><td colspan="2">'+movimentacao.dh_insert+'</td><td class="extrato-direita">'+(movimentacao.direcao==1?'-':'+')+real(movimentacao.valor)+'</td></tr>'+separador;
            }

            mensagem += '<tr><td colspan="3" class="extrato-direita">Saldo inicial: '+real(retorno.saldo_inicial)+'</td></tr>';
            mensagem += '<tr><td colspan="3" class="extrato-direita">Total: '+real(retorno.total)+'</td></tr>';
            mensagem += '<tr><td colspan="3" class="extrato-direita">Outras operações: '+real(retorno.outras_operacoes)+'</td></tr>';
            mensagem += '<tr><td colspan="3" class="extrato-direita">Saldo final: '+real(retorno.saldo_final)+'</td></tr>';


            mensagem += '</tbody></table>';
            
            aparecerRetorno(titulo, mensagem, true);
            break;
    }
}

function aparecerRetorno(h4, h5, imprimir) {
    if (h4==null) {
        document.getElementById('retorno').getElementsByTagName('h4')[0].style.display = "none";
        document.getElementById('retorno').getElementsByTagName('h4')[0].innerHTML = "";
    } else {
        document.getElementById('retorno').getElementsByTagName('h4')[0].style.display = "block";
        document.getElementById('retorno').getElementsByTagName('h4')[0].innerHTML = h4;
    }
    
    if (h5==null) {
        document.getElementById('retorno').getElementsByTagName('h5')[0].style.display = "none";
        document.getElementById('retorno').getElementsByTagName('h5')[0].innerHTML = "";
    } else {
        document.getElementById('retorno').getElementsByTagName('h5')[0].style.display = "block";
        document.getElementById('retorno').getElementsByTagName('h5')[0].innerHTML = h5;
    }

    if (imprimir) {
        aparecerBotaoImprimir();
    } else {
        sumirBotaoImprimir();
    }

    document.getElementById('retorno').style.display = "flex";
}

function real(valor) {	    
    return parseFloat(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, style: 'currency', currency: 'BRL'}); 
}

function aparecerBotaoImprimir() {
    document.getElementById('botao-retorno-imprimir').style.display = "block";
}

function sumirBotaoImprimir() {
    document.getElementById('botao-retorno-imprimir').style.display = "none";
}

function telaExtrato() {
    document.getElementById('extrato').style.display = "table";
    document.getElementById('saldo').style.display = "none";
    document.getElementById('aba-extrato').style = "background-color: white; transform: scale(1.1); z-index: 2;";
    document.getElementById('aba-saldo').style = "background-color: transparent; transform: scale(1); z-index: 1;";
}

function telaSaldo() {
    document.getElementById('extrato').style.display = "none";
    document.getElementById('saldo').style.display = "table";
    document.getElementById('aba-saldo').style = "background-color: white; transform: scale(1.1); z-index: 2;";
    document.getElementById('aba-extrato').style = "background-color: transparent; transform: scale(1); z-index: 1;";
}

function confirmarMovimentarCaixa(tipo, caixa) {
    let valor = document.getElementById('caixa-'+caixa).value;

    if (parseInt(valor)) {
        let operacao = "";

        switch (tipo) {
            case 1:
                operacao = "ENTRADA MANUAL";
                break;
            case 2:
                operacao = "SANGRIA MANUAL"
                break;
            case 3:
                operacao = "TRANSFERÊNCIA MANUAL";
                break;
        }
    
        let mensagem = "DESEJA MESMO REALIZAR A OPERACAÇÃO DE "+operacao+" NO VALOR DE "+real(valor)+(tipo==3?" DO CAIXA "+caixa+" PARA O CAIXA "+(caixa==1?2:1):" NO CAIXA "+caixa)+"?";
        let funcao = "movimentarCaixa("+tipo+", "+caixa+")";
    
        aparecerConfirmar(mensagem, null, funcao);
    } else {
        aparecerRetorno("INFORME UM NÚMERO MAIOR QUE ZERO!", null, false);
    }
    
}

function movimentarCaixa(tipo, caixa) {
    aparecerCarregando();

    let valor = document.getElementById('caixa-'+caixa).value;
    
    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/movimentar_caixa.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("tipo="+tipo+"&caixa="+caixa+"&valor="+valor);
     
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                apresentarMovimentacaoCaixa(retorno);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function retornaSaldo() {
    aparecerCarregando();
    
    let xmlreq = criaRequest();
    xmlreq.open('POST', '_php/consultar_saldo.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlreq.send();
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                recebeSaldo(retorno);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function recebeSaldo(retorno) {
    switch(retorno.status) {
        case '0':
            aparecerRetorno(retorno.descricao, null, false);
            break;
        case '1':
            document.getElementById("saldo-principal").value = real(retorno.saldo_principal);
            document.getElementById("saldo-premio").value = real(retorno.saldo_premio);
            document.getElementById("saldo-premiacoes-pendentes").value = real(retorno.saldo_premiacoes_pendentes);
            break;
    }
}


function aparecerConfirmar(h4, h5, funcao) {

    if (h4==null) {
        document.getElementById('confirmar').getElementsByTagName('h4')[0].style.display = "none";
        document.getElementById('confirmar').getElementsByTagName('h4')[0].innerHTML = "";
    } else {
        document.getElementById('confirmar').getElementsByTagName('h4')[0].style.display = "block";
        document.getElementById('confirmar').getElementsByTagName('h4')[0].innerHTML = h4;
    }
    
    if (h5==null) {
        document.getElementById('confirmar').getElementsByTagName('h5')[0].style.display = "none";
        document.getElementById('confirmar').getElementsByTagName('h5')[0].innerHTML = "";
    } else {
        document.getElementById('confirmar').getElementsByTagName('h5')[0].style.display = "block";
        document.getElementById('confirmar').getElementsByTagName('h5')[0].innerHTML = h5;
    }
    document.getElementById('botao-confirmar-sim').setAttribute("onclick", funcao);

    document.getElementById('confirmar').style.display = "flex";
}

function fecharConfirmar() {
    document.getElementById('confirmar').style.display = "none";
    document.getElementById('confirmar').getElementsByTagName('h4')[0].innerHTML = "";
    document.getElementById('confirmar').getElementsByTagName('h5')[0].innerHTML = "";
    document.getElementById('botao-confirmar-sim').setAttribute("onclick", "");
}

function apresentarMovimentacaoCaixa(retorno) {
    fecharConfirmar();
    switch (retorno.status) {
        case '0':
            aparecerRetorno(retorno.descricao, null, false);
            break;
        case '1':
            aparecerRetorno(retorno.descricao, null, false);
            retornaSaldo();
            zerarValores();
            break;
    }
}

function zerarValores() {
    document.getElementById('caixa-1').value = "";
    document.getElementById('caixa-2').value = "";
}