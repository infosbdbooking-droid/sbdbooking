<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('car', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('car', 'car_name')) {
                $table->string('car_name', 100)->nullable()->after('car_type_id');
            }
            if (!Schema::hasColumn('car', 'max_passengers')) {
                $table->integer('max_passengers')->nullable()->after('car_seats');
            }
            if (!Schema::hasColumn('car', 'max_bags')) {
                $table->integer('max_bags')->nullable()->after('max_passengers');
            }
            if (!Schema::hasColumn('car', 'min_trip_amount')) {
                $table->decimal('min_trip_amount', 10, 2)->nullable()->after('max_bags');
            }
            if (!Schema::hasColumn('car', 'booking_includes')) {
                $table->json('booking_includes')->nullable()->after('min_trip_amount');
            }
            if (!Schema::hasColumn('car', 'why_book_us')) {
                $table->json('why_book_us')->nullable()->after('booking_includes');
            }
            if (!Schema::hasColumn('car', 'trip_policies')) {
                $table->json('trip_policies')->nullable()->after('why_book_us');
            }
            if (!Schema::hasColumn('car', 'recent_reviews')) {
                $table->json('recent_reviews')->nullable()->after('trip_policies');
            }
            if (!Schema::hasColumn('car', 'rating_summary')) {
                $table->decimal('rating_summary', 3, 2)->nullable()->after('recent_reviews');
            }
            if (!Schema::hasColumn('car', 'rating_value')) {
                $table->decimal('rating_value', 3, 2)->nullable()->after('rating_summary');
            }
            if (!Schema::hasColumn('car', 'rating_count')) {
                $table->integer('rating_count')->nullable()->after('rating_value');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car', function (Blueprint $table) {
            $columns = ['car_name', 'max_passengers', 'max_bags', 'min_trip_amount', 
                       'booking_includes', 'why_book_us', 'trip_policies', 'recent_reviews',
                       'rating_summary', 'rating_value', 'rating_count'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('car', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
