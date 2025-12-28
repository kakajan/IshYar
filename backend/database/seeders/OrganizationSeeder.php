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
            'name'             => [
                'en' => 'IshYar Demo Company',
                'fa' => 'شرکت نمایشی ایشیار',
            ],
            'slug'             => 'ishyar-demo',
            'description'      => [
                'en' => 'A demonstration organization for IshYar Enterprise WorkSuite',
                'fa' => 'یک سازمان نمایشی برای مجموعه سازمانی ایشیار',
            ],
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
