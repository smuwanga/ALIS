<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TestType extends Eloquent
{

	/**
	 * Enabling soft deletes for specimen type details.
	 *
	 */
	use SoftDeletingTrait;
	protected $dates = ['deleted_at'];
    	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'test_types';

	/**
	 * TestCategory relationship
	 */
	public function testCategory()
	{
	  return $this->belongsTo('TestCategory', 'test_category_id');
	}

	/**
	 * SpecimenType relationship
	 */
	public function specimenTypes()
	{
	  return $this->belongsToMany('SpecimenType', 'testtype_specimentypes');
	}

	/**
	 * Measures relationship
	 */
	public function measures()
	{
	  return $this->belongsToMany('Measure', 'testtype_measures');
	}

	/**
	 * Test relationship
	 */
    public function tests()
    {
        return $this->hasMany('UnhlsTest', 'test_type_id');
    }

	/**
	 * Instrument relationship
	 */
	public function instruments()
	{
	  return $this->belongsToMany('Instrument', 'instrument_testtypes');
	}

	/**
	 * Set compatible specimen types
	 *
	 * @return void
	 */
	public function setSpecimenTypes($specimenTypes){

		$specimenTypesAdded = array();
		$testTypeID = 0;	

		if(is_array($specimenTypes)){
			foreach ($specimenTypes as $key => $value) {
				$specimenTypesAdded[] = array(
					'test_type_id' => (int)$this->id,
					'specimen_type_id' => (int)$value
					);
				$testTypeID = (int)$this->id;
			}

		}
		// Delete existing test_type measure mappings
		DB::table('testtype_specimentypes')->where('test_type_id', '=', $testTypeID)->delete();

		// Add the new mapping
		DB::table('testtype_specimentypes')->insert($specimenTypesAdded);
	}

	/**
	 * Set test type measures
	 *
	 * @return void
	 */
	public function setMeasures($measures){

		$measuresAdded = array();
		$testTypeID = 0;	

		if(is_array($measures)){
			foreach ($measures as $key => $value) {
				$measuresAdded[] = array(
					'test_type_id' => (int)$this->id,
					'measure_id' => (int)$value
					);
			}
			$testTypeID = (int)$this->id;
		}
		// Delete existing test_type measure mappings
		DB::table('testtype_measures')->where('test_type_id', '=', $testTypeID)->delete();

		if (!empty($measures)) {
			// Add the new mapping
			DB::table('testtype_measures')->insert($measuresAdded);
		}
	}

	/**
	 * Set compatible specimen organisms
	 *
	 * @return void
	 */
// todo: to set organisms update function to fit new db stracture
	public function setOrganisms($organisms){

		$organismsAdded = array();
		$testTypeID = 0;	

		if(is_array($organisms)){
			foreach ($organisms as $key => $value) {
				$organismsAdded[] = array(
					'test_type_id' => (int)$this->id,
					'organism_id' => (int)$value,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
					);
				$testTypeID = (int)$this->id;
			}

		}
		// Delete existing test_type organism mappings
		DB::table('testtype_organisms')->where('test_type_id', '=', $testTypeID)->delete();

		if (!empty($organisms)) {
			// Add the new mapping
			DB::table('testtype_organisms')->insert($organismsAdded);
		}
	}

	/**
	* Given the test name we return the test type ID
	*
	* @param $testname the name of the test
	*/
	public static function getTestTypeIdByTestName($testName)
	{
		try 
		{
			$testName = trim($testName);
			$testTypeId = TestType::where('name', 'like', $testName)->orderBy('name')->firstOrFail();
			return $testTypeId->id;
		} catch (ModelNotFoundException $e) 
		{
			Log::error("The test type ` $testName ` does not exist:  ". $e->getMessage());
			//TODO: send email?
			return null;
		}
	}

	/**
	* Get TestTypes that support prevalence counts
	*
	* @return Collection TestTypes
	*/
	public static function supportPrevalenceCounts()
	{

		$testTypes = new Illuminate\Database\Eloquent\Collection();

		// Get ALPHANUMERIC measures whose possible results (or their interpretation) can be
		// reduced to either Positive or Negative
		$measures = DB::table('measures')->select(DB::raw('measures.id, measures.name'))
					->join('measure_ranges', 'measures.id', '=', 'measure_ranges.measure_id')
					->where('measures.measure_type_id', '=', Measure::ALPHANUMERIC)
					->where(function($query){
						$query->where('measure_ranges.alphanumeric', '=', 'Positive')
								->orWhere('measure_ranges.alphanumeric', '=', 'Negative')
								->orWhere('measure_ranges.interpretation', '=', 'Positive')
								->orWhere('measure_ranges.interpretation', '=', 'Negative');
					})->get();

		foreach ($measures as $measure) {
			$measureORM = Measure::find($measure->id);
			$objArray = $measureORM->testTypes()->first();
			if(!empty($objArray)){
				foreach ($measureORM->testTypes()->get() as $tType) {
					if($tType->measures()->count() == 1){
						$testTypes->add($tType);
					}
				}
			}
		}
		 return $testTypes->unique()->sortBy('name');
	}

	/**
	* Return the rate of positive test results (Optionally given the year, month, date)
	*
	* @param $year, $month, $date
	*/
	public function getPrevalenceCount($year = 0, $month = 0, $date = 0)
	{
		$theDate = "";
		if ($year > 0) {
			$theDate .= $year;
			if ($month > 0) {
				$theDate .= "-".sprintf("%02d", $month);
				if ($date > 0) {
					$theDate .= "-".sprintf("%02d", $date);
				}
			}
		}
		// TODO: Should be changed to a more flexible format i.e. that supports localization
		$data =  UnhlsTest::select(DB::raw(
			"ROUND(COUNT(DISTINCT IF((unhls_test_results.result='Positive' OR ".
				"(measure_ranges.alphanumeric=unhls_test_results.result AND measure_ranges.interpretation = 'Positive')),".
				" unhls_tests.id,NULL))*100/COUNT(DISTINCT unhls_tests.id), 2 ) AS rate"))
				->join('test_types', 'unhls_tests.test_type_id', '=', 'test_types.id')
				->join('testtype_measures', 'test_types.id', '=', 'testtype_measures.test_type_id')
				->join('measure_ranges', 'testtype_measures.measure_id', '=', 'measure_ranges.measure_id')
				->join('unhls_test_results', function($join){
					$join->on('unhls_tests.id', '=', 'unhls_test_results.test_id')
						 ->on('testtype_measures.measure_id', '=', 'unhls_test_results.measure_id');
					})
				->join('measures', 'testtype_measures.measure_id', '=', 'measures.id')
				->where('test_types.id', '=', $this->id)
				->whereIn('test_status_id', array(UnhlsTest::COMPLETED, UnhlsTest::VERIFIED))
				->where('measures.measure_type_id', '=', Measure::ALPHANUMERIC)
				->where(function($query){
					$query->where('measure_ranges.alphanumeric', '=', 'Positive')
							->orWhere('measure_ranges.alphanumeric', '=', 'Negative')
							->orWhere('measure_ranges.interpretation', '=', 'Positive')
							->orWhere('measure_ranges.interpretation', '=', 'Negative');
				})
				->where(function($query) use ($theDate){
					if (strlen($theDate)>0) {
						$query->where('time_created', 'LIKE', $theDate."%");
					}
					})
				->groupBy('test_types.id')
				->get();
		return $data;
	}
	/**
	* Return the prevalence counts for all TestTypes for the given date range
	*
	* @param $from, $to
	*/
	public static function getPrevalenceCounts($from, $to, $testTypeID = 0, $ageRange=null){
		$toPlusOne = date_add(new DateTime($to), date_interval_create_from_date_string('1 day'));

		// TODO: Should be changed to a more flexible format i.e. that supports localization
		$data =  UnhlsTest::select(DB::raw("test_types.id as id, test_types.name as test, ".
					"COUNT(DISTINCT unhls_tests.specimen_id) as total, ".
					"COUNT(DISTINCT IF((unhls_test_results.result='Positive' OR ".
						"(measure_ranges.alphanumeric = unhls_test_results.result AND measure_ranges.interpretation = 'Positive')),".
						"unhls_tests.specimen_id,NULL)) positive, ".
					"COUNT(DISTINCT IF((unhls_test_results.result='Negative' OR ".
						"(measure_ranges.alphanumeric = unhls_test_results.result AND measure_ranges.interpretation = 'Negative')),".
						"unhls_tests.specimen_id,NULL)) negative, ".
					"ROUND(COUNT(DISTINCT IF((unhls_test_results.result = 'Positive' OR ".
						"(measure_ranges.alphanumeric = unhls_test_results.result AND measure_ranges.interpretation = 'Positive'))".
						", unhls_tests.specimen_id, NULL))*100/COUNT(DISTINCT unhls_tests.specimen_id ) , 2 ) AS rate"
					))
				->join('test_types', 'unhls_tests.test_type_id', '=', 'test_types.id')
				->join('testtype_measures', 'test_types.id', '=', 'testtype_measures.test_type_id')
				->join('measure_ranges', 'testtype_measures.measure_id', '=', 'measure_ranges.measure_id')
				->join('measures', 'testtype_measures.measure_id', '=', 'measures.id')
				->join('unhls_test_results', function($join){
					$join->on('unhls_tests.id', '=', 'unhls_test_results.test_id')
						->on('testtype_measures.measure_id', '=', 'unhls_test_results.measure_id');
					})
				->join('measure_types', 'measure_types.id', '=', 'measures.measure_type_id')
				->whereIn('test_status_id', array(UnhlsTest::COMPLETED, UnhlsTest::VERIFIED, UnhlsTest::APPROVED))
				->where(function($query) use ($testTypeID){
					if ($testTypeID != 0) {
						$query->where('unhls_tests.test_type_id', $testTypeID);
					}
				})
				->where(function($query){
					$query->where('measure_ranges.alphanumeric', '=', 'Positive')
							->orWhere('measure_ranges.alphanumeric', '=', 'Negative')
							->orWhere('measure_ranges.interpretation', '=', 'Positive')
							->orWhere('measure_ranges.interpretation', '=', 'Negative');
				});
			if($ageRange){
				$data = $data->join('unhls_visits', 'unhls_tests.visit_id', '=', 'unhls_visits.id')
							   ->join('unhls_patients', 'unhls_visits.patient_id', '=', 'unhls_patients.id');
							  
							   		$age = explode('-', $ageRange);
									$ageStart = $age[0];
									$ageEnd = $age[1];
									$now = new DateTime('now');
									$clonedDate = clone $now;
									$finishDate = $clonedDate->sub(new DateInterval('P'.$ageStart.'Y'))->format('Y-m-d');
									$clonedDate = clone $now;
									$startDate = $clonedDate->sub(new DateInterval('P'.$ageEnd.'Y'))->format('Y-m-d');
							   		$data = $data->whereBetween('dob', [$startDate, $finishDate]);
							  
			}	
				$data = $data->whereBetween('time_created', array($from, $toPlusOne))
				->groupBy('test_types.id')
				->get();
		return $data;
	}

	public static function supportTurnaoundCounts()
	{

		$testTypes = new Illuminate\Database\Eloquent\Collection();

		// Get ALPHANUMERIC measures whose possible results (or their interpretation) can be
		// reduced to either Positive or Negative
		$measures = DB::table('measures')->select(DB::raw('measures.id, measures.name'))
					->join('measure_ranges', 'measures.id', '=', 'measure_ranges.measure_id')
					->where('measures.measure_type_id', '=', Measure::ALPHANUMERIC)
					->where(function($query){
						$query->where('measure_ranges.alphanumeric', '=', 'Positive')
								->orWhere('measure_ranges.alphanumeric', '=', 'Negative')
								->orWhere('measure_ranges.interpretation', '=', 'Positive')
								->orWhere('measure_ranges.interpretation', '=', 'Negative');
					})->get();

		foreach ($measures as $measure) {
			$measureORM = Measure::find($measure->id);
			$objArray = $measureORM->testTypes()->first();
			if(!empty($objArray)){
				foreach ($measureORM->testTypes()->get() as $tType) {
					if($tType->measures()->count() == 1){
						$testTypes->add($tType);
					}
				}
			}
		}
		 return $testTypes->unique()->sortBy('name');
	}

	/**
	* Return the total number and rate of tests within and beyond the turnaround time (Optionally given the year, month, date)
	*
	* @param $year, $month, $date
	*/
	public function getTurnaroundCount($year = 0, $month = 0, $date = 0)
	{
		$theDate = "";
		if ($year > 0) {
			$theDate .= $year;
			if ($month > 0) {
				$theDate .= "-".sprintf("%02d", $month);
				if ($date > 0) {
					$theDate .= "-".sprintf("%02d", $date);
				}
			}
		}

		$data =	UnhlsTest::select(DB::raw("test_type_id,tt.name as name, tt.test_category_id as lab_section, count(DISTINCT unhls_tests.id) as total,SUM(case when (tt.targetTAT*60) >= TIMESTAMPDIFF(MINUTE, unhls_tests.time_started, unhls_tests.time_verified) then 1 when TIMESTAMPDIFF(MINUTE, unhls_tests.time_started, unhls_tests.time_verified) = 'null' then 0 else 0 end) as Within, SUM(case when TIMESTAMPDIFF(MINUTE, unhls_tests.time_started, unhls_tests.time_verified)>(tt.targetTAT*60) then 1 else 0 end) as Beyond, DATE_FORMAT(unhls_tests.time_started,'%Y%m') year_month_created, unhls_tests.time_created, unhls_tests.time_started, unhls_tests.time_verified, TIMESTAMPDIFF(MINUTE, unhls_tests.time_created, unhls_tests.time_started) as waiting_time, SEC_TO_TIME(AVG(TIME_TO_SEC(unhls_tests.time_started) - TIME_TO_SEC(unhls_tests.time_created))) as WT, TIMESTAMPDIFF(MINUTE, unhls_tests.time_started, unhls_tests.time_verified) as testing_time, tt.targetTAT*60 AS ETAT ,ROUND(SEC_TO_TIME(AVG(TIME_TO_SEC(unhls_tests.time_completed) - TIME_TO_SEC(unhls_tests.time_started)))/60) as ACT, SUM(TIMESTAMPDIFF(MINUTE, unhls_tests.time_started, unhls_tests.time_completed)) as AverageTAT"))
			->JOIN('test_types as tt', 'unhls_tests.test_type_id', '=', 'tt.id') 
			->where('tt.id', '=', $this->id)
			->where('unhls_tests.time_completed', '!=', 'null')
				->where(function($query) use ($theDate){
					if (strlen($theDate)>0) {
						$query->where('time_created', 'LIKE', $theDate."%");
					}
					})
			->GROUPBY('test_type_id')
			->get();
		return $data;
	}
	/**
	* Return the counts for all TestTypes for the given date range
	*
	* @param $from, $to
	*/
	public static function getTurnaroundCounts($from, $to, $testTypeID = 0, $ageRange=null){
		$toPlusOne = date_add(new DateTime($to), date_interval_create_from_date_string('1 day'));

		// TODO: Should be changed to a more flexible format i.e. that supports localization
		$data =  UnhlsTest::select(DB::raw("test_type_id,tt.name, tt.test_category_id as lab_section, count(DISTINCT unhls_tests.id) as total,SUM(case when (tt.targetTAT*60) >= TIMESTAMPDIFF(MINUTE, unhls_tests.time_started, unhls_tests.time_verified) then 1 when TIMESTAMPDIFF(MINUTE, unhls_tests.time_started, unhls_tests.time_verified) = 'null' then 0 else 0 end) as Within, SUM(case when TIMESTAMPDIFF(MINUTE, unhls_tests.time_started, unhls_tests.time_verified)>(tt.targetTAT*60) then 1 else 0 end) as Beyond, DATE_FORMAT(unhls_tests.time_started,'%Y%m') year_month_created, unhls_tests.time_created, unhls_tests.time_started, unhls_tests.time_verified, TIMESTAMPDIFF(MINUTE, unhls_tests.time_created, unhls_tests.time_started) as waiting_time, SEC_TO_TIME(AVG(TIME_TO_SEC(unhls_tests.time_started) - TIME_TO_SEC(unhls_tests.time_created))) as WT, TIMESTAMPDIFF(MINUTE, unhls_tests.time_started, unhls_tests.time_verified) as testing_time, tt.targetTAT*60 AS ETAT ,ROUND(SEC_TO_TIME(AVG(TIME_TO_SEC(unhls_tests.time_completed) - TIME_TO_SEC(unhls_tests.time_started)))/60) as ACT, SUM(TIMESTAMPDIFF(MINUTE, unhls_tests.time_started, unhls_tests.time_completed)) as AverageTAT"))
			->JOIN('test_types as tt', 'unhls_tests.test_type_id', '=', 'tt.id')
			->where('unhls_tests.time_completed', '!=', 'null')
				->where(function($query) use ($testTypeID){
					if ($testTypeID != 0) {
						$query->where('unhls_tests.test_type_id', $testTypeID);
					}
				})
				;
			if($ageRange){
				$data = $data->join('unhls_visits', 'unhls_tests.visit_id', '=', 'unhls_visits.id')
							   ->join('unhls_patients', 'unhls_visits.patient_id', '=', 'unhls_patients.id');
							  
							   		$age = explode('-', $ageRange);
									$ageStart = $age[0];
									$ageEnd = $age[1];
									$now = new DateTime('now');
									$clonedDate = clone $now;
									$finishDate = $clonedDate->sub(new DateInterval('P'.$ageStart.'Y'))->format('Y-m-d');
									$clonedDate = clone $now;
									$startDate = $clonedDate->sub(new DateInterval('P'.$ageEnd.'Y'))->format('Y-m-d');
							   		$data = $data->whereBetween('dob', [$startDate, $finishDate]);
							  
			}	
				$data = $data->whereBetween('time_created', array($from, $toPlusOne))
				->groupBy('test_type_id')
				->get();
		return $data;
	}
	/**
	* Return the counts for a test type given the test_status_id, and date range for ungrouped tests
	*
	* @param $testStatusID, $from, $to
	*/
	public function countPerStatus($testStatusID, $from = null, $to = null)
	{

		$tests = UnhlsTest::where('test_type_id', $this->id)->whereIn('test_status_id', $testStatusID);

		if($to && $from){
			$tests = $tests->whereBetween('time_created', [$from, $to]);
		}

		return $tests->count();

	}
	/**
	* Returns grouped test Counts with optional gender, age range, date range
	*
	* @param $testStatusID, $from, $to
	*/
	public function groupedTestCount($gender=null, $ageRange=null, $from=null, $to=null){
			$tests = UnhlsTest::where('test_type_id', $this->id)
						 ->whereIn('test_status_id', [UnhlsTest::COMPLETED, UnhlsTest::VERIFIED, UnhlsTest::APPROVED]);
			if($to && $from){
				$tests = $tests->whereBetween('time_created', [$from, $to]);
			}
			if($ageRange || $gender){
				$tests = $tests->join('unhls_visits', 'unhls_tests.visit_id', '=', 'unhls_visits.id')
							   ->join('unhls_patients', 'unhls_visits.patient_id', '=', 'unhls_patients.id');
							   if($gender){
							   		$tests = $tests->whereIn('gender', $gender);
							   	}
							   	if($ageRange){
							   		$age = explode('-', $ageRange);
									$ageStart = $age[0];
									$ageEnd = $age[1];
									$now = new DateTime('now');
									$clonedDate = clone $now;
									$finishDate = $clonedDate->sub(new DateInterval('P'.$ageStart.'Y'))->format('Y-m-d');
									$clonedDate = clone $now;
									$startDate = $clonedDate->sub(new DateInterval('P'.$ageEnd.'Y'))->format('Y-m-d');
							   		$tests = $tests->whereBetween('dob', [$startDate, $finishDate]);
							   	}
			}

		return $tests->count();
	}
	/**
	* Check if a certain test type has measures that are either numeric or alphanumeric
	*
	*/
	public function hasAlphanumericMeasures(){
		$boolean = TestTypeMeasure::where('test_type_id', $this->id)
						->join('measures', 'testtype_measures.measure_id', '=', 'measures.id')
						->where('measure_type_id', Measure::ALPHANUMERIC);
		return $boolean->count();
	}

	public function hasNumericMeasures(){
		$boolean = TestTypeMeasure::where('test_type_id', $this->id)
						->join('measures', 'testtype_measures.measure_id', '=', 'measures.id')
						->where('measure_type_id', Measure::NUMERIC);
		return $boolean->count();
	}
	/**
	 * Accreditation constants
	 */
	const ACCREDITED = 1;
	/**
	 * Check to see if test is accredited
	 *
	 * @return boolean
	 */
	public function isAccredited()
	{
		if($this->accredited == null || $this->accredited != TestType::ACCREDITED){
			return false;
		}
		else 
			return true;
	}
	/**
	 * Get cd4 counts based on either baseline/follow-up and <500/>500
	 *
	 * @return counts
	 */
	public function cd4($from = null, $to = null, $range, $comment)
	{
		$tests = array();
		$measureIds = Measure::where('name', 'CD4')->lists('id');
		$toPlusOne = date_add(new DateTime($to), date_interval_create_from_date_string('1 day'));
		$tests = $this->tests()->whereBetween('time_created', [$from, $toPlusOne])->lists('id');
		$results = TestResult::whereIn('test_id', $tests)->whereIn('measure_id', $measureIds)->where('result', $comment)->lists('test_id');
		$qualifier = TestResult::whereIn('test_id', $tests)->whereIn('measure_id', $measureIds)->whereRaw("result REGEXP '^[0-9]+$'");
		if($range == '< 500')
		{
			$qualifier = $qualifier->where('result', '<', 500);
		}
		else if($range == '> 500')
		{
			$qualifier = $qualifier->where('result', '>', 500);
		}
		$qualifier = $qualifier->lists('test_id');
		return count(array_intersect(array_unique($qualifier), array_unique($results)));
	}

	public function isCulture(){
		if (!is_null($this->testNameMapping)) {
			return ($this->testNameMapping->system_name == 'culture_sensitivity') ? true : false ;
		}else{
			return false;
		}
	}

	public function isGramStain(){
		if (!is_null($this->testNameMapping)) {
			return ($this->testNameMapping->system_name == 'gram_stain') ? true : false ;
		}else{
			return false;
		}
	}

	public function isHIV(){
		if (!is_null($this->testNameMapping)) {
			return ($this->testNameMapping->system_name == 'hiv') ? true : false ;
		}else{
			return false;
		}
	}

    public function testNameMapping()
    {
        return $this->hasOne('TestNameMapping');
    }
}
