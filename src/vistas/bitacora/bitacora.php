<?php 
    use src\config\inc\componentesModelo;
    $componente = new componentesModelo();
?>
<input type="hidden" class="nombreVista" value="bitacora">
<?php 
    $instruccionesLista=[
        'encabezado'=>'Bitacora',
    ];
    echo $componente->listaDataTable($instruccionesLista);
?>