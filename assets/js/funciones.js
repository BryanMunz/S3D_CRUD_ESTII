document.addEventListener("DOMContentLoaded", function () {
    $('#tbl').DataTable({
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json"
        },
        "order": [
            [0, "desc"]
        ]
    });
    $('#sucursal1').on('change', function() {
        // Obtener el valor seleccionado en el primer select
        var selectedValue = $(this).val();

        // Actualizar el segundo select con la misma selección
        $('#sucursal').val(selectedValue);
    });
    $(".confirmar").submit(function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Esta seguro de eliminar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'SI, Eliminar!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        })
    })
    $("#nom_cliente").autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.ajax({
                url: "ajax.php",
                dataType: "json",
                data: {
                    q: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $("#idcliente").val(ui.item.id);
            $("#nom_cliente").val(ui.item.label);
            $("#tel_cliente").val(ui.item.telefono);
            $("#dir_cliente").val(ui.item.direccion);
        }
    })
    $("#producto").autocomplete({
        minLength: 3,
        source: function (request, response) {
            const sucursal1 = $('#sucursal1').val();
            $.ajax({
                url: "ajax.php",
                dataType: "json",
                data: {
                    sucursal1: sucursal1,
                    pro: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $("#id").val(ui.item.id);
            $("#producto").val(ui.item.value);
            $("#precio").val(ui.item.p_venta);
            $("#cantidad").focus();
        }
    })

    $("#producto_especial").autocomplete({
        minLength: 3,
        source: function (request, response) {
            $.ajax({
                url: "ajax.php",
                dataType: "json",
                data: {
                    pro: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $("#id").val(ui.item.id);
            $("#producto").val(ui.item.value);
            $("#precio").val(ui.item.p_especial);
            $("#cantidad").focus();
        }
    })
    
    $('#btn_pc').click(function (e) {
        
    generarPDFPC();
                            
    })

    // Mostrar el modal cuando se hace clic en el botón "Tipos de precio"
    $('#btn_mostrar_precios').click(function (e) {
        e.preventDefault();
        $('#cambioModalPrecio').modal('show');
    });

    //Funcion para cambiar el valor del producto por de el de p_especial
    $('#btn_precio_especial').click(function (e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: { actualizar_precio_especial: true }, // Envía una señal para actualizar a precio especial
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Precio del producto actualizado',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(function() {
                            window.location.reload();
                        });
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'warning',
                        title: 'Error al actualizar el precio',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            },
            error: function() {
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: 'Error en la solicitud AJAX',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    });

    // Mostrar el modal cuando se hace clic en el botón "ganancias"
    $('#open-modal').click(function (e) {
        e.preventDefault();
        $('#myModal').modal('show');
    
        $('#date-form').submit(function (event) {
            event.preventDefault();
            var selectedDate = $('#selected-date').val();
    
            // Verificar si se ha seleccionado una fecha
            if (selectedDate === "") {
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: 'Por favor, seleccione una fecha',
                    showConfirmButton: false,
                    timer: 2000
                });
                return;
            }
    
            // Realizar la solicitud AJAX solo si hay una fecha seleccionada
            $.ajax({
                type: 'GET',
                url: 'ajax.php',
                data: { obtenerVentasDia: true, fecha: selectedDate },
                success: function (response) {
                    var data = JSON.parse(response);
                    $('#modal-date').text(selectedDate);
    
                    // Muestra ganancia
                    if (data.total_ventas === null || data.total_ventas === "" || data.total_ventas == '0') {
                        $('#ganancia').text("Sin ventas");
                    } else {
                        var formattedTotalVentas = parseFloat(data.total_ventas).toFixed(2);
                        $('#ganancia').text(formatNumber(formattedTotalVentas));
                    }

                    // Muestra ventas totales por sucursal
                    if (data.totalvendido_sucursal) {
                        $('#totalvendido-sucursal').empty();
                        $.each(data.totalvendido_sucursal, function (index, sucursal) {
                            $('#totalvendido-sucursal').append('<li>' + sucursal.sucursal + ': ' + formatNumber(sucursal.ganancias) + '</li>');
                        });
                    }
    
                    // Muestra ventas totales
                    if (data.ganancia === null || data.ganancia === "") {
                        $('#total-venta').text("Sin ventas");
                    } else {
                        var formattedGanancia = parseFloat(data.ganancia).toFixed(2);
                        $('#total-venta').text(formatNumber(formattedGanancia));
                    }
    
                    // Muestra ganancias por sucursal
                    if (data.ganancias_sucursal) {
                        $('#ganancias-sucursal').empty();
                        $.each(data.ganancias_sucursal, function (index, sucursal) {
                            $('#ganancias-sucursal').append('<li>' + sucursal.sucursal + ': ' + formatNumber(sucursal.ganancias) + '</li>');
                        });
                    }
    
                    $('#modal-results').show();
                }
            });
        });
    });

    // Función para formatear números con el signo de dólar y comas separadoras de miles
    function formatNumber(number) {
        return '$' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    
    
    //Funcion para cambiar el valor del producto por de el de p_venta
    $('#btn_precio_normal').click(function (e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax.php', 
            type: 'POST',
            data: { actualizar_precio_normal: true }, // Envía una señal para actualizar a precio venta
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Precio del producto actualizado',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(function() {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'warning',
                        title: 'Error al actualizar el precio',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            },
            error: function() {
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: 'Error en la solicitud AJAX',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    });

    
    // Mostrar el modal cuando se hace clic en el botón "Calcular cambio"
    $('#btn_mostrar_pdf').click(function (e) {
        e.preventDefault();
        $('#cambioModal').modal('show');
    });
    
    // Calcular el cambio cuando se hace clic en el botón "Calcular Cambio" o se presiona "Enter" en el campo "Monto Recibido"
    $('#calcularCambio, #montoRecibido').on('click keypress', function (e) {
        if (e.type === 'click' || (e.type === 'keypress' && e.which === 13)) {
            var totalVenta = parseFloat($('#totalVenta').val());
            var montoRecibido = parseFloat($('#montoRecibido').val());
    
            // Verificar si se presionó "Enter" en el campo "Monto Recibido" o si se hizo clic en el botón "Calcular Cambio"
            if (e.which === 13 || e.target.id === 'calcularCambio') {
                if (isNaN(totalVenta) || isNaN(montoRecibido)) {
                    Swal.fire({
                        position: 'center',
                        icon: 'warning',
                        title: 'Por favor, ingrese números válidos en los campos.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    return; // Detener la ejecución si hay un error en la entrada
                }
    
                if (montoRecibido < totalVenta) {
                    Swal.fire({
                        position: 'center',
                        icon: 'warning',
                        title: 'El monto recibido debe ser igual o mayor al total de la venta',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    return; // Detener la ejecución si el monto es menor
                }
    
                var cambio = montoRecibido - totalVenta;
                $('#cambio').val(cambio.toFixed(2));
                $('#cambioTotal').text(cambio.toFixed(2));
            }
        }
    });


    $('#btn_generar').click(function (e) {
        e.preventDefault();
        var rows = $('#tblDetalle tr').length;
        if (rows > 3) {
            var action = 'procesarVenta';
            var id = $('#idcliente').val();
            var cambio = $('#cambio').val(); // Obtener el valor de cambio
            $.ajax({
                url: 'ajax.php',
                async: true,
                data: {
                    procesarVenta: action,
                    id: id,
                    cambio: cambio // Enviar la variable cambio a la acción
                },
                success: function (response) {
                    const res = JSON.parse(response);
                    if (response != 'error') {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Venta Generada',
                            showConfirmButton: false,
                            timer: 2000
                        })
                        setTimeout(() => {
                            generarPDF2(res.id_cliente, res.id_venta);
                            location.reload();
                        }, 300);
                    } else {
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'Error al generar la venta',
                            showConfirmButton: false,
                            timer: 2000
                        })
                    }
                },
                error: function (error) {
    
                }
            });
        } else {
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'No hay producto para generar la venta',
                showConfirmButton: false,
                timer: 2000
            })
        }
    });
    
    if (document.getElementById("detalle_venta")) {
        listar();
    }
})

