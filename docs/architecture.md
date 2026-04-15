# Arquitetura

## Camadas

### Bootstrap

Arquivos em `bootstrap/` inicializam:

- carregamento de ambiente
- autoload
- sessao
- helpers globais

Arquivos principais:

- [bootstrap/app.php](/C:/xampp/htdocs/help_desk_php/bootstrap/app.php)
- [bootstrap/autoload.php](/C:/xampp/htdocs/help_desk_php/bootstrap/autoload.php)
- [bootstrap/helpers.php](/C:/xampp/htdocs/help_desk_php/bootstrap/helpers.php)

### HTTP

O front controller fica em [public/index.php](/C:/xampp/htdocs/help_desk_php/public/index.php).

Fluxo:

1. sobe o bootstrap
2. instancia o `Router`
3. carrega [routes/web.php](/C:/xampp/htdocs/help_desk_php/routes/web.php)
4. valida CSRF em `POST`
5. despacha para o controller

### Controllers

Controllers ficam em `src/Controllers/` e fazem:

- validacao basica de entrada
- orquestracao do caso de uso
- chamada aos repositories
- renderizacao das views

Controllers atuais:

- `AuthController`
- `DashboardController`
- `TicketController`

### Repositories

Repositories ficam em `src/Repositories/` e encapsulam SQL.

Repositorios atuais:

- `UserRepository`
- `CategoryRepository`
- `TicketRepository`

### Support

Classes de infraestrutura ficam em `src/Support/`.

Hoje existem:

- `Database`
- `Validator`
- `View`

### Database

O subdominio de migrations e schema diff fica em `src/Database/`.

Classes principais:

- `Migration`
- `MigrationManager`
- `SchemaState`
- `SchemaSqlBuilder`
- `SchemaDiff`
- `SchemaMigrationGenerator`

## Dependencias entre camadas

- `public/index.php` depende de `bootstrap/`
- `routes/` depende de controllers
- controllers dependem de repositories e support
- repositories dependem de `Database`
- migrations dependem de `Database`

## Decisoes importantes

- Sem framework full-stack: menor acoplamento e controle direto do fluxo.
- Sem ORM: SQL explicito e simples de depurar.
- Schema declarativo proprio: permite um fluxo de migrations inteligente sem adicionar Prisma ou Doctrine.

