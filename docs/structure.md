# Estrutura de Pastas

## Raiz do projeto

```text
bootstrap/
config/
database/
docs/
public/
resources/
routes/
src/
storage/
bin/
```

## Pastas

### `bootstrap/`

Inicializacao da aplicacao.

- `app.php`: sobe o ambiente da aplicacao
- `autoload.php`: autoload simples por pastas
- `helpers.php`: helpers globais

### `config/`

Configuracoes por contexto.

- `app.php`
- `database.php`

### `database/`

Responsavel pelo modelo e historico do banco.

- `schema.php`: schema declarativo atual
- `schema.snapshot.json`: ultimo estado conhecido para comparacao
- `schema.sql`: referencia SQL consolidada
- `migrations/`: migrations geradas ou manuais

### `docs/`

Documentacao tecnica.

### `public/`

Ponto de entrada HTTP e assets publicos.

- `index.php`
- `.htaccess`
- `assets/css/app.css`

### `resources/`

Templates da aplicacao.

- `views/layout.php`
- `views/auth/`
- `views/dashboard/`
- `views/errors/`
- `views/tickets/`

### `routes/`

Declaracao de rotas HTTP.

### `src/`

Codigo principal da aplicacao.

- `Controllers/`
- `Core/`
- `Database/`
- `Http/`
- `Repositories/`
- `Support/`

### `storage/`

Arquivos gerados em runtime.

- `uploads/`
- `logs/`

### `bin/`

Comandos CLI operacionais.

- `create_admin.php`
- `migrate.php`
- `make_migration.php`

