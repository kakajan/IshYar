<?php
namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::create([
            'name'             => 'IshYar Demo Company',
            'slug'             => 'ishyar-demo',
            'description'      => 'A demonstration organization for IshYar Enterprise WorkSuite',
            'timezone'         => 'UTC',
            'default_locale'   => 'en',
            'default_currency' => 'USD',
            'is_active'        => true,
            'settings'         => [
                'features'      => [
                    'tasks'         => true,
                    'time_tracking' => true,
                    'notifications' => true,
                    'reports'       => true,
                ],
                'notifications' => [
                    'email'    => true,
                    'push'     => true,
                    'telegram' => false,
                ],
            ],
        ]);
    }
}
