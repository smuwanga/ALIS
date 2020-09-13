<?php

class District extends Eloquent
{
	protected $table = "unhls_districts";

	// Microbiology Patient Relationship

	public function microPatient()
	{
		return $this->belongsTo('MicroPatientDetail', 'id');
	}
	
}