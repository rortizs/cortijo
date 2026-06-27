# Fix dashboard sales KPIs

## Problem
The Cortijo dashboard now shows zero for `No. Ventas`, `Total Ventas`, `No. Compras`, and `Total Compras`, while `Total Facturado` and DTE counters still load. This indicates the AJAX endpoint and UI wiring are alive, but the affected KPI SQL is using the wrong business date/filter.

## Evidence
- Screenshot shows the four daily cards at zero while `Total Facturado` is populated.
- `functionsGlobal.js::resumenDocumentosOperados()` loads those cards from `adminController.php` service `resumenDocumentosOperados`.
- `Admin::resumenDocumentosOperados()` filters `cotizaciones`, `totalCotizado`, `compras`, and `totalCompras` with `DATE(created_at)=CURDATE()`.
- Commit `e20dad9` changed `Total Facturado`/`facturaciones` to current-year `fechaFactura` + FEL filters but left daily visible cards on `created_at`, creating inconsistent dashboard semantics.

## Scope
Restore dashboard daily sales/purchase cards using business document dates and numeric zero defaults. Keep the change under 400 changed lines and preserve existing DOM keys for compatibility.

## Non-goals
- Rename misleading legacy keys like `cotizaciones`/`totalCotizado`.
- Redesign the dashboard UI.
- Deploy to production in this work unit.
