<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tbl_permissions')->insert([
            [
                'id' => 1,
                'name' => 'View Dashboard',
                'slug' => 'view-dashboard',
                'description' => 'Quyền view dashboard',
                'module' => 'Dashboard',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 2,
                'name' => 'View Member',
                'slug' => 'view-member',
                'description' => 'Quyền view member',
                'module' => 'Member',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 3,
                'name' => 'Create Member',
                'slug' => 'create-member',
                'description' => 'Quyền create member',
                'module' => 'Member',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 4,
                'name' => 'Edit Member',
                'slug' => 'edit-member',
                'description' => 'Quyền edit member',
                'module' => 'Member',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 5,
                'name' => 'Delete Member',
                'slug' => 'delete-member',
                'description' => 'Quyền delete member',
                'module' => 'Member',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 6,
                'name' => 'View Team',
                'slug' => 'view-team',
                'description' => 'Quyền view team',
                'module' => 'Team',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 7,
                'name' => 'Create Team',
                'slug' => 'create-team',
                'description' => 'Quyền create team',
                'module' => 'Team',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 8,
                'name' => 'Edit Team',
                'slug' => 'edit-team',
                'description' => 'Quyền edit team',
                'module' => 'Team',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 9,
                'name' => 'Delete Team',
                'slug' => 'delete-team',
                'description' => 'Quyền delete team',
                'module' => 'Team',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 10,
                'name' => 'View Role',
                'slug' => 'view-role',
                'description' => 'Quyền view role',
                'module' => 'Role',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 11,
                'name' => 'Edit Role',
                'slug' => 'edit-role',
                'description' => 'Quyền edit role',
                'module' => 'Role',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 12,
                'name' => 'View Task',
                'slug' => 'view-task',
                'description' => 'Quyền view task',
                'module' => 'Task',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 13,
                'name' => 'Create Task',
                'slug' => 'create-task',
                'description' => 'Quyền create task',
                'module' => 'Task',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 14,
                'name' => 'Edit Task',
                'slug' => 'edit-task',
                'description' => 'Quyền edit task',
                'module' => 'Task',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 15,
                'name' => 'Delete Task',
                'slug' => 'delete-task',
                'description' => 'Quyền delete task',
                'module' => 'Task',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 16,
                'name' => 'Assign Task',
                'slug' => 'assign-task',
                'description' => 'Quyền assign task',
                'module' => 'Task',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 17,
                'name' => 'View Customer',
                'slug' => 'view-customer',
                'description' => 'Quyền view customer',
                'module' => 'Customer',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 18,
                'name' => 'Create Customer',
                'slug' => 'create-customer',
                'description' => 'Quyền create customer',
                'module' => 'Customer',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 19,
                'name' => 'Edit Customer',
                'slug' => 'edit-customer',
                'description' => 'Quyền edit customer',
                'module' => 'Customer',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 20,
                'name' => 'Delete Customer',
                'slug' => 'delete-customer',
                'description' => 'Quyền delete customer',
                'module' => 'Customer',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 21,
                'name' => 'Support Customer',
                'slug' => 'support-customer',
                'description' => 'Quyền support customer',
                'module' => 'Customer',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 22,
                'name' => 'View Contract',
                'slug' => 'view-contract',
                'description' => 'Quyền view contract',
                'module' => 'Contract',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 23,
                'name' => 'Create Contract',
                'slug' => 'create-contract',
                'description' => 'Quyền create contract',
                'module' => 'Contract',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 24,
                'name' => 'Edit Contract',
                'slug' => 'edit-contract',
                'description' => 'Quyền edit contract',
                'module' => 'Contract',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 25,
                'name' => 'Delete Contract',
                'slug' => 'delete-contract',
                'description' => 'Quyền delete contract',
                'module' => 'Contract',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ],
            [
                'id' => 26,
                'name' => 'Approve Contract',
                'slug' => 'approve-contract',
                'description' => 'Quyền approve contract',
                'module' => 'Contract',
                'created_at' => '2025-04-25 07:57:35',
                'updated_at' => '2025-04-25 07:57:35'
            ]
        ]);
    }
}