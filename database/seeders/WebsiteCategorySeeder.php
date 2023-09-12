<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\WebsiteCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WebsiteCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WebsiteCategory::truncate();
        WebsiteCategory::insert([
            [
                'name' => "Education",
            ],
            [
                'name' => "Health",
            ],
        ]);

    }
}
