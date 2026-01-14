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

            // Check if MediaImage already exists
            $existingMediaImage = MediaImage::where('POSID', $POSID)
                ->where('name', $imageFile)
                ->where('relation', 'Product')
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
            $mediaImage->name = $imageFile;
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
            $description = "Experience the ultimate in sports apparel with our premium {$productName}. Crafted with meticulous attention to detail, this jersey combines authentic design elements with modern comfort technology. Made from high-quality, breathable fabric that wicks away moisture, keeping you cool and dry during intense matches or casual wear. The jersey features official team colors, embroidered logos, and reinforced stitching for enhanced durability. Whether you're supporting your favorite team from the stands or playing on the field, this jersey offers the perfect blend of style, comfort, and performance. Available in multiple sizes to ensure the perfect fit for every fan and player.";

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
                $existingProductImage = $existingProductImages->get($mediaImage->name);

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
                    $productImage->image_name = $mediaImage->name;
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
                return $mediaImage->name;
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
