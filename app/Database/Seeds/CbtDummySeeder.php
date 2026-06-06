<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\CbtEngine;

/**
 * Seeder dummy data CBT — simulasi ujian lengkap dari peserta s/d analisis residu.
 * Target ujian: jadwal_id=16 (UJIAN CBT, kelas_id=7)
 *               jadwal_id=15 (UTS Fisika Expert, kelas_id=NULL → semua siswa)
 */
class CbtDummySeeder extends Seeder
{
    protected $db;

    public function run(): void
    {
        $this->db = \Config\Database::connect();

        CLI_write('=== CBT Dummy Seeder ===');

        $this->expandPaketSoal();
        $this->createPesertaAndAttempts();
        $this->fillBiodataTambahan();

        CLI_write('Selesai!');
    }

    // ── 1. Perluas paket soal ──────────────────────────────────────────────
    private function expandPaketSoal(): void
    {
        // Soal dengan parameter IRT lengkap
        $soalIds = $this->db->table('soal_ujian')
            ->select('soal_id, tingkat_kesulitan, jawaban_benar')
            ->orderBy('soal_id', 'ASC')
            ->limit(30)
            ->get()->getResultArray();

        if (count($soalIds) < 5) {
            CLI_write('[SKIP] Soal tidak cukup');
            return;
        }

        // Ambil semua paket CBT
        $pakets = $this->db->table('paket_ujian_cbt')->get()->getResultArray();

        foreach ($pakets as $paket) {
            // Hapus item lama
            $this->db->table('paket_ujian_item_cbt')->where('paket_id', $paket['paket_id'])->delete();

            // Isi 10 soal per paket (urutan diacak per paket)
            $shuffled = $soalIds;
            shuffle($shuffled);
            $selected = array_slice($shuffled, 0, min(10, count($shuffled)));

            foreach ($selected as $i => $s) {
                $this->db->table('paket_ujian_item_cbt')->insert([
                    'paket_id'   => $paket['paket_id'],
                    'soal_id'    => $s['soal_id'],
                    'nomor_urut' => $i + 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        CLI_write('[OK] Paket soal diperluas ke 10 soal/paket');
    }

    // ── 2. Buat peserta + attempt ──────────────────────────────────────────
    private function createPesertaAndAttempts(): void
    {
        // Jadwal yang akan disimulasikan
        $jadwals = [
            ['jadwal_id' => 15, 'kelas_id' => null],  // UTS Fisika Expert — semua siswa
            ['jadwal_id' => 16, 'kelas_id' => 7],      // UJIAN CBT — kelas 7
        ];

        // Kemampuan tiap siswa (theta distribusi normal -2..+2)
        $siswaList = $this->db->table('siswa s')
            ->select('s.siswa_id, s.kelas_id, s.nama_lengkap')
            ->join('users u', 'u.user_id = s.user_id')
            ->where('u.status', 'active')
            ->get()->getResultArray();

        // Assign theta ke tiap siswa (seeded random)
        $thetaMap = [];
        $thetaValues = [-1.8,-1.5,-1.3,-1.1,-0.9,-0.7,-0.5,-0.3,-0.1,0.0,0.1,0.3,0.5,0.7,0.9,1.1,1.3,1.5,1.8,2.0,-2.0,-1.6,-1.2,-0.8,-0.4,0.0,0.4,0.8,1.2,1.6,0.2,0.6,1.0,-0.2,-0.6,-1.0,1.4,-1.4,0.8,-0.6];
        foreach ($siswaList as $i => $s) {
            $thetaMap[$s['siswa_id']] = $thetaValues[$i % count($thetaValues)];
        }

        $now = date('Y-m-d H:i:s');
        $created = 0;

        foreach ($jadwals as $jadwal) {
            // Ambil paket untuk jadwal ini (dari ujian yg terkait jadwal)
            $ujianId = $this->db->table('jadwal_ujian')->where('jadwal_id', $jadwal['jadwal_id'])->get()->getRowArray()['ujian_id'] ?? null;
            if (!$ujianId) continue;

            $pakets = $this->db->table('paket_ujian_cbt')->where('ujian_id', $ujianId)->get()->getResultArray();
            if (empty($pakets)) continue;

            // Tentukan siswa yang ikut
            $siswaTerlibat = array_filter($siswaList, function($s) use ($jadwal) {
                if ($jadwal['kelas_id'] === null) return true;
                return (int)$s['kelas_id'] === (int)$jadwal['kelas_id'];
            });

            foreach ($siswaTerlibat as $siswa) {
                $siswaId  = (int)$siswa['siswa_id'];
                $jadwalId = (int)$jadwal['jadwal_id'];

                // Cek/buat peserta_ujian
                $peserta = $this->db->table('peserta_ujian')
                    ->where('siswa_id', $siswaId)
                    ->where('jadwal_id', $jadwalId)
                    ->get()->getRowArray();

                if (!$peserta) {
                    $this->db->table('peserta_ujian')->insert([
                        'siswa_id'       => $siswaId,
                        'jadwal_id'      => $jadwalId,
                        'status'         => 'belum_mulai',
                        'waktu_mulai'    => null,
                        'waktu_selesai'  => null,
                    ]);
                    $pesertaId = $this->db->insertID();
                } else {
                    $pesertaId = (int)$peserta['peserta_ujian_id'];
                    // Skip kalau sudah ada attempt selesai
                    $hasAttempt = $this->db->table('attempt_ujian')
                        ->where('peserta_ujian_id', $pesertaId)
                        ->where('status', 'selesai')
                        ->countAllResults();
                    if ($hasAttempt > 0) continue;
                }

                // Pilih paket acak
                $paket    = $pakets[array_rand($pakets)];
                $paketId  = (int)$paket['paket_id'];

                // Ambil soal dari paket
                $soalList = $this->db->table('paket_ujian_item_cbt pci')
                    ->select('pci.soal_id, pci.nomor_urut, sq.tingkat_kesulitan, sq.a, sq.c, sq.jawaban_benar')
                    ->join('soal_ujian sq', 'sq.soal_id = pci.soal_id')
                    ->where('pci.paket_id', $paketId)
                    ->orderBy('pci.nomor_urut', 'ASC')
                    ->get()->getResultArray();

                if (empty($soalList)) continue;

                // Waktu ujian: mulai antara 7-14 hari lalu
                $daysAgo   = rand(1, 14);
                $startHour = rand(7, 15);
                $startTime = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days") + ($startHour * 3600) + rand(0, 1800));
                $duration  = rand(900, 3600); // 15-60 menit
                $endTime   = date('Y-m-d H:i:s', strtotime($startTime) + $duration);

                // Buat attempt_ujian
                $this->db->table('attempt_ujian')->insert([
                    'peserta_ujian_id' => $pesertaId,
                    'paket_id'         => $paketId,
                    'nomor_attempt'    => 1,
                    'status'           => 'selesai',
                    'waktu_mulai'      => $startTime,
                    'waktu_selesai'    => $endTime,
                    'nilai_akhir'      => 0, // akan diupdate
                    'theta_akhir'      => 0,
                    'sem_akhir'        => 0,
                ]);
                $attemptId = $this->db->insertID();

                // Buat snapshot soal (attempt_soal_cbt)
                foreach ($soalList as $s) {
                    $this->db->table('attempt_soal_cbt')->insert([
                        'attempt_id'       => $attemptId,
                        'paket_id'         => $paketId,
                        'original_soal_id' => $s['soal_id'],
                        'nomor_urut'       => $s['nomor_urut'],
                        'tingkat_kesulitan'=> $s['tingkat_kesulitan'],
                        'a'                => $s['a'] ?? 1.0,
                        'c'                => $s['c'] ?? 0.0,
                        'jawaban_benar'    => $s['jawaban_benar'],
                        'created_at'       => $startTime,
                    ]);
                }

                // Simulasi jawaban berdasarkan theta siswa
                $theta    = $thetaMap[$siswaId] ?? 0.0;
                $responses = [];
                $answerTime = strtotime($startTime);

                foreach ($soalList as $idx => $s) {
                    $a    = (float)($s['a'] ?? 1.0);
                    $b    = (float)$s['tingkat_kesulitan'];
                    $c    = (float)($s['c'] ?? 0.0);
                    $P    = $c + (1 - $c) / (1 + exp(-1.702 * $a * ($theta - $b)));
                    $correct = (mt_rand() / mt_getrandmax()) < $P;

                    $options  = ['A','B','C','D','E'];
                    $kunci    = $s['jawaban_benar'];
                    if ($correct) {
                        $jawaban = $kunci;
                    } else {
                        $salah = array_filter($options, fn($o) => $o !== $kunci);
                        $jawaban = $salah[array_rand($salah)];
                    }

                    // Waktu menjawab: distribusi antar soal
                    $answerTime += rand(30, $duration / count($soalList) * 2);
                    $answerTimeStr = date('Y-m-d H:i:s', min($answerTime, strtotime($endTime)));

                    $this->db->table('attempt_jawaban_cbt')->insert([
                        'attempt_id'     => $attemptId,
                        'soal_id'        => $s['soal_id'],
                        'nomor_tampil'   => $idx + 1,
                        'jawaban_siswa'  => $jawaban,
                        'is_correct'     => $correct ? 1 : 0,
                        'waktu_menjawab' => $answerTimeStr,
                    ]);

                    $responses[] = [
                        'soal_id' => (int)$s['soal_id'],
                        'a'       => $a,
                        'b'       => $b,
                        'c'       => $c,
                        'u'       => $correct ? 1 : 0,
                    ];
                }

                // Hitung EAP
                $eap       = CbtEngine::estimasiEAP($responses);
                $residu    = CbtEngine::analisisResidu($eap['theta_final'], $responses);
                $batchNow  = date('Y-m-d H:i:s');

                // Update attempt dengan nilai EAP
                $this->db->table('attempt_ujian')->where('attempt_id', $attemptId)->update([
                    'nilai_akhir' => $eap['NA'],
                    'theta_akhir' => $eap['theta_final'],
                    'sem_akhir'   => $eap['SEM'],
                ]);

                // Update peserta_ujian
                $this->db->table('peserta_ujian')->where('peserta_ujian_id', $pesertaId)->update([
                    'status'        => 'selesai',
                    'waktu_mulai'   => $startTime,
                    'waktu_selesai' => $endTime,
                ]);

                // Simpan analisis residu
                $batch = array_map(fn($r) => [
                    'attempt_id'    => $attemptId,
                    'soal_id'       => $r['soal_id'],
                    'is_correct'    => $r['jawab_id'],
                    'p_residu'      => $r['p'],
                    'q_residu'      => $r['q'],
                    'z_score'       => $r['z'],
                    'kategori_soal' => $r['kategori_soal'],
                    'keterangan'    => $r['keterangan'],
                    'created_at'    => $batchNow,
                ], $residu);

                if (!empty($batch)) {
                    $this->db->table('attempt_analisis_cbt')->insertBatch($batch);
                }

                $created++;
            }
        }

        CLI_write("[OK] {$created} attempt selesai dibuat");
    }

    // ── 3. Isi biodata tambahan ────────────────────────────────────────────
    private function fillBiodataTambahan(): void
    {
        $template = $this->db->table('form_templates')->get()->getRowArray();
        if (!$template) { CLI_write('[SKIP] Tidak ada form template'); return; }

        $templateId = (int)$template['template_id'];

        // Ambil field provinsi dan agama
        $fieldProvinsi = $this->db->table('form_fields')->where('template_id', $templateId)->where('label', 'Provinsi')->get()->getRowArray();
        $fieldAgama    = $this->db->table('form_fields')->where('template_id', $templateId)->where('label', 'Agama')->get()->getRowArray();

        if (!$fieldProvinsi || !$fieldAgama) { CLI_write('[SKIP] Field Provinsi/Agama tidak ditemukan'); return; }

        // Opsi
        $provinsiOpts = array_column($this->db->table('form_field_options')->where('field_id', $fieldProvinsi['field_id'])->get()->getResultArray(), 'label');
        $agamaOpts    = array_column($this->db->table('form_field_options')->where('field_id', $fieldAgama['field_id'])->get()->getResultArray(), 'label');

        if (empty($provinsiOpts) || empty($agamaOpts)) { CLI_write('[SKIP] Opsi Provinsi/Agama kosong'); return; }

        $siswas = $this->db->table('siswa')->get()->getResultArray();
        $filled = 0;

        foreach ($siswas as $s) {
            $siswaId = (int)$s['siswa_id'];

            // Cek sudah ada response
            $existing = $this->db->table('form_responses')->where('siswa_id', $siswaId)->where('template_id', $templateId)->get()->getRowArray();
            if ($existing) continue;

            // Buat response
            $this->db->table('form_responses')->insert(['template_id' => $templateId, 'siswa_id' => $siswaId, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $responseId = $this->db->insertID();

            // Provinsi acak (didistribusikan ke beberapa provinsi utama)
            $mainProvinsi = ['DKI Jakarta','Jawa Barat','Jawa Tengah','Jawa Timur','DI Yogyakarta','Banten','Sumatera Utara','Sulawesi Selatan'];
            $provinsi = in_array($provinsiOpts[0], $mainProvinsi) ? $mainProvinsi[array_rand($mainProvinsi)] : $provinsiOpts[array_rand($provinsiOpts)];

            // Cari opsi yang cocok
            $pVal = in_array($provinsi, $provinsiOpts) ? $provinsi : $provinsiOpts[array_rand($provinsiOpts)];
            $aVal = $agamaOpts[array_rand($agamaOpts)];

            // Dominankan Islam
            if (in_array('Islam', $agamaOpts) && mt_rand(1, 10) <= 7) $aVal = 'Islam';

            $this->db->table('form_response_values')->insertBatch([
                ['response_id' => $responseId, 'field_id' => $fieldProvinsi['field_id'], 'nilai' => $pVal],
                ['response_id' => $responseId, 'field_id' => $fieldAgama['field_id'],    'nilai' => $aVal],
            ]);

            $filled++;
        }

        CLI_write("[OK] {$filled} biodata tambahan diisi");
    }
}

function CLI_write(string $msg): void
{
    \CodeIgniter\CLI\CLI::write($msg);
}
