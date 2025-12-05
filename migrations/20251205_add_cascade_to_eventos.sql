-- Migration: Add ON DELETE CASCADE to foreign keys that reference eventos(id)
-- Generated: 2025-12-05
-- IMPORTANT: Make a backup before running this migration.

-- Drop existing foreign keys (names expected from the schema dump)
ALTER TABLE artista_evento DROP FOREIGN KEY artista_evento_ibfk_2;
ALTER TABLE evento_estilo DROP FOREIGN KEY evento_estilo_ibfk_1;
ALTER TABLE foto_evento DROP FOREIGN KEY foto_evento_ibfk_1;
ALTER TABLE integrante_evento DROP FOREIGN KEY integrante_evento_ibfk_1;

-- Recreate with ON DELETE CASCADE
ALTER TABLE artista_evento
  ADD CONSTRAINT artista_evento_ibfk_2 FOREIGN KEY (id_evento) REFERENCES eventos(id) ON DELETE CASCADE;

ALTER TABLE evento_estilo
  ADD CONSTRAINT evento_estilo_ibfk_1 FOREIGN KEY (id_evento) REFERENCES eventos(id) ON DELETE CASCADE;

ALTER TABLE foto_evento
  ADD CONSTRAINT foto_evento_ibfk_1 FOREIGN KEY (id_evento) REFERENCES eventos(id) ON DELETE CASCADE;

ALTER TABLE integrante_evento
  ADD CONSTRAINT integrante_evento_ibfk_1 FOREIGN KEY (id_evento) REFERENCES eventos(id) ON DELETE CASCADE;

-- End of migration
