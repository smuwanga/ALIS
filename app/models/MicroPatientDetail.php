<?php

class MicroPatientDetail extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'micro_patients_details';

	public function patient()
	{
		return $this->hasOne('UnhlsPatient', 'id');
	}

	public function drug()
	{
		return $this->belongsTo('Drug');
	}

	public function district()
	{
		return $this->hasMany('District', 'id');
	}

	
}

