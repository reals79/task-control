<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tableNames = config('permission.table_names');

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        DB::table($tableNames['roles'])->truncate();
        DB::table($tableNames['permissions'])->truncate();

        // create permissions
        Permission::create(['title' => 'Управление организациями', 'name' => 'companies']);
        Permission::create(['title' => 'Управление пользователями', 'name' => 'users']);
        Permission::create(['title' => 'Управление отделами', 'name' => 'departments']);
        Permission::create(['title' => 'Управление задачами', 'name' => 'tasks']);

        $role0 = Role::create(['title' => 'Super Admin', 'name' => 'super-admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create roles and assign existing permissions
        $role1 = Role::create(['title' => 'Администратор', 'name' => 'admin']);
        $role1->givePermissionTo(['users', 'departments', 'tasks']);

        $role2 = Role::create(['title' => 'Пользователь', 'name' => 'user']);
        $role2->givePermissionTo('tasks');

        /* $user = Factory(App\User::class)->create([
            'name' => 'Example Super-Admin User',
            'email' => 'superadmin@example.com',
        ]);
        $user->assignRole($role0); */
    }
}
