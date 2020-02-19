<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		
	   Eloquent::unguard();

        DB::unprepared(file_get_contents(base_path() . "/app/database/seeds/facilities.sql"));
        echo "bulk facilities seeded!\n";

        DB::unprepared(file_get_contents(base_path() . "/app/database/seeds/permissions_update.sql"));
        echo "Updated permissions Table!\n";

        DB::unprepared(file_get_contents(base_path() . "/app/database/seeds/test_status.sql"));
        echo "Updated test_statuses Table!\n";
    }

}
