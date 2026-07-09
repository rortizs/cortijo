#!/usr/bin/env node
const fs = require('fs');
const assert = require('assert');
const vm = require('vm');

const functionsGlobal = fs.readFileSync('erp/assets/js/functionsGlobal.js', 'utf8');
const caja = fs.readFileSync('erp/assets/js/caja.js', 'utf8');

function extractFunction(source, functionName) {
  const start = source.indexOf(`function ${functionName}(`);
  assert(start >= 0, `Missing function ${functionName}`);

  const braceStart = source.indexOf('{', start);
  let depth = 0;
  for (let i = braceStart; i < source.length; i += 1) {
    if (source[i] === '{') depth += 1;
    if (source[i] === '}') depth -= 1;
    if (depth === 0) return source.slice(start, i + 1);
  }

  throw new Error(`Could not extract function ${functionName}`);
}

function createCajaContext(mode) {
  const values = {
    '#fechaFactura': '2026-07-08',
    '#tipoDocumento option:selected': 'FACTURA',
    '#correlativo': 'F-1',
    '#idClientes': '1',
    '#nit': 'CF',
    '#nombre': 'Consumidor Final',
    '#direccion': 'Ciudad',
    '#vendedores': '1',
    '#tipoVenta': '1',
    '#subTotal': '100',
    '#descuentoM': '0',
    '#descuentoP': '0',
    '#total': '100',
    '#totalPagado': '100',
    '#cambio': '0',
    '#tasaCambio': '1',
    '#idPedido': '',
    '#anticipoP': '0',
    '#saldoP': '0',
    '#observaciones': '',
    '#boletos': '',
    '#numeroItemsFactura': '10',
    '#cuotas': '0',
    '#montoCuotas': '0',
    '#diasCredito': '0',
    '#iva': '12',
    '#costo_de_venta': '0',
    '#idFormato': '1',
    '#tipoTransaccion option:selected': '1',
    '#mail': '',
    '#telefono': '',
    '#noOrden': '',
    '#fechaOrden': '',
    '#usuarioOrden': '',
  };
  const state = {
    order: [],
    opened: [],
    alerts: [],
    attrs: {},
    hidden: [],
    reloaded: false,
    posted: false,
  };

  function jquery(selector) {
    return {
      val() { return values[selector] || ''; },
      text() { return values[selector] || ''; },
      attr(attrs) { state.attrs[selector] = Object.assign(state.attrs[selector] || {}, attrs); return this; },
      hide() { state.hidden.push(selector); return this; },
      show() { return this; },
      each(callback) { return this; },
    };
  }

  jquery.post = function post(url, params, callback) {
    state.posted = true;
    state.order.push('post');
    if (mode === 'success' || mode === 'blocked') callback([{ message: 'success', idVenta: 123 }]);
    if (mode === 'empty') callback(null);
    return {
      fail(handler) {
        if (mode === 'fail') handler();
        return this;
      }
    };
  };
  jquery.each = function each(items, callback) {
    for (let i = 0; i < items.length; i += 1) callback(i, items[i]);
  };

  const context = {
    $: jquery,
    accounting: { unformat(value) { return Number(value) || 0; } },
    dbProject: 'erp_cortijo',
    pathJasper: 'views/jasper/',
    window: {
      open(url, target) {
        state.order.push(`open:${url || ''}`);
        if (mode === 'blocked') return null;
        const popup = {
          closed: false,
          _location: '',
          close() { this.closed = true; state.order.push('close'); },
        };
        Object.defineProperty(popup, 'location', {
          set(value) { this._location = value; state.opened.push(value); state.order.push(`location:${value}`); },
          get() { return this._location; }
        });
        state.opened.push(url || '');
        return popup;
      }
    },
    location: { reload() { state.reloaded = true; state.order.push('reload'); } },
    alert(message) { state.alerts.push(message); },
    setTimeout(callback) { callback(); },
  };

  vm.runInNewContext(`${extractFunction(caja, 'cerrarVenta')}; cerrarVenta;`, context);
  return { context, state };
}

function runCajaScenario(mode) {
  const { context, state } = createCajaContext(mode);
  context.cerrarVenta();
  return state;
}

const success = runCajaScenario('success');
assert(success.posted, 'cerrarVenta must POST the sale');
assert(success.order.indexOf('open:') !== -1, 'cerrarVenta must pre-open a blank tab');
assert(success.order.indexOf('open:') < success.order.indexOf('post'), 'blank tab must be opened before async POST');
assert(success.opened.includes('views/jasper/formatoFactura.php?idVenta=123&modulo=normal'), 'success must navigate the pre-opened tab to invoice URL');
assert(success.reloaded, 'successful invoice flow should reload caja');

for (const mode of ['empty', 'fail']) {
  const state = runCajaScenario(mode);
  assert(state.hidden.includes('#loader'), `${mode} response must hide loader`);
  assert(state.attrs['#btnImprimir'] && state.attrs['#btnImprimir'].disabled === false, `${mode} response must re-enable print button`);
  assert(state.alerts.length > 0, `${mode} response must notify user`);
  assert(state.order.includes('close'), `${mode} response must close blank print window`);
}

const blocked = runCajaScenario('blocked');
assert(blocked.alerts.some((message) => message.includes('bloqueó la pestaña de impresión')), 'popup-blocked success must tell the user how to recover');

function createTotalDtesContext(hasTable, responseData) {
  const state = { posted: false, html: {}, text: {}, shown: [], hidden: [] };
  function jquery(selector) {
    return {
      length: selector === '#totalDtes' && hasTable ? 1 : 0,
      html(value) { if (value !== undefined) state.html[selector] = value; return this; },
      text(value) { if (value !== undefined) state.text[selector] = value; return this; },
      show() { state.shown.push(selector); return this; },
      hide() { state.hidden.push(selector); return this; },
      DataTable() { return this; },
    };
  }
  jquery.post = function post(url, params, callback) {
    state.posted = true;
    callback(responseData);
    return { done() { return this; } };
  };
  jquery.each = function each(items, callback) {
    for (let i = 0; i < items.length; i += 1) callback(i, items[i]);
  };

  const context = {
    $: jquery,
    params: {},
    accounting: { formatNumber(value) { return String(value); } },
    obtenerNombreMes(month) { return `Mes ${month}`; },
    initDT() {},
  };
  vm.runInNewContext(`${extractFunction(functionsGlobal, 'totalDtes')}; totalDtes;`, context);
  context.totalDtes();
  return state;
}

assert.strictEqual(createTotalDtesContext(false, null).posted, false, 'totalDtes must not call AJAX when table is absent');
assert.doesNotThrow(() => createTotalDtesContext(true, null), 'totalDtes must tolerate null responses');

const datepickerIndex = functionsGlobal.indexOf('$("#fechaInicio,#fechaFin,#fechaInicioM,#fechaFinM").datetimepicker');
const guardIndex = functionsGlobal.indexOf('if (!hasDashboardWidgets) {');
assert(datepickerIndex !== -1 && guardIndex !== -1 && datepickerIndex < guardIndex, 'Datepicker initialization must remain before dashboard early return');

console.log('caja loader/popup regression checks passed');
