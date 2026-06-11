<?php

namespace App\Controllers;

class AdminAnalitik extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // =========================================================
    // HALAMAN DASHBOARD ANALITIK
    // =========================================================
    public function index()
    {
        $data = [
            'filters' => $this->getFilters(),

            'filterOptions' => [
                'sekolah' => $this->db
                    ->table('sekolah')
                    ->orderBy('nama_sekolah', 'ASC')
                    ->get()
                    ->getResultArray(),

                'kelas' => $this->db
                    ->table('kelas')
                    ->orderBy('nama_kelas', 'ASC')
                    ->get()
                    ->getResultArray(),

                'variabel' => $this->db
                    ->table('variabel')
                    ->orderBy('nama_variabel', 'ASC')
                    ->get()
                    ->getResultArray(),

                'indikator' => $this->db
                    ->table('indikator')
                    ->orderBy('nama_indikator', 'ASC')
                    ->get()
                    ->getResultArray(),

                'materi' => $this->db
                    ->table('materi')
                    ->orderBy('nama_materi', 'ASC')
                    ->get()
                    ->getResultArray(),
            ]
        ];

        return view('admin/analitik/dashboard', $data);
    }

    // =========================================================
    // FILTER
    // =========================================================
    private function getFilters()
    {
        return [
            'sekolah_id'  => $this->request->getGet('sekolah_id'),
            'kelas_id'    => $this->request->getGet('kelas_id'),
            'variabel_id' => $this->request->getGet('variabel_id'),
            'indikator_id'=> $this->request->getGet('indikator_id'),
            'materi_id'   => $this->request->getGet('materi_id'),
        ];
    }

    // =========================================================
    // APPLY FILTER
    // =========================================================
    private function applyFilter(&$builder, $filters)
    {
        if (!empty($filters['sekolah_id'])) {
            $builder->where('k.sekolah_id', $filters['sekolah_id']);
        }

        if (!empty($filters['kelas_id'])) {
            $builder->where('s.kelas_id', $filters['kelas_id']);
        }

        if (!empty($filters['variabel_id'])) {
            $builder->where('su.variabel_id', $filters['variabel_id']);
        }

        if (!empty($filters['indikator_id'])) {
            $builder->where('su.indikator_id', $filters['indikator_id']);
        }

        if (!empty($filters['materi_id'])) {
            $builder->where('su.materi_id', $filters['materi_id']);
        }

        return $builder;
    }

    // =========================================================
    // SUMMARY CARD
    // =========================================================
    public function getSummaryAjax()
    {
        $filters = $this->getFilters();

        $builder = $this->db->table('attempt_ujian au');

        $builder->select("
            COUNT(DISTINCT s.siswa_id) AS jumlah_siswa,
            COUNT(au.attempt_id) AS jumlah_attempt,
            ROUND(AVG(au.nilai_akhir),2) AS rata_nilai,
            ROUND(AVG(au.theta_akhir),2) AS rata_theta,
            ROUND(MAX(au.nilai_akhir),2) AS nilai_tertinggi,
            ROUND(MIN(au.nilai_akhir),2) AS nilai_terendah
        ");

        $builder->join('peserta_ujian pu','pu.peserta_ujian_id=au.peserta_ujian_id');
        $builder->join('siswa s','s.siswa_id=pu.siswa_id');
        $builder->join('kelas k','k.kelas_id=s.kelas_id');

        $this->applyFilter($builder,$filters);

        return $this->response->setJSON(
            $builder->get()->getRowArray()
        );
    }

    // =========================================================
    // GROWTH ATTEMPT
    // =========================================================
    public function getGrowthAjax()
    {
        $filters = $this->getFilters();

        $builder = $this->db->table('attempt_ujian au');

        $builder->select("
            au.nomor_attempt,
            ROUND(AVG(au.nilai_akhir),2) AS avg_nilai,
            ROUND(AVG(au.theta_akhir),2) AS avg_theta
        ");

        $builder->join('peserta_ujian pu','pu.peserta_ujian_id=au.peserta_ujian_id');
        $builder->join('siswa s','s.siswa_id=pu.siswa_id');
        $builder->join('kelas k','k.kelas_id=s.kelas_id');

        $this->applyFilter($builder,$filters);

        $builder->groupBy('au.nomor_attempt');
        $builder->orderBy('au.nomor_attempt','ASC');

        $rows = $builder->get()->getResultArray();

        $labels = [];
        $nilai = [];
        $theta = [];

        foreach($rows as $row)
        {
            $labels[] = 'Attempt '.$row['nomor_attempt'];
            $nilai[] = (float)$row['avg_nilai'];
            $theta[] = (float)$row['avg_theta'];
        }

        return $this->response->setJSON([
            'labels'=>$labels,
            'nilai'=>$nilai,
            'theta'=>$theta
        ]);
    }

    // =========================================================
    // ANALISIS MATERI
    // =========================================================
    public function getMateriAjax()
    {
        $filters = $this->getFilters();

        $builder = $this->db->table('attempt_jawaban_cbt aj');

        $builder->select("
            m.nama_materi,
            COUNT(*) total_soal,
            SUM(aj.is_correct) jumlah_benar,
            ROUND((SUM(aj.is_correct)/COUNT(*))*100,2) persen
        ");

        $builder->join('attempt_ujian au','au.attempt_id=aj.attempt_id');
        $builder->join('peserta_ujian pu','pu.peserta_ujian_id=au.peserta_ujian_id');
        $builder->join('siswa s','s.siswa_id=pu.siswa_id');
        $builder->join('kelas k','k.kelas_id=s.kelas_id');

        $builder->join('soal_ujian su','su.soal_id=aj.soal_id');
        $builder->join('materi m','m.materi_id=su.materi_id');

        $this->applyFilter($builder,$filters);

        $builder->groupBy('m.materi_id');
        $builder->orderBy('persen','DESC');

        return $this->response->setJSON(
            $builder->get()->getResultArray()
        );
    }

    // =========================================================
    // ANALISIS INDIKATOR
    // =========================================================
    public function getIndikatorAjax()
    {
        $filters = $this->getFilters();

        $builder = $this->db->table('attempt_jawaban_cbt aj');

        $builder->select("
            i.nama_indikator,
            ROUND(AVG(aj.is_correct)*100,2) persen
        ");

        $builder->join('attempt_ujian au','au.attempt_id=aj.attempt_id');
        $builder->join('peserta_ujian pu','pu.peserta_ujian_id=au.peserta_ujian_id');
        $builder->join('siswa s','s.siswa_id=pu.siswa_id');
        $builder->join('kelas k','k.kelas_id=s.kelas_id');

        $builder->join('soal_ujian su','su.soal_id=aj.soal_id');
        $builder->join('indikator i','i.indikator_id=su.indikator_id');

        $this->applyFilter($builder,$filters);

        $builder->groupBy('i.indikator_id');

        return $this->response->setJSON(
            $builder->get()->getResultArray()
        );
    }

    // =========================================================
    // ANALISIS VARIABEL
    // =========================================================
    public function getVariabelAjax()
    {
        $filters = $this->getFilters();

        $builder = $this->db->table('attempt_jawaban_cbt aj');

        $builder->select("
            v.nama_variabel,
            ROUND(AVG(aj.is_correct)*100,2) persen
        ");

        $builder->join('attempt_ujian au','au.attempt_id=aj.attempt_id');
        $builder->join('peserta_ujian pu','pu.peserta_ujian_id=au.peserta_ujian_id');
        $builder->join('siswa s','s.siswa_id=pu.siswa_id');
        $builder->join('kelas k','k.kelas_id=s.kelas_id');

        $builder->join('soal_ujian su','su.soal_id=aj.soal_id');
        $builder->join('variabel v','v.variabel_id=su.variabel_id');

        $this->applyFilter($builder,$filters);

        $builder->groupBy('v.variabel_id');

        return $this->response->setJSON(
            $builder->get()->getResultArray()
        );
    }
}