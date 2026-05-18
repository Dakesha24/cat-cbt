<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianSoalCatModel extends Model
{
    protected $table = 'ujian_soal_cat';
    protected $primaryKey = 'ujian_soal_cat_id';
    protected $allowedFields = [
        'ujian_id',
        'soal_id',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function linkSoal(int $ujianId, int $soalId): bool
    {
        $exists = $this->where('ujian_id', $ujianId)
            ->where('soal_id', $soalId)
            ->first();

        if ($exists) {
            return false;
        }

        return (bool) $this->insert([
            'ujian_id' => $ujianId,
            'soal_id'  => $soalId,
        ]);
    }

    public function unlinkSoal(int $ujianId, int $soalId): bool
    {
        return (bool) $this->where('ujian_id', $ujianId)
            ->where('soal_id', $soalId)
            ->delete();
    }

    public function deleteBySoal(int $soalId): bool
    {
        return (bool) $this->where('soal_id', $soalId)->delete();
    }

    public function getSoalByUjian(int $ujianId): array
    {
        $linked = $this->db->table($this->table . ' usc')
            ->select('soal_ujian.*, bank_ujian.nama_ujian as nama_bank_ujian')
            ->join('soal_ujian', 'soal_ujian.soal_id = usc.soal_id')
            ->join('bank_ujian', 'bank_ujian.bank_ujian_id = soal_ujian.bank_ujian_id', 'left')
            ->where('usc.ujian_id', $ujianId)
            ->orderBy('usc.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $legacy = $this->db->table('soal_ujian')
            ->select('soal_ujian.*, bank_ujian.nama_ujian as nama_bank_ujian')
            ->join('bank_ujian', 'bank_ujian.bank_ujian_id = soal_ujian.bank_ujian_id', 'left')
            ->where('soal_ujian.ujian_id', $ujianId)
            ->orderBy('soal_ujian.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $merged = [];
        foreach (array_merge($linked, $legacy) as $row) {
            $soalId = (int) ($row['soal_id'] ?? 0);
            if ($soalId > 0 && !isset($merged[$soalId])) {
                $merged[$soalId] = $row;
            }
        }

        $rows = array_values($merged);
        usort($rows, static function (array $a, array $b): int {
            return strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
        });

        return $rows;
    }

    public function countSoalByUjian(int $ujianId): int
    {
        return count($this->getSoalByUjian($ujianId));
    }

    public function findSoalForUjian(int $ujianId, int $soalId): ?array
    {
        foreach ($this->getSoalByUjian($ujianId) as $row) {
            if ((int) ($row['soal_id'] ?? 0) === $soalId) {
                return $row;
            }
        }

        return null;
    }
}
