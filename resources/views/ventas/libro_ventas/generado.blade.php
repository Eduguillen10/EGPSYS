@extends ('layouts.listar')
@section ('contenido')

<div class="row">
	<div class="text-center col-log-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Libro de Ventas</h3>
	</div>
</div>
<div class="row">
	<div class="col-log-5 col-md-5 col-sm-5 col-xs-12">
		<p><b>Fecha Desde:</b> {{date("d/m/Y", strtotime($fecha_desde))}} <b>Fecha Hasta:</b> {{date("d/m/Y", strtotime($fecha_hasta))}}</p>
		
	</div>
	
	<div class="col-sm-12">
		@if (count($libroventas) <= 0 && count($nota_creditov) <= 0 && count($nota_debitov) <= 0)
		
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			No se ha envontrado ningun registro con esas caracteristicas de busqueda
		</div>
		
		@endif
	</div>
</div>
@if (count($libroventas) > 0 || count($nota_creditov) > 0 || count($nota_debitov) > 0)
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<thead style="background-color:#A9D0F5">
					<th>Fecha</th>
					<th>Nro. Documento</th>
					<th>Razón Social</th>
					<th>Doc.</th>
					<th>Gravada 10%</th>
					<th>Gravada 5%</th>
					<th>IVA 10%</th>
					<th>IVA 5%</th>
					<th>Exenta</th>
					<th>Total</th>
				</thead>   
                @php
                    $totalGravada10 = 0;
                    $totalGravada5 = 0;
                    $totalIVA10 = 0;
                    $totalIVA5 = 0;
                    $totalExenta = 0;
                    $totalventa = 0;
                    $totalventa_lv = 0;
                @endphp            
                @foreach ($libroventas as $lv)
                    <tr>					
                        <td>{{date('d/m/Y', strtotime($lv->fecha))}}</td>
                        <td>{{ $lv->num_documento}}</td>
                        <td>{{ $lv->cliente}}</td>
                        <td>{{ $lv->num_documento}}</td>
                        <td align="right">{{ number_format($lv->totalgravada10, 0, ',', '.')}}</td>					
                        <td align="right">{{ number_format($lv->totalgravada5, 0, ',', '.')}}</td>
                        <td align="right">{{ number_format($lv->totaliva10, 0, ',', '.')}}</td>
                        <td align="right">{{ number_format($lv->totaliva5, 0, ',', '.')}}</td>
                        <td align="right">{{ number_format($lv->totalexenta, 0, ',', '.')}}</td>					
                        <td align="right">{{ number_format($lv->totalventa, 0, ',', '.')}}</td>
                    </tr>
                    <!-- Aca hacer el sumador  -->
                    @php
                        $totalGravada10 += $lv->totalgravada10;
                        $totalGravada5 += $lv->totalgravada5;
                        $totalIVA10 += $lv->totaliva10;
                        $totalIVA5 += $lv->totaliva5;
                        $totalExenta += $lv->totalexenta;
                        $totalventa += $lv->totalventa;
                        $totalventa_lv = $totalventa;
                    @endphp
				@endforeach
               <!-- Aca mostrar total  -->
                <tr>
                    <td colspan="4" align="right"><strong>Total:</strong></td>
                    <td align="right">{{ number_format($totalGravada10, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalGravada5, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalIVA10, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalIVA5, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalExenta, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalventa_lv, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="13" style="padding-bottom: 1em !important; padding-top: 1em !important;"><b>Notas de debito</b></td>
                </tr>
                @php
                    $totalGravada10 = 0;
                    $totalGravada5 = 0;
                    $totalIVA10 = 0;
                    $totalIVA5 = 0;
                    $totalExenta = 0;
                    $totalventa_ndv = 0;
                @endphp
                @foreach ($nota_debitov as $ndv)
                    <tr>
                        <td>{{date('d/m/Y', strtotime($ndv->fecha_factura))}}</td>
                        <td>{{ $ndv->num_documento}}</td>
                        <td>{{ $ndv->cliente}}</td>
                        <td>{{ $ndv->num_documento}}</td>
                        <td align="right">{{ number_format($ndv->totalgravada10, 0, ',', '.')}}</td>
                        <td align="right">{{ number_format($ndv->totalgravada5, 0, ',', '.')}}</td>
                        <td align="right">{{ number_format($ndv->totaliva10, 0, ',', '.')}}</td>
                        <td align="right">{{ number_format($ndv->totaliva5, 0, ',', '.')}}</td>
                        <td align="right">{{ number_format($ndv->totalexenta, 0, ',', '.')}}</td>
                        <td align="right">{{ number_format($ndv->totalventa, 0, ',', '.')}}</td>
                    </tr>
                    @php
                        $totalGravada10 += $ndv->totalgravada10;
                        $totalGravada5 += $ndv->totalgravada5;
                        $totalIVA10 += $ndv->totaliva10;
                        $totalIVA5 += $ndv->totaliva5;
                        $totalExenta += $ndv->totalexenta;
                        $totalventa_ndv += $ndv->totalventa;
                    @endphp
				@endforeach
                <tr>
                    <td colspan="4" align="right"><strong>Total:</strong></td>
                    <td align="right">{{ number_format($totalGravada10, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalGravada5, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalIVA10, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalIVA5, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalExenta, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalventa_ndv, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="13" style="padding-bottom: 1em !important; padding-top: 1em !important;"><b>Notas de credito</b></td>
                </tr>
                 <!-- Aca es Notas de credito  -->
                 @php
                    $totalGravada10 = 0;
                    $totalGravada5 = 0;
                    $totalIVA10 = 0;
                    $totalIVA5 = 0;
                    $totalExenta = 0;
                    $totalventa_nv = 0;
                @endphp            
                @foreach ($nota_creditov as $nv)
                    <tr>					
                        <td>{{date('d/m/Y', strtotime($nv->fecha_factura))}}</td>
                        <td>{{ $nv->num_documento}}</td>
                        <td>{{ $nv->cliente}}</td>
                        <td>{{ $nv->num_documento}}</td>
                        <td align="right">{{ number_format($nv->totalgravada10, 0, ',', '.')}}</td>					
                        <td align="right">{{ number_format($nv->totalgravada5, 0, ',', '.')}}</td>
                        <td align="right">{{ number_format($nv->totaliva10, 0, ',', '.')}}</td>
                        <td align="right">{{ number_format($nv->totaliva5, 0, ',', '.')}}</td>
                        <td align="right">{{ number_format($nv->totalexenta, 0, ',', '.')}}</td>					
                        <td align="right">{{ number_format($nv->totalventa, 0, ',', '.')}}</td>
                    </tr>
                    <!-- Aca hacer el sumador  -->
                    @php
                        $totalGravada10 += $nv->totalgravada10;
                        $totalGravada5 += $nv->totalgravada5;
                        $totalIVA10 += $nv->totaliva10;
                        $totalIVA5 += $nv->totaliva5;
                        $totalExenta += $nv->totalexenta;
                        $totalventa_nv += $nv->totalventa;                        
                    @endphp
				@endforeach
               <!-- Aca mostrar total  -->
                <tr>
                    <td colspan="4" align="right"><strong>Total:</strong></td>
                    <td align="right">{{ number_format($totalGravada10, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalGravada5, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalIVA10, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalIVA5, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalExenta, 0, ',', '.') }}</td>
                    <td align="right">{{ number_format($totalventa_nv, 0, ',', '.') }}</td>
                </tr>                
                <!-- Aca crear otra variable y hacer la resta de ambos  -->               
                <tr>
                    <td colspan="9" align="right"><strong>Total Libro de Ventas:</strong></td>
                    <td align="right">{{ number_format($totalventa_lv, 0, ',', '.') }}</td>
                </tr>

                <tr>
                    <td colspan="9" align="right"><strong>Total Notas de Debito:</strong></td>
                    <td align="right">{{ number_format($totalventa_ndv, 0, ',', '.') }}</td>
                </tr>

                <!-- Aca mostrar total nota_creditov -->
                <tr>
                    <td colspan="9" align="right"><strong>Total Notas de Crédito:</strong></td>
                    <td align="right">{{ number_format($totalventa_nv, 0, ',', '.') }}</td>
                </tr>

                <!-- Aca realizar la resta -->
                @php
                    $restaVenta = $totalventa_lv + $totalventa_ndv - $totalventa_nv;
                @endphp

                <!-- Aca mostrar la resta -->
                <tr>
                    <td colspan="9" align="right"><strong>Total Neto (Ventas + Notas de Debito - Notas de Crédito):</strong></td>
                    <td align="right">{{ number_format($restaVenta, 0, ',', '.') }}</td>
                </tr>

				</table>
		</div>
	</div>
</div>
@endif
@endsection
