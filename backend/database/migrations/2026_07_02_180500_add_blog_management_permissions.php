<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'blogs_dashboard',
            'blogs',
            'blog_categories',
            'blog_tags',
            'blog_comments',
            'blog_settings'
        ];

        foreach ($permissions as $permTitle) {
            $permissionId = DB::table('permissions')->where('title', $permTitle)->value('id');
            if (!$permissionId) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'title' => $permTitle,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Assign to Super Admin (role_id = 1)
            $existsSuper = DB::table('permission_role')
                ->where('role_id', 1)
                ->where('permission_id', $permissionId)
                ->exists();
            if (!$existsSuper) {
                DB::table('permission_role')->insert([
                    'role_id' => 1,
                    'permission_id' => $permissionId,
                ]);
            }

            // Assign to Admin (role_id = 13)
            $existsAdmin = DB::table('permission_role')
                ->where('role_id', 13)
                ->where('permission_id', $permissionId)
                ->exists();
            if (!$existsAdmin) {
                DB::table('permission_role')->insert([
                    'role_id' => 13,
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
        $permissions = [
            'blogs_dashboard',
            'blogs',
            'blog_categories',
            'blog_tags',
            'blog_comments',
            'blog_settings'
        ];

        foreach ($permissions as $permTitle) {
            $permissionId = DB::table('permissions')->where('title', $permTitle)->value('id');
            if ($permissionId) {
                DB::table('permission_role')->where('permission_id', $permissionId)->delete();
                DB::table('permissions')->where('id', $permissionId)->delete();
            }
        }
    }
};