$('#btn_mostrar_pdf_').click(function (e) {
        e.preventDefault();
        var idCliente = $('#idcliente').val();
        var idVenta = $('#id_venta').val();
        generarPDF2(idCliente, idVenta);
    });


function calcularPrecio(e) {
    e.preventDefault();
    const cant = $("#cantidad").val();
    const precio = $('#precio').val();
    const total = cant * precio;
    $('#sub_total').val(total);
    if (e.which == 13) {
        if (cant > 0 && cant != '') {
            const id = $('#id').val();
            registrarDetalle(e, id, cant, precio);
            $('#producto').focus();
        } else {
            $('#cantidad').focus();
            return false;
        }
    }
}

function calcularPrecio2(e) {
    e.preventDefault();
    const cant = $("#cantidad_normal").val();
    const precio = $('#precio_normal').val();
    const sucursal = $('#sucursal').val();
    const total = cant * precio;
    $('#sub_total_normal').val(total);
    if (e.which == 13) {
        const productoC = $('#producto_normal').val();
        if (cant > 0 && cant != '' && productoC != '' && sucursal > 0) {
            registrarComun(e, productoC, cant, precio, sucursal);
            $('#producto_comun').focus();
        }else if(productoC == ''){
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'Ingrese el nombre del producto',
                showConfirmButton: false,
                timer: 2000
            })
        }else if(sucursal == '' || sucursal == null){
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'Ingrese la sucursal',
                showConfirmButton: false,
                timer: 2000
            })
        }
        else {
            $('#cantidad_normal').focus();
            return false;
        }
    }
}

