# 🍻 Sorteio Premiado 🎉

Um Web App focado em Android, criado para proporcionar lucro e entretenimento para bares! Desenvolvi esse projeto em 2017 e recentemente apliquei uma atualização para facilitar sua implementação com Docker.

#### 1. Apresentação do sistema
#### 2. Como instalar

## 1. Apresentação do sistema

O sistema é dividido em duas partes, sendo que uma delas vai instalada em uma Android TV Box ou SmartTV Android (onde são apresentado os sorteios ao público) e outra vai instalado no celular do dono do bar, que ficará responsável por gerenciar as apostas.

  ### 1.1. Televisão
  
![Televisão - Tela](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Televis%C3%A3o%20-%20Tela.png)

  ### 1.2. Celular
  
![Celular - Login](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Celular%20-%20Login.jpg) 
![Celular - Menu](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Celular%20-%20Menu.jpg)
![Celular - Configuracoes 1](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Celular%20-%20Configuracoes%201.jpg)
![Celular - Configuracoes 2](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Celular%20-%20Configuracoes%202.jpg) 
![Celular - Apostas](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Celular%20-%20Apostas.jpg)
![Celular - Sorteios](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Celular%20-%20Sorteios.jpg) 
![Celular - Premiacoes](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Celular%20-%20Premiacoes.jpg)
![Celular - Caixa 1](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Celular%20-%20Caixa%201.jpg) 
![Celular - Caixa 2](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Celular%20-%20Caixa%202.jpg)

## 2. Como instalar?

### 2.1. Docker

```bash
docker compose up --build
```

### 2.2. Servidor linux

Servidor WEB VPS linux (que permita utilizar o crontab). É necessário que o apache, php e mysql estejam instalados e configurados corretamente.

#### 2.2.1 Instalação

	Download dos arquivos:
		>> cd /var/www/html
		>> wget https://github.com/RafaelCecchin/sorteio-premiado/archive/master.zip
		>> unzip master.zip
		>> mv sorteio-premiado-master/* ./
		>> rm -r sorteio-premiado-master
		>> rm master.zip
	Configuração base de dados:
		>> mysql -u root -p
	 	[PASSWORD]
		>> CREATE DATABASE sorteios CHARACTER SET utf8 COLLATE utf8_general_ci;
		>> SET time_zone = 'America/Sao Paulo'; SET time_zone = "+03:00"; SET @@session.time_zone = "+03:00";
		>> exit
		>> mysql -u root -p sorteios < /var/www/html/_db/_dump_bd/sorteios.sql
		[PASSWORD]
		>> sudo nano /var/www/html/_bd/conexao_bd.php
		Informe no arquivo as credenciais da base de dados e tecle CTRL + O e CTRL + X para salvar e sair.
	Configuração do crontab:
		>> sudo crontab -e
		Informe no arquivo o seguinte dado (sem aspas): "*/5 * * * * php -f /var/www/html/_iframes/_sorteio/_php/sorteio.php"
		Tecle :wq para salvar e sair
	Alterar a permissão do arquivo de sorteios:
		>> chmod 700 /var/www/html/_iframes/_sorteio/_php/sorteio.php
		
#### 2.2.2 Geração dos APK's

	Baixe e instale o programa WEBSITE 2 APK BUILDER PRO (https://websitetoapk.com/).
	Geração do APK de apostas (celular):
![Web2apk Builder 1](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Web2apk%201.png)

	No campo "APP TITLE", informe o título do aplicativo de apostas. Esse é o nome que vai aparecer em baixo do ícone do aplicativo em seu celular android.
	No campo "APP ORIENTATION", selecione a opção "PORTRAIT" (retrato).
	No campo "URL", informe a URL do seu WEB APP, apontando para o diretório _iframes/_login/login.html. Também informe se a conexão é HTTP ou HTTPS.
	No campo "CACHE MODE", selecione a opção "NO CACHE". Isso permite que atualizações do front-end sejam aplicadas para o cliente de forma mais eficaz.
	Clique no botão "CUSTOMIZE APP PERMISSIONS" e desmarque todas as opções de permissão possíveis. Após isso, clique em "OK".
		
![Web2apk Builder 3](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Web2apk%203.png)
	
	Em extras, deixe selecionado apenas "FULL SCREENS", "JAVASCRIPT API's", "ALLOW EXTERNALS URLs" e "CONFIRM ON EXIT".
	Clique no botão "GENERATE APK" e em "OK" quando a seguinte mensagem aparecer:
![Web2apk Builder 4](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Web2apk%204.png)
		
	Clique em "FINISH!" quando a seguinte mensagem aparecer:
![Web2apk Builder 6](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Web2apk%206.png)

	Para geração do APK de sorteios (televisão), será necessário fazer as seguintes alterações:
![Web2apk Builder 2](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Web2apk%202.png)
	
	Altere o campo "APP TITLE" para "Sorteios" ou outro nome que desejar.
	Altere o campo "APP ORIENTATION" para opção "LANDSCAPE".
	No campo "URL", altere o diretório para _iframes/_sorteio/sorteio.html
	Clique no botão "GENERATE APK" e em "OK" quando a seguinte mensagem aparecer:
![Web2apk Builder 4](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Web2apk%204.png)
	
	Clique em "FINISH!" quando a seguinte mensagem aparecer:
![Web2apk Builder 6](https://github.com/RafaelCecchin/sorteio-premiado/blob/master/_img/Web2apk%206.png)
	
## 3. Acesso ao painel

Usuário: root@dominio.com
Senha: 1234

A alteração desses dados deve ser feita diretamente via banco de dados. 
Obs: a senha deve ser salva no banco de dados após ser criptografada pela função password_hash do PHP, com o parâmetro PASSWORD_DEFAULT.