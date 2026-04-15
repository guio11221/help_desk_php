# Operacao e CLI

## Comandos disponiveis

### Aplicar migrations pendentes

```powershell
php bin/migrate.php up
```

### Reverter o ultimo batch

```powershell
php bin/migrate.php rollback
```

### Ver status das migrations

```powershell
php bin/migrate.php status
```

### Criar migration automatica por diff

```powershell
php bin/make_migration.php add_phone_to_users
```

### Criar migration vazia

```powershell
php bin/make_migration.php manual_fix --blank
```

### Criar admin inicial

```powershell
php bin/create_admin.php --name="Admin" --email="admin@local.test" --password="SenhaForte123!" --role=admin
```

## Procedimentos operacionais

### Adicionar uma coluna

1. edite `database/schema.php`
2. gere a migration com `php bin/make_migration.php nome_da_migration`
3. revise o arquivo criado em `database/migrations/`
4. aplique com `php bin/migrate.php up`

### Corrigir uma migration gerada

Se o diff automatico nao gerar o SQL ideal:

1. abra a migration gerada
2. ajuste manualmente `up()` e `down()`
3. mantenha o `schema.php` consistente com a mudanca real

### Investigar problema de schema

Checklist:

- conferir `database/schema.php`
- conferir `database/schema.snapshot.json`
- conferir `database/migrations/`
- rodar `php bin/migrate.php status`

