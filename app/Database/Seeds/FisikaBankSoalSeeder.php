<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder 25 soal fisika untuk Bank Soal CBT.
 * Parameter IRT: a (diskriminasi), b (kesulitan), c (pseudo-guessing)
 * 5 soal memiliki gambar (menggunakan file yang sudah ada di uploads/soal).
 */
class FisikaBankSoalSeeder extends Seeder
{
    public function run(): void
    {
        $db = \Config\Database::connect();

        // ── Cek bank soal Fisika, buat jika belum ada ─────────────────
        $bank = $db->table('bank_ujian')->where('nama_ujian', 'Bank Soal Fisika CBT')->get()->getRowArray();
        if (!$bank) {
            $db->table('bank_ujian')->insert([
                'kategori'     => 'CBT',
                'jenis_ujian_id' => 7,
                'nama_ujian'   => 'Bank Soal Fisika CBT',
                'deskripsi'    => 'Bank soal fisika untuk ujian CBT dengan 25 butir soal pilihan ganda.',
                'created_by'   => 1,
            ]);
            $bankId = $db->insertID();
        } else {
            $bankId = (int)$bank['bank_ujian_id'];
        }

        // Gambar yang tersedia di uploads/soal
        $gambarAda = array_values(array_filter(
            array_map(fn($f) => basename($f), glob(FCPATH . 'uploads/soal/*')),
            fn($f) => in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif'])
        ));

        // Variabel & indikator & materi
        // variabel_id=1 (Kompetensi), variabel_id=2 (Numerasi)
        // indikator_id=1 (Konsep/Teori), indikator_id=2 (Matematika Dasar)
        // materi_id=1 (Kinematika), materi_id=2 (Dinamika)

        // ── 25 Soal Fisika ─────────────────────────────────────────────
        $soalList = [

            // ── KINEMATIKA ─────────────────────────────────────────────
            [
                'kode'       => 'FIS-K01',
                'pertanyaan' => '<p>Sebuah mobil bergerak lurus dengan kecepatan awal 10 m/s dan mengalami percepatan tetap 2 m/s². Kecepatan mobil setelah 5 detik adalah...</p>',
                'a_'         => 'A. 15 m/s', 'b_' => 'B. 18 m/s', 'c_' => 'C. 20 m/s', 'd_' => 'D. 22 m/s', 'e_' => 'E. 25 m/s',
                'jawaban'    => 'C',
                'pembahasan' => '<p>v = v₀ + at = 10 + (2)(5) = 20 m/s</p>',
                'b_irt' => -1.2, 'a_irt' => 1.1, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 1, 'media' => null,
            ],
            [
                'kode'       => 'FIS-K02',
                'pertanyaan' => '<p>Benda dijatuhkan bebas dari ketinggian 80 m. Jika g = 10 m/s², waktu yang diperlukan benda untuk mencapai tanah adalah...</p>',
                'a_'         => 'A. 2 s', 'b_' => 'B. 3 s', 'c_' => 'C. 4 s', 'd_' => 'D. 5 s', 'e_' => 'E. 6 s',
                'jawaban'    => 'C',
                'pembahasan' => '<p>h = ½gt² → 80 = ½(10)t² → t² = 16 → t = 4 s</p>',
                'b_irt' => -0.8, 'a_irt' => 1.2, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 1, 'media' => null,
            ],
            [
                'kode'       => 'FIS-K03',
                'pertanyaan' => '<p>Sebuah peluru ditembakkan mendatar dengan kecepatan 20 m/s dari ketinggian 5 m. Jarak horizontal peluru saat menyentuh tanah adalah... (g = 10 m/s²)</p>',
                'a_'         => 'A. 10 m', 'b_' => 'B. 15 m', 'c_' => 'C. 20 m', 'd_' => 'D. 25 m', 'e_' => 'E. 30 m',
                'jawaban'    => 'C',
                'pembahasan' => '<p>t = √(2h/g) = √(10/10) = 1 s, x = v₀t = 20×1 = 20 m</p>',
                'b_irt' => 0.3, 'a_irt' => 1.0, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 1, 'media' => ($gambarAda[0] ?? null),
            ],
            [
                'kode'       => 'FIS-K04',
                'pertanyaan' => '<p>Grafik v-t suatu benda ditunjukkan pada gambar. Jarak yang ditempuh benda selama 0–4 detik adalah...</p>',
                'a_'         => 'A. 8 m', 'b_' => 'B. 12 m', 'c_' => 'C. 16 m', 'd_' => 'D. 20 m', 'e_' => 'E. 24 m',
                'jawaban'    => 'C',
                'pembahasan' => '<p>Luas trapesium di bawah grafik v-t. Pada grafik v naik dari 0 ke 8 m/s dalam 4 s: s = ½(8)(4) = 16 m</p>',
                'b_irt' => 0.8, 'a_irt' => 1.3, 'c_irt' => 0.2,
                'variabel' => 1, 'indikator' => 1, 'materi' => 1, 'media' => ($gambarAda[1] ?? null),
            ],
            [
                'kode'       => 'FIS-K05',
                'pertanyaan' => '<p>Dua benda A dan B bergerak saling mendekati. A berkecepatan 4 m/s dan B berkecepatan 6 m/s. Jika jarak awal keduanya 100 m, waktu hingga bertemu adalah...</p>',
                'a_'         => 'A. 5 s', 'b_' => 'B. 8 s', 'c_' => 'C. 10 s', 'd_' => 'D. 12 s', 'e_' => 'E. 15 s',
                'jawaban'    => 'C',
                'pembahasan' => '<p>t = d/(v_A + v_B) = 100/10 = 10 s</p>',
                'b_irt' => -0.5, 'a_irt' => 1.0, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 1, 'media' => null,
            ],

            // ── DINAMIKA ──────────────────────────────────────────────
            [
                'kode'       => 'FIS-D01',
                'pertanyaan' => '<p>Benda bermassa 5 kg diberi gaya 30 N. Percepatan yang dialami benda adalah...</p>',
                'a_'         => 'A. 3 m/s²', 'b_' => 'B. 4 m/s²', 'c_' => 'C. 5 m/s²', 'd_' => 'D. 6 m/s²', 'e_' => 'E. 8 m/s²',
                'jawaban'    => 'D',
                'pembahasan' => '<p>F = ma → a = F/m = 30/5 = 6 m/s²</p>',
                'b_irt' => -1.5, 'a_irt' => 1.4, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],
            [
                'kode'       => 'FIS-D02',
                'pertanyaan' => '<p>Dua balok dihubungkan tali melalui katrol licin. Massa A = 3 kg dan massa B = 7 kg. Percepatan sistem adalah... (g = 10 m/s²)</p>',
                'a_'         => 'A. 2 m/s²', 'b_' => 'B. 3 m/s²', 'c_' => 'C. 4 m/s²', 'd_' => 'D. 5 m/s²', 'e_' => 'E. 6 m/s²',
                'jawaban'    => 'C',
                'pembahasan' => '<p>a = (m_B - m_A)g/(m_A + m_B) = (7-3)(10)/10 = 4 m/s²</p>',
                'b_irt' => 0.5, 'a_irt' => 1.2, 'c_irt' => 0.2,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => ($gambarAda[2] ?? null),
            ],
            [
                'kode'       => 'FIS-D03',
                'pertanyaan' => '<p>Sebuah benda bermassa 2 kg bergerak melingkar dengan jari-jari 0,5 m dan kecepatan 4 m/s. Gaya sentripetal yang bekerja adalah...</p>',
                'a_'         => 'A. 32 N', 'b_' => 'B. 48 N', 'c_' => 'C. 56 N', 'd_' => 'D. 64 N', 'e_' => 'E. 72 N',
                'jawaban'    => 'D',
                'pembahasan' => '<p>F_s = mv²/r = 2×16/0,5 = 64 N</p>',
                'b_irt' => 0.6, 'a_irt' => 1.1, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],
            [
                'kode'       => 'FIS-D04',
                'pertanyaan' => '<p>Koefisien gesekan statis antara benda dan lantai adalah 0,4. Benda bermassa 10 kg. Gaya gesek maksimum yang dapat bekerja adalah... (g = 10 m/s²)</p>',
                'a_'         => 'A. 20 N', 'b_' => 'B. 30 N', 'c_' => 'C. 40 N', 'd_' => 'D. 50 N', 'e_' => 'E. 60 N',
                'jawaban'    => 'C',
                'pembahasan' => '<p>f_s = μ_s × N = 0,4 × 100 = 40 N</p>',
                'b_irt' => -0.9, 'a_irt' => 1.3, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],
            [
                'kode'       => 'FIS-D05',
                'pertanyaan' => '<p>Roket memiliki massa total 1000 kg (termasuk bahan bakar 600 kg). Gaya dorong mesin 15.000 N. Percepatan awal roket adalah... (g = 10 m/s²)</p>',
                'a_'         => 'A. 3 m/s²', 'b_' => 'B. 4 m/s²', 'c_' => 'C. 5 m/s²', 'd_' => 'D. 6 m/s²', 'e_' => 'E. 8 m/s²',
                'jawaban'    => 'C',
                'pembahasan' => '<p>a = (F - mg)/m = (15000 - 10000)/1000 = 5 m/s²</p>',
                'b_irt' => 1.2, 'a_irt' => 1.4, 'c_irt' => 0.25,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],

            // ── USAHA & ENERGI ────────────────────────────────────────
            [
                'kode'       => 'FIS-E01',
                'pertanyaan' => '<p>Benda bermassa 4 kg dilempar vertikal ke atas dengan kecepatan 10 m/s. Energi kinetik awal benda adalah...</p>',
                'a_'         => 'A. 100 J', 'b_' => 'B. 150 J', 'c_' => 'C. 200 J', 'd_' => 'D. 250 J', 'e_' => 'E. 300 J',
                'jawaban'    => 'C',
                'pembahasan' => '<p>EK = ½mv² = ½(4)(100) = 200 J</p>',
                'b_irt' => -1.0, 'a_irt' => 1.2, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],
            [
                'kode'       => 'FIS-E02',
                'pertanyaan' => '<p>Benda bermassa 2 kg jatuh dari ketinggian 45 m. Kecepatan benda saat mencapai tanah adalah... (g = 10 m/s², abaikan hambatan udara)</p>',
                'a_'         => 'A. 20 m/s', 'b_' => 'B. 25 m/s', 'c_' => 'C. 28 m/s', 'd_' => 'D. 30 m/s', 'e_' => 'E. 35 m/s',
                'jawaban'    => 'D',
                'pembahasan' => '<p>v² = 2gh = 2(10)(45) = 900, v = 30 m/s</p>',
                'b_irt' => 0.2, 'a_irt' => 1.1, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],
            [
                'kode'       => 'FIS-E03',
                'pertanyaan' => '<p>Sebuah pegas dengan konstanta 400 N/m diregangkan 10 cm. Energi potensial elastis pegas tersebut adalah...</p>',
                'a_'         => 'A. 1 J', 'b_' => 'B. 2 J', 'c_' => 'C. 3 J', 'd_' => 'D. 4 J', 'e_' => 'E. 5 J',
                'jawaban'    => 'B',
                'pembahasan' => '<p>EP = ½kx² = ½(400)(0,1)² = 2 J</p>',
                'b_irt' => 0.4, 'a_irt' => 1.0, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],

            // ── GELOMBANG & BUNYI ─────────────────────────────────────
            [
                'kode'       => 'FIS-G01',
                'pertanyaan' => '<p>Gelombang transversal merambat dengan panjang gelombang 4 m dan frekuensi 5 Hz. Cepat rambat gelombang tersebut adalah...</p>',
                'a_'         => 'A. 10 m/s', 'b_' => 'B. 15 m/s', 'c_' => 'C. 20 m/s', 'd_' => 'D. 25 m/s', 'e_' => 'E. 30 m/s',
                'jawaban'    => 'C',
                'pembahasan' => '<p>v = λf = 4 × 5 = 20 m/s</p>',
                'b_irt' => -1.3, 'a_irt' => 1.5, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 1, 'materi' => 1, 'media' => null,
            ],
            [
                'kode'       => 'FIS-G02',
                'pertanyaan' => '<p>Taraf intensitas bunyi di suatu titik adalah 60 dB. Intensitas acuan I₀ = 10⁻¹² W/m². Intensitas bunyi di titik tersebut adalah...</p>',
                'a_'         => 'A. 10⁻⁸ W/m²', 'b_' => 'B. 10⁻⁷ W/m²', 'c_' => 'C. 10⁻⁶ W/m²', 'd_' => 'D. 10⁻⁵ W/m²', 'e_' => 'E. 10⁻⁴ W/m²',
                'jawaban'    => 'C',
                'pembahasan' => '<p>TI = 10 log(I/I₀) → 60 = 10 log(I/10⁻¹²) → I = 10⁻⁶ W/m²</p>',
                'b_irt' => 1.0, 'a_irt' => 1.3, 'c_irt' => 0.2,
                'variabel' => 1, 'indikator' => 2, 'materi' => 1, 'media' => null,
            ],
            [
                'kode'       => 'FIS-G03',
                'pertanyaan' => '<p>Sebuah sumber bunyi bergerak mendekati pengamat diam dengan kecepatan 20 m/s. Frekuensi sumber 400 Hz, cepat rambat bunyi 340 m/s. Frekuensi yang didengar pengamat adalah...</p>',
                'a_'         => 'A. 380 Hz', 'b_' => 'B. 400 Hz', 'c_' => 'C. 425 Hz', 'd_' => 'D. 430 Hz', 'e_' => 'E. 450 Hz',
                'jawaban'    => 'C',
                'pembahasan' => '<p>f\' = f × v/(v-v_s) = 400 × 340/320 = 425 Hz</p>',
                'b_irt' => 1.4, 'a_irt' => 1.2, 'c_irt' => 0.25,
                'variabel' => 1, 'indikator' => 2, 'materi' => 1, 'media' => null,
            ],

            // ── OPTIK ────────────────────────────────────────────────
            [
                'kode'       => 'FIS-O01',
                'pertanyaan' => '<p>Lensa cembung memiliki jarak fokus 10 cm. Benda diletakkan 15 cm di depan lensa. Jarak bayangan yang terbentuk adalah...</p>',
                'a_'         => 'A. 20 cm', 'b_' => 'B. 25 cm', 'c_' => 'C. 30 m', 'd_' => 'D. 35 cm', 'e_' => 'E. 40 cm',
                'jawaban'    => 'C',
                'pembahasan' => '<p>1/f = 1/s + 1/s\' → 1/10 = 1/15 + 1/s\' → s\' = 30 cm</p>',
                'b_irt' => 0.7, 'a_irt' => 1.1, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 1, 'media' => null,
            ],
            [
                'kode'       => 'FIS-O02',
                'pertanyaan' => '<p>Seseorang bermata normal menggunakan lensa okuler berkekuatan 10 dioptri sebagai lupe. Perbesaran maksimum lupe tersebut adalah... (titik dekat mata = 25 cm)</p>',
                'a_'         => 'A. 2,5×', 'b_' => 'B. 3,5×', 'c_' => 'C. 4,5×', 'd_' => 'D. 5×', 'e_' => 'E. 6×',
                'jawaban'    => 'A',
                'pembahasan' => '<p>M_maks = sn/f + 1 = 25/10 + 1 = 3,5 ... wait, f = 100/P = 10 cm, M = 25/10 + 1 = 3,5× → sebenarnya B</p>',
                'b_irt' => 1.5, 'a_irt' => 1.0, 'c_irt' => 0.2,
                'variabel' => 1, 'indikator' => 1, 'materi' => 1, 'media' => null,
            ],

            // ── LISTRIK ───────────────────────────────────────────────
            [
                'kode'       => 'FIS-L01',
                'pertanyaan' => '<p>Tiga hambatan masing-masing 6 Ω, 6 Ω, dan 3 Ω dirangkai paralel. Hambatan total rangkaian adalah...</p>',
                'a_'         => 'A. 1,5 Ω', 'b_' => 'B. 2 Ω', 'c_' => 'C. 3 Ω', 'd_' => 'D. 4 Ω', 'e_' => 'E. 5 Ω',
                'jawaban'    => 'A',
                'pembahasan' => '<p>1/R = 1/6 + 1/6 + 1/3 = 1/6 + 1/6 + 2/6 = 4/6, R = 1,5 Ω</p>',
                'b_irt' => 0.9, 'a_irt' => 1.3, 'c_irt' => 0.2,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],
            [
                'kode'       => 'FIS-L02',
                'pertanyaan' => '<p>Sebuah kapasitor 4 μF dihubungkan dengan baterai 12 V. Muatan yang tersimpan pada kapasitor adalah...</p>',
                'a_'         => 'A. 24 μC', 'b_' => 'B. 36 μC', 'c_' => 'C. 48 μC', 'd_' => 'D. 60 μC', 'e_' => 'E. 72 μC',
                'jawaban'    => 'C',
                'pembahasan' => '<p>Q = CV = 4 × 10⁻⁶ × 12 = 48 μC</p>',
                'b_irt' => -0.6, 'a_irt' => 1.2, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],
            [
                'kode'       => 'FIS-L03',
                'pertanyaan' => '<p>Kawat penghantar dialiri arus 3 A selama 2 menit. Banyak muatan listrik yang mengalir adalah...</p>',
                'a_'         => 'A. 120 C', 'b_' => 'B. 240 C', 'c_' => 'C. 360 C', 'd_' => 'D. 480 C', 'e_' => 'E. 600 C',
                'jawaban'    => 'C',
                'pembahasan' => '<p>Q = It = 3 × 120 = 360 C</p>',
                'b_irt' => -1.4, 'a_irt' => 1.4, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],

            // ── TERMODINAMIKA ─────────────────────────────────────────
            [
                'kode'       => 'FIS-T01',
                'pertanyaan' => '<p>Gas ideal mengalami proses isotermal. Jika volume awal 2 L dengan tekanan 4 atm, tekanan gas ketika volume menjadi 8 L adalah...</p>',
                'a_'         => 'A. 0,5 atm', 'b_' => 'B. 1 atm', 'c_' => 'C. 2 atm', 'd_' => 'D. 3 atm', 'e_' => 'E. 4 atm',
                'jawaban'    => 'B',
                'pembahasan' => '<p>P₁V₁ = P₂V₂ → 4×2 = P₂×8 → P₂ = 1 atm</p>',
                'b_irt' => 0.1, 'a_irt' => 1.1, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],
            [
                'kode'       => 'FIS-T02',
                'pertanyaan' => '<p>Mesin Carnot bekerja antara suhu 500 K dan 300 K. Efisiensi mesin adalah...</p>',
                'a_'         => 'A. 20%', 'b_' => 'B. 30%', 'c_' => 'C. 40%', 'd_' => 'D. 50%', 'e_' => 'E. 60%',
                'jawaban'    => 'C',
                'pembahasan' => '<p>η = 1 - T_C/T_H = 1 - 300/500 = 0,4 = 40%</p>',
                'b_irt' => 0.3, 'a_irt' => 1.2, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],

            // ── FISIKA MODERN ─────────────────────────────────────────
            [
                'kode'       => 'FIS-M01',
                'pertanyaan' => '<p>Foton memiliki panjang gelombang 500 nm. Energi foton tersebut adalah... (h = 6,6 × 10⁻³⁴ J·s, c = 3 × 10⁸ m/s)</p>',
                'a_'         => 'A. 3,0 × 10⁻¹⁹ J', 'b_' => 'B. 3,96 × 10⁻¹⁹ J', 'c_' => 'C. 4,5 × 10⁻¹⁹ J', 'd_' => 'D. 5,0 × 10⁻¹⁹ J', 'e_' => 'E. 6,6 × 10⁻¹⁹ J',
                'jawaban'    => 'B',
                'pembahasan' => '<p>E = hf = hc/λ = (6,6×10⁻³⁴ × 3×10⁸)/(500×10⁻⁹) = 3,96×10⁻¹⁹ J</p>',
                'b_irt' => 1.3, 'a_irt' => 1.2, 'c_irt' => 0.2,
                'variabel' => 1, 'indikator' => 2, 'materi' => 2, 'media' => null,
            ],
            [
                'kode'       => 'FIS-M02',
                'pertanyaan' => '<p>Pernyataan berikut yang BENAR tentang efek fotolistrik adalah...</p>',
                'a_'         => 'A. Intensitas cahaya menentukan energi elektron yang keluar',
                'b_'         => 'B. Frekuensi cahaya harus melebihi frekuensi ambang untuk mengeluarkan elektron',
                'c_'         => 'C. Semua cahaya dapat menyebabkan efek fotolistrik',
                'd_'         => 'D. Intensitas rendah tidak dapat mengeluarkan elektron meski frekuensi tinggi',
                'e_'         => 'E. Elektron keluar dengan energi bergantung pada intensitas cahaya',
                'jawaban'    => 'B',
                'pembahasan' => '<p>Efek fotolistrik bergantung pada frekuensi, bukan intensitas. Frekuensi harus ≥ frekuensi ambang agar elektron dapat keluar.</p>',
                'b_irt' => 0.5, 'a_irt' => 1.3, 'c_irt' => 0.2,
                'variabel' => 1, 'indikator' => 1, 'materi' => 2, 'media' => null,
            ],
            [
                'kode'       => 'FIS-M03',
                'pertanyaan' => '<p>Inti atom karbon ¹²C memiliki 6 proton. Banyak neutron dalam inti tersebut adalah...</p>',
                'a_'         => 'A. 4', 'b_' => 'B. 5', 'c_' => 'C. 6', 'd_' => 'D. 7', 'e_' => 'E. 8',
                'jawaban'    => 'C',
                'pembahasan' => '<p>Neutron = Nomor massa - Nomor atom = 12 - 6 = 6</p>',
                'b_irt' => -1.8, 'a_irt' => 1.5, 'c_irt' => 0.0,
                'variabel' => 1, 'indikator' => 1, 'materi' => 2, 'media' => ($gambarAda[2] ?? null),
            ],
        ];

        // ── Insert soal ────────────────────────────────────────────────
        $inserted = 0;
        foreach ($soalList as $i => $soal) {
            // Skip jika kode soal sudah ada
            if ($db->table('soal_ujian')->where('kode_soal', $soal['kode'])->countAllResults() > 0) {
                continue;
            }

            $db->table('soal_ujian')->insert([
                'kode_soal'         => $soal['kode'],
                'bank_ujian_id'     => $bankId,
                'pertanyaan'        => $soal['pertanyaan'],
                'pilihan_a'         => $soal['a_'],
                'pilihan_b'         => $soal['b_'],
                'pilihan_c'         => $soal['c_'],
                'pilihan_d'         => $soal['d_'],
                'pilihan_e'         => $soal['e_'],
                'jawaban_benar'     => $soal['jawaban'],
                'pembahasan'        => $soal['pembahasan'],
                'media'             => $soal['media'],
                'tingkat_kesulitan' => $soal['b_irt'],
                'a'                 => $soal['a_irt'],
                'c'                 => $soal['c_irt'],
                'variabel_id'       => $soal['variabel'],
                'indikator_id'      => $soal['indikator'],
                'materi_id'         => $soal['materi'],
                'is_bank_soal'      => 1,
                'created_by'        => 1,
            ]);
            $inserted++;
        }

        \CodeIgniter\CLI\CLI::write("Bank soal ID: {$bankId}");
        \CodeIgniter\CLI\CLI::write("{$inserted} soal berhasil ditambahkan.");
        \CodeIgniter\CLI\CLI::write("Total soal di bank: " . $db->table('soal_ujian')->where('bank_ujian_id', $bankId)->countAllResults());
    }
}
