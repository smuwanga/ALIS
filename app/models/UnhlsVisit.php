<?php

class UnhlsVisit extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'unhls_visits';

	public $timestamps = true;


	const APPOINTMENT_MADE = 1;
	const TEST_REQUEST_MADE = 2;
	const SPECIMEN_RECEIVED = 3;
	const TESTS_COMPLETED = 4;

	/**
	 * Test relationship
	 */
	public function tests()
	{
		return $this->hasMany('UnhlsTest', 'visit_id');
	}

	/**
	 * Ward relationship
	 */
	public function ward()
	{
		return $this->belongsTo('Ward', 'ward_id');
	}
	/**
	 * Refferral Facility relationship
	 */
	public function facility()
	{
		return $this->belongsTo('Facility', 'facility_id');
	}

	/**
	 * Patient relationship
	 */
	public function patient()
	{
		return $this->belongsTo('UnhlsPatient', 'patient_id');
	}

	/**
	 * status
	 */
	public function visitStatus()
	{
		return $this->belongsTo('VisitStatus');
	}

	public function therapy()
	{
		return $this->belongsTo('Therapy','visit_id');
	}
	/**
	 * Specimen relationship
	 */
	public function specimens()
	{
		return $this->belongsTo('UnhlsSpecimen', 'visit_id');
	}

	public function isAppointment()
	{
		if ($this->visit_status_id == UnhlsVisit::APPOINTMENT_MADE) {
			return true;
		}else{
			return false;
		}
	}

	public function isRequest()
	{
		if ($this->visit_status_id == UnhlsVisit::TEST_REQUEST_MADE) {
			return true;
		}else{
			return false;
		}
	}

	public function hasSpecimenReceived()
	{
		if ($this->visit_status_id == UnhlsVisit::SPECIMEN_RECEIVED) {
			return true;
		}else{
			return false;
		}
	}

	public function hasRequests()
	{
		if ($this->visit_status_id == UnhlsVisit::TEST_REQUEST_MADE ||
			$this->visit_status_id == UnhlsVisit::SPECIMEN_RECEIVED ||
			$this->visit_status_id == UnhlsVisit::TESTS_COMPLETED) {
			return true;
		}else{
			return false;
		}
	}


	public function hasBeenCompleted()
	{
		if ($this->visit_status_id == UnhlsVisit::TESTS_COMPLETED) {
			return true;
		}else{
			return false;
		}
	}
	public function getWard(){
		$ward_name = 'N\A';
		if($this->ward_id != NULL || $this->ward_id !=0){
			$ward=Ward::find($this->ward_id);
		    $ward_name= $ward->name;
		}

		return $ward_name;
		
	}

	public function exactAge($format = "YYMM", $at = NULL)
	{
		if(!$at)$at = new DateTime($this->created_at);

		$dateOfBirth = new DateTime($this->patient->dob);
		$interval = $dateOfBirth->diff($at);		
		$age = "";

		switch ($format) {
			case 'ref_range_Y':
				$seconds = ($interval->days * 24 * 3600) + ($interval->h * 3600) + ($interval->i * 60) + ($interval->s);
				$age = $seconds/(365*24*60*60);
				break;
			case 'Y':
			    
				$age = $interval->y;break;
			case 'YY':
			    
				$age = $interval->y ." years ";break;
			default:
				if($interval->y == 0){
					
					
					if($interval->format('%a') > 31){
						$age = $interval->format('%m months');
					}else{
						$age = $interval->format('%a days');
					 }
				}
				elseif($interval->y > 0 && $interval->y <= 2){
				
					$age = $interval->format('%m') + 12 * $interval->format('%y')." months";
				}
				else{
					
					$age=$interval->y." years ";

					$seconds = ($interval->days * 24 * 3600) + ($interval->h * 3600) + ($interval->i * 60) + ($interval->s);
				    $age = round($seconds/(365*24*60*60), 1)." years ";
				}
				
				break;
		}

		return $age;
	}
	/**
	 * Search for visits meeting the given criteria
	 *
	 * @param String $searchString
	 * @param String $visitStatusId
	 * @param String $dateFrom
	 * @param String $dateTo
	 * @return Collection
	 */
	public static function search($searchString = '', $visitStatusId = 0, $dateFrom = NULL, $dateTo = NULL)
	{
		$visits = UnhlsVisit::with('patient')->where(function($q) use ($searchString){

			$q->whereHas('patient', function($q)  use ($searchString){
				$q->where(function($q) use ($searchString){
					$q->where('external_patient_number', 'like', '%' . $searchString . '%')
					  ->orWhere('patient_number', 'like', '%' . $searchString . '%')
					  ->orWhere('name', 'like', '%' . $searchString . '%')
					  ->orWhere('ulin', 'like', '%' . $searchString . '%');
				});
			});
		});
		if ($visitStatusId > 0) {
			$visits = $visits->where(function($q) use ($visitStatusId)
			{
				$q->where('visit_status_id','=', $visitStatusId);
			});
		}
		//  put default to get content for today
		if ($dateFrom||$dateTo) {
			$visits = $visits->where(function($q) use ($dateFrom, $dateTo)
			{
				if($dateFrom)$q->where('created_at', '>=', $dateFrom);

				if($dateTo){
					$dateTo = $dateTo . ' 23:59:59';
					$q->where('created_at', '<=', $dateTo);
				}
			});
		}

		$visits = $visits->orderBy('created_at', 'ASC');

		return $visits;
	}

	/**
	 * Search for visits meeting the given criteria
	 *
	 * @param String $searchString
	 * @param String $testStatusId
	 * @param String $dateFrom
	 * @param String $dateTo
	 * @return Collection
	 */
	public static function searchWithTests1($searchString = '', $testStatusId = 0,$testCategoryId=0, $dateFrom = NULL, $dateTo = NULL)
	{
		$visits = UnhlsVisit::with('patient')->where(function($q) use ($searchString,$testStatusId){

			$q->whereHas('patient', function($q)  use ($searchString){
				$q->where(function($q) use ($searchString){
					$q->where('external_patient_number', 'like', '%' . $searchString . '%')
					  ->orWhere('patient_number', 'like', '%' . $searchString . '%')
					  ->orWhere('name', 'like', '%' . $searchString . '%')
					  ->orWhere('ulin', 'like', '%' . $searchString . '%');
				});
			});

			$q->whereHas('tests', function($q)  use ($testStatusId){
				$q->where(function($q) use ($testStatusId){
					$q->where('test_status_id', '=',  $testStatusId );
				});
			});

		});
		/**if ($visitStatusId > 0) {
			$visits = $visits->where(function($q) use ($visitStatusId)
			{
				$q->where('visit_status_id','=', $visitStatusId);
			});
		}*/
		
		//  put default to get content for today
		if ($dateFrom||$dateTo) {
			$visits = $visits->where(function($q) use ($dateFrom, $dateTo)
			{
				if($dateFrom)$q->where('created_at', '>=', $dateFrom);

				if($dateTo){
					$dateTo = $dateTo . ' 23:59:59';
					$q->where('created_at', '<=', $dateTo);
				}
			});
		}

		$visits = $visits->orderBy('created_at', 'ASC');

		return $visits;
	}

	/**
	 * Search for visits meeting the given criteria
	 *
	 * @param String $searchString
	 * @param String $testStatusId
	 * @param String $dateFrom
	 * @param String $dateTo
	 * @return Collection
	 */
	public static function searchWithTests($searchString = '', $testStatusId = 0,$testCategoryId=0, $dateFrom = NULL, $dateTo = NULL)
	{
		//$dateFrom ='2019-02-01';
		$condition="";
		if($searchString != ''){
			$condition = " AND (p.patient_number like '%".$searchString."%' 
				OR p.external_patient_number like '%".$searchString."%' 
				OR p.ulin like '%".$searchString."%' OR p.name like '%".$searchString."%')";
		}
		if($testStatusId > 0){
			$condition = $condition." AND t.test_status_id = ".$testStatusId;
		}
		if($testCategoryId > 0){
			$condition = $condition." AND tt.test_category_id = ".$testCategoryId;
		}

		$sqlStatement = "select v.created_at ,p.patient_number,p.external_patient_number,
		    p.ulin,p.nin,p.name,v.visit_lab_number,v.id,w.name ward
        from unhls_visits v 
			left join unhls_tests t on v.id = t.visit_id 
			left join test_types tt on tt.id = t.test_type_id 
			left join test_categories tc on tc.id = tt.test_category_id 
			left join unhls_patients p on p.id = v.patient_id 
			left join wards w on w.id=v.ward_id
		where v.created_at >= '".$dateFrom."' and v.created_at <='".$dateTo." 23:59:59' ".$condition.
		" group by visit_id";

		//\Log::info($sqlStatement);
		//		where v.created_at >= '".$dateFrom."' and v.created_at <='".$dateTo." 23:59:59' ".$condition.
		//$resultset = DB::select($sqlStatement);

		
		
		
		$resultset = DB::select(DB::raw($sqlStatement)); //$query is raw SQL query say SELECT * FROM a LEFT JOIN b on a.id = b.a_id GROUP BY a.id
		
		return $resultset;
	}
}
