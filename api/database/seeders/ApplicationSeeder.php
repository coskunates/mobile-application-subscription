<?php

namespace Database\Seeders;

use App\Models\ApplicationRemoteCredential;
use App\Models\Application;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            $application = new Application();
            $application->name = 'test_app_' . $i;
            $application->hook_url = getenv('HOOK_API_URL') . $i;
            $application->save();

            $remoteCredentials = new ApplicationRemoteCredential();
            $remoteCredentials->application_id = $application->id;
            $remoteCredentials->os = 1;
            $remoteCredentials->username = $faker->userName();
            $remoteCredentials->password = $faker->password(8, 16);
            $remoteCredentials->save();

            $remoteCredentials = new ApplicationRemoteCredential();
            $remoteCredentials->application_id = $application->id;
            $remoteCredentials->os = 2;
            $remoteCredentials->username = $faker->userName();
            $remoteCredentials->password = $faker->password(8, 16);
            $remoteCredentials->save();
        }
    }
}
