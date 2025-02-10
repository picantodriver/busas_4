<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_acad::terms","view_acad::years","view_campuses","view_colleges","view_courses","view_curricula","view_curricula::acad::terms","view_programs","view_programs::curricula","view_programs::major","view_signatories","view_students","view_students::graduation::infos","view_students::records","view_students::registration::infos","view_user","view_any_acad::terms","create_acad::terms","update_acad::terms","restore_acad::terms","restore_any_acad::terms","replicate_acad::terms","reorder_acad::terms","delete_acad::terms","delete_any_acad::terms","force_delete_acad::terms","force_delete_any_acad::terms","view_any_acad::years","create_acad::years","update_acad::years","restore_acad::years","restore_any_acad::years","replicate_acad::years","reorder_acad::years","delete_acad::years","delete_any_acad::years","force_delete_acad::years","force_delete_any_acad::years","view_any_campuses","create_campuses","update_campuses","restore_campuses","restore_any_campuses","replicate_campuses","reorder_campuses","delete_campuses","delete_any_campuses","force_delete_campuses","force_delete_any_campuses","view_any_colleges","create_colleges","update_colleges","restore_colleges","restore_any_colleges","replicate_colleges","reorder_colleges","delete_colleges","delete_any_colleges","force_delete_colleges","force_delete_any_colleges","view_any_courses","create_courses","update_courses","restore_courses","restore_any_courses","replicate_courses","reorder_courses","delete_courses","delete_any_courses","force_delete_courses","force_delete_any_courses","view_any_curricula","create_curricula","update_curricula","restore_curricula","restore_any_curricula","replicate_curricula","reorder_curricula","delete_curricula","delete_any_curricula","force_delete_curricula","force_delete_any_curricula","view_any_curricula::acad::terms","create_curricula::acad::terms","update_curricula::acad::terms","restore_curricula::acad::terms","restore_any_curricula::acad::terms","replicate_curricula::acad::terms","reorder_curricula::acad::terms","delete_curricula::acad::terms","delete_any_curricula::acad::terms","force_delete_curricula::acad::terms","force_delete_any_curricula::acad::terms","view_any_programs","create_programs","update_programs","restore_programs","restore_any_programs","replicate_programs","reorder_programs","delete_programs","delete_any_programs","force_delete_programs","force_delete_any_programs","view_any_programs::curricula","create_programs::curricula","update_programs::curricula","restore_programs::curricula","restore_any_programs::curricula","replicate_programs::curricula","reorder_programs::curricula","delete_programs::curricula","delete_any_programs::curricula","force_delete_programs::curricula","force_delete_any_programs::curricula","view_any_programs::major","create_programs::major","update_programs::major","restore_programs::major","restore_any_programs::major","replicate_programs::major","reorder_programs::major","delete_programs::major","delete_any_programs::major","force_delete_programs::major","force_delete_any_programs::major","view_any_signatories","create_signatories","update_signatories","restore_signatories","restore_any_signatories","replicate_signatories","reorder_signatories","delete_signatories","delete_any_signatories","force_delete_signatories","force_delete_any_signatories","view_any_students","create_students","update_students","restore_students","restore_any_students","replicate_students","reorder_students","delete_students","delete_any_students","force_delete_students","force_delete_any_students","view_any_students::graduation::infos","create_students::graduation::infos","update_students::graduation::infos","restore_students::graduation::infos","restore_any_students::graduation::infos","replicate_students::graduation::infos","reorder_students::graduation::infos","delete_students::graduation::infos","delete_any_students::graduation::infos","force_delete_students::graduation::infos","force_delete_any_students::graduation::infos","view_any_students::records","create_students::records","update_students::records","restore_students::records","restore_any_students::records","replicate_students::records","reorder_students::records","delete_students::records","delete_any_students::records","force_delete_students::records","force_delete_any_students::records","view_any_students::registration::infos","create_students::registration::infos","update_students::registration::infos","restore_students::registration::infos","restore_any_students::registration::infos","replicate_students::registration::infos","reorder_students::registration::infos","delete_students::registration::infos","delete_any_students::registration::infos","force_delete_students::registration::infos","force_delete_any_students::registration::infos","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user"]},{"name":"staff","guard_name":"web","permissions":[]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
