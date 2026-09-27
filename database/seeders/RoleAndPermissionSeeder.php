<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all granular system permissions
        $permissions = [
            // Student Permissions
            'view_students',
            'create_students',
            'edit_students',
            'delete_students',
            'transfer_students',
            'print_student_id',

            // Academic & Curriculum
            'view_courses',
            'manage_courses',
            'view_batches',
            'manage_batches',
            'view_attendance',
            'mark_attendance',
            'view_study_materials',
            'manage_study_materials',
            'view_exams',
            'manage_exams',
            'evaluate_exams',
            'view_certificates',
            'issue_certificates',

            // Financial & Billing
            'view_fee_invoices',
            'manage_fee_invoices',
            'view_payments',
            'record_payments',
            'print_receipts',

            // Branch & Staff Administration
            'view_branches',
            'manage_branches',
            'view_trainers',
            'manage_trainers',
            'view_announcements',
            'manage_announcements',
            'manage_roles_permissions',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 2. Define Roles and grant permissions
        // Role: Super Admin
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        // Role: Franchise Owner (Full control of their franchise)
        $ownerRole = Role::firstOrCreate(['name' => 'franchise_owner', 'guard_name' => 'web']);
        $ownerRole->syncPermissions(Permission::all());

        // Role: Branch Admin
        $branchAdminRole = Role::firstOrCreate(['name' => 'branch_admin', 'guard_name' => 'web']);
        $branchAdminRole->syncPermissions([
            'view_students', 'create_students', 'edit_students', 'transfer_students', 'print_student_id',
            'view_courses', 'view_batches', 'manage_batches', 'view_attendance', 'mark_attendance',
            'view_study_materials', 'view_fee_invoices', 'manage_fee_invoices', 'view_payments',
            'record_payments', 'print_receipts', 'view_trainers', 'manage_trainers',
            'view_exams', 'evaluate_exams', 'view_certificates', 'view_announcements',
        ]);

        // Role: Trainer (Faculty) - Strictly academics & attendance; NO financials or branches
        $trainerRole = Role::firstOrCreate(['name' => 'trainer', 'guard_name' => 'web']);
        $trainerRole->syncPermissions([
            'view_students',
            'view_courses',
            'view_batches',
            'view_attendance',
            'mark_attendance',
            'view_study_materials',
            'manage_study_materials',
            'view_exams',
            'manage_exams',
            'evaluate_exams',
            'view_announcements',
        ]);

        // Role: Accountant - Strictly finance & student billing; NO syllabus, exams or branch settings
        $accountantRole = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $accountantRole->syncPermissions([
            'view_students',
            'view_fee_invoices',
            'manage_fee_invoices',
            'view_payments',
            'record_payments',
            'print_receipts',
            'view_announcements',
        ]);

        // Role: Student
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $studentRole->syncPermissions([
            'view_courses',
            'view_study_materials',
            'view_exams',
            'view_certificates',
        ]);

        // 3. Assign Spatie roles to all existing users based on their current user.role
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role && Role::where('name', $user->role)->exists()) {
                $user->syncRoles([$user->role]);
            }
        }
    }
}
