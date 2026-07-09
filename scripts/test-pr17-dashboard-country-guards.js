#!/usr/bin/env node
const fs = require('fs');
const vm = require('vm');

const functionsGlobal = fs.readFileSync('erp/assets/js/functionsGlobal.js', 'utf8');
const mainPhp = fs.readFileSync('erp/main.php', 'utf8');
const adminPhp = fs.readFileSync('erp/models/admin.php', 'utf8');
const inventariosController = fs.readFileSync('erp/controllers/inventariosController.php', 'utf8');
const ensureCountrySql = fs.readFileSync('scripts/sql/ensure-guatemala-country.sql', 'utf8');

const failures = [];
function check(condition, message) {
  if (!condition) failures.push(message);
}

function extractFunction(source, functionName) {
  const start = source.indexOf(`function ${functionName}(`);
  if (start < 0) return '';

  const braceStart = source.indexOf('{', start);
  let depth = 0;
  for (let i = braceStart; i < source.length; i += 1) {
    if (source[i] === '{') depth += 1;
    if (source[i] === '}') depth -= 1;
    if (depth === 0) return source.slice(start, i + 1);
  }

  throw new Error(`Could not extract function ${functionName}`);
}

function runDashboardVentasPorMesScenario() {
  const state = {
    posts: [],
    html: {},
    text: {},
    dataTables: [],
    chartRows: null,
  };
  const values = {
    '#ventasMesAnio': '2026',
    '#ventasTablaAnio': '2026',
  };

  function jquery(selector) {
    return {
      val(value) {
        if (value !== undefined) values[selector] = String(value);
        return values[selector] || '';
      },
      text(value) {
        if (value !== undefined) state.text[selector] = String(value);
        return state.text[selector] || '';
      },
      html(value) {
        if (value !== undefined) state.html[selector] = String(value);
        return state.html[selector] || '';
      },
      DataTable(options) {
        state.dataTables.push({ selector, options });
        return { destroy() {} };
      },
    };
  }
  jquery.fn = { DataTable: { isDataTable() { return false; } } };
  jquery.extend = function extend(target, source) {
    const output = {};
    Object.keys(target || {}).forEach((key) => { output[key] = target[key]; });
    Object.keys(source || {}).forEach((key) => { output[key] = source[key]; });
    return output;
  };
  jquery.each = function each(items, callback) {
    for (let i = 0; i < items.length; i += 1) callback(i, items[i]);
  };
  jquery.post = function post(url, params, callback) {
    state.posts.push({ url, params });
    callback([
      { mes: '1', cantidadVentas: '2', totalVentas: '15.50' },
      { mes: '3', cantidadVentas: '3', totalVentas: '5.25' },
    ]);
    return { done() { return this; } };
  };

  const context = {
    $: jquery,
    _dtLang: {},
    accounting: {
      formatNumber(value, decimals) {
        const number = Number(value) || 0;
        if (decimals === 0) return String(Math.round(number));
        return number.toFixed(decimals || 0);
      },
    },
    document: {
      getElementById(id) {
        if (id === 'chart_ventas_mes' || id === 'tblVentasPorMes') return { id };
        return null;
      },
    },
    google: {
      charts: {
        load() {},
        setOnLoadCallback(callback) { callback(); },
      },
      visualization: {
        arrayToDataTable(rows) { return rows; },
        ColumnChart: function ColumnChart() {
          return { draw(rows) { state.chartRows = rows; } };
        },
      },
    },
  };

  const source = [
    extractFunction(functionsGlobal, 'hasGoogleCharts'),
    extractFunction(functionsGlobal, 'initDT'),
    extractFunction(functionsGlobal, 'renderVentasPorMesTabla'),
    extractFunction(functionsGlobal, 'drawVentasPorMesChart'),
    extractFunction(functionsGlobal, 'ventasPorMes'),
    extractFunction(functionsGlobal, 'ventasPorMesTabla'),
    '; ventasPorMes;',
  ].filter(Boolean).join('\n');

  vm.runInNewContext(source, context);
  context.ventasPorMes();
  return state;
}

const readyStart = functionsGlobal.indexOf('$(document).ready(function ()');
const dashboardBranch = readyStart >= 0 ? functionsGlobal.slice(readyStart, functionsGlobal.indexOf('if (dbProject', readyStart)) : '';
check(readyStart >= 0, 'functionsGlobal.js should keep a document ready initializer');
check(!/ventasPorMesTabla\s*\(\s*\)\s*;/.test(dashboardBranch), 'dashboard ready must not call ventasPorMesTabla separately from ventasPorMes');
check(!/onchange="ventasPorMesTabla\s*\(\s*\)\s*;?"/.test(mainPhp), 'main.php table year onchange must not trigger a second ventasPorMes AJAX path');

