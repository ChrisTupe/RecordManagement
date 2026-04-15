<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Office of the President and CEO', 'code' => 'OP', 'description' => 'Executive office'],
            ['name' => 'Office of the Vice-President for Admin and Finance Group', 'code' => 'VP-AFG', 'description' => 'Vice President for Administration and Finance'],
            ['name' => 'Office of the Vice-President for Operation and Business Devt Group', 'code' => 'VP-OBG', 'description' => 'Vice President for Operations and Business Development'],
            ['name' => 'Office of the Assistant Vice-President for Legal Services', 'code' => 'AVP-LS', 'description' => 'Legal Services Office'],
            ['name' => 'Strategy and Corporate Management Dept.', 'code' => 'SCM', 'description' => 'Strategic planning and corporate management'],
            ['name' => 'Management System Information Div', 'code' => 'MSID', 'description' => 'Management System Information Division'],
            ['name' => 'Management Information System Dept', 'code' => 'MIS', 'description' => 'Information technology and systems'],
            ['name' => 'Records Management Div', 'code' => 'RMD', 'description' => 'Records management and documentation'],
            ['name' => 'Finance Dept.', 'code' => 'FIN', 'description' => 'Financial management'],
            ['name' => 'New Business Venture Unit', 'code' => 'NBV', 'description' => 'New business ventures'],
            ['name' => 'Admin. Dept.', 'code' => 'ADMIN', 'description' => 'Administrative department'],
            ['name' => 'Marketing Dept.', 'code' => 'MKT', 'description' => 'Marketing and business development'],
            ['name' => 'Engineering Dept.', 'code' => 'ENG', 'description' => 'Engineering department'],
            ['name' => 'Security Dept.', 'code' => 'SEC', 'description' => 'Security and safety'],
            ['name' => 'Public Affairs Div.', 'code' => 'PA', 'description' => 'Public relations and affairs'],
            ['name' => 'HRD', 'code' => 'HRD', 'description' => 'Human Resources Development'],
            ['name' => 'COA', 'code' => 'COA', 'description' => 'Commission on Audit'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['name' => $department['name']],
                [
                    'code' => $department['code'],
                    'description' => $department['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
