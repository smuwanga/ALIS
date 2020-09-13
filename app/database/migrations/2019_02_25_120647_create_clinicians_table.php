<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCliniciansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clinicians', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('cadre');
			$table->string('phone');
			$table->string('email');
			$table->timestamps();
		});
		DB::update('ALTER TABLE clinicians ADD active INT(3) NOT NULL DEFAULT 0 AFTER created_at');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('clinicians');
	}

}
