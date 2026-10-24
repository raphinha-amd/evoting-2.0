<?php

namespace App\Imports;

use App\Models\Kelas;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;

class ClassImport implements ToModel
{
    protected $prodi_id;

    public function __construct($prodi_id = null)
    {
        $this->prodi_id = $prodi_id;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $name = trim($row[1] ?? '');
        if (empty($name)) {
            return null;
        }

        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;
        while (Kelas::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return new Kelas([
            'prodi_id' => $this->prodi_id,
            'name' => $name,
            'slug' => $slug,
            'is_active' => true,
        ]);
    }
}
