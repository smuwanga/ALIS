<div id="accession_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" style="color:red">Accession Samples</h4>
      </div>
      <?php $yearmonth = date('ym'); ?>

      {!! Form::open(array('url'=>'/sample_reception/save','id'=>'form_id','class'=>'form-horizontal')) !!}

      {!! Form::text('type', "auto_accessioned", array('class' => 'form-control col-sm-4 hidden' )) !!}
      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('barcode', 'Package ID / Barcode:', array('class' =>'col-md-4 ')) !!}
          <div class="col-md-6">
            {!! Form::text("barcode",Input::old('barcode'),['class'=>'form-control', 'id'=>'pool_type'])  !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('yearmonth', 'YearMonth:', array('class' =>'col-md-4 ')) !!}
          <div class="col-md-6">
            {!! Form::text("yearmonth",$yearmonth,['class'=>'form-control', 'id'=>'yearmonth'])  !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('box_number', 'Box Number:', array('class' =>'col-md-4 ')) !!}
          <div class="col-md-6">
            {!! Form::text("box_number",Input::old('box_number'),['class'=>'form-control', 'id'=>'pool_type'])  !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('start_position', 'Start Position:', array('class' =>'col-md-4 ')) !!}
          <div class="col-md-6">
            {!! Form::text('start_position', Input::old('start_position'), array('class' => 'form-control text-line')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('number_of_locators', 'Number of Positions:', array('class' =>'col-md-4 ')) !!}
          <div class="col-md-6">
            {!! Form::text('number_of_locators', Input::old('number_of_locators'), array('class' => 'form-control text-line')) !!}
          </div>
        </div>

        <div class="form-group">
          {!! Form::label('priority', 'High Priority:', array('class' =>'col-md-4 ')) !!}
          <div class="col-md-6">
            <input type="checkbox" name="priority" value=1 ></input>
          </div>
        </div>


      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">submit</button>
        <button type="button" class="btn btn-danger"  data-dismiss="modal">Cancel</button>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
