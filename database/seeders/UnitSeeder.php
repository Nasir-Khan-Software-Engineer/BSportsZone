<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;
class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::truncate();
        
        for($i=1; $i<=5; $i++){
            $unit = new Unit();
            $unit->posid = 1;
            $unit->name = "Unit-".$i;
            $unit->shortform = "Shortform-".$i;
            $unit->note = "This is demo note";
            $unit->created_by = 1;
            $unit->save();
        }
    }
}
