<?php

use Illuminate\Database\QueryException;

/**
 * Contains functions for managing specimen types  
 *
 */
class UnhlsSpecimenController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// List all the active specimens and patients
		//$specimens = UnhlsSpecimen::orderBy('name', 'ASC')->get();

		// Load the view and pass the specimens
		return View::make('specimens.index')->with('specimens', $specimens);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//Create Specimen
		$now = new DateTime();
		$collectionDate = $now->format('Y-m-d H:i');
		$receptionDate = $now->format('Y-m-d H:i');
		$categories = ['Select Lab Section']+TestCategory::lists('name', 'id');
		$specimenTypes = ['select Specimen Type']+SpecimenType::lists('name', 'id');
		$clinicians = ['Select clinician']+Clinician::lists('name', 'id');
		$patient = new UnhlsPatient;
		$ulinFormat = AdhocConfig::where('name','ULIN')->first()->getULINFormat();

		return View::make('specimens.create')
					->with('patient', $patient)
					->with('clinicians',$clinicians)
					->with('receptionDate', $receptionDate)
					->with('collectionDate', $collectionDate)
					->with('specimenType', $specimenTypes)
					->with('testCategory', $categories)
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
		$rules = array('name' => 'required|unique:specimen_types,name');
		$validator = Validator::make(Input::all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator);
		} else {
			// store
			$specimen = new UnhlsSpecimen;
			$specimen->name = Input::get('name');
			$specimen->description = Input::get('description');

			try{
				$specimen->save();
			$url = Session::get('SOURCE_URL');
			return Redirect::to($url)
                    ->with('message', trans('messages.success-creating-specimen-type'));
			}catch(QueryException $e){
                Log::error($e);
			}
			
			// redirect
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
		//Show a specimen
		$specimen = UnhlsSpecimen::find($id);

		//Show the view and pass the $specimen to it
		return View::make('specimen.show')->with('specimen', $specimen);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//Get the specimen
		$specimen = UnhlsSpecimen::find($id);

		//Open the Edit View and pass to it the $specimen
		return View::make('specimen.edit')->with('specimen', $specimen);
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
		$rules = array('name' => 'required');
		$validator = Validator::make(Input::all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator);
		} else {
			// Update
			$specimen = UnhlsSpecimen::find($id);
			$specimen->name = Input::get('name');
			$specimen->description = Input::get('description');
			$specimen->save();

			// redirect
			$url = Session::get('SOURCE_URL');
			
			return Redirect::to($url)
			->with('message', trans('messages.success-updating-specimen-type'))->with('activespecimen', $specimen ->id);
		
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

	/**
	 * Remove the specified resource from storage (soft delete).
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function delete($id)
	{
		//Soft delete the specimen
		$specimen = UnhlsSpecimen::find($id);
		$inUseByTesttype = $specimen->testTypes->toArray();
		$inUseBySpecimen = $specimen->specimen->toArray();
		if (empty($inUseByTesttype) && empty($inUseBySpecimen)) {
		    // The specimen type is not in use
			$specimen->delete();
		} else {
		    // The specimen type is in use
		    return Redirect::route('specimen.index')
		    	->with('message', trans('messages.failure-specimen-type-in-use'));
		}
		// redirect
		  $url = Session::get('SOURCE_URL');
			
			return Redirect::to($url)
			->with('message', trans('messages.success-updating-specimen-type'));
	}
}