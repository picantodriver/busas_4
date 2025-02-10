<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StudentsRecords;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentsRecordsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_students::records');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudentsRecords $studentsRecords): bool
    {
        return $user->can('view_students::records');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_students::records');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudentsRecords $studentsRecords): bool
    {
        return $user->can('update_students::records');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudentsRecords $studentsRecords): bool
    {
        return $user->can('delete_students::records');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_students::records');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, StudentsRecords $studentsRecords): bool
    {
        return $user->can('force_delete_students::records');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_students::records');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, StudentsRecords $studentsRecords): bool
    {
        return $user->can('restore_students::records');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_students::records');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, StudentsRecords $studentsRecords): bool
    {
        return $user->can('replicate_students::records');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_students::records');
    }
}
