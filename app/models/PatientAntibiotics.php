<?php

class PatientAntibiotics extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'patient_antibiotics';

	public function patient()
	{
		return $this->belongsTo('UnhlsPatient');
	}

	public function drug()
	{
		return $this->belongsTo('Drug');
	}
	
}

