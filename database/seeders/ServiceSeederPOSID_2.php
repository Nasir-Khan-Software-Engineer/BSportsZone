<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStock;

class ServiceSeederPOSID_2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/data/service_add_posid_2.csv');

         if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: $csvFile");
            return;
        }

        $file = fopen($csvFile, 'r');
        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {

            $data = array_combine($header, $row);

            $product = new Product();

            $product->posid         = 2;
            $product->code          = 'AR17-'.$data['code'];
            $product->name          = $data['name'];
            $product->status        = "completed";
            $product->shop_id       = 1;
            $product->unit_id       = $data['unit'];

            $product->cost              = $data['price'];
            $product->quantity          = 1000;
            $product->stock_alert       = 10;
            $product->brand_id          = $data['brand'];
            $product->supplier_id       = 1;
            $product->buying_warranty   = "1 Year";
            $product->buying_guarantee  = "None";
            $product->buying_note       = "This is text note";
            
            $product->price             = $data['price'];
            $product->max_sale_qty      = 5;
            $product->min_sale_qty      = 1;
            $product->tax_type          = "percentage";
            $product->tax_value         = 5;
            $product->discount_type     = "percentage";
            $product->discount_value    = 5;
            $product->selling_warranty  = "1 Year";
            $product->selling_guarantee = "None";
            $product->selling_note      = "This is text note";
            
            $product->description       = "Demo Description";
            $product->created_by = 3;

            $product->save();

            DB::table('category_product')->insert([
                'product_id' => $product->id,
                'category_id' => $data['category']
            ]);

            $ps =  new ProductStock();
            $ps->product_id =  $product->id;
            $ps->change_type = 'IN';
            $ps->quantity = 100;
            $ps->price = $data['price'];
            $ps->discount = 0;
            $ps->tax = 0;
            $ps->reference_type = 'PURCHASE';
            $ps->created_by = 3;

            $ps->save();
            
            
        }// end loop

        fclose($file);
    }
}
