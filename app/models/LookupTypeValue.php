<?php
# functions to create and manage drop down lists
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class LookupTypeValue extends Eloquent
{
	protected $table = "look_up_values";

		/**
	* Relationship with districts
	*/
	public function lookuptype()
	{
		return $this->belongsTo('App\LookupType','lookuptypeid','id');
	}
}

