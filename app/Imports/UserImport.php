<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;

class UserImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    protected $kelas_id;
    protected $role;

    public function __construct($kelas_id, $role)
    {
        $this->kelas_id = $kelas_id;
        $this->role = $role;
    }

    public function model(array $row)
    {
        $unencryptedPassword = $row[3] ?? rand(0, 99999999);
        $password = bcrypt($unencryptedPassword);

        return new User([
            'fullname' => $row[1],
            'username' => $row[2] ?? rand(0, 99999999),
            'password' => $password,
            'unencrypted_password' => $unencryptedPassword,
            'role' => $this->role,
            'kelas_id' => $this->kelas_id,
            'is_active' => true,
        ]);
    }
}
