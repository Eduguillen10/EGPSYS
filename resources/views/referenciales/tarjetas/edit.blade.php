@extends ('layouts.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-md-6 col-xs-12">
			<h3>Editar Tarjeta: {{ $tarjetas->descripcion}}</h3>
			@if (count($errors)>0)
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
					<li>{{$error}}</li>
					@endforeach
				</ul>
			</div>
			@endif

			<form action="{{url('/referenciales/tarjetas/'.$tarjetas->id_tarjeta)}}" method="POST" accept-charset="UTF-8">
				<input type="hidden" name="_method" value="PUT">
				{{csrf_field()}}
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<label for="descripcion">Tarjeta</label>
					<input type="text" name="descripcion" class="form-control" value="{{$tarjetas->descripcion}}" placeholder="Descripción...">
				</div>
			</div>
			<div class="col-lg-6 col-sm-6 col-xs-12">
	    		<div class="form-group">
	       			<label for="entidademisora" class="control-label">Entidad Emisora</label>
	        		<select name="identidademisora" id="identidademisora" class="form-control"> 
	                @foreach ($entidademisora as $ent)
	                    <option value="{{$ent->identidademisora}}" @if ($ent->identidademisora == $tarjetas->identidademisora) selected="selected" @endif /> {{$ent->identidademisora}} - {{$ent->descripcion}} </option>
	                @endforeach
	        		</select>
	    		</div>
			</div>

			<div class="form-group">
				<button class="btn btn-primary" type="submit">Guardar</button>
				<a href="{{ url('referenciales/tarjetas') }}" class="btn btn-danger">Cancelar</a>
			</div>

			</form>
		</div>
	</div>
@endsection