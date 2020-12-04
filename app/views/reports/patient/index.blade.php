@extends("layout")
@section("content")
	<div>
		<ol class="breadcrumb">
		  <li><a href="{{{URL::route('user.home')}}}">{{ trans('messages.home') }}</a></li>
		  <li class="active">{{ Lang::choice('messages.report', 2) }}</li>
		</ol>
	</div>
	
	<br>
<div class="panel panel-primary">
	<div class="panel-heading ">
		<span class="glyphicon glyphicon-user"></span>
		{{ trans('messages.patient-report') }}
	</div>
	<div class="panel-body">

	    @if(Session::has('message'))
			<div class="alert alert-info">{{ trans(Session::get('message')) }}</div>
		@endif
	    <table id="patients_table" class="row-border hover table table-bordered table-condensed table-striped">

			<thead>
				<tr>
					<th>{{ trans('messages.patient-id') }}</th>
					<th>{{trans('messages.patient-number')}}</th>
					@if(Entrust::can('view_names'))
						<th>{{trans('messages.full-name')}}</th>
					@endif
					<th>{{trans('messages.gender')}}</th>
					<th>{{trans('messages.age')}}</th>
					<th>{{trans('messages.actions')}}</th>
				</tr>
			</thead>
			<tbody>
			@forelse($patients as $key => $patient)
				<tr>
					<td>{{ $patient->ulin }}</td>
					<td>{{ empty($patient->external_patient_number)?$patient->patient_number:
						$patient->external_patient_number }}
					</td>
					@if(Entrust::can('view_names'))
						<td>{{ $patient->name }}</td>
					@endif
					<td>{{ ($patient->gender == 0)? 'Male':'Female' }}</td>
					<td>{{ $patient_helper->newAge($patient->dob) }}</td>
					<td>
					<!-- show the patient report(uses the show method found at GET /patient/{id} -->
						<a class="btn btn-sm btn-info" href="{{ URL::to('patientvisits/' . $patient->id) }}" >
							<span class="glyphicon glyphicon-eye-open"></span>
							{{trans('messages.view-visits')}}
						</a>
					</td>
				</tr>
			@endforeach 
			</tbody>
		</table>
	</div>

</div>
@stop
