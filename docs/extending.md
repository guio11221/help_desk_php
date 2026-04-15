# Guia de Extensao

## Adicionar nova rota

1. crie ou ajuste um controller em `src/Controllers/`
2. registre a rota em `routes/web.php`
3. crie a view correspondente em `resources/views/` se necessario

## Adicionar nova entidade

Fluxo recomendado:

1. adicione a tabela em `database/schema.php`
2. gere a migration por diff
3. crie o repository em `src/Repositories/`
4. crie controller e views se fizer sentido
5. exponha a funcionalidade nas rotas

## Adicionar novo repository

Padrão atual:

- receber `Database` no construtor
- concentrar SQL do agregado naquele repository
- retornar arrays simples

## Adicionar nova tela

1. crie o template em `resources/views/`
2. renderize via `View::render()` indiretamente pelo controller
3. se houver formulario `POST`, inclua `<?= csrf_field() ?>`

## Adicionar validacao

Validacoes pequenas podem ficar:

- no controller
- em `src/Support/Validator.php` se forem reutilizaveis

## Evolucoes tecnicas sugeridas

- services para regras de negocio mais densas
- notificacoes por e-mail
- auditoria
- departamentos e SLA
- conciliacao do schema declarativo com o banco real

