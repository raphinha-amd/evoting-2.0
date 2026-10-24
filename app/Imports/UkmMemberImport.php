<?php

namespace App\Imports;

use App\Models\Ukm;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UkmMemberImport implements ToCollection
{
    protected Ukm $ukm;
    protected int $importedCount = 0;
    protected int $alreadyMemberCount = 0;
    protected array $notFoundNims = [];

    public function __construct(Ukm $ukm)
    {
        $this->ukm = $ukm;
    }

    public function collection(Collection $rows)
    {
        $nims = [];

        foreach ($rows as $index => $row) {
            $firstCol = isset($row[0]) ? trim((string)$row[0]) : '';
            $secondCol = isset($row[1]) ? trim((string)$row[1]) : '';

            // Check if row is a header
            if ($index === 0) {
                $firstLower = strtolower($firstCol);
                $secondLower = strtolower($secondCol);
                if (in_array($firstLower, ['no', 'nim', 'username', 'no.', 'nomor', 'nim mahasiswa']) ||
                    in_array($secondLower, ['nim', 'username', 'nim mahasiswa'])) {
                    continue;
                }
            }

            $candidateNim = '';
            // If first column is just a row index (1, 2, 3...) and second column is filled, second column is likely the NIM
            if (is_numeric($firstCol) && strlen($firstCol) <= 4 && !empty($secondCol)) {
                $candidateNim = $secondCol;
            } elseif (!empty($firstCol)) {
                $candidateNim = $firstCol;
            } elseif (!empty($secondCol)) {
                $candidateNim = $secondCol;
            }

            if (!empty($candidateNim) && !in_array(strtolower($candidateNim), ['nim', 'username', 'no', 'nomor'])) {
                $nims[] = $candidateNim;
            }
        }

        if (empty($nims)) {
            return;
        }

        $nims = array_unique($nims);
        $users = User::whereIn('username', $nims)->get()->keyBy('username');

        $userIdsToAttach = [];
        foreach ($nims as $nim) {
            if (isset($users[$nim])) {
                $userIdsToAttach[] = $users[$nim]->id;
            } else {
                $this->notFoundNims[] = $nim;
            }
        }

        if (!empty($userIdsToAttach)) {
            $syncResult = $this->ukm->users()->syncWithoutDetaching($userIdsToAttach);
            $this->importedCount = count($syncResult['attached'] ?? []);
            $this->alreadyMemberCount = count($syncResult['existing'] ?? []);
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getAlreadyMemberCount(): int
    {
        return $this->alreadyMemberCount;
    }

    public function getNotFoundNims(): array
    {
        return $this->notFoundNims;
    }
}
