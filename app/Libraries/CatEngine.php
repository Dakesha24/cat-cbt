<?php
namespace App\Libraries;

/**
 * CatEngine — Mesin kalkulasi IRT untuk ujian CAT (Computer Adaptive Testing)
 *
 * Model yang dipakai: Rasch 1PL
 *   P(θ) = e^(θ−b) / (1 + e^(θ−b))
 *   I(θ) = P(θ) × Q(θ)
 *   SE    = 1 / √(ΣI)
 *
 * Semua method static — tidak ada state, tidak ada DB access.
 * Controller hanya memanggil method ini untuk kalkulasi.
 */
class CatEngine
{
    // -------------------------------------------------------------------------
    // KONSTANTA
    // -------------------------------------------------------------------------

    /** Nilai Euler yang dipakai di formula IRT */
    private const E = M_E;

    // -------------------------------------------------------------------------
    // FORMULA IRT 1PL (RASCH)
    // -------------------------------------------------------------------------

    /**
     * Hitung P(θ) — probabilitas menjawab benar soal dengan kesulitan b
     * pada kemampuan θ menggunakan model Rasch (1PL).
     *
     * Rumus: P(θ) = e^(θ−b) / (1 + e^(θ−b))
     */
    public static function hitungPi(float $theta, float $b): float
    {
        $exp = pow(self::E, $theta - $b);
        return $exp / (1 + $exp);
    }

    /**
     * Hitung Q(θ) = 1 − P(θ)
     */
    public static function hitungQi(float $Pi): float
    {
        return 1.0 - $Pi;
    }

    /**
     * Hitung Fisher Information I(θ) untuk satu soal.
     *
     * Rumus 1PL: I(θ) = P(θ) × Q(θ)
     */
    public static function hitungIi(float $Pi, float $Qi): float
    {
        return $Pi * $Qi;
    }

    /**
     * Akumulasi total Fisher Information dari semua soal yang sudah dijawab
     * ditambah soal yang baru saja dijawab.
     *
     * @param array $soalYangDijawab  Array soal snapshot (masing-masing harus punya 'tingkat_kesulitan')
     * @param float $theta            Nilai θ saat ini
     * @param float $IiSoalBaru       Fisher Information soal yang baru dijawab
     */
    public static function hitungTotalIi(array $soalYangDijawab, float $theta, float $IiSoalBaru): float
    {
        $total = $IiSoalBaru;

        foreach ($soalYangDijawab as $soal) {
            $bi  = (float) ($soal['tingkat_kesulitan'] ?? 0);
            $Pi  = self::hitungPi($theta, $bi);
            $Qi  = self::hitungQi($Pi);
            $total += self::hitungIi($Pi, $Qi);
        }

        return $total;
    }

    /**
     * Hitung Standard Error of Measurement.
     *
     * Rumus: SE = 1 / √(ΣI)
     * Jika total informasi = 0, kembalikan nilai SE lama sebagai fallback.
     */
    public static function hitungSE(float $totalIi, float $SEFallback = 1.0): float
    {
        return $totalIi > 0 ? (1.0 / sqrt($totalIi)) : $SEFallback;
    }

    /**
     * Estimasi theta menggunakan MLE (Maximum Likelihood Estimation) — Newton-Raphson.
     *
     * Rumus iterasi 1PL:
     *   θ_{t+1} = θ_t + Σ(u_i − P_i) / Σ(P_i × Q_i)
     *
     * @param array $responses  [['b' => float, 'correct' => 0|1], ...]
     * @param float $thetaInit  Tebakan awal θ (default 0)
     * @param int   $maxIter    Batas iterasi (default 30)
     */
    public static function estimateThetaMLE(array $responses, float $thetaInit = 0.0, int $maxIter = 30): float
    {
        if (empty($responses)) {
            return 0.0;
        }

        $theta = $thetaInit;

        for ($iter = 0; $iter < $maxIter; $iter++) {
            $numerator   = 0.0;
            $denominator = 0.0;

            foreach ($responses as $resp) {
                $b = (float) ($resp['b'] ?? 0);
                $u = (int)   ($resp['correct'] ?? 0);
                $P = self::hitungPi($theta, $b);
                $Q = self::hitungQi($P);
                $numerator   += ($u - $P);
                $denominator += ($P * $Q);
            }

            if (abs($denominator) < 1e-10) {
                break;
            }

            $delta  = $numerator / $denominator;
            $theta += $delta;
            $theta  = max(-4.0, min(4.0, $theta));

            if (abs($delta) < 0.0001) {
                break;
            }
        }

        return round($theta, 4);
    }