function calcularDescuento(e, id) {
    if (e.which == 13) {
        let descuento = 'descuento';
        $.ajax({
            url: "ajax.php",
            type: 'GET',
            dataType: "json",
            data: {
                id: id,
                desc: e.target.value,
                descuento: descuento
            },
            success: function (response) {

                if (response.mensaje == 'descontado') {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Descuento Aplicado',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    listar();
                } else {}
            }
        });
    }
}

function listar() {
    let html = '';
    let detalle = 'detalle';
    $.ajax({
        url: "ajax.php",
        dataType: "json",
        data: {
            detalle: detalle
        },
        success: function (response) {
            response.forEach(row => {
                html += `<tr>
                    <td>${row['id']}</td>
                    <td>${row['descripcion']}</td>
                    <td>${row['cantidad']}</td>
                    <td>${row['precio_venta']}</td>
                    <td>${row['sub_total']}</td>
                    <td>
                        <button class="btn btn-danger" type="button" onclick="deleteDetalle(${row['id']})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>`;
            });
            document.querySelector("#detalle_venta").innerHTML = html;
            calcular();
        }
    });
}

function registrarDetalle(e, id, cant, precio) {
    if (document.getElementById('producto').value != '') {
        if (id != null) {
            let action = 'regDetalle';

            // Obtener la cantidad disponible en la base de datos
            $.ajax({
                url: "ajax.php",
                type: 'GET',
                dataType: "json",
                data: {
                    id: id,
                    obtenerCantidadDisponible: true
                },
                success: function (response) {
                    console.log('Entró en success');
                    const cantidadDisponible = parseInt(response.cantidad_disponible);

                    if (parseInt(cant) <= cantidadDisponible) {
                        // La cantidad ingresada es válida
                        // Continúa con la lógica para registrar el detalle
                        $.ajax({
                            url: "ajax.php",
                            type: 'POST',
                            dataType: "json",
                            data: {
                                id: id,
                                cant: cant,
                                regDetalle: action,
                                precio: precio
                            },
                            success: function (response) {
                                if (response == 'registrado') {
                                    $('#cantidad').val('');
                                    $('#precio').val('');
                                    $("#producto").val('');
                                    $("#sub_total").val('');
                                    $("#producto").focus();
                                    listar();
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Producto Ingresado',
                                        showConfirmButton: false,
                                        timer: 2000
                                    });
                                } else if (response == 'actualizado') {
                                    $('#cantidad').val('');
                                    $('#precio').val('');
                                    $("#producto").val('');
                                    $("#producto").focus();
                                    listar();
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'success',
                                        title: 'Producto Actualizado',
                                        showConfirmButton: false,
                                        timer: 2000
                                    });
                                } else {
                                    $('#id').val('');
                                    $('#cantidad').val('');
                                    $('#precio').val('');
                                    $("#producto").val('');
                                    $("#producto").focus();
                                    Swal.fire({
                                        position: 'center',
                                        icon: 'error',
                                        title: response,
                                        showConfirmButton: false,
                                        timer: 2000
                                    });
                                }
                            }
                        });
                    } else {
                        // La cantidad ingresada es mayor que la cantidad disponible
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'La cantidad ingresada es mayor que la cantidad disponible',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                }
            });
        }
    }
}

