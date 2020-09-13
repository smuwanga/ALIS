<?php

class UnhlsPurposeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// List all the active test purpose
		$purposes = UnhlsPurpose::orderBy('name', 'ASC')->get();
		// Load the view and pass the test purposes		
		return View::make('testpurpose.index')->with('purpose',$purposes);
	}


	/**
	 * tes the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//Create Test purpose
		return View::make('testpurpose.create');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
			//
		$rules = array('name' => 'required|unique:unhls_testpurposes,name');
		$validator = Validator::make(Input::all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator);
		} else {
			// store
			$purpose = new UnhlsPurpose;
			$purpose->name = Input::get('name');
			$purpose->description = Input::get('description');

			try{
				$purpose->save();
			// $url = Session::get('SOURCE_URL');
			return Redirect::to('testpurpose')
                    ->with('message', trans('Successfully test-purpose is created'));
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
			//Show a purpose
		$purpose = UnhlsPurpose::find($id);

		//Show the view and pass the $purpose to it
		return View::make('testpurpose.show')->with('purpose', $purpose);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//Get the purpose
		$purpose = UnhlsPurpose::find($id);

		//Open the Edit View and pass to it the $specimentype
		return View::make('testpurpose.edit')->with('purpose', $purpose);
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
			$purpose = UnhlsPurpose::find($id);
			$purpose->name = Input::get('name');
			$purpose->description = Input::get('description');
			$purpose->save();

			// redirect
			//$url = Session::get('SOURCE_URL');
			
			return Redirect::to('testpurpose')
			->with('message', trans('Successfully test-purpose is updated'))->with('activetestpurpose', $purpose ->id);
		
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
		//Soft delete the testpurpose
		$purpose = UnhlsPurpose::find($id);
		// $inUseByTesttype = $specimentype->testTypes->toArray();
		// $inUseBySpecimen = $specimentype->specimen->toArray();
		// if (empty($inUseByTesttype) && empty($inUseBySpecimen)) {
		    // The specimen type is not in use
			$purpose->delete();
		// } else {
		//     // The specimen type is in use
		//     return Redirect::route('testpurpose.index')
		//     	->with('message', trans('messages.failure-specimen-type-in-use'));
		// }
		// redirect
		  //$url = Session::get('SOURCE_URL');
			
			return Redirect::to('testpurpose')
			->with('message', trans('messages.success-updating-specimen-type'));
	}


}
