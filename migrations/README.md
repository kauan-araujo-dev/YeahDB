Migração: ON DELETE CASCADE para FKs de `eventos`

Objetivo
- Atualizar as chaves estrangeiras que referenciam `eventos.id` para usar `ON DELETE CASCADE`, de forma que ao deletar um evento o SGBD remova automaticamente os registros filhos em tabelas relacionadas.

Arquivos criados
- `migrations/20251205_add_cascade_to_eventos.sql` — contém os `ALTER TABLE` necessários.

Instruções (recomendado fazer em ambiente de desenvolvimento primeiro)

1) Backup do banco (obrigatório)
- Via PowerShell (ajuste `-u` e caminho conforme seu ambiente):

  mysqldump -u root -p yeah_db > C:\backup\yeah_db_backup.sql

  Você será solicitado a informar a senha do usuário MySQL.

- Ou exporte via phpMyAdmin (Export -> SQL).

2) Aplicar a migração (opção A: via cliente MySQL)
- No PowerShell, rode:

  mysql -u root -p yeah_db < "c:/xampp/htdocs/Yeahdb/migrations/20251205_add_cascade_to_eventos.sql"

  (ajuste usuário, senha e caminho se necessário)

- Alternativa: abra o cliente `mysql` e rode `SOURCE`:

  mysql -u root -p
  USE yeah_db;
  SOURCE c:/xampp/htdocs/Yeahdb/migrations/20251205_add_cascade_to_eventos.sql;

3) Aplicar via phpMyAdmin (opção B)
- Abra phpMyAdmin, selecione o banco `yeah_db`.
- Vá para a aba SQL e cole todo o conteúdo do arquivo `20251205_add_cascade_to_eventos.sql` e execute.

Observações
- Os nomes das constraints usados neste arquivo são os que constavam no dump (`artista_evento_ibfk_2`, `evento_estilo_ibfk_1`, `foto_evento_ibfk_1`, `integrante_evento_ibfk_1`). Se no seu banco essas constraints tiverem nomes diferentes, use `SHOW CREATE TABLE <tabela>;` para confirmar os nomes e ajustar o SQL.
- Caso ocorra erro ao dropar uma constraint (nome inexistente), ajuste o arquivo de migração conforme o nome correto ou remova a linha correspondente e execute manualmente apenas os comandos necessários.
- Após aplicar a migração, tente excluir um evento de teste para confirmar que os registros filhos são removidos automaticamente.

Se quiser, eu posso:
- Gerar um pequeno script PowerShell que executa backup + migração automaticamente.
- Detectar automaticamente os nomes das constraints e criar um SQL de migração que funcione sem mudanças (posso tentar, se quiser que eu faça isso).