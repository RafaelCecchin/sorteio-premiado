function criaRequest() {
    try{
        request = new XMLHttpRequest();        
    }catch (IEAtual){
        try{
            request = new ActiveXObject("Msxml2.XMLHTTP");       
        }catch(IEAntigo){
            try{
                request = new ActiveXObject("Microsoft.XMLHTTP");          
            }catch(falha){
                request = false;
            }
        }
    }
     
    if (!request) 
        alert("Seu Navegador não suporta Ajax!");
    else
        return request;
}

