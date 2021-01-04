trocarValores();
iniciaTimer();
mostrarOnline();

function trocarBolas(numeros) {
    let numeros_sorteados = numeros.split(',');

    if (numeros_sorteados.length==20) {
        for (let i=0; i<20; i++) {
            document.getElementsByTagName('h1')[i].innerText = ("00" + numeros_sorteados[i]).slice(-2);
            trocarCores(numeros_sorteados[i], i);
        }   
    } 
}

function mostrarOnline() {
    setTimeout(function() {
        let on = online();

        if (on) {
            document.getElementById('online').innerHTML = "ONLINE";
            document.getElementById('online').style.color = "rgb(70, 255, 70)";
        } else {
            document.getElementById('online').innerHTML = "OFFLINE";
            document.getElementById('online').style.color = "RED";
        }
        mostrarOnline();
    }, 5000);
}

function online() {
    return window.navigator.onLine;
}

function trocarCores(numero, i) {
    let num = numero.toString().split('');
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

function apagarBolas() {
    for (let i=0; i<20; i++) {
        document.getElementsByClassName('esfera')[i].style.opacity = "0";
    }
}

function aparecerBolas() {
    document.getElementsByClassName('esfera')[20].style.opacity = 100;
    for (let i=0; i<20; i++) {
        setTimeout(function() {
            document.getElementsByClassName('esfera')[i].style.opacity = "100";
            document.getElementsByClassName('esfera')[20].style.background = document.getElementsByClassName('esfera')[i].style.background;
            document.getElementsByTagName('h1')[20].innerText = document.getElementsByTagName('h1')[i].innerText;
            if (i==19) {
                setTimeout(function() {
                    document.getElementsByClassName('esfera')[20].style.opacity = "0";
                }, (2000*i) + 2000);
            }
        }, 2000 * i);
    }
}

function trocarValores() {
    let t = 5000;
    for(let i=0; i<8; i++) {
        setTimeout(function() {
            for(let c=0; c<8; c++) {
                if (c!=i) {
                    document.getElementsByTagName('h4')[c].style.display = "none";
                } else {
                    document.getElementsByTagName('h4')[c].style.display = "block";
                }
            }
            if (i==7) {
                setTimeout(function() {
                    trocarValores();
                }, t);
            }
        }, t * i);
    }
}

function timer(segundos) {
    if (segundos>=300) {
    	segundos = 1;
    }

    let ms = segundosParaMS(segundos);
    let min = 4 - ms[0];
    let seg = 59 - ms[1];
    let seg_aux = seg;
    let aux = 0;

    for (let m = min; m>=0; m--) {
        setTimeout(function() {
	    if (aux==1) {
 		seg = 59;
	    }
            for (let s = seg; s>=0; s--) {
                setTimeout(function() {
                    document.getElementById('tempo').innerText = ("00" + m).slice(-2)+':'+("00" + s).slice(-2);
		    if (m==0 && s==0) {
                    iniciaSorteio();
                    apagarTimer();
		   	setTimeout(function () {
			    aparecerTimer();
		            iniciaTimer();
                        }, 5000);
                    }
                }, 1000 * (seg-s));
            }
            aux = 1;
        }, m==min?0:60000 * ((min-m)-1) + (seg_aux*1000) + 1000);
    }
} 

function apagarTimer() {
	document.getElementById('tempo').innerText = '';
}

function aparecerTimer() {
	document.getElementById('tempo').innerText = '';
}

function iniciaSorteio() {
    apagarNumerosBonus();
    apagarNumeroSorteio();
    apagarBolas();
    aparecerBolaSorteada();
    aparecerMensagem(); 

    setTimeout(function () {
        let xmlreq = criaRequest();
        xmlreq.open('POST', '_php/retorna_sorteio.php', true);
        xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xmlreq.send();
    
        xmlreq.onreadystatechange = function(){
            if (xmlreq.readyState == 4) {
                if (xmlreq.status == 200) {
	                let retorno = JSON.parse(xmlreq.responseText);
	                sorteio(retorno);
                } else {
                    iniciaSorteio();
                }
            } 
        }
    }, 5000);
}

function sorteio(retorno) {
    let numero_sorteio = retorno.numero_sorteio;
    let numeros_sorteados = retorno.numeros_sorteados;
    let numero_bonus = retorno.numero_bonus;
  
    apagarMensagem();
    trocarNumeroSorteio(numero_sorteio);
    trocarBolas(numeros_sorteados);
    aparecerBolas();
    setTimeout(function () {
         aparecerValores();
         trocarNumerosBonus(numero_bonus);
         aparecerPremiacoes(retorno.bilhetes_premiados);
    }, 40000);
}

function aparecerPremiacoes(premiacoes) {
    for (let i = 0; i<premiacoes.length; i++) {
        let bilhete = premiacoes[i].bilhete;

        setTimeout(function () {   
            aparecerPremiacao(bilhete);
            console.log(15000*i);
        }, 15000*i);

        setTimeout(function () {    
            apagarPremiacao();
            console.log((15000*i)+10000);
        }, (15000*i)+10000);
    }
}

function apagarNumeroSorteio() {
    document.getElementById('numero-sorteio').innerText = '';
}

function segundosParaMS(segundos) {
    let tempo = Number(segundos);
    let m = Math.floor(tempo % 3600 / 60);
    let s = Math.floor(tempo % 3600 % 60);
    let ms = [m, s];

    return ms; 
}

function apagarNumerosBonus() {
	for (let i=0; i<5; i++) {
		document.getElementsByTagName('h3')[i].innerText = '';
	}
}

function trocarNumerosBonus(valor) {
    let numeroBonus = valor;

    while (numeroBonus.length<5) {
    	numeroBonus='0'+numeroBonus;
    }	

    for (let i = 0; i<5; i++) {
        document.getElementsByTagName('h3')[i].innerText = numeroBonus[i];
    }
}

function trocarBonus3(valor) {
    document.getElementsByTagName('h2')[0].innerText = 'Bonus 3: R$'+valor;
}

function trocarBonus4(valor) {
    document.getElementsByTagName('h2')[1].innerText = 'Bonus 4: R$'+valor;
}

function trocarAcumulado(valor) {
    document.getElementsByTagName('h2')[2].innerText = 'Acumulado: R$'+valor;
}

function trocarNumeroSorteio(numero) {
    document.getElementById('titulo-sorteio').innerText = "SORTEIO";
    document.getElementById('numero-sorteio').innerText = numero;
}

function apagarValores() {
    document.getElementById('valores').style.display = 'none';
}

function aparecerBolaSorteada() {
    apagarValores();
    document.getElementById('bola-sorteada').style.display = 'flex';
}

function apagarBolaSorteada() {
    document.getElementById('bola-sorteada').style.display = 'none';
}

function aparecerValores() {
    apagarBolaSorteada();
    document.getElementById('valores').style.display = 'flex';
}

function aparecerMensagem() {
    document.getElementById('mensagem').style.display = 'flex';
}

function apagarMensagem() {
    document.getElementById('mensagem').style.display = 'none';
}

function aparecerPremiacao(bilhete) {
    document.getElementById('premiacao').style.display = 'block';
    document.getElementById('premiacao').innerHTML = 'BILHETE '+bilhete+' FOI PREMIADO!';
}

function apagarPremiacao() {
    document.getElementById('premiacao').style.display = 'none';
    document.getElementById('premiacao').innerHTML = '';
}


function iniciaTimer() {
    let xmlreq = criaRequest();
    xmlreq.open('POST', '_php/retorna_tempo.php', true);
    xmlreq.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlreq.send();
    
    xmlreq.onreadystatechange = function(){
        if (xmlreq.readyState == 4) {
            if (xmlreq.status == 200) {
                let retorno = JSON.parse(xmlreq.responseText);
		        timer(retorno.segundos);
	        } else {
                setTimeout(function () {
                    iniciaTimer();
               }, 5000);
            }
        } 
    }
}
  