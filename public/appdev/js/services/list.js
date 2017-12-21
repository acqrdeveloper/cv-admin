define(['app'], function(app){

	app.factory('ListSrv', service);

	function service(){
		var s = {
			getEstadosEmpresa: getEstadosEmpresa,
			getReportColums: getReportColums,
			getReportResultSetColumns: getReportResultSetColumns,
			years: getYears,
			days: getDays,
			tiposPago: getTiposPago,
			ciclos: getCiclos,
			reportes: getReportes,
			months: getMonths,
			getTimes: getTimes,
			getTimes2: getTimes2
		};

		return s;

		function getDays(){
			var days = [{id: 0, value: 'Todos'}];
			for(var i = 1; i<=31; i++){
				days.push({id:i, value:i});
			}
			return days;
		}

		function getCiclos(){
			return [
				{value:"-", name:"Ciclo"},
				{value:"QUINCENAL", name:"Quincenal"},
				{value:"MENSUAL", name:"Mensual"}
			];
		}

		function getEstadosEmpresa(){
			return [
	            {value: '-', name: 'Estado Cliente'},
	            {value: 'A', name: 'Activo'},
	            {value: 'I', name: 'Inactivo'},
	            {value: 'P', name: 'Pendiente'},
	            {value: 'E', name: 'Eliminado'},
			];
		}

		function getMonths(){
			return ['Todos','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];
		}

		function getReportes(){
			return [
				{value:"payment", name:"Pagos"},
				{value:"invoicepayed", name:"Comprobantes Pagados"},
				{value:"ownmissing", name:"Saldos y Excedentes"},
				{value:"monthpay", name:"Cuadres"},
				{value:"guarantee", name:"Garantias"},
			];
		}

		function getReportColums(type){
			switch(type){
				case 'auditorio':
					return ['Empresa', 'Sede', 'Oficina', 'Piso', 'Fecha', 'Hora Inicio', 'Hora Fin', 'Evento', /*'Sillas', 'Mesas', 'Audio', */'Comprobante', 'Monto','Estado', 'Estado Auditorio'];
				case 'coffeebreak':
					return ['Empresa', 'Sede', 'Oficina', 'Piso', 'Fecha', 'Hora Inicio', 'Hora Fin', 'Combo', 'Nro. Personas'];
				case 'guarantee':
					return ['Empresa','Número','Observacion','Fecha de Uso','Monto','Se Uso en'];
				case 'invoicepayed':
					return ['Empresa','Comprobante','Número','Creada','Pago','Tipo de pago','Monto'];
				case 'monthpay':
					return ['Empresa','Ciclo','Comprobante','Número','Creada','Monto','Estado','Pago','Efectivo','Deposito'];
				case 'payment':
					return ['Empresa','Comprobante','Número','Creada','Pago','Tipo de pago','Monto','Detraccion','Monto Visa','Interes Visa'];
				case 'paymentintime':
					return ['Año','Mes','x1','x2','x3','x4','Total','x1 Importe','x2 Importe','x3 Importe','x4 Importe','Total'];
				case 'sunatcomprobante':
					return ['Mes','Factura','Boleta','Provicional','Credito','Debito'];
				case 'localvisitantes':
					return ['Local','01','02','03','04','05','06','07','08','09','10','11','12','Total'];
				case 'empresaactual':
					return ['Nombre', 'Estado', 'Activo', 'Pendiente', 'Suspendido', 'Eliminado', 'Total', 'Activo con Convenio', 'Activo sin Convenio'];
				default:
					return [];
			}
		}

		function getReportResultSetColumns(type){
			switch(type){
				case 'auditorio':
					return [{id:'empresa_nombre'},{id:'local_nombre'},{id:'nombre'},{id:'piso_id', classes:'text-center'},{id:'fecha_reserva', classes:'text-center'},{id:'hora_inicio', classes:'text-center'},{id:'hora_fin', classes:'text-center'},{id:'evento_nombre'},/*{id:'evento_silla', classes:'text-center'},{id:'evento_mesa', classes:'text-center'},{id:'evento_audio', classes:'text-center'},*/{id:'factura_comprobante', classes:'text-center'},{id:'factura_monto', classes:'text-right'},{id:'factura_estado', classes:'text-center'},{id:'reserva_estado', classes:'text-center'}];
				case 'coffeebreak':
					return [{id:'empresa_nombre'}, {id:'local_nombre'}, {id:'nombre'}, {id:'piso_id', classes:'text-center'}, {id:'fecha_reserva', classes:'text-center'}, {id:'hora_inicio', classes:'text-center'}, {id:'hora_fin', classes:'text-center'}, {id:'concepto_nombre', classes:'text-center'}, {id:'cantidad', classes:'text-center'}];
				case 'guarantee':
					return [{id:'empresa_nombre', classes:''}, {id:'numero', classes:''}, {id:'descripcion'}, {id:'fecha_uso', classes:''}, {id:'monto_pago', classes: ''}, {id:'nfacturaUso', classes:''}];
				case 'invoicepayed':
					return [{id:'empresa_nombre', classes:''},{id:'comprobante', classes:''},{id:'numero', classes:''},{id:'fecha_emision', classes:''},{id:'fecha_pago', classes:''},{id:'tipo', classes:''},{id:'monto_pago', classes:'text-right'}];
				case 'monthpay':
					return [{id:'empresa_nombre', classes:''}, {id:'preferencia_facturacion', classes:'text-center'}, {id:'comprobante', classes:'text-center'}, {id:'numero', classes:'text-center'}, {id:'fecha_emision', classes:'text-center'}, {id:'monto', classes:'text-right'}, {id:'estado', classes:'text-center'}, {id:'fecha_pago', classes:'text-center'}, {id:'efectivo', classes:'text-right'}, {id:'deposito', classes:'text-right'}];
				case 'payment':
					return [{id:'empresa_nombre', classes:''}, {id: 'comprobante', classes: 'text-center'}, {id: 'numero', classes: 'text-center'}, {id: 'fecha_emision', classes: 'text-center'}, {id: 'fecha_pago', classes: 'text-center'}, {id: 'tipo', classes: 'text-center'}, {id: 'monto_pago', classes: 'text-center'}, {id: 'detraccionD', classes: 'text-right'}, {id: 'dif_dep_pos', classes: 'text-right'}, {id: 'des_com_pos', classes: 'text-right'}];
				case 'sunatcomprobante':
					return [{id:'mes', classes:'text-center'}, {id:'factura', classes:'text-right'}, {id:'boleta', classes:'text-right'}, {id:'provicional', classes:'text-right'}, {id:'credito', classes:'text-right'}, {id:'debito', classes:'text-right'}];
				case 'localvisitantes':
					return [{id: 'nombre'}, {id: '01', classes: 'text-right'}, {id: '02', classes: 'text-right'}, {id: '03', classes: 'text-right'}, {id: '04', classes: 'text-right'}, {id: '05', classes: 'text-right'}, {id: '06', classes: 'text-right'}, {id: '07', classes: 'text-right'}, {id: '08', classes: 'text-right'}, {id: '09', classes: 'text-right'}, {id: '10', classes: 'text-right'}, {id: '11', classes: 'text-right'}, {id: '12', classes: 'text-right'}, {id: 'total', classes: 'text-right'}];
				case 'empresaactual':
					return [{id:'nombre'}, {id:'estado', classes:'text-center'}, {id:'activo', classes:'text-right'}, {id:'pendiente', classes:'text-right'}, {id:'suspendido', classes:'text-right'}, {id:'eliminado', classes:'text-right'}, {id:'total', classes:'text-right'}, {id:'activo_conconvenio', classes:'text-right'}, {id:'activo_sinconvenio', classes:'text-right'}];
				default:
					return [];
			}
		}

		function getTiposPago(){
			return [
				{value:"-", name:"Tipo Pago"},
				{value:"EFECTIVO", name:"Efectivo"},
				{value:"DEPOSITO", name:"Deposito"},
				{value:"GARANTIA", name:"Garantia"},
				{value:"FACTURA", name:"Factura"},
			];
		}

		function getYears(){
			var years = [];
			for(var i = ((new Date()).getFullYear()+1); i>=2015; i--){
				years.push(i);
			}
			return years;
		}

		function getTimes(){
			return [{id:"08:00:00", value:"08:00 AM"}, {id:"08:30:00", value:"08:30 AM"}, {id:"09:00:00", value:"09:00 AM"}, {id:"09:30:00", value:"09:30 AM"}, {id:"10:00:00", value:"10:00 AM"}, {id:"10:30:00", value:"10:30 AM"}, {id:"11:00:00", value:"11:00 AM"}, {id:"11:30:00", value:"11:30 AM"}, {id:"12:00:00", value:"12:00 AM"}, {id:"12:30:00", value:"12:30 AM"}, {id:"13:00:00", value:"01:00 PM"}, {id:"13:30:00", value:"01:30 PM"}, {id:"14:00:00", value:"02:00 PM"}, {id:"14:30:00", value:"02:30 PM"}, {id:"15:00:00", value:"03:00 PM"}, {id:"15:30:00", value:"03:30 PM"}, {id:"16:00:00", value:"04:00 PM"}, {id:"16:30:00", value:"04:30 PM"}, {id:"17:00:00", value:"05:00 PM"}, {id:"17:30:00", value:"05:30 PM"}, {id:"18:00:00", value:"06:00 PM"}, {id:"18:30:00", value:"06:30 PM"}, {id:"19:00:00", value:"07:00 PM"}, {id:"19:30:00", value:"07:30 PM"}, {id:"20:00:00", value:"08:00 PM"}, {id:"20:30:00", value:"08:30 PM"}, {id:"21:00:00", value:"09:00 PM"}, {id:"21:30:00", value:"09:30 PM"}, {id:"22:00:00", value:"10:00 PM"}];
		}

		function getTimes2(){
			return [{id:"08:00:00", value:"08:00 AM"}, {id:"09:00:00", value:"09:00 AM"}, {id:"10:00:00", value:"10:00 AM"}, {id:"11:00:00", value:"11:00 AM"}, {id:"12:00:00", value:"12:00 AM"}, {id:"13:00:00", value:"01:00 PM"}, {id:"14:00:00", value:"02:00 PM"}, {id:"15:00:00", value:"03:00 PM"}, {id:"16:00:00", value:"04:00 PM"}, {id:"17:00:00", value:"05:00 PM"}, {id:"18:00:00", value:"06:00 PM"}, {id:"19:00:00", value:"07:00 PM"}, {id:"20:00:00", value:"08:00 PM"}, {id:"21:00:00", value:"09:00 PM"}, {id:"22:00:00", value:"10:00 PM"}];
		}
	}
});