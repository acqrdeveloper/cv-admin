<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>| Centros Virtuales</title>
    <link rel="stylesheet" href="{{ asset('/css/app.min.css') }}"/>
</head>
<body>
    <div ui-view></div>
<script>

    window.sessionStorage.setItem("roles", "{!! DB::table('rol_opcion')->where('rol_id', Auth::user()->rol_id)->pluck('opcion_id') !!}");

    window.iVariables = {
        ciudades: [{id:'MIRAFLORES', value:'Miraflores'},{id:'SURCO', value:'Surco'}],
        coffeebreak: {!! DB::table('concepto')->where('estado','A')->where('tipo','CB')->get(['id','nombre','descripcion', 'precio']) !!},
        conceptos: [{id:'P',value:'PLAN'}, {id:'G',value:'GARANTÍA'}, {id:'HO',value:'HORAS OFICINAS'}, {id:'HR',value:'HORAS REUNION'}, {id:'C',value:'COMBO'}, {id:'D',value:'DESCUENTO'}, {id:'CB',value:'COFFEEBREAK'}, {id:'E',value:'OTROS'} ],
        cuenta_bancaria: {!! DB::table('cuenta_bancaria')->get(['num_cue_ban as id',DB::raw('CONCAT(num_cue_ban," ",tip_cue) as cuenta')]) !!},
        entidad_bancaria: {!! DB::table('entidad_bancaria')->pluck('nom_ent_ban') !!},
        estados: {},
        inbox:{!! DB::table('bandeja')->where( "leido", 0 )->where( "a_tipo", "E" )->count() !!},
        mycrm: {!! DB::table('crm')->where('usuario_id',Auth::user()->id )->where('fecha', \DB::raw("DATE(NOW())") )->where('estado','A')->whereIn('crm_tipo_id',array(1,2,6))->get(['id']) !!},
        asesores: {!! DB::table('usuario')->where('estado','A')->where('asesor','S')->get(['id','nombre']) !!},
        noasesores: {!! DB::table('usuario')->where('estado','A')->where('asesor','N')->get(['id','nombre']) !!},
        locales: {!! DB::table('clocal')->where('estado','A')->get(['id','nombre','modeloids']) !!},
        modelos: {!! DB::table('modelo')->get(['id','nombre']) !!},
        oficinas: {!! DB::table('oficina')->get(['id', 'nombre_o', 'local_id', 'modelo_id', 'piso_id', 'nombre']) !!},
        planes: {!! DB::table('plan')->where('estado','A')->get(['id','nombre','cantidad_copias','cantidad_impresiones','proyector','cochera','precio','promocion','tipo','promocion_mes']) !!},
        pos: {'1':0.0337,'2':0.042451},
        extras:{!! DB::table('extras')->where('estado','A')->get(['id','nombre']) !!},
        crm_tipo:{!! DB::table('crm_tipo')->get(['id','nombre']) !!},
        notaconcepto:{!! DB::table('factura_nota_concepto')->get(['id','nombre','tipo']) !!},
        user: {nombre: "{!! Auth::user()->nombre !!}",rold_id: "{!! Auth::user()->rol_id !!}"},
        ws: "{{ config('cv.ws_server') }}",
        roles: {!! DB::table('rol')->get(['id','nombre']) !!}
    };

    @foreach(DB::table('estados')->pluck('estados') as $key=>$value)
        Object.assign(window.iVariables.estados, {!! $value !!});
    @endforeach
</script>
@if(config('app.env')=='local')
    <script data-main="{{ asset('/main.js') }}" src="{{ asset('/node_modules/requirejs/require.js') }}"></script>
@else
    <script src="{{ asset('/js/app.min.js') }}"></script>
@endif
</body>
</html>