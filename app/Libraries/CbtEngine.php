<?php

namespace App\Libraries;

/**
 * Engine penilaian CBT berbasis IRT 3PL dengan estimasi kemampuan EAP.
 *
 * Semua method static dan murni: tanpa state dan tanpa akses database.
 * Theta diestimasi sekali di akhir ujian karena EAP membutuhkan seluruh jawaban.
 */
class CbtEngine
{
    private const THETA_MIN  = -5.0;
    private const THETA_MAX  = 5.0;
    private const THETA_STEP = 0.5;

    /**
     * Membangun grid theta -5, -4.5, ..., 4.5, 5.
     */
    public static function gridTheta(): array
    {
        $grid = [];
        for ($t = self::THETA_MIN; $t <= self::THETA_MAX + 1e-9; $t += self::THETA_STEP) {
            $grid[] = round($t, 1);
        }
        return $grid;
    }

    /**
     * Langkah 1: P(theta), probabilitas jawaban benar model 3PL.
     * P = c + (1 - c) / (1 + e^(-a(theta - b)))
     */
    public static function hitungPi(float $theta, float $a, float $b, float $c): float
    {
        $eksponen = -$a * ($theta - $b);
        $logistik = 1.0 / (1.0 + exp($eksponen));
        return $c + (1.0 - $c) * $logistik;
    }

    /**
     * Langkah 2: Q(theta), probabilitas jawaban salah.
     */
    public static function hitungQi(float $Pi): float
    {
        return 1.0 - $Pi;
    }

    /**
     * Langkah 4: W(theta), prior normal standar N(0,1).
     */
    public static function hitungPrior(float $theta): float
    {
        $konstanta = 1.0 / sqrt(2.0 * M_PI);
        return $konstanta * exp(-($theta * $theta) / 2.0);
    }

    /**
     * Langkah 3: L(theta), likelihood satu titik theta untuk semua soal.
     *
     * @param array $responses [['a'=>float, 'b'=>float, 'c'=>float, 'u'=>0|1], ...]
     */
    public static function hitungLikelihood(float $theta, array $responses): float
    {
        $L = 1.0;
        foreach ($responses as $soal) {
            $P = self::hitungPi($theta, (float) $soal['a'], (float) $soal['b'], (float) $soal['c']);
            $Q = self::hitungQi($P);
            $L *= ((int) $soal['u'] === 1) ? $P : $Q;
        }
        return $L;
    }

    /**
     * Langkah 5-11: estimasi EAP penuh untuk menghasilkan TF, SEM, dan NA.
     *
     * @return array ['theta_final'=>float, 'SEM'=>float, 'NA'=>float]
     */
    public static function estimasiEAP(array $responses): array
    {
        $pembilang = 0.0; // Langkah 8: Sigma TLW sebagai pembilang EAP
        $penyebut = 0.0;  // Langkah 8: Sigma LW sebagai penyebut EAP
        $sumT2LW = 0.0;   // Langkah 8: Sigma T2LW untuk menghitung SEM

        foreach (self::gridTheta() as $theta) {
            $L = self::hitungLikelihood($theta, $responses); // Langkah 3: L = product P^u * Q^(1-u)
            $W = self::hitungPrior($theta);                  // Langkah 4: W = prior normal standar
            $LW = $L * $W;                                   // Langkah 5: LW = L * W
            $TLW = $theta * $LW;                             // Langkah 6: TLW = theta * LW
            $T2LW = $theta * $theta * $LW;                   // Langkah 7: T2LW = theta^2 * LW

            $pembilang += $TLW; // Langkah 8: Sigma TLW
            $penyebut += $LW;   // Langkah 8: Sigma LW
            $sumT2LW += $T2LW;  // Langkah 8: Sigma T2LW
        }

        if ($penyebut <= 0.0) {
            return ['theta_final' => 0.0, 'SEM' => 0.0, 'NA' => 50.0];
        }

        $thetaFinal = $pembilang / $penyebut; // Langkah 9: thetaFinal = pembilang / penyebut
        $variansi = ($sumT2LW / $penyebut) - ($thetaFinal * $thetaFinal); // Langkah 10: variansi EAP
        $SEM = $variansi > 0.0 ? sqrt($variansi) : 0.0; // Langkah 10: SEM = sqrt(variansi)
        $NA = self::hitungKemampuanKognitif($thetaFinal); // Langkah 11: NA = 50 + 10 * thetaFinal

        return [
            'theta_final' => round($thetaFinal, 4),
            'SEM' => round($SEM, 4),
            'NA' => $NA,
        ];
    }

    /**
     * Langkah 11: NA = 50 + 10*theta, dibatasi ke rentang 0-100.
     */
    public static function hitungKemampuanKognitif(float $theta): float
    {
        $skor = 50.0 + (10.0 * $theta);
        return round(max(0.0, min(100.0, $skor)), 2);
    }

    /**
     * Langkah 12a: kategori soal berdasarkan parameter b.
     */
    public static function kategoriSoal(float $b): string
    {
        if ($b >= 1.0) {
            return 'Sulit';
        }
        if ($b >= -1.0) {
            return 'Sedang';
        }
        return 'Mudah';
    }

    /**
     * Langkah 12b: analisis residu per soal memakai thetaFinal.
     *
     * @return array daftar baris siap simpan ke attempt_analisis_cbt
     */
    public static function analisisResidu(float $thetaFinal, array $responses): array
    {
        $hasil = [];
        foreach ($responses as $soal) {
            $a = (float) $soal['a'];
            $b = (float) $soal['b'];
            $c = (float) $soal['c'];
            $u = (int) $soal['u'];

            $P = self::hitungPi($thetaFinal, $a, $b, $c); // Langkah 12: P residu pada thetaFinal
            $Q = self::hitungQi($P);              // Langkah 12: Q = 1 - P

            $penyebutZ = sqrt($P * $Q); // Langkah 12: penyebut z = sqrt(P * Q)
            $z = $penyebutZ > 0.0 ? ($u - $P) / $penyebutZ : 0.0; // Langkah 12: z = (u - P) / sqrt(P * Q)
            $kategoriSoal = self::kategoriSoal($b); // Langkah 12: kategori berdasarkan parameter b

            if ($z > 2.0 && $kategoriSoal === 'Sulit') {
                $keterangan = 'Lucky Guess';
            } elseif ($z < -2.0 && $kategoriSoal === 'Mudah') {
                $keterangan = 'Ceroboh';
            } else {
                $keterangan = 'Normal';
            }

            $hasil[] = [
                'soal_id' => $soal['soal_id'] ?? null,
                'jawab_id' => $u,
                'kategori_soal' => $kategoriSoal,
                'p' => round($P, 6),
                'q' => round($Q, 6),
                'z' => round($z, 4),
                'keterangan' => $keterangan,
            ];
        }

        return $hasil;
    }

    /**
     * Skor lama (% benar). Tetap ada sebagai fallback, bukan penilaian final CBT.
     */
    public static function hitungSkorSederhana(int $jumlahBenar, int $totalSoal): float
    {
        return $totalSoal > 0 ? round(($jumlahBenar / $totalSoal) * 100, 2) : 0.0;
    }
}
