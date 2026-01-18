<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clubs = [

            'Portugal',
            'Spain',
            'England',
            'Germany',
            'Italy',
            'Argentina',
            'Brazil',
            'France',
            'Football Jersey',
            'Cricket Jersey',
            'Football Club Jersey',
            'Cricket Club Jersey',
            'Inter Miami Jersey',
            // Football Clubs
            'FC Barcelona',
            'Real Madrid',
            'Atletico Madrid',
            'Manchester United',
            'Manchester City',
            'Liverpool',
            'Chelsea',
            'Arsenal',
            'Tottenham Hotspur',
            'Bayern Munich',
            'Borussia Dortmund',
            'Juventus',
            'AC Milan',
            'Inter Milan',
            'Paris Saint-Germain',
            'Ajax',
            'Benfica',
            'Porto',
            'Galatasaray',
            'Fenerbahce',
            'Al-Hilal',
            'Al-Nassr',
        ];

        foreach ($clubs as $club) {
            Category::create([
                'POSID' => 1,
                'name' => $club,
                'icon' => 'club',
                'created_by' => 1,
            ]);
        }
    }
}
