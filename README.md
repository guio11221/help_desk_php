# Help Desk

Projeto de Help Desk em PHP 8.0+ com PostgreSQL, estruturado para manutencao e deploy serio.

## Estrutura de pastas

- `bootstrap/` inicializacao, helpers e autoload
- `config/` configuracoes de aplicacao e banco
- `routes/` rotas HTTP
- `src/Controllers` controladores
- `src/Repositories` acesso a dados
- `src/Database` migrations
- `src/Support` infraestrutura compartilhada
- `resources/views` templates
- `public/` front controller e assets publicos
- `database/migrations` historico versionado do schema
- `bin/` comandos CLI
- `storage/` uploads e logs

## Recursos implementados

- autenticacao com sessao
- controle de acesso por papel
- dashboard operacional
- abertura e listagem de chamados
- comentarios publicos e internos
- atribuicao de responsavel
- upload de anexos
- migrations e criador de migrations
- script de criacao do primeiro admin

## Requisitos

- PHP 8.0+
- extensao `pdo_pgsql`
- PostgreSQL 13+
- Apache com `mod_rewrite`

## Instalacao

1. Copie `.env.example` para `.env`.
2. Ajuste as credenciais do PostgreSQL.
3. Rode `php bin/migrate.php up`.
4. Rode `php bin/create_admin.php --name="Admin" --email="admin@local.test" --password="SenhaForte123!" --role=admin`.
5. Aponte o Apache para `public/`.

## Comandos

- `php bin/migrate.php up`
- `php bin/migrate.php rollback`
- `php bin/migrate.php status`
- `php bin/make_migration.php add_sla_fields`
- `php bin/make_migration.php manual_fix --blank`

## Migrations inteligentes

O arquivo fonte do schema fica em `database/schema.php`.

Fluxo:

1. Edite `database/schema.php`.
2. Rode `php bin/make_migration.php nome_da_migration`.
3. O sistema compara `database/schema.php` com `database/schema.snapshot.json`.
4. Ele gera automaticamente SQL de `CREATE TABLE`, `ALTER TABLE`, `DROP COLUMN`, `CREATE INDEX`, `DROP INDEX` e atualiza o snapshot.
5. Depois aplique com `php bin/migrate.php up`.

Limites atuais do diff automatico:

- detecta criacao e remocao de tabelas
- detecta criacao, remocao e alteracao de colunas
- detecta mudanca de tipo, nullability, default, unique, primary key e foreign key
- detecta criacao, remocao e alteracao de indexes

Renomeacao de tabela ou coluna ainda aparece como `drop + create`, nao como `rename`.

## Documentacao completa

A documentacao detalhada do servico esta em [docs/README.md](/C:/xampp/htdocs/help_desk_php/docs/README.md).
