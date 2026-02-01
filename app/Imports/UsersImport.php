<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Models\Role;

class UsersImport implements ToModel, WithHeadingRow
{
    protected $roleId;

    public function __construct($roleId)
    {
        $this->roleId = $roleId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Simple validation or skip existing emails?
        if (User::where('email', $row['email'])->exists()) {
            return null;
        }

        $user = User::create([
            'name'     => $row['nombre'], // Assuming headings match Spanish or English? Let's assume Spanish 'nombre', 'email', 'password'
            'email'    => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password'), // Default password if missing
        ]);

        // Assign selected Role
        $role = Role::find($this->roleId);
        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }
}
