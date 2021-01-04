function selecionaLogin() {
    document.getElementById('op-login').style = "border-bottom: 2px solid #5fbae9; margin: 0px 10px; color: black;";
    document.getElementsByTagName('li')[1].style = "border-bottom: 0px solid #5fbae9; margin: 2px 10px; color: #cccccc;";
    document.getElementsByTagName('iframe')[0].src = "./_iframes/_login/login.html";
}

function selecionaSorteio() {
    document.getElementById('op-sorteio').style = "border-bottom: 2px solid #5fbae9; margin: 0px 10px; color: black;";
    document.getElementsByTagName('li')[0].style = "border-bottom: 0px solid #5fbae9; margin: 2px 10px; color: #cccccc;";
    document.getElementsByTagName('iframe')[0].src = "./_iframes/_sorteio/sorteio.html";
}