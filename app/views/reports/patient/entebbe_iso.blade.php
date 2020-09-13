<style type="text/css">
	 table {
	 	padding: 2px;
	 }
	 img{
		padding-left: 20px;
	}
</style>
<table style="border-bottom: 1px solid #cecfd5; font-size:9px; font-family: Bookman Old Style;">
	<thead>
	    <tr>
	            <th width="20%"><strong>Sample Type</strong></th>
	            <th width="20%"><strong>Date Collected</strong></th>
	            
	            <th width="20%"><strong>Date Received</strong></th>
	            
	            <th width="20%"><strong>{{ Lang::choice('messages.test-category', 1)}}</strong></th>
	            <th width="20%"><strong>Tests Requested</strong></th>
	        </tr>
	</thead>
	<tbody>
	    @if(isset($tests))
	        @forelse($tests as $test)
	                <tr>    
	                    <td>{{$test->specimen->specimen_type_id}}</td>

	                    @if($test->specimen->specimen_status_id == UnhlsSpecimen::NOT_COLLECTED)
	                        
	                        <td>{{trans('messages.specimen-not-collected')}}</td>
	                        <td>not received</td>
	                    @elseif($test->specimen->specimen_status_id == UnhlsSpecimen::ACCEPTED)
	                        <td >{{ ($test->specimen->time_collected)?$test->specimen->time_collected:'' }}</td>
	                        <td >{{isset($test->specimen->time_accepted)?$test->specimen->time_accepted : ''}}</td>
	                        
	                    @elseif($test->test_status_id == UnhlsTest::REJECTED)
	                        <td >{{trans('messages.specimen-not-collected')}}</td>
	                        <td >{{isset($test->specimen->time_rejected)?$test->specimen->time_rejected:''}}</td>
	                        
	                    @endif

	                    <td >{{ isset($test->testType->testCategory->name)?$test->testType->testCategory->name:'' }}</td>
	                    <td >{{ isset($test->testType->name)?$test->testType->name:'' }}</td>
	                </tr>
	        @empty
	            <tr>
	                <td colspan="5">{{trans("messages.no-records-found")}}</td>
	            </tr>
	        @endforelse
	    @endif
	</tbody>

</table>

<br>
<br>
<table style="border-bottom: 1px solid #cecfd5; font-size:9px;font-family: Bookman Old Style;">
	<tr>
		<td colspan="2"><b>{{Lang::choice('messages.test-type', 1)}}</b></td>
		
		<td colspan="3"><b>TEST RESULTS</b></td>
		
	</tr>
