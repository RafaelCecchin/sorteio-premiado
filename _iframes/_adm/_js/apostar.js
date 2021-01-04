trocarCores();

let numerosEscolhidos = [];

function selecionarBola(num) {
    if (!numerosEscolhidos.includes(num)) {
        if (numerosEscolhidos.length<10) {
            numerosEscolhidos.push(num);
            document.getElementsByClassName('bolas-conteudo')[num-1].style.background = "rgba(228, 228, 228, 0.8)";
        }
    } else {
        numerosEscolhidos.splice(numerosEscolhidos.indexOf(num),1);
        document.getElementsByClassName('bolas-conteudo')[num-1].style.background = "";
    }
}

function aparecerEscolherBolas() {
    document.getElementById('escolher-bolas').style.display = "flex";
}

function escolherBolas() {
    document.getElementById('numeros-apostas').value = numerosEscolhidos;
    fecharEscolherBolas();
    document.getElementsByClassName('opcoes-apostas-rapidas')[0].selected = true;
}

function trocarCores() {
    for (let i=0; i<80; i++) {
        let num = (i+1).toString().split('');
        switch (num[num.length-1]) {
            case "0":
                document.getElementsByClassName('esfera')[i].style.background = "radial-gradient(at top left, rgb(207, 207, 207)30%, rgb(34, 34, 34))";
                break;
            case "1":
                document.getElementsByClassName('esfera')[i].style.background = "radial-gradient(at top left, rgb(228, 0, 0) 30%, rgb(34, 34, 34))";
                break;
            case "2":
                document.getElementsByClassName('esfera')[i].style.background = "radial-gradient(at top left, rgb(90, 187, 0) 30%, rgb(34, 34, 34))";
                break;
            case "3":
                document.getElementsByClassName('esfera')[i].style.background = "radial-gradient(at top left, rgb(46, 41, 20)30%, rgb(34, 34, 34))";
                break;
            case "4":
                document.getElementsByClassName('esfera')[i].style.background = "radial-gradient(at top left, rgb(10, 186, 255)30%, rgb(34, 34, 34))";
                break;
            case "5":
                document.getElementsByClassName('esfera')[i].style.background = "radial-gradient(at top left, rgb(255, 239, 10)30%, rgb(34, 34, 34))";
                break;
            case "6":
                document.getElementsByClassName('esfera')[i].style.background = "radial-gradient(at top left, rgb(235, 10, 255)30%, rgb(34, 34, 34))";
                break;
            case "7":
                document.getElementsByClassName('esfera')[i].style.background = "radial-gradient(at top left, rgb(29, 29, 29)30%, rgb(0, 0, 0))";
                break;
            case "8":
                document.getElementsByClassName('esfera')[i].style.background = "radial-gradient(at top left, rgb(59, 59, 59)30%, rgb(34, 34, 34))";
                break;
            case "9":
                document.getElementsByClassName('esfera')[i].style.background = "radial-gradient(at top left, rgb(255, 153, 0)30%, rgb(34, 34, 34))";
                break;
        }
    }
}


function fecharEscolherBolas() {
    document.getElementById('escolher-bolas').style.display = "none";
}

function gerarApostaAleatoria() {
    let qtd = 0;
    
    for (i in document.getElementsByClassName('opcoes-apostas-rapidas')) {
        if (document.getElementsByClassName('opcoes-apostas-rapidas')[i].selected) {
            qtd = document.getElementsByClassName('opcoes-apostas-rapidas')[i].value;
        }
    }

    if (qtd>0 && qtd<=10) {
        let min = 1;
        let max = 80;
        let apostas = [];
        let numeroAtual = 0;
        for (let c = 0; c<qtd; c++) {
            do {
                numeroAtual = Math.floor(Math.random() * (max - min + 1)) + min;
            } while (apostas.includes(numeroAtual));
            
            apostas.push(numeroAtual);
        }
        document.getElementById('numeros-apostas').value = apostas;
    }
}

function salvarAposta() {
    document.getElementById('numeros-apostas').value = document.getElementById('numeros-apostas').value.split(",").sort(function(a, b){return a - b});
    
    let numeros = document.getElementById('numeros-apostas').value;
    let valor = document.getElementById('valor-apostas').value;
    let rodadas = returnSelected('opcoes-rodadas-apostas');
    let total = valor * rodadas;
    
    if (numeros!="" && rodadas!="" && valor!=0 && total!=0) {
        aparecerConfirmarAposta(numeros, valor, rodadas, total);
    }
}

function aparecerConfirmarAposta(numeros, valor, rodadas, total) {
    let mensagem = 'Números: '+numeros+'<br/>Rodadas: '+rodadas+'<br/>Total/Rodada: R$'+valor+',00<br/>Total: R$'+total+',00<br/>';
    aparecerConfirmar("CONFIRMAR APOSTA?", mensagem, "confirmarAposta()");
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

function confirmarAposta() {
    aparecerCarregando();

    let numeros = document.getElementById('numeros-apostas').value;
    let valor = document.getElementById('valor-apostas').value;
    let rodadas = returnSelected('opcoes-rodadas-apostas');
    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/realizar_aposta.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                

                let retorno = JSON.parse(xmlreq.responseText);
                aparecerMensagemAposta(retorno);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }

    xmlreq.send("numeros="+numeros+"&valor="+valor+"&rodadas="+rodadas);
}