try {
  const dashboardState = runDashboardVentasPorMesScenario();
  check(dashboardState.posts.length === 1, `ventasPorMes should make one AJAX request for chart and table, got ${dashboardState.posts.length}`);
  check(dashboardState.posts[0] && dashboardState.posts[0].params.service === 'ventasPorMes', 'ventasPorMes should call the ventasPorMes service');
  check(dashboardState.chartRows && dashboardState.chartRows[1][0] === 'Ene' && dashboardState.chartRows[1][1] === 15.5, 'ventasPorMes should draw chart rows from the AJAX data');
  check((dashboardState.html['#tblVentasPorMesBody'] || '').indexOf('Enero') !== -1, 'ventasPorMes should populate the monthly table body from the same AJAX data');
  check(dashboardState.text['#ventasTablaTotalCantidad'] === '5', 'ventasPorMes should populate table total quantity from the same AJAX data');
  check(dashboardState.text['#ventasTablaTotalMonto'] === '20.75', 'ventasPorMes should populate table total amount from the same AJAX data');
} catch (error) {
  failures.push(`dashboard JS regression scenario threw: ${error.message}`);
}

check(/function\s+paisExiste\s*\(\s*\$idPais\s*\)/.test(adminPhp), 'Admin model must expose paisExiste($idPais) for country existence checks');
check(/FROM\s+paises/i.test(adminPhp) && /WHERE\s+id\s*=\s*"\s*\.\s*\$idPais/i.test(adminPhp), 'paisExiste must query paises by sanitized id');
check(/require_once\s*\(\s*["']\.\.\/models\/admin\.php["']\s*\)/.test(inventariosController), 'inventariosController must load the Admin model for country validation');
check(/\$admin\s*=\s*new\s+Admin\s*\(\s*\)/.test(inventariosController), 'inventariosController must instantiate Admin for country validation');
check(/empty\(\$_SESSION\['userName'\]\).*empty\(\$_SESSION\['idRoles'\]\)/s.test(inventariosController), 'inventariosController must enforce an active authenticated session before write actions');
check(/ctype_digit\(\$idPaisesNormalizado\)/.test(inventariosController), 'country guard must reject malformed values such as 1abc instead of casting them to 1');
check(/\$_POST\['data'\]\[\$indiceCampo\]\['idPaises'\]\s*=\s*\(string\)\s*\$idPaises/.test(inventariosController), 'country guard must normalize the posted idPaises value before the dynamic save path');
const malformedGuard = inventariosController.indexOf('ctype_digit($idPaisesNormalizado)');
const paisExisteCall = inventariosController.indexOf('paisExiste($idPaises)');
const savePath = inventariosController.indexOf('$tableStructure =');
check(malformedGuard !== -1 && paisExisteCall !== -1 && savePath !== -1 && malformedGuard < paisExisteCall && paisExisteCall < savePath, 'inventariosController must reject malformed, missing, or non-existent idPaises before saving empresas');
check(/pa[íi]s seleccionado no existe|pa[íi]s v[aá]lido/i.test(inventariosController), 'invalid country response must include a clear JSON error message');

const databaseGuardIndex = ensureCountrySql.indexOf('wrong_database_selected__expected_erp_elcortijo');
const transactionMatch = ensureCountrySql.match(/^START TRANSACTION;/m);
const transactionIndex = transactionMatch ? transactionMatch.index : -1;
check(/DATABASE\s*\(\s*\)/i.test(ensureCountrySql) && /erp_elcortijo/.test(ensureCountrySql), 'ensure-guatemala-country.sql must check the active database is erp_elcortijo');
check(!/CREATE\s+PROCEDURE/i.test(ensureCountrySql), 'ensure-guatemala-country.sql must not create persistent objects in the wrong database while checking context');
check(databaseGuardIndex !== -1 && transactionIndex !== -1 && databaseGuardIndex < transactionIndex, 'ensure-guatemala-country.sql must fail before transaction/mutations when run against the wrong database');

if (failures.length) {
  console.error('PR #17 regression checks failed:');
  failures.forEach((failure, index) => console.error(`${index + 1}. ${failure}`));
  process.exit(1);
}

console.log('PR #17 dashboard/country regression checks passed');
