# Design

## Approach
Make the smallest backend-only correction in the dashboard data providers:

1. Preserve the current AJAX contract: `documento` keys remain unchanged.
2. Use `fechaFactura` as the business date for sales/facturing KPIs.
3. Use `IFNULL(SUM(total), 0)` for monetary KPI totals so empty days return `0` instead of `NULL`.
4. Use half-open date ranges for monthly `fechaFactura` comparisons to avoid excluding same-day records after midnight.
5. Remove debug output before JSON responses.

## Review workload
Expected change: one regression script plus two PHP model methods. Budget risk: low; chained PRs are not needed unless implementation expands beyond 400 changed lines.
