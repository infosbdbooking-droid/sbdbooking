<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Insert 'contact_messages' permission if not exists
        $permissionId = DB::table('permissions')->where('title', 'contact_messages')->value('id');
        if (!$permissionId) {
            $permissionId = DB::table('permissions')->insertGetId([
                'title' => 'contact_messages',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Assign this permission to the Admin role (role_id = 1)
        $adminRole = DB::table('roles')->where('id', 1)->first();
        if ($adminRole && $permissionId) {
            $exists = DB::table('permission_role')
                ->where('role_id', $adminRole->id)
                ->where('permission_id', $permissionId)
                ->exists();
            if (!$exists) {
                DB::table('permission_role')->insert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('title', 'contact_messages')->value('id');
        if ($permissionId) {
            DB::table('permission_role')->where('permission_id', $permissionId)->delete();
            DB::table('permissions')->where('id', $permissionId)->delete();
        }
    }
};
