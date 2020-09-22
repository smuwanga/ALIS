<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UnhlsVisitBatch extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('unhls_visit_batches', function(Blueprint $table)
		{
			$table->increments('id'); // Batch ID
			$table->string('status', 15)->default('pending')->nullable(); // Transfer status of batch
			$table->bigInteger('total')->nullable(); // Total number of visits in batch
			$table->bigInteger('from')->nullable(); // First visit ID in batch
			$table->bigInteger('to')->nullable();   // Last visit ID in batch
			$table->longText('data')->nullable();   // JSON to be stored
            $table->dateTime('time_transferred')->default(null)->nullable(); // Date transferred to warehouse
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
		Schema::drop('unhls_visit_batches');
	}

}
