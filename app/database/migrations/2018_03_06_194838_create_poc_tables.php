<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePocTables extends Migration {

	/**
	* Run the migrations.
	*
	* @return void
	*/
	public function up()
	{
		Schema::create('poc_tables', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('infant_name')->nullable();
			$table->string('exp_no')->nullable();
			$table->float('age')->nullable();
			$table->string('gender')->nullable();
			$table->string('caretaker_number')->nullable();
			$table->string('contrimoxazole')->nullable();
			$table->string('delivery_details')->nullable();
			$table->string('infant_pmtctarv')->nullable();
			$table->string('entry_point')->nullable();
			$table->string('other_entry_point')->nullable();
			$table->date('collection_date')->nullable();
			$table->string('sample_id')->nullable();
			$table->string('pcr_level')->nullable();
			$table->string('non_routine')->nullable();
			$table->string('feeding_status')->nullable();
			$table->string('mother_hts')->nullable();
			$table->string('mother_art')->nullable();
			$table->string('mother_nin')->nullable();
			$table->string('pmtct_antenatal')->nullable();
			$table->string('pmtct_delivery')->nullable();
			$table->string('pmtct_postnatal')->nullable();
			$table->string('created_by')->nullable();
			$table->date('admission_date')->nullable();
			$table->string('breastfeeding_status')->nullable();
			$table->string('mother_name')->nullable();
			$table->string('mother_hiv_status')->nullable();
			$table->string('provisional_diagnosis')->nullable();
			$table->string('mother_pmtctarv')->nullable();
			$table->integer('facility_id')->nullable();
			$table->integer('district_id')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});
	}
	/**
	* Reverse the migrations.
	*
	* @return void
	*/
	public function down()
	{
		Schema::drop('poc_tables')->nullable();
	}

}
