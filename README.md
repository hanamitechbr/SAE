# Sistema para o Agendamento de Equipamentos

Sistema simples para suprir uma necessidade interna da escola. Permite que os professores agendem equipamentos como `Tablets`, `Notebooks` e os `Labs. de Informática`.

## Configuração

### 1. Criar Credenciais no Google Cloud Console

1. Acesse: https://console.cloud.google.com/
2. Crie um novo projeto ou selecione um existente
3. Vá em "APIs e Serviços" > "Credenciais"
4. Clique em "Criar Credenciais" > "ID do cliente OAuth"
5. Configure a tela de consentimento OAuth
6. Tipo de aplicativo: "Aplicativo da Web"
7. Adicione URI de redirecionamento: `http://127.0.0.1:8081/SAE/callback.php`
8. Copie o Client ID e Client Secret

### 2. Configurar o Projeto

1. Edite o arquivo `config.php` e adicione suas credenciais:
   - `GOOGLE_CLIENT_ID`
   - `GOOGLE_CLIENT_SECRET`
   - Ajuste `GOOGLE_REDIRECT_URI`

2. Configure as credenciais do banco de dados no `config.php`:
   - `DB_HOST`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`

### 3. Criar Banco de Dados

Vá no seu PHPMyAdmin e importe o arquivo `db.sql` para criar as tabelas necessárias, ele criará um banco de dados chamado `sae`, se não quiser o nome crie você mesmo e depois remova as linhas `CREATE DATBASE sae;` e `USE sae;`.

### 4. Executar o Projeto

1. Coloque os arquivos na pasta do seu servidor web
2. Acesse: `http://127.0.0.1:8081/SAE/`
3. Clique em "Entrar com Google"

## Estrutura de Arquivos

- `index.php` - Página de login
- `callback.php` - Processa o retorno do Google OAuth
- `dashboard.php` - Página após login
- `logout.php` - Encerra a sessão
- `config.php` - Configurações
- `database.php` - Funções do banco de dados
- `style.css` - Estilos
- `db.sql` - Script de criação do banco

## Requisitos

- PHP 7.4+
- MySQL 5.7+
- Composer instalado
- Extensão cURL habilitada
- Extensão PDO habilitada

# Libs

- composer require google/apiclient:^2.0
- composer require vlucas/phpdotenv


# Aplicativo com Nim e Flutter
- Vou tentar desenvolver esse aplicativo usando ``Nim`` ``(backend)`` e ``Flutter`` ``(para o frontend)``

   ## Requisitos para conseguir rodar (caso seja um desenvolvedor)
    - jester
      instale utilizando o comando ``nimble install jester``
   - Flutter
      - Instale a extensão ``Flutter`` no VS Code
      - Aperte ``CTRL + SHIFT + P`` e digite ``"Flutter"``
      - Clique em ``Flutter: New project``
      - Instale o SDK e tudo o que a extensão pedir e no fim do processo clique em ``Add SDK to PATH``
   - Crie um projeto Flutter (você criará durante a instalação do Flutter)

   - Rode o servidor backend
      - Use o comando ``nim c -r main.nim``
      - Dê a permissão que o Windows precisa (vai abrir um pop-up, é só clicar em ``Permitir``)
      - Ele vai listar o seu servidor local no terminal, basta segurar ``CTRL`` e dar um clique em cima do link
         Ou acesse: ``http://127.0.0.1:5000``
         Se aparecer ``"API online"``, deu certo. Se não, faz o L.