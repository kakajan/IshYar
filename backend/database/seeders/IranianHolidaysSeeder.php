<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class IranianHolidaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            // Nowruz holidays (1-4 Farvardin)
            [
                'jalali_date' => '01-01',
                'title' => 'نوروز',
                'title_en' => 'Nowruz',
                'description' => 'آغاز سال نو',
                'type' => 'national',
                'is_official_holiday' => true,
                'is_recurring' => true,
            ],
            [
                'jalali_date' => '01-02',
                'title' => 'نوروز',
                'title_en' => 'Nowruz',
                'type' => 'national',
                'is_official_holiday' => true,
                'is_recurring' => true,
            ],
            [
                'jalali_date' => '01-03',
                'title' => 'نوروز',
                'title_en' => 'Nowruz',
                'type' => 'national',
                'is_official_holiday' => true,
                'is_recurring' => true,
            ],
            [
                'jalali_date' => '01-04',
                'title' => 'نوروز',
                'title_en' => 'Nowruz',
                'type' => 'national',
                'is_official_holiday' => true,
                'is_recurring' => true,
            ],
            // 12 Farvardin - Islamic Republic Day
            [
                'jalali_date' => '01-12',
                'title' => 'روز جمهوری اسلامی',
                'title_en' => 'Islamic Republic Day',
                'type' => 'national',
                'is_official_holiday' => true,
                'is_recurring' => true,
            ],
            // 13 Farvardin - Sizdah Bedar
            [
                'jalali_date' => '01-13',
                'title' => 'سیزده‌بدر',
                'title_en' => 'Sizdah Bedar',
                'description' => 'روز طبیعت',
                'type' => 'national',
                'is_official_holiday' => true,
                'is_recurring' => true,
            ],
            // 14 Khordad - Imam Khomeini Death Anniversary
            [
                'jalali_date' => '03-14',
                'title' => 'رحلت امام خمینی',
                'title_en' => 'Imam Khomeini Death Anniversary',
                'type' => 'religious',
                'is_official_holiday' => true,
                'is_recurring' => true,
            ],
            // 15 Khordad - 15th of Khordad Uprising
            [
                'jalali_date' => '03-15',
                'title' => 'قیام ۱۵ خرداد',
                'title_en' => '15th of Khordad Uprising',
                'type' => 'national',
                'is_official_holiday' => true,
                'is_recurring' => true,
            ],
            // 22 Bahman - Islamic Revolution Day
            [
                'jalali_date' => '11-22',
                'title' => 'پیروزی انقلاب اسلامی',
                'title_en' => 'Islamic Revolution Day',
                'description' => 'سالروز پیروزی انقلاب اسلامی ۱۳۵۷',
                'type' => 'national',
                'is_official_holiday' => true,
                'is_recurring' => true,
            ],
            // 29 Esfand - Oil Nationalization Day
            [
                'jalali_date' => '12-29',
                'title' => 'ملی شدن صنعت نفت',
                'title_en' => 'Oil Nationalization Day',
                'type' => 'national',
                'is_official_holiday' => true,
                'is_recurring' => true,
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(
                [
                    'jalali_date' => $holiday['jalali_date'],
                    'organization_id' => null,
                ],
                $holiday
            );
        }

        // Note: Islamic holidays (based on Hijri calendar) are not included here
        // as they vary each year. They should be synced from an external API
        // or calculated based on the Hijri calendar.
        // Examples: Eid al-Fitr, Eid al-Adha, Ashura, Mawlid, etc.
    }
}
