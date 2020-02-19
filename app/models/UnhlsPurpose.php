<?php

class UnhlsPurpose extends Eloquent
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'unhls_testpurposes';

	public $timestamps = true;

	/**
	 * Specimen relationship
	 */
	public function specimen()
	{
	  return $this->hasMany('UnhlsSpecimen');
	}
}
