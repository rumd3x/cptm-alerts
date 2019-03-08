# cptm-alerts
Notificações em tempo real sobre mudanças de status nas linhas dos trens e metrôs de São Paulo diretamente no canal do Slack.

[![Build Status](https://travis-ci.org/rumd3x/cptm-alerts.svg?branch=master)](https://travis-ci.org/rumd3x/cptm-alerts)
[![Latest Stable Version](https://poser.pugx.org/rumd3x/cptm-alerts/v/stable)](https://packagist.org/packages/rumd3x/cptm-alerts)
[![License](https://poser.pugx.org/rumd3x/cptm-alerts/license)](https://packagist.org/packages/rumd3x/cptm-alerts)
[![composer.lock](https://poser.pugx.org/rumd3x/cptm-alerts/composerlock)](https://packagist.org/packages/rumd3x/cptm-alerts)

![Notificação Exemplo](/docs/images/notificacoes.png)

## Getting started
### Workspace
1. Você precisará de um Workspace no slack. Se não tiver [crie-o](https://slack.com/get-started)

### Token
1. Você precisará gerar um token para o seu Workspace se comunicar com o projeto.

2. Para isso [crie um App no seu Workspace](https://api.slack.com/apps)

![App no Workspace](/docs/images/app.jpg)

3. Após criado o App, vá até **Bot Users** no menu lateral e crie um Bot para seu App.

4. Após criado o Bot, vá até **OAuth & Permissions** no menu lateral. Na seção **Scope** dê a permissão *chat:write:bot* e *bot* para o seu App. Será solicitado que o App seja reinstalado no Workspace para as novas permissões.

![App configurado corretamente](/docs/images/appconfig.jpg)

5. Após dadas as permissões e reinstalado o App salve a **Bot User OAuth Access Token** ela será necessária para configurar o projeto.

### Configurando o Projeto

1. Baixe o projeto para uma pasta em sua maquina:
```sh
git clone https://github.com/rumd3x/cptm-alerts.git
# ou
composer create-project rumd3x/cptm-alerts
```

2. Vá até a pasta do projeto: `cd cptm-alerts`.

3. Instale as dependências: `composer install`.

4. Crie o seu arquivo de configurações do projeto: `cp .env.example .env`.

5. Edite o arquivo `.env`. Em `SLACK_CHANNEL` coloque o canal do slack em que o bot publicará as alterações de status. Em `SLACK_KEY` coloque o **Bot User OAuth Access Token** salvo anteriormente.

6. Para verificar o status das linhas execute `php /caminho/do/projeto/run.php`.

7. Para receber automaticamente as mudanças de status coloque o comando num cron. Exemplo:
```sh
* * * * * php /caminho/do/projeto/run.php >> /caminho/do/log/run.log 2>&1
```

### Arquivo .env
No arquivo `.env` existem possibilidades de personalização no comportamento da aplicação, por meio de configurações no arquivo `.env`

#### NOTIFY_LEVEL
A configuração `NOTIFY_LEVEL` deve conter um número inteiro válido e representa o menor nível de criticidade que a aplicação notificará.

Os níveis existentes são:
```
Nível 0: Mudanças já esperadas, como o encerramento das operações as 0h e o início das operações.
Nível 1: Mudanças positivas, como a normalização da operação após um período de lentidão.
Nível 2: Mudanças alarmantes, como a operação da linha estar com lentidão.
Nível 3: Mudanças perigosas, como a paralização da operação em uma linha.
```

- Exemplo:

Se desejar receber notificações em todos os níveis deverá configurar para `NOTIFY_LEVEL=0`. Se não quiser receber notificações de mudanças já esperadas trocar para `NOTIFY_LEVEL=1`. Se quiser receber apenas notificações de paralização `NOTIFY_LEVEL=3`.

#### NOTIFY_DAYS
A configuração `NOTIFY_DAYS` diz os dias que deverão ser enviadas notificações. Deve conter os dias que as notificações serão enviadas separados por vírgula.

Os valores são:
```
all: Enviar Notificações todos os dias
0: Domingo
1: Segunda-feira
2: Terça-feira
3: Quarta-feira
4: Quinta-feira
5: Sexta-feira
6: Sábado
```

- Exemplo:
Para receber notificações todos os dias use `NOTIFY_DAYS=all`. Para receber notificações somente em dias da semana use `NOTIFY_DAYS=1,2,3,4,5`.

#### NOTIFY_LINES
A configuração `NOTIFY_LINES` diz as linhas dos trens/metrô que deverão ser monitoradas. Deve conter o numero das linhas separados por vírgula.

Os valores são:
```
all: Enviar Notificações para todas as linhas
1: Linha 1 Azul do Metrô
2: Linha 2 Verde do Metrô
3: Linha 3 Vermelha do Metrô
4: Linha 4 Amarela do Metrô
5: Linha 5 Lilás do Metrô
6: Linha 6 Laranja do Metrô
7: Linha 7 Rubi da CPTM
8: Linha 8 Diamante da CPTM
9: Linha 9 Esmeralda da CPTM
10: Linha 10 Turquesa da CPTM
11: Linha 11 Coral da CPTM
12: Linha 12 Safira da CPTM
13: Linha 13 Jade da CPTM
15: Linha 15 Prata da CPTM
17: Linha 17 Ouro da CPTM
```

- Exemplo:
Para receber notificações de todas as linhas `NOTIFY_LINES=all`. Para receber notificações somente da linha azul e amarela use `NOTIFY_LINES=1,4`.

### Debugando
#### Se você fez tudo acima corretamente e não está recebendo notificações em seu canal do slack:
* Verifique se o composer foi executado corretamente
* Verifique a saída do php nos logs do cron (Ou execute o comando redirecionando a saída para um arquivo).
* Verifique os logs do projeto  no arquivo `/caminho/do/projeto/Storage/Logs/app.log`
* Verifique se o usuário rodando o php está no grupo de permissões correto
* Verifique se o arquivo *.env* tem permissão de leitura
* Verifique se a pasta *Storage* tem permissão de escrita

## Todo
* Refactor para suportar mais provedores além da CPTM.
* Outros canais de notificação? Quais?
