# Fluxo da Aplicacao

## Requisicao HTTP

1. o Apache entrega a requisicao para `public/index.php`
2. `bootstrap/app.php` carrega ambiente, sessao e autoload
3. `routes/web.php` registra as rotas
4. `Router` localiza o handler
5. o controller processa a entrada
6. repositories executam SQL
7. a view e renderizada por `View`

## Autenticacao

Fluxo principal:

1. usuario acessa `/login`
2. `AuthController::showLogin()` renderiza a tela
3. envio do formulario faz `POST /login`
4. `AuthController::login()` valida CSRF e credenciais
5. usuario autenticado e salvo na sessao
6. acesso segue para `/dashboard`

## Tickets

Rotas principais:

- `GET /tickets`
- `GET /tickets/create`
- `POST /tickets/store`
- `GET /tickets/show?code=...`
- `POST /tickets/comment`
- `POST /tickets/status`

## Controle de acesso

Helpers globais fazem a protecao:

- `require_auth()`
- `require_role([...])`
- `auth_role(...)`

Regras atuais:

- `requester` ve apenas os proprios chamados
- `agent` e `admin` podem atualizar status
- `admin` tambem passa nas verificacoes de `auth_role()`

## Upload de anexos

O upload e salvo em `storage/uploads/`.

O fluxo atual:

1. valida erro de upload
2. valida tamanho maximo
3. gera nome seguro
4. move o arquivo para o diretorio final
5. salva metadados no banco

