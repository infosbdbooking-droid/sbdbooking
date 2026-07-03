<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'alternate_mobile')) {
                $table->string('alternate_mobile')->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('users', 'aadhaar_number')) {
                $table->string('aadhaar_number')->nullable()->after('alternate_mobile');
            }
            if (!Schema::hasColumn('users', 'aadhaar_file')) {
                $table->string('aadhaar_file')->nullable()->after('aadhaar_number');
            }
            if (!Schema::hasColumn('users', 'pan_number')) {
                $table->string('pan_number')->nullable()->after('aadhaar_file');
            }
            if (!Schema::hasColumn('users', 'pan_file')) {
                $table->string('pan_file')->nullable()->after('pan_number');
            }
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('pan_file');
            }
            if (!Schema::hasColumn('users', 'gst_number')) {
                $table->string('gst_number')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('gst_number');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'pincode')) {
                $table->string('pincode')->nullable()->after('state');
            }
            if (!Schema::hasColumn('users', 'profile_status')) {
                $table->string('profile_status')->default('Pending')->after('status'); // Pending, Approved, Rejected
            }
            if (!Schema::hasColumn('users', 'profile_verified_at')) {
                $table->timestamp('profile_verified_at')->nullable()->after('profile_status');
            }
            if (!Schema::hasColumn('users', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('profile_verified_at');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('users', 'commission_type')) {
                $table->string('commission_type')->default('percentage')->after('rejection_reason'); // percentage, flat
            }
            if (!Schema::hasColumn('users', 'commission_percentage')) {
                $table->decimal('commission_percentage', 5, 2)->nullable()->after('commission_type');
            }
            if (!Schema::hasColumn('users', 'flat_commission')) {
                $table->decimal('flat_commission', 10, 2)->nullable()->after('commission_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }
            $columnsToDrop = [
                'mobile',
                'alternate_mobile',
                'aadhaar_number',
                'aadhaar_file',
                'pan_number',
                'pan_file',
                'photo',
                'gst_number',
                'address',
                'city',
                'state',
                'pincode',
                'profile_status',
                'profile_verified_at',
                'rejection_reason',
                'commission_type',
                'commission_percentage',
                'flat_commission',
            ];
            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
