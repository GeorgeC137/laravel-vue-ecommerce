<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function PHPSTORM_META\map;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->string('path', 255);
            $table->string('url', 255);
            $table->string('mime', 55);
            $table->integer('size');
            $table->integer('position');
            $table->timestamps();
        });

        DB::table('products')
            ->chunkById(100, function (Collection $products) {
                $products = $products->filter(fn($p) => (bool)$p->image && $p->image_mime !== null)->map(function ($p) {
                    return [
                        'product_id' => $p->id,
                        'path' => '',
                        'url' => $p->image,
                        'mime' => $p->image_mime,
                        'size' => $p->image_size,
                        'position' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                });

                DB::table('product_images')->insert($products->all());
            });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('image_mime');
            $table->dropColumn('image_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->nullable()->after('slug');
            $table->string('image_mime')->nullable()->after('image');
            $table->integer('image_size')->nullable()->after('image_mime');
        });

        DB::table('products')
            ->chunkById(100, function (Collection $products) {
                foreach ($products as $product) {
                    $image = DB::table('product_images')
                        ->select(['mime', 'size', 'url'])
                        ->where('product_id', $product->id)
                        ->first();

                    if ($image) {
                        DB::table('products')
                            ->where('id', $product->id)
                            ->update([
                                'image' => $image->url,
                                'image_size' => $image->size,
                                'image_mime' => $image->mime,
                            ]);
                    }
                }
            });

        Schema::dropIfExists('product_images');
    }
};
