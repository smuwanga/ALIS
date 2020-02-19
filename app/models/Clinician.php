<?php

class Clinician extends Eloquent {


	protected $fillable = [];

	 /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'clinicians';

    public $timestamps = false;

    
	/**
	*
	*
	*/
	public function tests()
	{
		return $this->hasMany('UnhlsTest');
	}

	
}