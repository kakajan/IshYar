<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JalaliSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JalaliApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed permissions/roles if necessary, or just act as a user
    }

    /** @test */
    public function it_can_get_user_settings()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/jalali/settings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'enabled',
                    'calendar_system',
                    'persian_numerals',
                    'first_day_of_week',
                ]
            ]);
    }

    /** @test */
    public function it_can_update_user_settings()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->patchJson('/api/v1/jalali/settings', [
                'enabled' => true,
                'calendar_system' => 'jalali',
                'persian_numerals' => false,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('jalali_settings', [
            'user_id' => $user->id,
            'calendar_system' => 'jalali',
            'persian_numerals' => 0, // boolean false stored as 0
        ]);
    }

    /** @test */
    public function it_can_convert_gregorian_to_jalali_via_api()
    {
        $user = User::factory()->create();

        // 2024-03-20 is 1403-01-01
        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/jalali/convert?date=2024-03-20&from=gregorian&to=jalali');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'output' => '1403/01/01',
                    'formatted' => '1 فروردین 1403',
                ]
            ]);
    }

    /** @test */
    public function it_can_convert_jalali_to_gregorian_via_api()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/jalali/convert?date=1403/01/01&from=jalali&to=gregorian');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'output' => '2024-03-20',
                ]
            ]);
    }

    /** @test */
    public function it_can_format_date_via_api()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/jalali/format?date=1403/01/01&format=medium&persian_numerals=0');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'formatted' => '1 فروردین 1403',
                ]
            ]);
    }

    /** @test */
    public function it_can_get_today_info()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/jalali/today');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'date',
                    'year',
                    'month',
                    'day',
                    'day_name',
                    'is_leap_year',
                ]
            ]);
    }
}
