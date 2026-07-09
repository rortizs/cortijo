-- Cortijo ERP manual repair: ensure Guatemala country catalog exists.
--
-- Purpose:
--   The Empresas form uses empresas.idPaises and the paises catalog.
--   On Cortijo production, paises was empty and empresas.idPaises was 0,
--   which blocked FEL alias/API credential updates from the UI.
--
-- Safety:
--   Review and run manually after a database backup. This script is not
--   executed by GitHub Actions deploy.
--   Target tenant database: erp_elcortijo.

-- Fail before any tenant data mutation if the active database is not Cortijo.
-- The wrong-DB branch deliberately selects from a non-existent table so mysql
-- clients abort before START TRANSACTION/INSERT/UPDATE.
SET @ensure_guatemala_country_db_guard = IF(
    DATABASE() = 'erp_elcortijo',
    'SELECT ''Database guard OK: erp_elcortijo'' AS database_guard',
    'SELECT ''Wrong database selected; expected erp_elcortijo'' AS database_guard FROM wrong_database_selected__expected_erp_elcortijo'
);
PREPARE ensure_guatemala_country_db_guard_stmt FROM @ensure_guatemala_country_db_guard;
EXECUTE ensure_guatemala_country_db_guard_stmt;
DEALLOCATE PREPARE ensure_guatemala_country_db_guard_stmt;

START TRANSACTION;

INSERT INTO paises (
    descripcion,
    simboloMoneda,
    iva,
    isr,
    isrExcedente,
    seguroSocial,
    nombreSeguroSocial,
    deduccionSinComprobacion,
    ingresoSujetosISR,
    salarioDiarioHE,
    idMetodoDias,
    diasPeriodoVacas,
    pctPagoBonoAnt,
    idEmpresas,
    updated_at
)
SELECT
    'Guatemala',
    'Q',
    '12',
    '5',
    '7',
    '4.83',
    'IGSS',
    '0.00',
    '0.00',
    '0.00',
    1,
    '15',
    '0',
    1,
    NOW()
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1
    FROM paises
    WHERE descripcion = 'Guatemala'
      AND idEmpresas = 1
);

UPDATE empresas e
JOIN paises p ON p.descripcion = 'Guatemala' AND p.idEmpresas = e.id
SET e.idPaises = p.id
WHERE e.id = 1
  AND (e.idPaises IS NULL OR e.idPaises = 0);

COMMIT;
