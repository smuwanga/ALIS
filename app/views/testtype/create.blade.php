@extends("layout")
@section("content")
<div>
	<ol class="breadcrumb">
	  <li><a href="{{{URL::route('user.home')}}}">{{trans('messages.home')}}</a></li>
	  <li><a href="{{ URL::route('testtype.index') }}">{{ Lang::choice('messages.test-type',1) }}</a></li>
	  <li class="active">{{trans('messages.create-test-type')}}</li>
	</ol>
</div>
<div class="panel panel-primary">
	<div class="panel-heading ">
		<span class="glyphicon glyphicon-cog"></span>
		{{trans('messages.create-test-type')}}
	</div>
	{{ Form::open(array('route' => array('testtype.index'), 'id' => 'form-create-testtype')) }}
	<div class="panel-body">
	<!-- if there are creation errors, they will show here -->
		
		@if($errors->all())
			<div class="alert alert-danger">
				{{ HTML::ul($errors->all()) }}
			</div>
		@endif

			<div class="form-group">
				{{ Form::label('name', Lang::choice('messages.name',1)) }}
				{{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
			</div>
			<div class="form-group">
				{{ Form::label('parentId','Standard name') }}
				{{ Form::select('parentId', array(0 => '')+$standardnames->lists('standard_name', 'id'), Input::old('parentId'), array('class' => 'form-control')) }}
			</div>
			<div class="form-group">
				{{ Form::label('description', trans('messages.description')) }}
				{{ Form::textarea('description', Input::old('description'), 
					array('class' => 'form-control', 'rows' => '2')) }}
			</div>
			<div class="form-group">
				{{ Form::label('test_category_id', Lang::choice('messages.test-category',1)) }}
				{{ Form::select('test_category_id', array(0 => '')+$testcategory->lists('name', 'id'),
					Input::old('test_category_id'),	array('class' => 'form-control')) }}
			</div>
			<div class="form-group">
				{{ Form::label('specimen_types', trans('messages.select-specimen-types')) }}
				<div class="form-pane panel panel-default">
					<div class="container-fluid">
						<?php 
							$cnt = 0;
							$zebra = "";
						?>
						@foreach($specimentypes as $key=>$value)
							{{ ($cnt%4==0)?"<div class='row $zebra'>":"" }}
							<?php
								$cnt++;
								$zebra = (((int)$cnt/4)%2==1?"row-striped":"");
							?>
							<div class="col-md-3">
								<label  class="checkbox">
									<input type="checkbox" name="specimentypes[]" value="{{ $value->id}}" />{{$value->name}}
								</label>
							</div>
							{{ ($cnt%4==0)?"</div>":"" }}
						@endforeach
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				{{ Form::label('measures', Lang::choice('messages.measure',2)) }}
				<div class="form-pane panel panel-default">
					<div class="container-fluid measure-container">
					</div>
		        	<a class="btn btn-default add-another-measure" href="javascript:void(0);" data-new-measure="1">
		         		<span class="glyphicon glyphicon-plus-sign"></span>{{trans('messages.add-new-measure')}}</a>
				</div>
			</div>
			<div class="form-group">
				{{ Form::label('targetTAT', trans('messages.target-turnaround-time'),array('class' => 'required')) }}
				{{ Form::text('targetTAT', Input::old('targetTAT'), array('class' => 'form-control','required' => 'required')) }}
				{{ Form::select('targetTAT_unit', array('','minutes' => 'Minutes', 'hours' => 'Hours','days'=>'Days'), 
						Input::old('targetTAT_unit'),array('class' => 'form-control','required' => 'required')) }}
				
			</div>
		<div class="panel-footer">
			<div class="form-group actions-row">
				{{ Form::button(
					'<span class="glyphicon glyphicon-save"></span> '.trans('messages.save'),
					['class' => 'btn btn-primary', 'onclick' => 'submit()']
				) }}
				{{ Form::button(trans('messages.cancel'), 
					['class' => 'btn btn-default', 'onclick' => 'javascript:history.go(-1)']
				) }}
			</div>
		</div>
	{{ Form::close() }}
</div>
@include("measure.measureinput")
@stop