<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStock;

class ServiceSeederPOSID_1 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $csvFile = database_path('seeders/data/service_add_posid_1.csv');

         if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: $csvFile");
            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {

            $data = array_combine($header, $row);

            $product = new Product();

            $product->posid         = 1;
            $product->code          = 'AU17-'.$data['code'];
            $product->name          = $data['name'];
            $product->unit_id       = $data['unit'];
            $product->brand_id          = $data['brand'];
            $product->price             = $data['price'];
            $product->description       = "Demo Description";
            $product->created_by = 1;

            $product->save();

            DB::table('category_product')->insert([
                'product_id' => $product->id,
                'category_id' => $data['category']
            ]);
        }// end loop

        fclose($file);
    }
}