</table>
@forelse($tests as $test)
	@if(!$test->testType->isCulture() && ( $test->testStatus->name == 'approved' || $test->testStatus->name == 'verified'))
	<table  id="results_content_id" style="border-bottom: 1px solid #cecfd5; font-size:9px;font-family: Bookman Old Style;">
		<tr>
			<td width="20%">{{ $test->testType->name }}</td>
			<td width="80%">
				<table style="padding: 1px;">
					<thead>

						<tr>
								<th><b>Parameter</b></th>
								<th><b>Result</b></th>
								<th><b>Reference Interval</b></th>
								<th><b>SI units</b></th>
								<th><b>Diagnostic Flag</b></th><!-- Diagnostic Flag column for results-->
							</tr>
					</thead>
					<tbody>
						@foreach($test->testResults as $result)
							<!-- show only parameters with values -->
							@if($result->result != '')
							<tr>
								@if($test->testType->measures->count() > 1)
								<td>
									{{ Measure::find($result->measure_id)->name }}:
								</td>
								@endif
								<td>
								@if($result->revised_result!=null)
											{{$result->revised_result}} (Revised result)
											@else
											{{$result->result}}
											@endif
								</td>
								<td>
									{{ Measure::getRange($test->visit->patient, $result->measure_id) }}
								</td>
								<td>
									{{ Measure::find($result->measure_id)->unit }}
								</td>
								<td>@if(!is_null(Measure::getRange($test->visit->patient, $result->measure_id)))
										{{Measure::measureFlag($test->visit->patient, $result->measure_id, $result->result) }}
									@endif
								</td><!-- Diagnostic Flag column for results-->
							</tr>
							@endif
						@endforeach
						@if($test->testType->name == 'HIV')
							<tr>
								<td><b>Interpretation:</b></td> <td>{{$test->interpreteHIVResults()}}</td>
							</tr>
						@else
							<tr>
								<td width="100%"><br><br>
									<b>{{trans('messages.comments')}}:</b> {{ $test->interpretation == '' ? 'Suitable for the test' : $test->interpretation }}
								</td>								
							</tr>	       
							<tr>
								<td width="50%" style="font-size:8px">
									@if($test->time_revised!=null)
									<b>Results Revision Date</b>:{{$test->time_revised}}
									@else
									<b>Results Entry Date</b>:{{ $test->time_completed }}
									@endif
								</td>
								<td width="50%">
									<b>{{trans('messages.tested-by')}}</b>:
									{{ $test->testedBy->name}}
								</td>								
							</tr>							
							<tr>
								@if($test->time_revised!=null)
								<td></td>
								<td></td>
								@else
								<td width="50%"><b>Reviewed by</b>:{{$test->verifiedBy->name}}</td>
								<td width="50%"><b>Date Reviewed</b>:{{$test->time_verified}}</td>	
								@endif							
							</tr>						
						@endif
						</tbody>
				</table>
			</td>		
		</tr>
	</table>
	@elseif($test->testType->isCulture())
        <!-- Culture and Sensitivity analysis -->
        @if(count($test->isolated_organisms)>0)<!-- if there are any isolated organisms -->
        <table style="border-bottom: 1px solid #cecfd5; font-size:9px;font-family: 'Courier New',Courier;">
            <tr>
              <td colspan="3"></td>
            </tr>
            <tr>
              <td colspan="6">Antimicrobial Susceptibility Testing(AST)</td>
            </tr>
            <tr>
                <th><b>Organism(s)</b></th>
                <th><b>Antibiotic(s)</b></th>
                <th><b>Result(s)</b></th>
            </tr>
        </table>
        @foreach($test->isolated_organisms as $isolated_organism)
        <table style="border-bottom: 1px solid #cecfd5;">
          <tr>
            <td rowspan="{{$isolated_organism->drug_susceptibilities->count()}}" class="organism"><i><b>{{$isolated_organism->organism->name}}</i></b></td>
              <?php $i = 1; ?>
            @if($isolated_organism->drug_susceptibilities->count() == 0)
              </tr>
            @else
              @foreach($isolated_organism->drug_susceptibilities as $drug_susceptibility)
                @if ($i > 1)
                <tr>
	                @endif
	                <?php $i++; ?>
	                <td style="font-size:10px;" class="antibiotic">{{$drug_susceptibility->drug->name}}</td>
	                <td style="font-size:10px;" class="result">{{$drug_susceptibility->drug_susceptibility_measure->symbol}}</td>
              	</tr>
              @endforeach       
            @endif
           
        </table>
        @endforeach

        <table style="border-bottom: 1px solid #cecfd5; font-size:10px;font-family: 'Courier New',Courier;">
            <tr>
              <td><b>Comment(s)</b></td>
              <td colspan="2">
              {{$test->interpretation}}
              </td>
            </tr>
             <tr>
				<td width="50%" style="font-size:9px">
					<b>Results Entry Date</b>:{{ $test->time_completed }}
				</td>
			</tr>
        </table>

        </hr>
        <table style="border-bottom: 1px solid #cecfd5; font-size:10px;font-family: 'Courier New',Courier;">
            <tr>
              <td colspan="2"><b>Analysis Performed by:</b></td>
              <td colspan="4">{{ $test->isCompleted()?$test->testedBy->name:'Pending' }}</td>
              <!-- <td><b>Verified by:</b></td>
              <td>{{ $test->isVerified()?$test->verifiedBy->name:'Pending' }}</td> -->
            </tr>
        </table>

        <table style="border-bottom: 1px solid #cecfd5; font-size:10px;font-family: Bookman Old Style;">
            <tr>
               <td colspan="2"><b>Results Guide</b></td>
               <td colspan="4"><b>S-Sensitive | R-Resistant | I-Intermediate</b></td>
            </tr>
        </table>
        @else<!-- if there are no isolated organisms -->
            @if($test->culture_observation)<!-- if there are comments -->
            <table>
                  <tr>
                    <td>{{ $test->culture_observation->observation }}</td>
                  </tr>
            </table>
            @endif<!--./ if there are comments -->
        @endif<!--./ if there are no isolated organisms -->
	@endif
@empty
<table  style="border-bottom: 1px solid #cecfd5;">
	<tr>
		<td colspan="6">{{trans("messages.no-records-found")}}</td>
	</tr>
</table>
@endforelse

<hr>

<table>
	<tr><td></td></tr>
	<tr>
		<td>
			<strong>Approved By : 
				
			  @if(isset($tests))
                @if(!empty($tests->first()))
                    @if(!empty($tests->first()->isApproved()))
                        
                        {{$tests->first()->approvedBy->name}}
                    @else

                    @endif

                @endif
            @endif
            </strong>
		</td>
		 <!--  -->
	</tr>
	<!-- <tr><td><u><strong></strong></u></td></tr> -->
</table>