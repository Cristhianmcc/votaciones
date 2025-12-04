-- =====================================================
-- CONFIGURACIÓN DE SUPABASE STORAGE
-- Sistema Electoral Perú 2026
-- =====================================================

-- IMPORTANTE: Ejecutar estos comandos en el Dashboard de Supabase
-- No en el SQL Editor, sino en Storage > Create Bucket

-- =====================================================
-- PASO 1: Crear Buckets (Desde Dashboard > Storage)
-- =====================================================

-- Bucket para fotos de candidatos:
-- Nombre: candidatos
-- Public: Yes (marcar como público)
-- File size limit: 5 MB
-- Allowed MIME types: image/jpeg, image/png, image/jpg

-- Bucket para logos de partidos:
-- Nombre: partidos
-- Public: Yes (marcar como público)
-- File size limit: 5 MB
-- Allowed MIME types: image/jpeg, image/png, image/jpg

-- =====================================================
-- PASO 2: Configurar Políticas de Acceso (Storage > Policies)
-- =====================================================

-- Policy para CANDIDATOS - SELECT (lectura pública)
-- Nombre: Public access for candidatos
-- Bucket: candidatos
-- Policy: SELECT
-- Target roles: public
-- SQL:
CREATE POLICY "Public access for candidatos"
ON storage.objects FOR SELECT
USING (bucket_id = 'candidatos');

-- Policy para CANDIDATOS - INSERT (subida autenticada)
-- Nombre: Authenticated users can upload candidatos
-- Bucket: candidatos
-- Policy: INSERT
-- Target roles: authenticated
-- SQL:
CREATE POLICY "Authenticated users can upload candidatos"
ON storage.objects FOR INSERT
WITH CHECK (bucket_id = 'candidatos');

-- Policy para PARTIDOS - SELECT (lectura pública)
-- Nombre: Public access for partidos
-- Bucket: partidos
-- Policy: SELECT
-- Target roles: public
-- SQL:
CREATE POLICY "Public access for partidos"
ON storage.objects FOR SELECT
USING (bucket_id = 'partidos');

-- Policy para PARTIDOS - INSERT (subida autenticada)
-- Nombre: Authenticated users can upload partidos
-- Bucket: partidos
-- Policy: INSERT
-- Target roles: authenticated
-- SQL:
CREATE POLICY "Authenticated users can upload partidos"
ON storage.objects FOR INSERT
WITH CHECK (bucket_id = 'partidos');

-- =====================================================
-- INSTRUCCIONES DE CONFIGURACIÓN MANUAL
-- =====================================================

/*
1. Ve a Supabase Dashboard: https://supabase.com/dashboard/project/matatan05sproject

2. En el menú lateral, haz clic en "Storage"

3. Haz clic en "Create bucket"

4. Crear primer bucket:
   - Name: candidatos
   - Public bucket: YES (activar toggle)
   - Click "Create bucket"

5. Crear segundo bucket:
   - Name: partidos
   - Public bucket: YES (activar toggle)
   - Click "Create bucket"

6. Configurar políticas para cada bucket:
   - Haz clic en el bucket "candidatos"
   - Ve a la pestaña "Policies"
   - Haz clic en "New Policy"
   - Selecciona "For full customization"
   - Copia y pega las políticas SQL de arriba
   
7. Repite el paso 6 para el bucket "partidos"

8. Verifica que los buckets aparezcan como públicos (icono de globo)

9. Prueba subir una imagen desde el panel de administración

NOTA: Las URLs públicas tendrán el formato:
https://matatan05sproject.supabase.co/storage/v1/object/public/candidatos/ARCHIVO.jpg
https://matatan05sproject.supabase.co/storage/v1/object/public/partidos/ARCHIVO.png
*/

-- =====================================================
-- ALTERNATIVA: Crear buckets por SQL (Requiere acceso service_role)
-- =====================================================

/*
-- Solo si tienes acceso a service_role key, ejecuta esto:

INSERT INTO storage.buckets (id, name, public)
VALUES 
  ('candidatos', 'candidatos', true),
  ('partidos', 'partidos', true)
ON CONFLICT (id) DO NOTHING;
*/
