-- Script de migración: cerrar ventas históricas con statusCierre=0
-- Causa: bug en procesarCierre que usaba fecha 1970-01-01
-- Ejecutar EN HORARIO NO LABORAL antes de deployar Fix 2.4
-- Verificar primero con el SELECT de diagnóstico

-- 1. Diagnóstico previo
SELECT
    fechaFactura,
    COUNT(*) as total_ventas,
    ROUND(SUM(total), 2) as monto
FROM ventas
WHERE statusCierre = '0'
  AND anulacion = '0'
  AND fechaFactura < CURDATE()
GROUP BY fechaFactura
ORDER BY fechaFactura DESC;

-- 2. Cierre masivo (descomentar para ejecutar)
-- UPDATE ventas
-- SET statusCierre = '1'
-- WHERE statusCierre = '0'
--   AND anulacion = '0'
--   AND fechaFactura < CURDATE();

-- 3. Verificación post-migración
-- SELECT COUNT(*) FROM ventas WHERE statusCierre='0' AND anulacion='0' AND fechaFactura < CURDATE();
-- Debe retornar 0
