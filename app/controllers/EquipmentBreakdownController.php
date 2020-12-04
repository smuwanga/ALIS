<?php

class EquipmentBreakdownController extends \BaseController {

	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{

		//

		$items = UNHLSEquipmentBreakdown::get();
		return View::make('equipment.breakdown.index')->with('items',$items);

	}


	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create()
	{
		//

		$breakdown_type = array('1' => 'Hardware', '2' => 'Software', '3' => 'Both');
		$equipment = UNHLSEquipmentInventory::get()->lists('name','id');
		return View::make('equipment.breakdown.create')
		->with('equipment',$equipment)
		->with('breakdown_type',$breakdown_type);

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

			'equipment_id' => 'required',
			'problem' => 'required',
			'action_taken' => 'required',
			);

			$validator = Validator::make(Input::all(), $rules);

			if ($validator->fails()) {
				return Redirect::back()->withErrors($validator);
			} else {

				$item = new UNHLSEquipmentBreakdown;

				$item->district_id = \Config::get('constants.DISTRICT_ID') ;
				$item->facility_id = \Config::get('constants.FACILITY_ID');
				$item->year_id = \Config::get('constants.FIN_YEAR_ID');

				$item->equipment_id = Input::get('equipment_id');
				$item->equipment_code = Input::get('equipment_code');
				$item->equipment_type = Input::get('equipment_type');
				$item->problem = Input::get('problem');
				$item->reporting_officer = Input::get('reporting_officer');
				$item->reporting_officer_email = Input::get('reporting_officer_email');
				$item->reporting_officer_contact = Input::get('reporting_officer_contact');
				$item->action_taken = Input::get('action_taken');
				$item->intervention_authority = Input::get('intervention_authority');
				$item->conclusion = Input::get('conclusion');
				$item->facility_code = Input::get('facility_code');
				$item->facility_level = Input::get('facility_level');
				$item->report_date = Input::get('report_date');
				$item->breakdown_date = Input::get('breakdown_date'); 
				$item->verified_by = Input::get('verified_by');
				$item->verification_date = Input::get('verification_date');


				$item->save();

				return Redirect::to('equipmentbreakdown');
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
			//
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
		* Show the form for creating a new resource.
		*
		* @return Response
		*/
		public function restore($id)
		{
			//

			$breakdown = UNHLSEquipmentBreakdown::find($id);
			return View::make('equipment.breakdown.restoration')->with('breakdown',$breakdown);

		}

		public function saveRestore()
		{
			//

			$rules = array(

			'reviewed_by' => 'required',
			'review_date' => 'required'

			);

			$validator = Validator::make(Input::all(), $rules);

			if ($validator->fails()) {
				return Redirect::back()->withErrors($validator);
			} else {

				$breakdown = UNHLSEquipmentBreakdown::find( Input::get('breakdown_id'));

				$breakdown->comment = Input::get('comment');
				$breakdown->restored_by = Input::get('reviewed_by');
				$breakdown->restore_date = Input::get('review_date');


				$breakdown->save();

				return Redirect::to('equipmentbreakdown');
			}
		}


	}
