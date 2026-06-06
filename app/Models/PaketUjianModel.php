<?php
namespace App\Models;

use CodeIgniter\Model;

class PaketUjianModel extends Model
{
    protected $table = 'paket_ujian_cbt';
    protected $primaryKey = 'paket_id';
    protected $allowedFields = [
        'ujian_id',
        'nama_paket',
        'nomor_paket',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    /**
     * Ambil semua paket milik suatu ujian, beserta jumlah soal
     */
    public function getByUjian($ujianId)
    {
        return $this->select('paket_ujian_cbt.*')
            ->selectCount('paket_ujian_item_cbt.soal_id', 'jumlah_soal')
            ->join('paket_ujian_item_cbt', 'paket_ujian_item_cbt.paket_id = paket_ujian_cbt.paket_id', 'left')
            ->where('paket_ujian_cbt.ujian_id', $ujianId)
            ->groupBy('paket_ujian_cbt.paket_id')
            ->orderBy('paket_ujian_cbt.nomor_paket', 'ASC')
            ->findAll();
    }

    /**
     * Ambil soal-soal dalam suatu paket, urut sesuai nomor_urut
     */
    public function getSoalByPaket($paketId, $shuffle = false)
    {
        $builder = $this->db->table('paket_ujian_item_cbt')
            ->select('paket_ujian_item_cbt.nomor_urut, soal_ujian.*')
            ->join('soal_ujian', 'soal_ujian.soal_id = paket_ujian_item_cbt.soal_id')
            ->where('paket_ujian_item_cbt.paket_id', $paketId);

        if ($shuffle) {
            $builder->orderBy('RAND()');
        } else {
            $builder->orderBy('paket_ujian_item_cbt.nomor_urut', 'ASC');
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Hapus semua paket milik suatu ujian (beserta item-nya via CASCADE)
     */
    public function deleteByUjian($ujianId)
    {
        return $this->where('ujian_id', $ujianId)->delete();
    }
}