function registrarComun(e, productoC, cant, precio, sucursal) {
    if (document.getElementById('producto').value == '') {
    if (cant != null) {
        let action = 'regDetalleComun';
        $.ajax({
            url: "ajax.php",
            type: 'POST',
            dataType: "json",
            data: {
                productoC: productoC,
                cant: cant,
                regDetalleComun: action,
                precio: precio,
                sucursal: sucursal
            },
            success: function (response) {
                if (response == 'registrado') {
                    $('#cantidad_normal').val('');
                    $('#precio_normal').val('');
                    $("#producto_normal").val('');
                    $("#sucursal").val('');
                    $("#sub_total_normal").val('');
                    $("#producto_normal").focus();
                    listar();
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Producto Ingresado',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else if (response == 'actualizado') {
                    $('#cantidad_normal').val('');
                    $('#precio_normal').val('');
                    $("#producto_normal").val('');
                    $("#sucursal").val('');
                    $("#producto_normal").focus();
                    listar();
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Producto Actualizado',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    $('#cantidad_normal').val('');
                    $('#precio_normal').val('');
                    $("#producto_normal").val('');
                    $("#sucursal").val('');
                    $("#producto_normal").focus();
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: response,
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            }
        });
    }
    }
}

function deleteDetalle(id) {
    let detalle = 'Eliminar'
    $.ajax({
        url: "ajax.php",
        data: {
            id: id,
            delete_detalle: detalle
        },
        success: function (response) {

            if (response == 'restado') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Producto Descontado',
                    showConfirmButton: false,
                    timer: 2000
                })
                document.querySelector("#producto").value = '';
                document.querySelector("#producto").focus();
                listar();
            } else if (response == 'ok') {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Producto Eliminado',
                    showConfirmButton: false,
                    timer: 2000
                })
                document.querySelector("#producto").value = '';
                document.querySelector("#producto").focus();
                listar();
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Error al eliminar el producto',
                    showConfirmButton: false,
                    timer: 2000
                })
            }
        }
    });
}

function calcular() {
    // obtenemos todas las filas del tbody
    var filas = document.querySelectorAll("#tblDetalle tbody tr");

    var total = 0;

    // recorremos cada una de las filas
    filas.forEach(function (e) {

        // obtenemos las columnas de cada fila
        var columnas = e.querySelectorAll("td");

        // obtenemos los valores de la cantidad e importe
        var importe = parseFloat(columnas[4].textContent);

        total += importe;
    });

    // mostramos la suma total
    var filas = document.querySelectorAll("#tblDetalle tfoot tr td");
    filas[1].textContent = total.toFixed(2);

    // Actualiza el campo "Total de la Venta" en el formulario de la ventana modal
    $('#totalVenta').val(total.toFixed(2));
}

function generarPDF(cliente, id_venta) {
    url = 'pdf/generar.php?cl=' + cliente + '&v=' + id_venta;
    window.open(url, '_blank');
}

function generarPDF2() {
    url = 'pdf/generarventa.php';
    window.open(url, '_blank');
}


function generarPDFPC() {
    url = 'pdf/generarPC.php';
    window.open(url, '_blank');
}

