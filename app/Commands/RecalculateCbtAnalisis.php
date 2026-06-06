<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\CbtEngine;

class RecalculateCbtAnalisis extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'cbt:recalculate-analisis';
    protected $description = 'Hitung ulang EAP + analisis residu untuk semua CBT attempt yang datanya kosong.';

    public function run(array $params): void
    {
        $db = \Config\Database::connect();

        // Semua attempt CBT selesai (paket_id not null = CBT)
        $attempts = $db->table('attempt_ujian')
            ->where('status', 'selesai')
            ->where('paket_id IS NOT NULL', null, false)
            ->get()->getResultArray();

        if (empty($attempts)) {
            CLI::write('Tidak ada CBT attempt yang selesai.', 'yellow');
            return;
        }

        // Filter: hanya yang belum punya data analisis
        $needsCalc = [];
        foreach ($attempts as $att) {
            $count = $db->table('attempt_analisis_cbt')
                ->where('attempt_id', $att['attempt_id'])
                ->countAllResults();
            if ($count === 0) {
                $needsCalc[] = $att;
            }
        }

        if (empty($needsCalc)) {
            CLI::write('Semua CBT attempt sudah memiliki data analisis.', 'green');
            return;
        }

        CLI::write('Ditemukan ' . count($needsCalc) . ' attempt yang perlu dihitung ulang...', 'yellow');

        $berhasil = 0;
        $gagal    = 0;

        foreach ($needsCalc as $attempt) {
            $attemptId = (int) $attempt['attempt_id'];

            // Ambil jawaban dari attempt_jawaban_cbt
            $rows = $db->table('attempt_jawaban_cbt aj')
                ->select('aj.soal_id, aj.is_correct')
                ->select('COALESCE(ats.a, s.a, 1.000) AS a', false)
                ->select('COALESCE(ats.tingkat_kesulitan, s.tingkat_kesulitan, 0.000) AS b', false)
                ->select('COALESCE(ats.c, s.c, 0.000) AS c', false)
                ->join('attempt_soal_cbt ats', 'ats.attempt_id = aj.attempt_id AND ats.original_soal_id = aj.soal_id', 'left')
                ->join('soal_ujian s', 's.soal_id = aj.soal_id', 'left')
                ->where('aj.attempt_id', $attemptId)
                ->get()->getResultArray();

            if (empty($rows)) {
                CLI::write("  [SKIP] attempt_id={$attemptId} — tidak ada jawaban di attempt_jawaban_cbt", 'yellow');
                $gagal++;
                continue;
            }

            $responses = array_map(fn($r) => [
                'soal_id' => (int) $r['soal_id'],
                'a'       => (float) $r['a'],
                'b'       => (float) $r['b'],
                'c'       => (float) $r['c'],
                'u'       => (int) $r['is_correct'],
            ], $rows);

            // Hitung EAP jika theta_akhir masih NULL
            $thetaAkhir = $attempt['theta_akhir'] !== null
                ? (float) $attempt['theta_akhir']
                : null;

            if ($thetaAkhir === null) {
                $eap        = CbtEngine::estimasiEAP($responses);
                $thetaAkhir = $eap['theta_final'];

                // Update theta dan nilai di attempt_ujian
                $db->table('attempt_ujian')->where('attempt_id', $attemptId)->update([
                    'theta_akhir' => $eap['theta_final'],
                    'sem_akhir'   => $eap['SEM'],
                    'nilai_akhir' => $eap['NA'],
                ]);
            }

            // Hitung analisis residu
            $residu = CbtEngine::analisisResidu($thetaAkhir, $responses);
            $now    = date('Y-m-d H:i:s');

            $batch = array_map(fn($row) => [
                'attempt_id'    => $attemptId,
                'soal_id'       => $row['soal_id'],
                'is_correct'    => $row['jawab_id'],
                'p_residu'      => $row['p'],
                'q_residu'      => $row['q'],
                'z_score'       => $row['z'],
                'kategori_soal' => $row['kategori_soal'],
                'keterangan'    => $row['keterangan'],
                'created_at'    => $now,
            ], $residu);

            $db->table('attempt_analisis_cbt')->insertBatch($batch);
            $berhasil++;
            CLI::write("  [OK] attempt_id={$attemptId} — θ={$thetaAkhir} ({$berhasil}/" . count($needsCalc) . ')', 'green');
        }

        CLI::write('');
        CLI::write("Selesai: {$berhasil} berhasil, {$gagal} dilewati.", $gagal > 0 ? 'yellow' : 'green');
    }
}
