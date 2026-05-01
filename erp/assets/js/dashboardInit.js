// Load the Visualization API and the corechart package.
google.charts.load('current', {'packages': ['corechart']});
// Set a callback to run when the Google Visualization API is loaded.
google.charts.setOnLoadCallback(drawChart);
google.charts.setOnLoadCallback(drawChart2);
google.charts.setOnLoadCallback(drawAxisTickColors);
google.charts.setOnLoadCallback(drawMultSeries);
//
//GRAFICA 1
function drawChart() {
    // Create the data table.
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Topping');
    data.addColumn('number', 'Slices');
    data.addRows([
        ['Producto 1', 1],
        ['Producto 2', 2],
        ['Producto 3', 3],
        ['Producto 4', 4],
        ['Producto 5', 5],
        ['Producto 6', 6],
        ['Producto 7', 7],
        ['Producto 8', 8],
        ['Producto 9', 9],
        ['Producto 10', 10]
    ]);
    // Set chart options
    var options = {'title': '',
        'width': '100%',
        'height': 300};
    // Instantiate and draw our chart, passing in some options.
    var chart1 = new google.visualization.PieChart(document.getElementById('chart_div1'));
    chart1.draw(data, options);
}
//GRAFICA 2
function drawAxisTickColors() {
    var data = new google.visualization.DataTable();
    data.addColumn('number', 'X');
    data.addColumn('number', 'Sucursal 1');
    data.addColumn('number', 'Sucursal 2');

    data.addRows([
        [0, 0, 0], [1, 10, 5], [2, 23.5, 15], [3, 17, 9], [4, 18, 10], [5, 9, 5],
        [6, 11, 3], [7, 27, 19], [8, 33, 25], [9, 40, 32], [10, 32, 24], [11, 35, 27],
        [12, 30, 22], [13, 40, 32], [14, 42, 34], [15, 47, 39], [16, 44, 36], [17, 48, 40],
        [18, 52, 44], [19, 54, 46], [20, 42, 34], [21, 55, 47], [22, 56, 48], [23, 57, 49],
        [24, 60, 52], [25, 50, 42], [26, 52, 44], [27, 51, 43], [28, 49, 41], [29, 53, 45],
        [30, 55, 47], [31, 60, 52], [32, 61, 53], [33, 59, 51], [34, 62, 54], [35, 65, 57],
        [36, 62, 54], [37, 58, 50], [38, 55, 47], [39, 61, 53], [40, 64, 56], [41, 65, 57],
        [42, 63, 55], [43, 66, 58], [44, 67, 59], [45, 69, 61], [46, 69, 61], [47, 70, 62],
        [48, 72, 64], [49, 68, 60], [50, 66, 58], [51, 65, 57], [52, 67, 59], [53, 70, 62],
        [54, 71, 63], [55, 72, 64], [56, 73, 65], [57, 75, 67], [58, 70, 62], [59, 68, 60],
        [60, 64, 56], [61, 60, 52], [62, 65, 57], [63, 67, 59], [64, 68, 60], [65, 69, 61],
        [66, 70, 62], [67, 72, 64], [68, 75, 67], [69, 80, 72]
    ]);

    var options = {
        'width': '100%',
        'height': 300,
        hAxis: {
            title: 'Hora',
            textStyle: {
                color: '#01579b',
                fontSize: 20,
                fontName: 'Arial',
                bold: true,
                italic: true
            },
            titleTextStyle: {
                color: '#01579b',
                fontSize: 16,
                fontName: 'Arial',
                bold: false,
                italic: true
            }
        },
        vAxis: {
            title: 'Monto Venta',
            textStyle: {
                color: '#1a237e',
                fontSize: 24,
                bold: true
            },
            titleTextStyle: {
                color: '#1a237e',
                fontSize: 24,
                bold: true
            }
        },
        colors: ['#a52714', '#097138']
    };
    var chart = new google.visualization.LineChart(document.getElementById('chart_div3'));
    chart.draw(data, options);
}
//GRAFICA 3
function drawMultSeries() {
    var data = new google.visualization.DataTable();
    data.addColumn('timeofday', 'Dias del Mes');
    data.addColumn('number', 'Sucursal 1');
    data.addColumn('number', 'Sucursal 2');

    data.addRows([
        [{v: [8, 0, 0], f: '1'}, 1, .25],
        [{v: [9, 0, 0], f: '2'}, 2, .5],
        [{v: [10, 0, 0], f: '3'}, 3, 1],
        [{v: [11, 0, 0], f: '4'}, 4, 2.25],
        [{v: [12, 0, 0], f: '5'}, 5, 2.25],
        [{v: [13, 0, 0], f: '6'}, 6, 3],
        [{v: [14, 0, 0], f: '7'}, 7, 4],
        [{v: [15, 0, 0], f: '8'}, 8, 5.25],
        [{v: [16, 0, 0], f: '9'}, 9, 7.5],
        [{v: [17, 0, 0], f: '10'}, 10, 10]
    ]);

    var options = {
        title: '',
        'width': '100%',
        'height': 300,
        hAxis: {
            title: 'Dias del Mes',
            format: 'd-m-Y'
        },
        vAxis: {
            title: 'Rating (scale of 1-10)'
        }
    };

    var chart = new google.visualization.ColumnChart(
            document.getElementById('chart_div4'));

    chart.draw(data, options);
}

//GRAFICA 4
function drawChart2() {
    var data = google.visualization.arrayToDataTable([
        ['Year', 'Sales', 'Expenses'],
        ['2004', 1000, 400],
        ['2005', 1170, 460],
        ['2006', 660, 1120],
        ['2007', 1030, 540]
    ]);

    var options = {
        title: 'Company Performance',
        'width': '100%',
        'height': 300,
        curveType: 'function',
        legend: {position: 'bottom'}
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div2'));

    chart.draw(data, options);
}