if (document.getElementById("stockMinimo")) {
    const action = "sales";
    $.ajax({
        url: 'chart.php',
        type: 'POST',
        data: {
            action
        },
        async: true,
        success: function (response) {
            if (response != 0) {
                var data = JSON.parse(response);
                var nombre = [];
                var cantidad = [];
                for (var i = 0; i < data.length; i++) {
                    nombre.push(data[i]['descripcion']);
                    cantidad.push(data[i]['existencia']);
                }
                var ctx = document.getElementById("stockMinimo");
                var myPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: nombre,
                        datasets: [{
                            data: cantidad,
                            backgroundColor: [
                                '#024A86', '#E7D40A', '#581845', '#C82A54', '#EF280F',
                                '#8C4966', '#FF689D', '#E36B2C', '#69C36D', '#23BAC4',
                                '#0074D9', '#FF851B', '#001F3F', '#39CCCC', '#01FF70',
                                '#FFDC00', '#F012BE', '#2ECC40', '#FF4136', '#7FDBFF'
                            ],                            
                        }],
                    },
                });
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
}
if (document.getElementById("ProductosVendidos")) {
    const action = "polarChart";
    $.ajax({
        url: 'chart.php',
        type: 'POST',
        async: true,
        data: {
            action
        },
        success: function (response) {
            if (response != 0) {
                var data = JSON.parse(response);
                var nombre = [];
                var total = [];

                for (var i = 0; i < data.length; i++) {
                    nombre.push(data[i]['descripcion']);
                    total.push(data[i]['total']); 
                }

                var ctx = document.getElementById("ProductosVendidos");
                var myPieChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: nombre,
                        datasets: [{
                            data: total,
                            backgroundColor: [
                                '#024A86', '#E7D40A', '#581845', '#C82A54', '#EF280F',
                                '#8C4966', '#FF689D', '#E36B2C', '#69C36D', '#23BAC4',
                                '#0074D9', '#FF851B', '#001F3F', '#39CCCC', '#01FF70',
                                '#FFDC00', '#F012BE', '#2ECC40', '#FF4136', '#7FDBFF'
                            ],   
                        }],
                    },
                });
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
}


function btnCambiar(e) {
    e.preventDefault();
    const actual = document.getElementById('actual').value;
    const nueva = document.getElementById('nueva').value;
    if (actual == "" || nueva == "") {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Los campos estan vacios',
            showConfirmButton: false,
            timer: 2000
        })
    } else {
        const cambio = 'pass';
        $.ajax({
            url: "ajax.php",
            type: 'POST',
            data: {
                actual: actual,
                nueva: nueva,
                cambio: cambio
            },
            success: function (response) {
                if (response == 'ok') {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Contraseña modificado',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    document.querySelector('#frmPass').reset();
                    $("#nuevo_pass").modal("hide");
                } else if (response == 'dif') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'La contraseña actual incorrecta',
                        showConfirmButton: false,
                        timer: 2000
                    })
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Error al modificar la contraseña',
                        showConfirmButton: false,
                        timer: 2000
                    })
                }
            }
        });
    }
}

function editarCliente(id) {
    const action = "editarCliente";
    $.ajax({
        url: 'ajax.php',
        type: 'GET',
        async: true,
        data: {
            editarCliente: action,
            id: id
        },
        success: function (response) {
            const datos = JSON.parse(response);
            $('#nombre').val(datos.nombre);
            $('#telefono').val(datos.telefono);
            $('#direccion').val(datos.direccion);
            $('#id').val(datos.idcliente);
            $('#btnAccion').val('Modificar');
        },
        error: function (error) {
            console.log(error);

        }
    });
}

function editarUsuario(id) {
    const action = "editarUsuario";
    $.ajax({
        url: 'ajax.php',
        type: 'GET',
        async: true,
        data: {
            editarUsuario: action,
            id: id
        },
        success: function (response) {
            const datos = JSON.parse(response);
            $('#nombre').val(datos.nombre);
            $('#usuario').val(datos.usuario);
            $('#correo').val(datos.correo);
            $('#id').val(datos.idusuario);
            $('#clave').val(datos.clave);
            $('#btnAccion').val('Modificar');
        },
        error: function (error) {
            console.log(error);

        }
    });
}

function editarProducto(id) {
    const action = "editarProducto";
    $.ajax({
        url: 'ajax.php',
        type: 'GET',
        async: true,
        data: {
            editarProducto: action,
            id: id
        },
        success: function (response) {
            const datos = JSON.parse(response);
            $('#codigo').val(datos.codigo);
            $('#producto').val(datos.descripcion);
            $('#precio').val(datos.precio);
            $('#id').val(datos.codproducto);
            $('#tipo').val(datos.id_tipo);
            $('#presentacion').val(datos.id_presentacion);
            $('#laboratorio').val(datos.id_lab);
            $('#vencimiento').val(datos.vencimiento);
            $('#cantidad').val(datos.existencia);
            $('#p_venta').val(datos.p_venta);
            $('#p_especial').val(datos.p_especial);
            if (datos.vencimiento != '0000-00-00') {
                $("#accion").prop("checked", true);
            }else{
                $("#accion").prop("checked", false);
            }
            $('#btnAccion').val('Modificar');
        },
        error: function (error) {
            console.log(error);

        }
    });
}

