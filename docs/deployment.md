# Instalacao e Deploy

## Requisitos

- PHP 8.0+
- extensao `pdo_pgsql`
- PostgreSQL 13+
- Apache com `mod_rewrite`

## Instalacao local

1. Configure o Apache para servir `public/`.
2. Copie `.env.example` para `.env`.
3. Ajuste as variaveis de banco e URL.
4. Rode as migrations.
5. Crie o usuario administrador inicial.

Comandos:

```powershell
php bin/migrate.php up
php bin/create_admin.php --name="Admin" --email="admin@local.test" --password="SenhaForte123!" --role=admin
```

## Variaveis de ambiente

Arquivo base: `.env.example`

Variaveis principais:

- `APP_NAME`
- `APP_ENV`
- `APP_DEBUG`
- `APP_URL`
- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `SESSION_NAME`
- `SESSION_SECURE`
- `UPLOAD_DIR`
- `MAX_UPLOAD_MB`

## Producao

Recomendacoes minimas:

- usar HTTPS
- `SESSION_SECURE=true`
- restringir escrita apenas em `storage/`
- manter `public/` como unica pasta publica
- revisar cada migration antes de aplicar
- manter backup do banco antes de mudancas estruturais

## Sequencia de deploy

1. atualizar codigo
2. revisar `.env`
3. rodar `php bin/migrate.php up`
4. validar login e listagem de chamados
5. validar uploads e permissao de escrita em `storage/uploads`