function requestError(error) {
    titulo = "ERRO DE CONEXÃO";
    mensagem = "Não foi possível fazer conexão com o servidor. Verifique se o seu aparelho está conectado à internet e tente novamente. Caso o problema persista, entre em contato com o suporte técnico. Erro: " + error;
    aparecerRetorno(titulo, mensagem, null);
}


function aparecerMensagemAposta(retorno) {
    switch(retorno.status) {
        case '0':
            aparecerRetorno(retorno.descricao, null, false);
            break;
        case '1':
            let quebra = "<br/>---------------------------------<br/>";
            let premiacao = retorno.premiacao;
            let qtdPremiacao = Object.keys(premiacao).length;

            let titulo = "SORTEIO PREMIADO";
            let mensagem = "---------------------------------<br/><b>BILHETE "+retorno.bilhete+"</b>"+quebra+retorno.quantidade_apostada+" aposta(s):<br/><b>"+retorno.numeros_apostados+"</b>"+quebra+"Número bonus: <br/><b>"+retorno.numero_bonus+"</b>"+quebra+retorno.rodadas+" rodada(s)"+quebra+"Valor total: R$"+real(retorno.valor*retorno.rodadas)+"<br/>Valor por rodada: R$"+real(retorno.valor)+quebra+retorno.dh_insert+quebra+"Paga por acerto: ";

            for (let i = 0; i<qtdPremiacao; i++) {
                mensagem += "<br/>"+premiacao[i].quantidade_acertos+": R$"+real(premiacao[i].multiplicador*retorno.valor);
            }

            mensagem += quebra;
            
            aparecerRetorno(titulo, mensagem, true);
            
            break;
    }
}

function reimprimirBilhete() {
    aparecerCarregando();

    let numero = document.getElementById('numero-bilhete').value;

    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/reimprimir_bilhete.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("bilhete="+numero);
     
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                aparecerMensagemAposta(retorno);
            }else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function aparecerRetorno(h4, h5, imprimir) {
    fecharConfirmar();
    
    
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

function fecharRetorno() {
    document.getElementById('retorno').style.display = "none";
    document.getElementById('retorno').getElementsByTagName('h4')[0].innerHTML = "";
    document.getElementById('retorno').getElementsByTagName('h5')[0].innerHTML = "";
}

function aparecerBotaoImprimir() {
    document.getElementById('botao-retorno-imprimir').style.display = "block";
}

function sumirBotaoImprimir() {
    document.getElementById('botao-retorno-imprimir').style.display = "none";
}

function recarregarPagina() {
    document.location.reload(true);
}

function repetirAposta() {
    aparecerCarregando();

    let numero = document.getElementById('numero-bilhete').value;

    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/repetir_aposta.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("bilhete="+numero);

    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                aparecerMensagemRepetir(retorno);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function aparecerMensagemRepetir(retorno) {
    aparecerRetorno(retorno.descricao, null, false);

    if (retorno.status==1) {
        document.getElementById('numeros-apostas').value = retorno.numeros;
    }
}

function returnSelected(className) {
    for (i in document.getElementsByClassName(className)) {
        if (document.getElementsByClassName(className)[i].selected) {
            return document.getElementsByClassName(className)[i].value;
        }
    }
}

function confirmarCancelamento() {
    let numero = document.getElementById('numero-bilhete').value;
    aparecerConfirmar("DESEJA MESMO CANCELAR O BILHETE "+numero+"?", null, "cancelarBilhete()");
}

function cancelarBilhete() {
    aparecerCarregando();

    let numero = document.getElementById('numero-bilhete').value;

    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/cancelar_bilhete.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("bilhete="+numero);
     
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                aparecerRetorno(retorno.descricao, null, false);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function consultarPremio () {
    aparecerCarregando();

    let numero = document.getElementById('numero-bilhete').value;

    let xmlreq = criaRequest();

    xmlreq.open('POST', '_php/consultar_bilhetes.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlreq.send("bilhete="+numero);
     
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
                aparecerConsulta(retorno);
            } else {
                requestError(xmlreq.statusText);
            }
            sumirCarregando(); 
        }
    }
}

function aparecerConsulta(retorno) {
    switch(retorno.status) {
        case '0':
            aparecerRetorno(retorno.descricao, null, false);
            break;
        case '1':
            let quebra = "<br/>---------------------------------<br/>";
            
            let titulo = "SORTEIO PREMIADO";
            let mensagem = "---------------------------------<br/><b>BILHETE "+retorno.bilhete+"</b>"+quebra;

            if (retorno.cancelado==0) {
                mensagem += retorno.rodadas+" rodada(s): <br/><b>";
          
                let premiacoes = retorno.premiacoes;
                let qtdPremiacoes = Object.keys(premiacoes).length;

                for (let i = 0; i<qtdPremiacoes; i++) {
                    let premiacao = premiacoes[i];

                    mensagem += (i+1)+"° ";

                    if (premiacao.fechado==0) {
                        mensagem += premiacao.numero_sorteio+" | R$????<br/>";
                    } else {
                        mensagem += premiacao.numero_sorteio+" | R$"+real(premiacao.lucro)+"<br/>";
                    }
                }
            } else {
                mensagem += "<font color='red'>CANCELADO</font><br/>";
            }
            
            mensagem += "</b>---------------------------------<br/>"+retorno.dh_consulta+quebra;

            aparecerRetorno(titulo, mensagem, true);
            break;
    }
}

function real(valor) {	    
    return parseFloat(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2}); 
}

function imprimirRetorno() {
    try {
        Website2APK.printPage();
    } catch (e) {
        window.print();
    }
}