function limpiar() {
    $('#formulario')[0].reset();
    $('#id').val('');
    $('#btnAccion').val('Registrar');
}
function editarTipo(id) {
    const action = "editarTipo";
    $.ajax({
        url: 'ajax.php',
        type: 'GET',
        async: true,
        data: {
            editarTipo: action,
            id: id
        },
        success: function (response) {
            const datos = JSON.parse(response);
            $('#nombre').val(datos.tipo);
            $('#id').val(datos.id);
            $('#btnAccion').val('Modificar');
        },
        error: function (error) {
            console.log(error);

        }
    });
}
function editarPresent(id) {
    const action = "editarPresent";
    $.ajax({
        url: 'ajax.php',
        type: 'GET',
        async: true,
        data: {
            editarPresent: action,
            id: id
        },
        success: function (response) {
            const datos = JSON.parse(response);
            $('#nombre').val(datos.nombre);
            $('#nombre_corto').val(datos.nombre_corto);
            $('#id').val(datos.id);
            $('#btnAccion').val('Modificar');
        },
        error: function (error) {
            console.log(error);

        }
    });
}
function editarLab(id) {
    const action = "editarLab";
    $.ajax({
        url: 'ajax.php',
        type: 'GET',
        async: true,
        data: {
            editarLab: action,
            id: id
        },
        success: function (response) {
            const datos = JSON.parse(response);
            $('#laboratorio').val(datos.laboratorio);
            $('#direccion').val(datos.direccion);
            $('#id').val(datos.id);
            $('#btnAccion').val('Modificar');
        },
        error: function (error) {
            console.log(error);

        }
    });
}

function enforceNumberValidation(ele) {
    if ($(ele).data('decimal') != null) {
        // found valid rule for decimal
        var decimal = parseInt($(ele).data('decimal')) || 0;
        var val = $(ele).val();
        if (decimal > 0) {
            var splitVal = val.split('.');
            if (splitVal.length == 2 && splitVal[1].length > decimal) {
                // user entered invalid input
                $(ele).val(splitVal[0] + '.' + splitVal[1].substr(0, decimal));
            }
        } else if (decimal == 0) {
            // do not allow decimal place
            var splitVal = val.split('.');
            if (splitVal.length > 1) {
                // user entered invalid input
                $(ele).val(splitVal[0]); // always trim everything after '.'
            }
        }
    }
}

function priceCheck(element, event) {
    result = (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 46;
    if (result) {
        let t = element.value;
        if (t === '' && event.charCode === 46) {
            return false;
        }
        let dotIndex = t.indexOf(".");
        let valueLength = t.length;
        if (dotIndex > 0) {
            if (dotIndex + 2 < valueLength) {
                return false;
            } else {
                return true;
            }
        } else if (dotIndex === 0) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}

//const main = document.querySelector('.select-container');
//const select = document.querySelector('select');
//
//main.addEventListener('mousedown', (e) => {
//  e.preventDefault();
//  const select2 = main.children[0];
//  const ul = document.createElement('ul');
//
//  select2.childNodes.forEach((option) => {
//    if (option.value) {
//      const li = document.createElement('li');
//      li.textContent = option.textContent;
//
//      li.addEventListener('mousedown', (event) => {
//        event.stopPropagation();
//        select.value = option.value;
//        main.value = option.value;
//        select.style.border = '2px solid #1E49A8';
//        ul.remove();
//      });
//
//      ul.appendChild(li);
//    }
//  });
//
//  ul.style.position = 'absolute';
//  ul.style.top = '38px'; /* Ajusta la posición en relación con el contenedor */
//  ul.style.left = '0'; /* Ajusta la posición en relación con el contenedor */
//
//  main.appendChild(ul);
//
//  document.addEventListener('click', (event) => {
//    if (!main.contains(event.target)) {
//      select.style.border = '1.9px solid lightgrey';
//      ul.remove();
//    }
//  });
//});

//pancojamo, tengo hambre :c