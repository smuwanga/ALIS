<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTherapy extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('therapy', function(Blueprint $table)
		{
				DB::update("ALTER TABLE therapy ADD COLUMN clinician_id INT after contact");

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('therapy', function(Blueprint $table)
		{

		});
	}

}
