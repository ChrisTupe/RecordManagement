<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create departments
        $finDept = Department::create([
            'name' => 'Finance Dept.',
            'code' => 'FIN',
            'description' => 'Finance Department',
            'is_active' => true,
        ]);

        $hrDept = Department::create([
            'name' => 'HRD',
            'code' => 'HR',
            'description' => 'Human Resources Department',
            'is_active' => true,
        ]);

        $rmDept = Department::create([
            'name' => 'Records Management Div',
            'code' => 'RM',
            'description' => 'Records Management Division',
            'is_active' => true,
        ]);

        // Create admin user in Finance
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'department_id' => $finDept->id,
        ]);

        // Create test user in HR
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'department_id' => $hrDept->id,
        ]);

        // Create another user in Finance (same department as admin)
        User::factory()->create([
            'name' => 'Finance User',
            'email' => 'finance@example.com',
            'password' => Hash::make('password123'),
            'department_id' => $finDept->id,
        ]);
    }
}
