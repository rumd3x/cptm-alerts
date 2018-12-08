# php-cptm-alerts
Alerta sobre mudanças de status nas linhas dos trens e metrôs de São Paulo diretamente no canal do Slack

## Getting started
1. Workspace
Você precisará de um Workspace no slack. Se não tiver [crie-o](https://slack.com/get-started)

2. Token
Você precisará gerar um token para o seu Workspace se comunicar com o projeto.

Para isso [crie um App no seu Workspace](https://api.slack.com/apps)

Após criado o App, vá até **Bot Users** no menu lateral e crie um Bot para seu App.

Após criado o Bot, vá até **OAuth & Permissions** no menu lateral. Na seção **Scope** dê a permissão *chat:write:bot* e *bot* para o seu App. Será solicitado que o App seja reinstalado no Workspace para as novas permissões.

Após dadas as permissões e reinstalado o App salve a **Bot User OAuth Access Token** ela será necessária para configurar o projeto.

3. Configurando o Projeto

Clone o repositório para uma pasta em seu servidor: `git clone https://github.com/rumd3x/php-cptm-alerts.git`.

Vá até a pasta do projeto: `cd php-cptm-alerts`.

Instale as dependências: `composer install`.

Crie o seu arquivo de configurações do projeto: `cp .env.example .env`.

Edite o arquivo `.env`. Em `SLACK_CHANNEL` coloque o canal do slack em que o bot publicará as alterações de status. Em `SLACK_KEY` coloque o **Bot User OAuth Access Token** salvo anteriormente.

Para verificar o status das linhas execute `php /caminho/do/projeto/run.php`.

Para receber automaticamente as mudanças de status coloque o comando num cron. Exemplo:
```sh
* * * * * php /caminho/do/projeto/run.php >> /caminho/do/log/run.log 2>&1
````