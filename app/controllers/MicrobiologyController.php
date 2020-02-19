<?php

class MicrobiologyController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$now = new DateTime();
		$collectionDate = $now->format('Y-m-d H:i');
		$receptionDate = $now->format('Y-m-d H:i');
		$districts = ['Select District']+District::orderBy('name', 'ASC')->lists('name', 'id');
		$wards = ['Select Sample Origin']+Ward::lists('name', 'id');
		$antibiotics = ['']+Drug::orderBy('name','ASC')->lists('name', 'id');
		$specimenTypes = ['select Specimen Type']+SpecimenType::lists('name', 'id');
		$ulinFormat = AdhocConfig::where('name','ULIN')->first()->getULINFormat();
		$facilities = ['Select facility']+Facility::where('active', '=', 1)->lists('name', 'id');
		// Test Category
		$categories = ['Select Lab Section']+TestCategory::lists('name', 'id');
		$testpurpose = ['Select Test Purpose']+UnhlsPurpose::orderBy('name', 'ASC')->lists('name', 'id');
		return View::make('microbio.create')
					->with('ward', $wards)
					->with('receptionDate', $receptionDate)
					->with('collectionDate', $collectionDate)
					->with('antibiotics', $antibiotics)
					->with('districts', $districts)
					->with('testCategory', $categories)
					->with('specimenType', $specimenTypes)
					->with('facilities', $facilities)
					->with('testpurpose', $testpurpose)
					->with('ulinFormat', $ulinFormat);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$rules = array(
			'patient_name' => 'required',
			'gender' => 'required',
			'dob' => 'required' ,
			'nationality' => 'required' ,
		);
		$validator = Validator::make(Input::all(), $rules);

		$antibiotic = Input::get('antibiotic');

		if ($validator->fails()) {

			return Redirect::back()->withErrors($validator)->withInput(Input::all());
		} else {

			$nation = ['0' => 'National', '1' => 'Refugee', '2' => 'Foreigner'];
			// store
			$patient = new UnhlsPatient;
			//$patient->patient_number = Input::get('patient_number');
			$patient->nin = Input::get('nin');
			$patient->name = Input::get('patient_name');
			$patient->gender = Input::get('gender');
			$patient->nationality = $nation[Input::get('nationality')];
			$patient->dob = Input::get('dob');
			$patient->village_residence = Input::get('village_residence');
			$patient->village_workplace = Input::get('village_workplace');
			$patient->district_residence = Input::get('district_residence');
			$patient->district_workplace = Input::get('district_workplace');
			//$patient->occupation = Input::get('occupation');
			//$patient->email = Input::get('email');
			//$patient->address = Input::get('address');
			$patient->phone_number = Input::get('phone_number');
			$patient->is_micro = 1;
			$patient->created_by = Auth::user()->id;

			$patient->save();
				if (Input::get('ulin')!= '') {
					$patient->ulin = Input::get('ulin');
				}else{
					$patient->ulin = $patient->getUlin();
				}
				$patient->save();
				$uuid = new UuidGenerator; 
				$uuid->save();

			$patientdetail = new MicroPatientDetail;
			$patientdetail->patient_id = $patient->id;
			$patientdetail->requested_by = Input::get('requested_by');
			$patientdetail->clinician_contact = Input::get('phone_contact');
			//$patientdetail->village_residence = Input::get('village_residence');
			//patientdetail->village_workplace = Input::get('village_workplace');
			//$patientdetail->occupation = Input::get('occupation');
			//$patientdetail->email = Input::get('email');
			//$patientdetail->address = Input::get('address');
			//$patientdetail->phone_number = Input::get('phone_number');
			//$patientdetail->patientid = Input::get('patientid');
			$patientdetail->sub_county_residence = Input::get('sub_county_residence');			
			$patientdetail->sub_county_workplace = Input::get('sub_county_workplace');			
			$patientdetail->name_next_kin = Input::get('name_next_kin');
			$patientdetail->contact_next_kin = Input::get('contact_next_kin');
			$patientdetail->residence_next_kin = Input::get('residence_next_kin');
			$patientdetail->admission_date = Input::get('admission_date');
			$patientdetail->transfered = Input::get('transfered');
			$patientdetail->facility_transfered = Input::get('facility_transfered');
			$patientdetail->clinical_notes = Input::get('clinical_notes');
			//$patientdetail->ward = Input::get('ward');
			$patientdetail->days_on_antibiotic = Input::get('antibiotic_days');
			
			$patientdetail->save();

			$visitType = ['Out-patient','In-patient', 'Referral'];
			$activeTest = array();

			// On antibiotics --- Lists all antibiotics
			$no_of_drugs = sizeof($antibiotic['drug']);

			if($no_of_drugs>1){
				for($i=0; $i<$no_of_drugs; $i++)
				{
					$drug = new PatientAntibiotics;
					$drug->patient_id = $patient->id;
					$drug->drug_id = $antibiotic['drug'][$i];
					$drug->save();
				}
			}

			/*
			 * - Create a visit
			 * - Fields required: visit_type, patient_id
			 */
			$visit = new UnhlsVisit;
			$visit->patient_id = $patient->id;
			$visit->visit_type = $visitType[Input::get('visit_type')];
			$visit->ward_id = Input::get('ward_id');
			$visit->bed_no = Input::get('bed_no');
			$visit->facility_id = Input::get('facility');
			$visit->facility_lab_number = Input::get('facility_lab_number');
			$visit->hospitalized = Input::get('hospitalized');
			$visit->on_antibiotics = Input::get('onAntibiotics');
			$visit->save();

			
			/*
			 * - Create tests requested
			 * - Fields required: visit_id, test_type_id, specimen_id, test_status_id, created_by, requested_by
			 */
            $testLists = Input::get('test_list');
            if(is_array($testLists)){
                foreach ($testLists as $testList) {
                    // Create Specimen - specimen_type_id, accepted_by, referred_from, referred_to
                    $specimen = new UnhlsSpecimen;
                    $specimen->specimen_type_id = $testList['specimen_type_id'];
                    $specimen->accepted_by = Auth::user()->id;
                    $specimen->time_collected = Input::get('collection_date');
                    $specimen->time_accepted = Input::get('reception_date');
                    $specimen->save();
                    foreach ($testList['test_type_id'] as $id) {
                        $testTypeID = (int)$id;

                        $test = new UnhlsTest;
                        $test->visit_id = $visit->id;
                        $test->test_type_id = $testTypeID;
                        $test->specimen_id = $specimen->id;
                        $test->test_status_id = UnhlsTest::PENDING;
                        $test->created_by = Auth::user()->id;
                        //$test->requested_by = Input::get('clinician');
                        //$test->purpose = Input::get('hiv_purpose');
                        $test->save();

                        $activeTest[] = $test->id;
                    }
                }
            }

			$url = Session::get('SOURCE_URL');
			
			return Redirect::to($url)->with('message', 'messages.success-creating-test')
					->with('activeTest', $activeTest);

		}
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$patient = UnhlsPatient::find($id);
		$now = new DateTime();
		$collectionDate = $now->format('Y-m-d H:i');
		$receptionDate = $now->format('Y-m-d H:i');
		$districts = ['Select District']+District::orderBy('name', 'ASC')->lists('name', 'id');
		$wards = ['Select Sample Origin']+Ward::lists('name', 'id');
		$antibiotics = ['']+Drug::orderBy('name','ASC')->lists('name', 'id');
		$specimenTypes = ['select Specimen Type']+SpecimenType::lists('name', 'id');
		$ulinFormat = AdhocConfig::where('name','ULIN')->first()->getULINFormat();
		$facilities = ['Select facility']+Facility::where('active', '=', 1)->lists('name', 'id');
		// Test Category
		$categories = ['Select Lab Section']+TestCategory::lists('name', 'id');
		$testpurpose = ['Select Test Purpose']+UnhlsPurpose::orderBy('name', 'ASC')->lists('name', 'id');
		$patientAntibiotics = PatientAntibiotics::where('patient_id', $patient->id)->lists('drug_id');
		$visits = UnhlsVisit::find($id);
		//dd($patientAntibiotics);
		return View::make('microbio.edit')
					->with('patient', $patient)
					->with('ward', $wards)
					->with('visits', $visits)
					->with('receptionDate', $receptionDate)
					->with('collectionDate', $collectionDate)
					->with('antibiotics', $antibiotics)
					->with('districts', $districts)
					->with('testCategory', $categories)
					->with('specimenType', $specimenTypes)
					->with('patientAntibiotics', $patientAntibiotics)
					->with('facilities', $facilities)
					->with('testpurpose', $testpurpose)
					->with('ulinFormat', $ulinFormat);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
		$rules = array(
			'patient_name' => 'required',
			'gender' => 'required',
			'dob' => 'required' ,
			'nationality' => 'required' ,
		);
		$validator = Validator::make(Input::all(), $rules);

		$antibiotic = Input::get('antibiotic');

		if ($validator->fails()) {

			return Redirect::back()->withErrors($validator)->withInput(Input::all());
		} else {

			$nation = ['0' => 'National', '1' => 'Refugee', '2' => 'Foreigner'];
			// store
			$patient = UnhlsPatient::find($id);
			//$patient->patient_number = Input::get('patient_number');
			$patient->nin = Input::get('nin');
			$patient->name = Input::get('patient_name');
			$patient->gender = Input::get('gender');
			$patient->nationality = $nation[Input::get('nationality')];
			$patient->dob = Input::get('dob');
			$patient->village_residence = Input::get('village_residence');
			$patient->village_workplace = Input::get('village_workplace');
			//$patient->occupation = Input::get('occupation');
			//$patient->email = Input::get('email');
			//$patient->address = Input::get('address');
			$patient->phone_number = Input::get('phone_number');
			$patient->is_micro = 1;
			$patient->created_by = Auth::user()->id;

			$patient->save();
				if (Input::get('ulin')!= '') {
					$patient->ulin = Input::get('ulin');
				}else{
					$patient->ulin = $patient->getUlin();
				}
				$patient->save();
				$uuid = new UuidGenerator; 
				$uuid->save();

			$patientdetail = MicroPatientDetail::find($id);
			$patientdetail->patient_id = $patient->id;
			$patientdetail->requested_by = Input::get('requested_by');
			$patientdetail->clinician_contact = Input::get('phone_contact');
			//$patientdetail->village_residence = Input::get('village_residence');
			//patientdetail->village_workplace = Input::get('village_workplace');
			//$patientdetail->occupation = Input::get('occupation');
			//$patientdetail->email = Input::get('email');
			//$patientdetail->address = Input::get('address');
			//$patientdetail->phone_number = Input::get('phone_number');
			//$patientdetail->patientid = Input::get('patientid');
			$patientdetail->sub_county_residence = Input::get('sub_county_residence');
			$patientdetail->district_residence = Input::get('district_residence');
			$patientdetail->sub_county_workplace = Input::get('sub_county_workplace');
			$patientdetail->district_workplace = Input::get('district_workplace');
			$patientdetail->name_next_kin = Input::get('name_next_kin');
			$patientdetail->contact_next_kin = Input::get('contact_next_kin');
			$patientdetail->residence_next_kin = Input::get('residence_next_kin');
			$patientdetail->admission_date = Input::get('admission_date');
			$patientdetail->transfered = Input::get('transfered');
			$patientdetail->facility_transfered = Input::get('facility_transfered');
			$patientdetail->clinical_notes = Input::get('clinical_notes');
			//$patientdetail->ward = Input::get('ward');
			$patientdetail->days_on_antibiotic = Input::get('antibiotic_days');
			
			$patientdetail->save();

			$visitType = ['Out-patient','In-patient', 'Referral'];
			$activeTest = array();

			// On antibiotics --- Lists all antibiotics
			$no_of_drugs = sizeof($antibiotic['drug']);

			if($no_of_drugs>1){
				for($i=0; $i<$no_of_drugs; $i++)
				{
					$drug = PatientAntibiotics::find($id);
					$drug->patient_id = $patient->id;
					$drug->drug_id = $antibiotic['drug'][$i];
					$drug->save();
				}
			}

			/*
			 * - Create a visit
			 * - Fields required: visit_type, patient_id
			 */
			$visit = UnhlsVisit::find($id);
			$visit->patient_id = $patient->id;
			$visit->visit_type = $visitType[Input::get('visit_type')];
			$visit->ward_id = Input::get('ward_id');
			$visit->bed_no = Input::get('bed_no');
			$visit->facility_id = Input::get('facility');
			$visit->facility_lab_number = Input::get('facility_lab_number');
			$visit->hospitalized = Input::get('hospitalized');
			$visit->on_antibiotics = Input::get('onAntibiotics');
			$visit->save();

			
			/*
			 * - Create tests requested
			 * - Fields required: visit_id, test_type_id, specimen_id, test_status_id, created_by, requested_by
			 */
            $testLists = Input::get('test_list');
            if(is_array($testLists)){
                foreach ($testLists as $testList) {
                    // Create Specimen - specimen_type_id, accepted_by, referred_from, referred_to
                    $specimen = new UnhlsSpecimen;
                    $specimen->specimen_type_id = $testList['specimen_type_id'];
                    $specimen->accepted_by = Auth::user()->id;
                    $specimen->time_collected = Input::get('collection_date');
                    $specimen->time_accepted = Input::get('reception_date');
                    $specimen->save();
                    foreach ($testList['test_type_id'] as $id) {
                        $testTypeID = (int)$id;

                        $test = new UnhlsTest;
                        $test->visit_id = $visit->id;
                        $test->test_type_id = $testTypeID;
                        $test->specimen_id = $specimen->id;
                        $test->test_status_id = UnhlsTest::PENDING;
                        $test->created_by = Auth::user()->id;
                        //$test->requested_by = Input::get('clinician');
                        //$test->purpose = Input::get('hiv_purpose');
                        $test->save();

                        $activeTest[] = $test->id;
                    }
                }
            }

			$url = Session::get('SOURCE_URL');
			
			return Redirect::to($url)->with('message', 'messages.success-creating-test')
					->with('activeTest', $activeTest);

		}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
