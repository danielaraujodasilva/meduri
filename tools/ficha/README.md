# Ficha Meduri

Esta pasta contem a migracao da antiga `ficha` do projeto `tatuagem` para `meduri/tools/ficha`.

## O que foi limpo nesta migracao

- credenciais de banco hardcoded foram removidas
- logs e dump antigo nao foram copiados
- a conexao agora depende de um arquivo local ou variaveis de ambiente

## Estrutura

- `index.php`: cadastro de cliente e anamnese
- `public/cadastrar_tatuagem.php`: cadastro de tatuagem e agendamento
- `public/clientes.php`: lista de clientes e tatuagens
- `agenda/`: agenda visual com FullCalendar
- `database/schema.sql`: esquema inicial do banco `meduri`

## Setup rapido

1. Crie um banco MySQL chamado `meduri`.
2. Importe `tools/ficha/database/schema.sql`.
3. Copie `tools/ficha/config/conexao.local.example.php` para `tools/ficha/config/conexao.local.php`.
4. Preencha host, porta, banco, usuario e senha.
5. Acesse `tools/ficha/` no navegador.

## Observacao

O arquivo `conexao.local.php` esta ignorado no git de proposito e nao deve ser versionado.
