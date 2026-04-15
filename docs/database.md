# Banco e Migrations

## Visao geral

O projeto possui duas formas de representar o banco:

- `database/schema.php`: representacao declarativa em PHP
- `database/migrations/*.php`: historico executavel das mudancas

O `schema.php` e a fonte de verdade para geracao automatica de migrations.

## Estrutura do schema declarativo

Exemplo simplificado:

```php
return [
    'extensions' => ['pgcrypto'],
    'tables' => [
        'users' => [
            'columns' => [
                'id' => [
                    'type' => 'BIGSERIAL',
                    'primary' => true,
                ],
                'email' => [
                    'type' => 'VARCHAR(180)',
                    'unique' => true,
                ],
            ],
            'indexes' => [],
        ],
    ],
];
```

## Propriedades suportadas em colunas

- `type`
- `primary`
- `unique`
- `nullable`
- `default`
- `references`
- `on_delete`

Exemplo com chave estrangeira:

```php
'requester_id' => [
    'type' => 'BIGINT',
    'references' => ['table' => 'users', 'column' => 'id'],
    'on_delete' => 'RESTRICT',
],
```

## Snapshot

O arquivo `database/schema.snapshot.json` guarda o ultimo estado conhecido do schema declarativo.

Ele e usado como base para gerar o diff.

Nao edite esse arquivo manualmente, a menos que saiba exatamente por que esta fazendo isso.

## Fluxo de migration inteligente

### Alterar schema

Voce altera `database/schema.php`.

Exemplo: adicionar um campo `phone` em `users`:

```php
'phone' => [
    'type' => 'VARCHAR(30)',
    'nullable' => true,
],
```

### Gerar migration

```powershell
php bin/make_migration.php add_phone_to_users
```

O sistema:

1. le `database/schema.php`
2. le `database/schema.snapshot.json`
3. calcula o diff
4. gera uma migration em `database/migrations/`
5. atualiza o snapshot

### Aplicar migration

```powershell
php bin/migrate.php up
```

### Fazer rollback

```powershell
php bin/migrate.php rollback
```

## Comandos

### Geracao automatica por diff

```powershell
php bin/make_migration.php add_phone_to_users
```

### Geracao manual em branco

```powershell
php bin/make_migration.php hotfix_manual --blank
```

### Status

```powershell
php bin/migrate.php status
```

## Tipos de alteracao que o diff detecta

- criacao de tabela
- remocao de tabela
- criacao de coluna
- remocao de coluna
- mudanca de tipo
- mudanca de `NULL/NOT NULL`
- mudanca de `DEFAULT`
- mudanca de `UNIQUE`
- mudanca de `PRIMARY KEY`
- mudanca de `FOREIGN KEY`
- criacao de indice
- remocao de indice
- alteracao de indice

## Limites atuais

- rename de coluna ainda vira `drop + add`
- rename de tabela ainda vira `drop + create`
- nao ha introspeccao automatica do banco real para reconciliar drift
- seeds nao fazem parte do diff

## Recomendacoes

- sempre gere a migration logo apos alterar `schema.php`
- revise a migration gerada antes de aplicar em producao
- evite renomear coluna ou tabela sem migration manual se houver dados criticos
- rode backup antes de mudancas destrutivas