    /**
     * @deprecated Gunakan estimateThetaMLE() untuk estimasi yang akurat.
     */
    public static function updateTheta(float $bSoalTerakhir): float
    {
        return $bSoalTerakhir;
    }

    // -------------------------------------------------------------------------
    // STOPPING RULE
    // -------------------------------------------------------------------------

    /**
     * Cek apakah CAT harus berhenti.
     *
     * Berhenti jika salah satu kondisi terpenuhi:
     *   1. SE sudah di bawah ambang minimum (estimasi cukup akurat)
     *   2. Perubahan SE antar soal terlalu kecil (konvergen, soal tambahan tidak membantu)
     *   3. Tidak ada soal berikutnya (pool habis)
     *
     * @return bool  true = berhenti, false = lanjut
     */
    public static function shouldStop(
        float  $SEBaru,
        float  $deltaSE,
        bool   $adaSoalBerikutnya,
        float  $seMinimum      = 0.25,
        float  $deltaSeMinimum = 0.01
    ): bool {
        if ($SEBaru < $seMinimum) {
            return true; // Akurasi sudah cukup
        }

        if (abs($deltaSE) < $deltaSeMinimum) {
            return true; // Soal tambahan tidak mengurangi SE secara signifikan
        }

        if (!$adaSoalBerikutnya) {
            return true; // Pool soal habis
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // KONVERSI SKOR
    // -------------------------------------------------------------------------

    /**
     * Konversi nilai θ IRT ke skor kognitif skala 0–100.
     *
     * Rumus: Skor = 50 + (16.67 × θ)
     * θ = 0   → skor 50 (rata-rata)
     * θ = 3   → skor ~100
     * θ = −3  → skor ~0
     */
    public static function hitungKemampuanKognitif(float $theta): float
    {
        $skor = 50.0 + (16.67 * $theta);

        // Clamp ke rentang 0–100
        return max(0.0, min(100.0, $skor));
    }

    // -------------------------------------------------------------------------
    // PEMILIHAN SOAL
    // -------------------------------------------------------------------------

    /**
     * Pilih soal awal: soal dengan tingkat kesulitan paling dekat ke 0 (b ≈ 0).
     * Soal "tengah" dipakai sebagai titik start agar CAT bisa naik/turun dari posisi netral.
     *
     * @param array $pool  Array soal snapshot (harus punya 'tingkat_kesulitan' dan 'attempt_soal_id')
     */
    public static function pilihSoalAwal(array $pool): ?array
    {
        if (empty($pool)) {
            return null;
        }

        usort($pool, static function (array $a, array $b): int {
            $distA = abs((float) ($a['tingkat_kesulitan'] ?? 0));
            $distB = abs((float) ($b['tingkat_kesulitan'] ?? 0));
            if ($distA === $distB) {
                return strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
            }
            return $distA <=> $distB;
        });

        return $pool[0];
    }

    /**
     * Pilih soal berikutnya berdasarkan jawaban terakhir.
     * Benar → naik ke soal lebih sulit (b lebih besar dari currentB)
     * Salah → turun ke soal lebih mudah (b lebih kecil dari currentB)
     *
     * @param array $pool          Array soal snapshot yang belum dijawab
     * @param array $answeredIds   Array attempt_soal_id yang sudah dijawab
     * @param float $currentB      Tingkat kesulitan soal terakhir
     * @param bool  $naik          true = jawaban benar (naik ke lebih sulit)
     */
    public static function pilihSoalBerikutnya(
        array $pool,
        array $answeredIds,
        float $currentB,
        bool  $naik
    ): ?array {
        $answeredLookup = array_map('intval', $answeredIds);

        $kandidat = array_filter($pool, static function (array $row) use ($answeredLookup, $currentB, $naik): bool {
            $soalId    = (int) ($row['attempt_soal_id'] ?? 0);
            $difficulty = (float) ($row['tingkat_kesulitan'] ?? 0);

            if (in_array($soalId, $answeredLookup, true)) {
                return false;
            }

            return $naik ? $difficulty > $currentB : $difficulty < $currentB;
        });

        if (empty($kandidat)) {
            return null;
        }

        usort($kandidat, static function (array $a, array $b) use ($naik): int {
            $diffA = (float) ($a['tingkat_kesulitan'] ?? 0);
            $diffB = (float) ($b['tingkat_kesulitan'] ?? 0);
            return $naik ? ($diffA <=> $diffB) : ($diffB <=> $diffA);
        });

        return array_values($kandidat)[0];
    }
}
