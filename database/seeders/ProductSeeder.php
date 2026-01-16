<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Variation;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
use App\Models\MediaImage;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Generate slug from text (same logic as JavaScript)
     */
    private function generateSlug(string $text): string
    {
        if (empty($text)) {
            return '';
        }
        
        // Convert to lowercase
        $slug = strtolower($text);
        
        // Replace spaces and special characters with hyphens
        $slug = preg_replace('/[^\w\s-]/', '', $slug); // Remove special characters except word chars, spaces, and hyphens
        $slug = preg_replace('/\s+/', '-', $slug); // Replace spaces with hyphens
        $slug = preg_replace('/-+/', '-', $slug); // Replace multiple hyphens with single hyphen
        $slug = trim($slug, '-'); // Remove leading and trailing hyphens
        
        // Limit to 100 characters
        if (strlen($slug) > 100) {
            $slug = substr($slug, 0, 100);
            // Remove trailing hyphen if exists
            $slug = rtrim($slug, '-');
        }
        
        return $slug;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $POSID = 1;
        $createdBy = 1;

        // Get existing brands, categories, and units
        $brands = Brand::where('POSID', $POSID)->pluck('id')->toArray();
        $categories = Category::where('POSID', $POSID)->pluck('id')->toArray();
        $units = Unit::where('POSID', $POSID)->pluck('id')->toArray();

        if (empty($brands) || empty($categories) || empty($units)) {
            $this->command->warn('Please run BrandSeeder, CategorySeeder, and UnitSeeder first.');
            return;
        }

        // Size options for variations
        $sizeOptions = ['S-Size', 'M-Size', 'L-Size', 'XL-Size'];

        // Jersey names - Football and Cricket teams
        $teams = [
            'Argentina', 'Brazil', 'France', 'Germany', 'Spain', 'England', 'Italy', 'Portugal',
            'Netherlands', 'Belgium', 'Croatia', 'Uruguay', 'Mexico', 'Japan', 'South Korea',
            'India', 'Pakistan', 'Australia', 'Bangladesh', 'Sri Lanka', 'New Zealand',
            'South Africa', 'West Indies', 'Afghanistan', 'Ireland', 'Scotland', 'Zimbabwe',
            'Kenya', 'Nepal', 'Oman', 'UAE', 'Hong Kong', 'Singapore', 'Malaysia', 'Thailand'
        ];

        $jerseyTypes = [
            'Home Jersey', 'Away Jersey', 'Third Jersey', 'Goalkeeper Jersey',
            'Training Jersey', 'Match Jersey', 'Replica Jersey', 'Authentic Jersey'
        ];

        $sleeveTypes = ['Half Sleeve', 'Full Sleeve', 'Sleeveless'];

        // Create MediaImage records for the 3 images
        $imageFiles = [
            'argentina.png',
            'argentina-back.png',
            'agentina-logo.png'
        ];

        $sourceImagePath = public_path('website/images');
        $targetDirectory = public_path("images/{$POSID}/Product");

        // Create target directory if it doesn't exist
        if (!File::exists($targetDirectory)) {
            File::makeDirectory($targetDirectory, 0755, true);
        }

        $mediaImages = [];
        foreach ($imageFiles as $imageFile) {
            $sourcePath = $sourceImagePath . '/' . $imageFile;
            
            if (!File::exists($sourcePath)) {
                $this->command->warn("Image file not found: {$sourcePath}");
                continue;
            }

            // Check if MediaImage already exists (check by file_name pattern)
            $existingMediaImage = MediaImage::where('POSID', $POSID)
                ->where('relation', 'Product')
                ->where('file_name', 'like', pathinfo($imageFile, PATHINFO_FILENAME) . '_%')
                ->first();

            if ($existingMediaImage) {
                // Use existing MediaImage
                $mediaImages[$imageFile] = $existingMediaImage;
                continue;
            }

            // Generate file name with timestamp
            $timestamp = now()->format('Y-m-d_H-i-s');
            $extension = pathinfo($imageFile, PATHINFO_EXTENSION);
            $fileNameWithoutExt = pathinfo($imageFile, PATHINFO_FILENAME);
            $fileName = $fileNameWithoutExt . '_' . $timestamp . '.' . $extension;
            $targetPath = $targetDirectory . '/' . $fileName;

            // Copy file to target directory
            File::copy($sourcePath, $targetPath);

            // Get file size
            $fileSize = File::size($targetPath);

            // Create MediaImage record
            $mediaImage = new MediaImage();
            $mediaImage->POSID = $POSID;
            $mediaImage->file_name = $fileName;
            $mediaImage->file_path = "images/{$POSID}/Product/{$fileName}";
            $mediaImage->size = $fileSize;
            $mediaImage->type = $extension;
            $mediaImage->relation = 'Product';
            $mediaImage->created_by = $createdBy;
            $mediaImage->save();

            $mediaImages[$imageFile] = $mediaImage;
        }

        if (empty($mediaImages)) {
            $this->command->error('No images were processed. Please check image files.');
            return;
        }

        // Generate 35 unique product names
        $productNames = [];
        $productCounter = 1;

        foreach ($teams as $team) {
            if (count($productNames) >= 35) break;

            foreach ($jerseyTypes as $jerseyType) {
                if (count($productNames) >= 35) break;

                foreach ($sleeveTypes as $sleeveType) {
                    if (count($productNames) >= 35) break;

                    $year = 2026;
                    $productName = "{$team} {$jerseyType} {$year} {$sleeveType}";
                    $productNames[] = $productName;
                }
            }
        }

        // If we don't have 35, add more variations
        while (count($productNames) < 35) {
            $team = $teams[array_rand($teams)];
            $jerseyType = $jerseyTypes[array_rand($jerseyTypes)];
            $sleeveType = $sleeveTypes[array_rand($sleeveTypes)];
            $year = 2026;
            $productName = "{$team} {$jerseyType} {$year} {$sleeveType}";
            
            if (!in_array($productName, $productNames)) {
                $productNames[] = $productName;
            }
        }

        // Create or update 35 products
        foreach ($productNames as $index => $productName) {
            // Select random brand, unit, and category
            $brandId = $brands[array_rand($brands)];
            $unitId = $units[array_rand($units)];
            $categoryId = $categories[array_rand($categories)];

            // Generate product code
            $productCode = 'PRD-' . str_pad($productCounter, 4, '0', STR_PAD_LEFT);

            // Random price between 500 and 1200
            $price = rand(500, 1200);
            
            // Fixed 10% discount
            $discountType = 'percentage';
            $discountValue = 10;

            // Check if product exists
            $product = Product::where('POSID', $POSID)
                ->where(function($query) use ($productCode, $productName) {
                    $query->where('code', $productCode)
                          ->orWhere('name', $productName);
                })
                ->first();

            // Generate SEO keyword and description
            $seoKeyword = strtolower(str_replace(' ', ', ', $productName)) . ', jersey, sports, football, cricket';
            $seoDescription = "Shop premium quality {$productName} online. Authentic design, comfortable fit, and durable materials. Perfect for fans and players. Available in multiple sizes with 10% discount.";

            // Mid-long description
            $description = '
                <h2 class="">2026 Official Team Jersey – Premium Edition</h2>

<p>Step into the spirit of the game with the <strong>2026 Official Team Jersey</strong>. Designed for both athletes and fans, this jersey combines <strong>modern aesthetics</strong>, <strong>premium comfort</strong>, and <strong>team pride</strong>. Whether you’re on the field, in the stands, or showing off your allegiance, this jersey delivers style, functionality, and durability.</p>

<h2>Why Choose This Jersey?</h2>
<ul>
    <li><strong>Officially Licensed:</strong> 100% authentic team merchandise with official logos and colors.</li>
    <li><strong>Premium Fabric:</strong> Lightweight, breathable, and moisture-wicking polyester for all-day comfort.</li>
    <li><strong>Enhanced Durability:</strong> Reinforced stitching to withstand intensive play and repeated washes.</li>
    <li><strong>Vibrant Colors:</strong> Long-lasting colors that won’t fade after washing.</li>
    <li><strong>Performance Fit:</strong> Ergonomic design for ease of movement and optimal comfort.</li>
</ul>

<h2>Material &amp; Technology</h2>
<p>This jersey uses <strong>advanced sports fabric technology</strong> to keep you cool and dry. Key features include:</p>
<ul>
    <li><strong>Moisture-Wicking Fabric:</strong> Draws sweat away from the skin to keep you dry during intense activity.</li>
    <li><strong>Breathable Mesh Panels:</strong> Strategically placed for enhanced airflow.</li>
    <li><strong>Anti-Odor Treatment:</strong> Keeps your jersey fresh longer.</li>
    <li><strong>Quick-Dry:</strong> Fabric dries fast for convenience after sports or washing.</li>
</ul>

<h2>Size Guide</h2>
<p>Please refer to our detailed size chart to find the perfect fit. Measurements are in inches.</p>

<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr>
            <th>Size</th>
            <th>Chest</th>
            <th>Waist</th>
            <th>Length</th>
            <th>Sleeve</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Small (S)</td>
            <td>34 - 36</td>
            <td>28 - 30</td>
            <td>27</td>
            <td>24</td>
        </tr>
        <tr>
            <td>Medium (M)</td>
            <td>38 - 40</td>
            <td>32 - 34</td>
            <td>28</td>
            <td>25</td>
        </tr>
        <tr>
            <td>Large (L)</td>
            <td>42 - 44</td>
            <td>36 - 38</td>
            <td>29</td>
            <td>26</td>
        </tr>
        <tr>
            <td>Extra Large (XL)</td>
            <td>46 - 48</td>
            <td>40 - 42</td>
            <td>30</td>
            <td>27</td>
        </tr>
        <tr>
            <td>XXL</td>
            <td>50 - 52</td>
            <td>44 - 46</td>
            <td>31</td>
            <td>28</td>
        </tr>
    </tbody>
</table>

<h2>How to Measure</h2>
<ul>
    <li><strong>Chest:</strong> Measure around the fullest part of your chest.</li>
    <li><strong>Waist:</strong> Measure around your natural waistline.</li>
    <li><strong>Length:</strong> Measure from shoulder to hemline.</li>
    <li><strong>Sleeve:</strong> Measure from shoulder seam to cuff.</li>
</ul>

<h2>Care Instructions</h2>
<ul>
    <li>Machine wash cold with like colors.</li>
    <li>Do not bleach or iron directly on the logo.</li>
    <li>Tumble dry low or hang to dry.</li>
    <li>Do not dry clean.</li>
</ul>

<h2>Customization Options</h2>
<p>Make your jersey truly yours! You can add:</p>
<ul>
    <li>Player name and number on the back</li>
    <li>Special patches or badges</li>
    <li>Limited edition prints (where applicable)</li>
</ul>

<h2>FAQs</h2>
<dl>
    <dt>Q: Is this the official team merchandise?</dt>
    <dd>A: Yes! This is 100% authentic, licensed merchandise from the official team.</dd>

    <dt>Q: Can I customize my jersey?</dt>
    <dd>A: Yes, you can add your name and number at checkout.</dd>

    <dt>Q: Does the jersey run true to size?</dt>
    <dd>A: Yes, please refer to the size chart above for accurate measurements.</dd>

    <dt>Q: Is the jersey suitable for sports activities?</dt>
    <dd>A: Absolutely! It’s designed with breathable, lightweight fabric perfect for active wear.</dd>

    <dt>Q: How long will it take to ship?</dt>
    <dd>A: Typically 3-7 business days, depending on your location.</dd>

    <dt>Q: Can I return the jersey if it doesn’t fit?</dt>
    <dd>A: Yes, we offer a 14-day return policy for unworn items.</dd>
</dl>

<h2>Customer Reviews</h2>
<p><em>"High-quality jersey! Fits perfectly and feels amazing on game day." – Samantha R.</em></p>
<p><em>"The colors are vibrant and the material is breathable. Love it!" – Michael P.</em></p>
<p><em>"Fast delivery and excellent quality. Will buy more for my family." – Jordan L.</em></p>

<h2>Why Fans Love It</h2>
<ul>
    <li>Show your team spirit in style</li>
    <li>Wearable comfort for games or casual outings</li>
    <li>Durable, long-lasting, and easy to care for</li>
</ul>

<p><strong>Order your 2026 Official Team Jersey today and join the winning team in style!</strong></p>
            
            ';

            // Generate slug from product name
            $slug = $this->generateSlug($productName);
            
            // Ensure slug is unique by appending a number if needed
            $baseSlug = $slug;
            $slugCounter = 1;
            $productIdToExclude = $product ? $product->id : null;
            
            while (Product::where('POSID', $POSID)
                ->where('slug', $slug)
                ->where('type', 'Product')
                ->when($productIdToExclude, function($query) use ($productIdToExclude) {
                    return $query->where('id', '!=', $productIdToExclude);
                })
                ->exists()) {
                $slug = $baseSlug . '-' . $slugCounter;
                $slugCounter++;
            }

            if ($product) {
                // Update existing product
                $product->code = $productCode;
                $product->name = $productName;
                $product->slug = $slug;
                $product->type = 'Product';
                $product->price = $price;
                $product->description = $description;
                $product->discount_type = $discountType;
                $product->discount_value = $discountValue;
                $product->brand_id = $brandId;
                $product->unit_id = $unitId;
                $product->image = 'argentina.png';
                $product->is_published = false;
                $product->seo_keyword = $seoKeyword;
                $product->seo_description = $seoDescription;
                $product->updated_by = $createdBy;
                $product->save();

                // Sync category (removes old and adds new)
                $product->categories()->sync([$categoryId]);
            } else {
                // Create new product
                $product = new Product();
                $product->POSID = $POSID;
                $product->code = $productCode;
                $product->name = $productName;
                $product->slug = $slug;
                $product->type = 'Product';
                $product->price = $price;
                $product->description = $description;
                $product->discount_type = $discountType;
                $product->discount_value = $discountValue;
                $product->brand_id = $brandId;
                $product->unit_id = $unitId;
                $product->image = 'argentina.png';
                $product->is_published = false;
                $product->seo_keyword = $seoKeyword;
                $product->seo_description = $seoDescription;
                $product->created_by = $createdBy;
                $product->save();

                // Attach category
                $product->categories()->attach($categoryId);
            }

            // Delete existing variations and create new ones
            Variation::where('product_id', $product->id)
                ->where('POSID', $POSID)
                ->delete();

            // Create 1-4 variations
            $variationCount = rand(1, 4);
            $usedSizes = [];

            for ($j = 0; $j < $variationCount; $j++) {
                // Select a unique size
                $availableSizes = array_diff($sizeOptions, $usedSizes);
                if (empty($availableSizes)) {
                    break; // All sizes used
                }

                $sizeTag = $availableSizes[array_rand($availableSizes)];
                $usedSizes[] = $sizeTag;

                $variation = new Variation();
                $variation->POSID = $POSID;
                $variation->product_id = $product->id;
                $variation->tagline = $sizeTag;
                $variation->description = "{$productName} - {$sizeTag}";
                $variation->selling_price = $price; // Same as product price
                $variation->stock = 0; // Always 0
                $variation->status = 'active';
                $variation->discount_type = $discountType; // Same as product (10% fixed)
                $variation->discount_value = $discountValue; // Same as product (10% fixed)
                $variation->save();
            }

            // Get existing product images
            $existingProductImages = ProductImage::where('product_id', $product->id)
                ->where('POSID', $POSID)
                ->get()
                ->keyBy('image_name');

            // Create or update ProductImage records for all 3 images
            $isFirstDefault = true;
            foreach ($mediaImages as $imageFile => $mediaImage) {
                $existingProductImage = $existingProductImages->get($mediaImage->file_name);

                if ($existingProductImage) {
                    // Update existing product image
                    $existingProductImage->is_default = ($isFirstDefault && $imageFile === 'argentina.png');
                    $existingProductImage->updated_by = $createdBy;
                    $existingProductImage->save();
                } else {
                    // Create new product image
                    $productImage = new ProductImage();
                    $productImage->POSID = $POSID;
                    $productImage->product_id = $product->id;
                    $productImage->image_name = $mediaImage->file_name;
                    // Set argentina.png as default (first one)
                    $productImage->is_default = ($isFirstDefault && $imageFile === 'argentina.png');
                    $productImage->created_by = $createdBy;
                    $productImage->save();
                }
                
                if ($isFirstDefault && $imageFile === 'argentina.png') {
                    $isFirstDefault = false;
                }
            }

            // Remove product images that are no longer in the mediaImages list
            $validImageNames = array_map(function($mediaImage) {
                return $mediaImage->file_name;
            }, $mediaImages);
            
            ProductImage::where('product_id', $product->id)
                ->where('POSID', $POSID)
                ->whereNotIn('image_name', $validImageNames)
                ->delete();

            $productCounter++;
        }

        $this->command->info("✅ Created/Updated 35 products with variations and images successfully!");
    }
}
