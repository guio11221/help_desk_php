# Visao Geral

## Objetivo

O Help Desk e uma aplicacao web em PHP com PostgreSQL para controle de chamados internos ou externos.

O sistema atual cobre:

- autenticacao por sessao
- papeis de acesso (`requester`, `agent`, `admin`)
- dashboard operacional
- abertura, listagem e detalhamento de chamados
- comentarios publicos e internos
- atribuicao de responsavel
- upload de anexos
- migrations versionadas
- geracao automatica de migrations por diff de schema

## Stack

- PHP 8.0+
- PostgreSQL
- Apache com `mod_rewrite`
- `PDO` para acesso ao banco
- Bootstrap no frontend

## Filosofia da base

O projeto foi organizado para ficar simples de operar e previsivel de manter:

- `public/` e a unica pasta exposta pela web
- `src/` concentra o codigo da aplicacao
- `resources/views/` guarda os templates
- `database/schema.php` e a fonte declarativa do schema
- `database/schema.snapshot.json` guarda o ultimo estado conhecido do schema
- `database/migrations/` guarda o historico executavel das mudancas

