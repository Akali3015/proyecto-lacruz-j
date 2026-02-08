//#region [ IMPORTACIONES ] COMIENZO
import {
    pedirDatosAjax
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
//#endregion [ IMPORTACIONES ] FIN

async function renderizarGraficas() {

    // Ventas por año
    const data = [
        { year: 2010, count: 10 },
        { year: 2011, count: 20 },
        { year: 2012, count: 15 },
        { year: 2013, count: 25 },
        { year: 2014, count: 22 },
        { year: 2015, count: 30 },
        { year: 2016, count: 28 },
        { year: 2017, count: 24 },
        { year: 2018, count: 21 },
        { year: 2019, count: 23 },
        { year: 2020, count: 27 },
        { year: 2021, count: 28 },
        { year: 2022, count: 33 },
        { year: 2023, count: 23 },
        { year: 2024, count: 56 },
        { year: 2025, count: 78 },
        { year: 2026, count: 99 },
        { year: 2027, count: 11 },
        { year: 2028, count: 34 },
        { year: 2029, count: 43 },
        { year: 2030, count: 45 },
        { year: 2031, count: 67 },
        { year: 2032, count: 12 },
        { year: 2033, count: 23 }
    ];
    new Chart($('#grafica1')[0], {
        type: 'bar',
        data: {
            labels: data.map(row => row.year),
            datasets: [
                {
                    label: ' Nro de ventas: ',
                    data: data.map(row => row.count),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(255, 159, 64, 0.5)',
                        'rgba(255, 205, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                        'rgba(201, 203, 207, 0.5)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    borderWidth: 2
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        },
    });

    // Indice de unidades vendidas de cada producto ventas en la semana
    let datosProductos = [
        { producto: 'Jabon', 'Unidades Vendidas': 111,unidad_medida:'Litros' },
        { producto: 'Cloro', 'Unidades Vendidas': 257, unidad_medida:'Litros'},
        { producto: 'Desinfectante', 'Unidades Vendidas': 40,unidad_medida:'Litros' },
        { producto: 'AZUFRE', 'Unidades Vendidas': 499, unidad_medida:'Kilos' },
    ];
    new Chart($('#grafica2')[0], {
        type: 'pie',
        data: {
            labels: datosProductos.map(row => row.producto),
            datasets: [
                {
                    label: datosProductos.map(producto=>producto.unidad_medida),
                    data: datosProductos.map(row => row['Unidades Vendidas']),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(255, 205, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                        'rgba(201, 203, 207, 0.5)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    borderWidth: 2
                }
            ]
        },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let value = context.parsed || 0;
                            let unidadMedida= context.dataset.label[context.dataIndex]
                            return ` ${value} ${unidadMedida} `;
                        }
                    }
                }
            }
        }
    });

    //Porcentaje de ingresos de productos vs servicios
    let promedioProductosServicios = [
        { item: 'Productos', promedio: 37 },
        { item: 'Servicios', promedio: 63 },
    ];
    new Chart($('#grafica3')[0], {
        type: 'doughnut',
        data: {
            labels: promedioProductosServicios.map(item => item.item),
            datasets: [
                {
                    label: ' Porcentaje equivalente',
                    data: promedioProductosServicios.map(row => row.promedio),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(75, 192, 192)',
                    ],
                    borderWidth: 2
                }
            ]
        },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let label = context.label || '';
                            let value = context.parsed || 0;
                            return ` ${label}: ${value}%`;
                        }
                    }
                }
            }
        }
    });

    //Top 10 mejores clientes de la empresa
    let comprasPorCliente = [
        { nombre_cliente: 'ANDERSON FREITEZ', promedioCompras: 37 },
        { nombre_cliente: 'CARLOS HURTADO', promedioCompras: 63 },
        { nombre_cliente: 'JANGELY LACRUZ', promedioCompras: 22 },
        { nombre_cliente: 'OMAR SHALOM', promedioCompras: 47 },
        { nombre_cliente: 'YEISON CARREÑO ', promedioCompras: 90 },
        { nombre_cliente: 'ANDERSON FREITEZ', promedioCompras: 37 },
        { nombre_cliente: 'CARLOS HURTADO', promedioCompras: 63 },
        { nombre_cliente: 'JANGELY LACRUZ', promedioCompras: 22 },
        { nombre_cliente: 'OMAR SHALOM', promedioCompras: 47 },
        { nombre_cliente: 'YEISON CARREÑO ', promedioCompras: 90 },
    ];
    new Chart($('#grafica4')[0], {
        type: 'bar',
        data: {
            labels: comprasPorCliente.map(cliente => cliente.nombre_cliente),
            datasets: [
                {
                    label: ' Aporte total($) ',
                    data: data.map(row => row.count),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(255, 159, 64, 0.5)',
                        'rgba(255, 205, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                        'rgba(201, 203, 207, 0.5)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    borderWidth: 2
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let value = context.formattedValue || 0;
                            return ` ${value}$`;
                        }
                    }
                }
            }
        },
    });
}

//#region [DELEGACIÓN DE EVENTOS] COMIENZO
$(document).on('DOMContentLoaded', async function (e) {
    renderizarGraficas();
})
//#endregion [DELEGACIÓN DE EVENTOS] FIN

