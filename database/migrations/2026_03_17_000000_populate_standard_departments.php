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
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing departments
        DB::table('departments')->truncate();

        // Insert the standard departments
        $departments = [
            ['name' => 'Office of the President and CEO', 'code' => 'OP', 'description' => 'Executive office', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Office of the Vice-President for Admin and Finance Group', 'code' => 'VP-AFG', 'description' => 'Vice President for Administration and Finance', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Office of the Vice-President for Operation and Business Devt Group', 'code' => 'VP-OBG', 'description' => 'Vice President for Operations and Business Development', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Office of the Assistant Vice-President for Legal Services', 'code' => 'AVP-LS', 'description' => 'Legal Services Office', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Strategy and Corporate Management Dept.', 'code' => 'SCM', 'description' => 'Strategic planning and corporate management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Management System Information Div', 'code' => 'MSID', 'description' => 'Management System Information Division', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Management Information System Dept', 'code' => 'MIS', 'description' => 'Information technology and systems', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Records Management Div', 'code' => 'RMD', 'description' => 'Records management and documentation', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finance Dept.', 'code' => 'FIN', 'description' => 'Financial management', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'New Business Venture Unit', 'code' => 'NBV', 'description' => 'New business ventures', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin. Dept.', 'code' => 'ADMIN', 'description' => 'Administrative department', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marketing Dept.', 'code' => 'MKT', 'description' => 'Marketing and business development', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Engineering Dept.', 'code' => 'ENG', 'description' => 'Engineering department', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Security Dept.', 'code' => 'SEC', 'description' => 'Security and safety', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Public Affairs Div.', 'code' => 'PA', 'description' => 'Public relations and affairs', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HRD', 'code' => 'HRD', 'description' => 'Human Resources Development', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'COA', 'code' => 'COA', 'description' => 'Commission on Audit', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('departments')->insert($departments);

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('departments')->truncate();
    }
};
