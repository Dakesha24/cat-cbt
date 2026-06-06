<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder 26 soal Fisika untuk Bank Soal Umum.
 * Jalankan: php spark db:seed FisikaUmumSeeder
 *
 * Dibuat/diperbarui:
 *  1. Mata pelajaran "Fisika" (jenis_ujian, kelas_id = NULL)
 *  2. Bank soal "Bank Soal Fisika Umum" (kategori = 'umum')
 *  3. Variabel, Indikator, Materi fisika
 *  4. 26 butir soal dengan parameter IRT (a, b, c) + tagging metadata
 */
class FisikaUmumSeeder extends Seeder
{
    public function run(): void
    {
        $db = \Config\Database::connect();

        // ── 1. Mata pelajaran "Fisika" umum ───────────────────────────
        $mapel = $db->table('jenis_ujian')
            ->where('nama_jenis', 'Fisika')
            ->where('kelas_id IS NULL', null, false)
            ->get()->getRowArray();

        if (!$mapel) {
            $db->table('jenis_ujian')->insert([
                'nama_jenis' => 'Fisika',
                'deskripsi'  => 'Mata pelajaran Fisika umum untuk semua kelas',
                'kelas_id'   => null,
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $jenisUjianId = $db->insertID();
            \CodeIgniter\CLI\CLI::write("Mata pelajaran 'Fisika' dibuat (ID: {$jenisUjianId}).");
        } else {
            $jenisUjianId = (int) $mapel['jenis_ujian_id'];
            \CodeIgniter\CLI\CLI::write("Mata pelajaran 'Fisika' sudah ada (ID: {$jenisUjianId}).");
        }

        // ── 2. Bank soal "Bank Soal Fisika Umum" ──────────────────────
        $bank = $db->table('bank_ujian')
            ->where('nama_ujian', 'Bank Soal Fisika Umum')
            ->where('kategori', 'umum')
            ->get()->getRowArray();

        if (!$bank) {
            $db->table('bank_ujian')->insert([
                'kategori'       => 'umum',
                'jenis_ujian_id' => $jenisUjianId,
                'nama_ujian'     => 'Bank Soal Fisika Umum',
                'deskripsi'      => 'Soal fisika pilihan ganda: kinematika, dinamika, energi, gelombang, optik, listrik, termodinamika, fisika modern.',
                'created_by'     => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
            $bankId = $db->insertID();
            \CodeIgniter\CLI\CLI::write("Bank soal dibuat (ID: {$bankId}).");
        } else {
            $bankId = (int) $bank['bank_ujian_id'];
            \CodeIgniter\CLI\CLI::write("Bank soal sudah ada (ID: {$bankId}).");
        }

        // ── 3. Variabel (domain utama fisika) ─────────────────────────
        $variabelList = [
            'Mekanika'        => 'Cabang fisika yang mempelajari gerak, gaya, dan energi benda.',
            'Gelombang & Optik' => 'Cabang fisika yang mempelajari gelombang, bunyi, cahaya, dan optik.',
            'Listrik & Magnet'  => 'Cabang fisika yang mempelajari muatan, arus, dan medan elektromagnetik.',
            'Termodinamika'   => 'Cabang fisika yang mempelajari kalor, suhu, dan proses termal.',
            'Fisika Modern'   => 'Cabang fisika yang mempelajari kuantum, foton, dan inti atom.',
        ];
        $varIds = [];
        foreach ($variabelList as $nama => $deskripsi) {
            $row = $db->table('variabel')->where('nama_variabel', $nama)->get()->getRowArray();
            if (!$row) {
                $db->table('variabel')->insert(['nama_variabel' => $nama, 'deskripsi' => $deskripsi, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                $varIds[$nama] = $db->insertID();
            } else {
                $varIds[$nama] = (int) $row['variabel_id'];
            }
        }
        \CodeIgniter\CLI\CLI::write("Variabel: " . count($varIds) . " tersedia.");

        // ── 4. Indikator (subdomain per variabel) ─────────────────────
        $indikatorList = [
            ['variabel' => 'Mekanika',          'nama' => 'Kinematika',         'deskripsi' => 'Mempelajari gerak benda tanpa mempertimbangkan penyebabnya.'],
            ['variabel' => 'Mekanika',          'nama' => 'Dinamika',           'deskripsi' => 'Mempelajari gaya sebagai penyebab gerak.'],
            ['variabel' => 'Mekanika',          'nama' => 'Usaha & Energi',     'deskripsi' => 'Mempelajari usaha, energi kinetik, energi potensial, dan kekekalannya.'],
            ['variabel' => 'Gelombang & Optik', 'nama' => 'Gelombang & Bunyi',  'deskripsi' => 'Mempelajari sifat gelombang, bunyi, dan efek Doppler.'],
            ['variabel' => 'Gelombang & Optik', 'nama' => 'Optik Geometri',     'deskripsi' => 'Mempelajari pemantulan, pembiasan, dan alat-alat optik.'],
            ['variabel' => 'Listrik & Magnet',  'nama' => 'Listrik',            'deskripsi' => 'Mempelajari muatan, arus, hambatan, dan rangkaian listrik.'],
            ['variabel' => 'Termodinamika',     'nama' => 'Hukum Gas',          'deskripsi' => 'Mempelajari hukum gas ideal dan proses termodinamika.'],
            ['variabel' => 'Fisika Modern',     'nama' => 'Fisika Kuantum',     'deskripsi' => 'Mempelajari foton, efek fotolistrik, dan inti atom.'],
        ];
        $indIds = [];
        foreach ($indikatorList as $ind) {
            $row = $db->table('indikator')
                ->where('nama_indikator', $ind['nama'])
                ->where('variabel_id', $varIds[$ind['variabel']])
                ->get()->getRowArray();
            if (!$row) {
                $db->table('indikator')->insert([
                    'variabel_id'    => $varIds[$ind['variabel']],
                    'nama_indikator' => $ind['nama'],
                    'deskripsi'      => $ind['deskripsi'],
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
                $indIds[$ind['nama']] = $db->insertID();
            } else {
                $indIds[$ind['nama']] = (int) $row['indikator_id'];
            }
        }
        \CodeIgniter\CLI\CLI::write("Indikator: " . count($indIds) . " tersedia.");

        // ── 5. Materi (topik spesifik) ─────────────────────────────────
        $materiList = [
            'Kinematika Lurus'      => 'GLBB, GLB, dan gerak bebas.',
            'Gerak Parabola'        => 'Gerak dua dimensi: horizontal + vertikal.',
            'Gerak Melingkar'       => 'Gerak melingkar beraturan dan gaya sentripetal.',
            'Hukum Newton'          => 'Hukum I, II, III Newton dan penerapannya.',
            'Gaya Gesek'            => 'Gaya gesek statis dan kinetis.',
            'Energi Kinetik'        => 'Energi kinetik dan teorema kerja-energi.',
            'Energi Potensial'      => 'Energi potensial gravitasi dan kekekalan energi mekanik.',
            'Pegas & Elastisitas'   => 'Hukum Hooke dan energi potensial elastis.',
            'Gelombang Mekanik'     => 'Besaran-besaran gelombang: λ, f, v, T.',
            'Bunyi & Akustik'       => 'Intensitas bunyi, taraf intensitas, dan efek Doppler.',
            'Lensa & Cermin'        => 'Pembiasan cahaya dan pembentukan bayangan lensa/cermin.',
            'Alat Optik'            => 'Lupe, mikroskop, teleskop, dan kamera.',
            'Rangkaian Listrik'     => 'Hambatan seri, paralel, dan campuran.',
            'Kapasitor & Muatan'    => 'Kapasitansi, muatan, dan energi kapasitor.',
            'Arus Listrik'          => 'Hubungan muatan, arus, dan waktu.',
            'Hukum Gas Ideal'       => 'Hukum Boyle, Charles, Gay-Lussac, dan gas ideal.',
            'Mesin Carnot'          => 'Siklus Carnot dan efisiensi mesin kalor.',
            'Foton & Fotolistrik'   => 'Energi foton dan efek fotolistrik.',
            'Inti Atom'             => 'Nomor atom, nomor massa, proton, dan neutron.',
        ];
        $matIds = [];
        foreach ($materiList as $nama => $deskripsi) {
            $row = $db->table('materi')->where('nama_materi', $nama)->get()->getRowArray();
            if (!$row) {
                $db->table('materi')->insert(['nama_materi' => $nama, 'deskripsi' => $deskripsi, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
                $matIds[$nama] = $db->insertID();
            } else {
                $matIds[$nama] = (int) $row['materi_id'];
            }
        }
        \CodeIgniter\CLI\CLI::write("Materi: " . count($matIds) . " tersedia.");

        // ── 6. Daftar soal dengan tagging metadata ─────────────────────
        // var  = key dari $varIds
        // ind  = key dari $indIds
        // mat  = key dari $matIds
        $soalList = [

            // KINEMATIKA
            ['kode' => 'FIS-U-K01', 'var' => 'Mekanika', 'ind' => 'Kinematika', 'mat' => 'Kinematika Lurus',
             'b_irt' => -1.2, 'a_irt' => 1.1, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Sebuah mobil bergerak lurus dengan kecepatan awal <strong>10 m/s</strong> dan percepatan tetap <strong>2 m/s²</strong>. Kecepatan mobil setelah 5 detik adalah…</p>',
             'a_' => '<p>15 m/s</p>', 'b_' => '<p>18 m/s</p>', 'c_' => '<p>20 m/s</p>', 'd_' => '<p>22 m/s</p>', 'e_' => '<p>25 m/s</p>',
             'pembahasan' => '<p>v = v₀ + at = 10 + 2×5 = <strong>20 m/s</strong>.</p>'],

            ['kode' => 'FIS-U-K02', 'var' => 'Mekanika', 'ind' => 'Kinematika', 'mat' => 'Kinematika Lurus',
             'b_irt' => -0.8, 'a_irt' => 1.2, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Benda dijatuhkan bebas dari ketinggian <strong>80 m</strong>. Jika g = 10 m/s², waktu benda mencapai tanah adalah…</p>',
             'a_' => '<p>2 s</p>', 'b_' => '<p>3 s</p>', 'c_' => '<p>4 s</p>', 'd_' => '<p>5 s</p>', 'e_' => '<p>6 s</p>',
             'pembahasan' => '<p>h = ½gt² → 80 = ½(10)t² → t = <strong>4 s</strong>.</p>'],

            ['kode' => 'FIS-U-K03', 'var' => 'Mekanika', 'ind' => 'Kinematika', 'mat' => 'Gerak Parabola',
             'b_irt' =>  0.3, 'a_irt' => 1.0, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Peluru ditembakkan mendatar dengan kecepatan <strong>20 m/s</strong> dari ketinggian <strong>5 m</strong>. Jarak horizontal saat menyentuh tanah adalah… (g = 10 m/s²)</p>',
             'a_' => '<p>10 m</p>', 'b_' => '<p>15 m</p>', 'c_' => '<p>20 m</p>', 'd_' => '<p>25 m</p>', 'e_' => '<p>30 m</p>',
             'pembahasan' => '<p>t = √(2h/g) = 1 s. x = v₀t = 20×1 = <strong>20 m</strong>.</p>'],

            ['kode' => 'FIS-U-K04', 'var' => 'Mekanika', 'ind' => 'Kinematika', 'mat' => 'Kinematika Lurus',
             'b_irt' => -0.5, 'a_irt' => 1.0, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Dua benda A (4 m/s) dan B (6 m/s) bergerak saling mendekati, jarak awal <strong>100 m</strong>. Waktu hingga bertemu adalah…</p>',
             'a_' => '<p>5 s</p>', 'b_' => '<p>8 s</p>', 'c_' => '<p>10 s</p>', 'd_' => '<p>12 s</p>', 'e_' => '<p>15 s</p>',
             'pembahasan' => '<p>t = d/(v_A+v_B) = 100/10 = <strong>10 s</strong>.</p>'],

            ['kode' => 'FIS-U-K05', 'var' => 'Mekanika', 'ind' => 'Kinematika', 'mat' => 'Gerak Melingkar',
             'b_irt' =>  0.1, 'a_irt' => 1.1, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Benda bergerak melingkar beraturan, jari-jari <strong>2 m</strong> dan frekuensi <strong>5 Hz</strong>. Kecepatan linier benda adalah…</p>',
             'a_' => '<p>10π m/s</p>', 'b_' => '<p>15π m/s</p>', 'c_' => '<p>20π m/s</p>', 'd_' => '<p>25π m/s</p>', 'e_' => '<p>30π m/s</p>',
             'pembahasan' => '<p>v = 2πrf = 2π×2×5 = <strong>20π m/s</strong>.</p>'],

            // DINAMIKA
            ['kode' => 'FIS-U-D01', 'var' => 'Mekanika', 'ind' => 'Dinamika', 'mat' => 'Hukum Newton',
             'b_irt' => -1.5, 'a_irt' => 1.4, 'c_irt' => 0.00, 'jawaban' => 'D',
             'pertanyaan' => '<p>Benda bermassa <strong>5 kg</strong> diberi gaya resultan <strong>30 N</strong>. Percepatan yang dialami benda adalah…</p>',
             'a_' => '<p>3 m/s²</p>', 'b_' => '<p>4 m/s²</p>', 'c_' => '<p>5 m/s²</p>', 'd_' => '<p>6 m/s²</p>', 'e_' => '<p>8 m/s²</p>',
             'pembahasan' => '<p>F = ma → a = 30/5 = <strong>6 m/s²</strong>.</p>'],

            ['kode' => 'FIS-U-D02', 'var' => 'Mekanika', 'ind' => 'Dinamika', 'mat' => 'Hukum Newton',
             'b_irt' =>  0.5, 'a_irt' => 1.2, 'c_irt' => 0.20, 'jawaban' => 'C',
             'pertanyaan' => '<p>Dua balok dihubungkan tali melalui katrol licin: massa A = <strong>3 kg</strong>, massa B = <strong>7 kg</strong>. Percepatan sistem adalah… (g = 10 m/s²)</p>',
             'a_' => '<p>2 m/s²</p>', 'b_' => '<p>3 m/s²</p>', 'c_' => '<p>4 m/s²</p>', 'd_' => '<p>5 m/s²</p>', 'e_' => '<p>6 m/s²</p>',
             'pembahasan' => '<p>a = (m_B−m_A)g/(m_A+m_B) = 4×10/10 = <strong>4 m/s²</strong>.</p>'],

            ['kode' => 'FIS-U-D03', 'var' => 'Mekanika', 'ind' => 'Dinamika', 'mat' => 'Gerak Melingkar',
             'b_irt' =>  0.6, 'a_irt' => 1.1, 'c_irt' => 0.00, 'jawaban' => 'D',
             'pertanyaan' => '<p>Benda bermassa <strong>2 kg</strong> bergerak melingkar, jari-jari <strong>0,5 m</strong>, kecepatan <strong>4 m/s</strong>. Gaya sentripetal yang bekerja adalah…</p>',
             'a_' => '<p>32 N</p>', 'b_' => '<p>48 N</p>', 'c_' => '<p>56 N</p>', 'd_' => '<p>64 N</p>', 'e_' => '<p>72 N</p>',
             'pembahasan' => '<p>F_s = mv²/r = 2×16/0,5 = <strong>64 N</strong>.</p>'],

            ['kode' => 'FIS-U-D04', 'var' => 'Mekanika', 'ind' => 'Dinamika', 'mat' => 'Gaya Gesek',
             'b_irt' => -0.9, 'a_irt' => 1.3, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Koefisien gesek statis <strong>0,4</strong>, massa benda <strong>10 kg</strong>. Gaya gesek statis maksimum adalah… (g = 10 m/s²)</p>',
             'a_' => '<p>20 N</p>', 'b_' => '<p>30 N</p>', 'c_' => '<p>40 N</p>', 'd_' => '<p>50 N</p>', 'e_' => '<p>60 N</p>',
             'pembahasan' => '<p>f_s = μ_s×N = 0,4×100 = <strong>40 N</strong>.</p>'],

            ['kode' => 'FIS-U-D05', 'var' => 'Mekanika', 'ind' => 'Dinamika', 'mat' => 'Hukum Newton',
             'b_irt' =>  1.2, 'a_irt' => 1.4, 'c_irt' => 0.25, 'jawaban' => 'C',
             'pertanyaan' => '<p>Roket bermassa total <strong>1.000 kg</strong>, gaya dorong mesin <strong>15.000 N</strong>. Percepatan awal roket adalah… (g = 10 m/s²)</p>',
             'a_' => '<p>3 m/s²</p>', 'b_' => '<p>4 m/s²</p>', 'c_' => '<p>5 m/s²</p>', 'd_' => '<p>6 m/s²</p>', 'e_' => '<p>8 m/s²</p>',
             'pembahasan' => '<p>a = (F−mg)/m = (15000−10000)/1000 = <strong>5 m/s²</strong>.</p>'],

            // USAHA & ENERGI
            ['kode' => 'FIS-U-E01', 'var' => 'Mekanika', 'ind' => 'Usaha & Energi', 'mat' => 'Energi Kinetik',
             'b_irt' => -1.0, 'a_irt' => 1.2, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Benda bermassa <strong>4 kg</strong> bergerak dengan kecepatan <strong>10 m/s</strong>. Energi kinetik benda adalah…</p>',
             'a_' => '<p>100 J</p>', 'b_' => '<p>150 J</p>', 'c_' => '<p>200 J</p>', 'd_' => '<p>250 J</p>', 'e_' => '<p>300 J</p>',
             'pembahasan' => '<p>EK = ½mv² = ½×4×100 = <strong>200 J</strong>.</p>'],

            ['kode' => 'FIS-U-E02', 'var' => 'Mekanika', 'ind' => 'Usaha & Energi', 'mat' => 'Energi Potensial',
             'b_irt' =>  0.2, 'a_irt' => 1.1, 'c_irt' => 0.00, 'jawaban' => 'D',
             'pertanyaan' => '<p>Benda bermassa <strong>2 kg</strong> jatuh bebas dari ketinggian <strong>45 m</strong>. Kecepatan saat menyentuh tanah adalah… (g = 10 m/s²)</p>',
             'a_' => '<p>20 m/s</p>', 'b_' => '<p>25 m/s</p>', 'c_' => '<p>28 m/s</p>', 'd_' => '<p>30 m/s</p>', 'e_' => '<p>35 m/s</p>',
             'pembahasan' => '<p>v² = 2gh = 2×10×45 = 900 → v = <strong>30 m/s</strong>.</p>'],

            ['kode' => 'FIS-U-E03', 'var' => 'Mekanika', 'ind' => 'Usaha & Energi', 'mat' => 'Pegas & Elastisitas',
             'b_irt' =>  0.4, 'a_irt' => 1.0, 'c_irt' => 0.00, 'jawaban' => 'B',
             'pertanyaan' => '<p>Pegas dengan konstanta <strong>400 N/m</strong> diregangkan <strong>10 cm</strong>. Energi potensial elastis pegas adalah…</p>',
             'a_' => '<p>1 J</p>', 'b_' => '<p>2 J</p>', 'c_' => '<p>3 J</p>', 'd_' => '<p>4 J</p>', 'e_' => '<p>5 J</p>',
             'pembahasan' => '<p>EP = ½kx² = ½×400×(0,1)² = <strong>2 J</strong>.</p>'],

            // GELOMBANG & BUNYI
            ['kode' => 'FIS-U-G01', 'var' => 'Gelombang & Optik', 'ind' => 'Gelombang & Bunyi', 'mat' => 'Gelombang Mekanik',
             'b_irt' => -1.3, 'a_irt' => 1.5, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Gelombang transversal merambat dengan panjang gelombang <strong>4 m</strong> dan frekuensi <strong>5 Hz</strong>. Cepat rambat gelombang adalah…</p>',
             'a_' => '<p>10 m/s</p>', 'b_' => '<p>15 m/s</p>', 'c_' => '<p>20 m/s</p>', 'd_' => '<p>25 m/s</p>', 'e_' => '<p>30 m/s</p>',
             'pembahasan' => '<p>v = λf = 4×5 = <strong>20 m/s</strong>.</p>'],

            ['kode' => 'FIS-U-G02', 'var' => 'Gelombang & Optik', 'ind' => 'Gelombang & Bunyi', 'mat' => 'Bunyi & Akustik',
             'b_irt' =>  1.0, 'a_irt' => 1.3, 'c_irt' => 0.20, 'jawaban' => 'C',
             'pertanyaan' => '<p>Taraf intensitas bunyi di suatu titik adalah <strong>60 dB</strong>. Jika I₀ = 10⁻¹² W/m², intensitas bunyi di titik tersebut adalah…</p>',
             'a_' => '<p>10⁻⁸ W/m²</p>', 'b_' => '<p>10⁻⁷ W/m²</p>', 'c_' => '<p>10⁻⁶ W/m²</p>', 'd_' => '<p>10⁻⁵ W/m²</p>', 'e_' => '<p>10⁻⁴ W/m²</p>',
             'pembahasan' => '<p>TI = 10 log(I/I₀) → 60 = 10 log(I/10⁻¹²) → I = <strong>10⁻⁶ W/m²</strong>.</p>'],

            ['kode' => 'FIS-U-G03', 'var' => 'Gelombang & Optik', 'ind' => 'Gelombang & Bunyi', 'mat' => 'Bunyi & Akustik',
             'b_irt' =>  1.4, 'a_irt' => 1.2, 'c_irt' => 0.25, 'jawaban' => 'C',
             'pertanyaan' => '<p>Sumber bunyi mendekati pengamat diam dengan kecepatan <strong>20 m/s</strong>. Frekuensi sumber <strong>400 Hz</strong>, cepat rambat bunyi <strong>340 m/s</strong>. Frekuensi yang didengar pengamat adalah…</p>',
             'a_' => '<p>380 Hz</p>', 'b_' => '<p>400 Hz</p>', 'c_' => '<p>425 Hz</p>', 'd_' => '<p>430 Hz</p>', 'e_' => '<p>450 Hz</p>',
             'pembahasan' => '<p>f\' = f×v/(v−v_s) = 400×340/320 = <strong>425 Hz</strong>.</p>'],

            // OPTIK
            ['kode' => 'FIS-U-O01', 'var' => 'Gelombang & Optik', 'ind' => 'Optik Geometri', 'mat' => 'Lensa & Cermin',
             'b_irt' =>  0.7, 'a_irt' => 1.1, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Lensa cembung berjarak fokus <strong>10 cm</strong>. Benda diletakkan <strong>15 cm</strong> di depan lensa. Jarak bayangan yang terbentuk adalah…</p>',
             'a_' => '<p>20 cm</p>', 'b_' => '<p>25 cm</p>', 'c_' => '<p>30 cm</p>', 'd_' => '<p>35 cm</p>', 'e_' => '<p>40 cm</p>',
             'pembahasan' => '<p>1/f = 1/s + 1/s\' → 1/10 = 1/15 + 1/s\' → s\' = <strong>30 cm</strong>.</p>'],

            ['kode' => 'FIS-U-O02', 'var' => 'Gelombang & Optik', 'ind' => 'Optik Geometri', 'mat' => 'Alat Optik',
             'b_irt' =>  1.5, 'a_irt' => 1.0, 'c_irt' => 0.20, 'jawaban' => 'B',
             'pertanyaan' => '<p>Lupe berkekuatan <strong>10 dioptri</strong> digunakan oleh mata normal (titik dekat 25 cm). Perbesaran maksimum lupe adalah…</p>',
             'a_' => '<p>2,5×</p>', 'b_' => '<p>3,5×</p>', 'c_' => '<p>4,5×</p>', 'd_' => '<p>5×</p>', 'e_' => '<p>6×</p>',
             'pembahasan' => '<p>f = 100/10 = 10 cm. M_maks = sn/f + 1 = 25/10 + 1 = <strong>3,5×</strong>.</p>'],

            // LISTRIK
            ['kode' => 'FIS-U-L01', 'var' => 'Listrik & Magnet', 'ind' => 'Listrik', 'mat' => 'Rangkaian Listrik',
             'b_irt' =>  0.9, 'a_irt' => 1.3, 'c_irt' => 0.20, 'jawaban' => 'A',
             'pertanyaan' => '<p>Tiga hambatan <strong>6 Ω, 6 Ω, dan 3 Ω</strong> dirangkai paralel. Hambatan total rangkaian adalah…</p>',
             'a_' => '<p>1,5 Ω</p>', 'b_' => '<p>2 Ω</p>', 'c_' => '<p>3 Ω</p>', 'd_' => '<p>4 Ω</p>', 'e_' => '<p>5 Ω</p>',
             'pembahasan' => '<p>1/R = 1/6+1/6+1/3 = 4/6 → R = <strong>1,5 Ω</strong>.</p>'],

            ['kode' => 'FIS-U-L02', 'var' => 'Listrik & Magnet', 'ind' => 'Listrik', 'mat' => 'Kapasitor & Muatan',
             'b_irt' => -0.6, 'a_irt' => 1.2, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Kapasitor <strong>4 μF</strong> dihubungkan dengan baterai <strong>12 V</strong>. Muatan yang tersimpan adalah…</p>',
             'a_' => '<p>24 μC</p>', 'b_' => '<p>36 μC</p>', 'c_' => '<p>48 μC</p>', 'd_' => '<p>60 μC</p>', 'e_' => '<p>72 μC</p>',
             'pembahasan' => '<p>Q = CV = 4×12 = <strong>48 μC</strong>.</p>'],

            ['kode' => 'FIS-U-L03', 'var' => 'Listrik & Magnet', 'ind' => 'Listrik', 'mat' => 'Arus Listrik',
             'b_irt' => -1.4, 'a_irt' => 1.4, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Kawat penghantar dialiri arus <strong>3 A</strong> selama <strong>2 menit</strong>. Muatan listrik yang mengalir adalah…</p>',
             'a_' => '<p>120 C</p>', 'b_' => '<p>240 C</p>', 'c_' => '<p>360 C</p>', 'd_' => '<p>480 C</p>', 'e_' => '<p>600 C</p>',
             'pembahasan' => '<p>Q = It = 3×120 = <strong>360 C</strong>.</p>'],

            // TERMODINAMIKA
            ['kode' => 'FIS-U-T01', 'var' => 'Termodinamika', 'ind' => 'Hukum Gas', 'mat' => 'Hukum Gas Ideal',
             'b_irt' =>  0.1, 'a_irt' => 1.1, 'c_irt' => 0.00, 'jawaban' => 'B',
             'pertanyaan' => '<p>Gas ideal mengalami proses isotermal. Volume awal <strong>2 L</strong>, tekanan <strong>4 atm</strong>. Tekanan saat volume menjadi <strong>8 L</strong> adalah…</p>',
             'a_' => '<p>0,5 atm</p>', 'b_' => '<p>1 atm</p>', 'c_' => '<p>2 atm</p>', 'd_' => '<p>3 atm</p>', 'e_' => '<p>4 atm</p>',
             'pembahasan' => '<p>P₁V₁ = P₂V₂ → 4×2 = P₂×8 → P₂ = <strong>1 atm</strong>.</p>'],

            ['kode' => 'FIS-U-T02', 'var' => 'Termodinamika', 'ind' => 'Hukum Gas', 'mat' => 'Mesin Carnot',
             'b_irt' =>  0.3, 'a_irt' => 1.2, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Mesin Carnot beroperasi antara suhu <strong>500 K</strong> dan <strong>300 K</strong>. Efisiensi mesin adalah…</p>',
             'a_' => '<p>20%</p>', 'b_' => '<p>30%</p>', 'c_' => '<p>40%</p>', 'd_' => '<p>50%</p>', 'e_' => '<p>60%</p>',
             'pembahasan' => '<p>η = 1−T_C/T_H = 1−300/500 = <strong>40%</strong>.</p>'],

            // FISIKA MODERN
            ['kode' => 'FIS-U-M01', 'var' => 'Fisika Modern', 'ind' => 'Fisika Kuantum', 'mat' => 'Foton & Fotolistrik',
             'b_irt' =>  1.3, 'a_irt' => 1.2, 'c_irt' => 0.20, 'jawaban' => 'B',
             'pertanyaan' => '<p>Foton memiliki panjang gelombang <strong>500 nm</strong>. Energi foton tersebut adalah… (h = 6,6×10⁻³⁴ J·s, c = 3×10⁸ m/s)</p>',
             'a_' => '<p>3,0 × 10⁻¹⁹ J</p>', 'b_' => '<p>3,96 × 10⁻¹⁹ J</p>', 'c_' => '<p>4,5 × 10⁻¹⁹ J</p>', 'd_' => '<p>5,0 × 10⁻¹⁹ J</p>', 'e_' => '<p>6,6 × 10⁻¹⁹ J</p>',
             'pembahasan' => '<p>E = hc/λ = (6,6×10⁻³⁴×3×10⁸)/(500×10⁻⁹) = <strong>3,96×10⁻¹⁹ J</strong>.</p>'],

            ['kode' => 'FIS-U-M02', 'var' => 'Fisika Modern', 'ind' => 'Fisika Kuantum', 'mat' => 'Foton & Fotolistrik',
             'b_irt' =>  0.5, 'a_irt' => 1.3, 'c_irt' => 0.20, 'jawaban' => 'B',
             'pertanyaan' => '<p>Pernyataan yang <strong>benar</strong> tentang efek fotolistrik adalah…</p>',
             'a_' => '<p>Intensitas cahaya menentukan energi elektron yang keluar.</p>',
             'b_' => '<p>Frekuensi cahaya harus melebihi frekuensi ambang agar elektron keluar.</p>',
             'c_' => '<p>Semua cahaya dapat menyebabkan efek fotolistrik.</p>',
             'd_' => '<p>Intensitas rendah tidak dapat mengeluarkan elektron meski frekuensi tinggi.</p>',
             'e_' => '<p>Energi elektron bergantung pada intensitas, bukan frekuensi cahaya.</p>',
             'pembahasan' => '<p>Efek fotolistrik bergantung pada <strong>frekuensi</strong>, bukan intensitas. Frekuensi harus ≥ frekuensi ambang agar elektron dapat dilepas.</p>'],

            ['kode' => 'FIS-U-M03', 'var' => 'Fisika Modern', 'ind' => 'Fisika Kuantum', 'mat' => 'Inti Atom',
             'b_irt' => -1.8, 'a_irt' => 1.5, 'c_irt' => 0.00, 'jawaban' => 'C',
             'pertanyaan' => '<p>Inti atom karbon <sup>12</sup>C memiliki 6 proton. Banyak <strong>neutron</strong> dalam inti tersebut adalah…</p>',
             'a_' => '<p>4</p>', 'b_' => '<p>5</p>', 'c_' => '<p>6</p>', 'd_' => '<p>7</p>', 'e_' => '<p>8</p>',
             'pembahasan' => '<p>Neutron = Nomor massa − Nomor atom = 12 − 6 = <strong>6</strong>.</p>'],
        ];

        // ── 7. Insert atau update setiap soal ─────────────────────────
        $inserted = 0;
        $updated  = 0;
        foreach ($soalList as $soal) {
            $existing = $db->table('soal_ujian')->where('kode_soal', $soal['kode'])->get()->getRowArray();

            $data = [
                'bank_ujian_id'     => $bankId,
                'pertanyaan'        => $soal['pertanyaan'],
                'pilihan_a'         => $soal['a_'],
                'pilihan_b'         => $soal['b_'],
                'pilihan_c'         => $soal['c_'],
                'pilihan_d'         => $soal['d_'],
                'pilihan_e'         => $soal['e_'],
                'jawaban_benar'     => $soal['jawaban'],
                'pembahasan'        => $soal['pembahasan'],
                'media'             => null,
                'tingkat_kesulitan' => $soal['b_irt'],
                'a'                 => $soal['a_irt'],
                'c'                 => $soal['c_irt'],
                'variabel_id'       => $varIds[$soal['var']],
                'indikator_id'      => $indIds[$soal['ind']],
                'materi_id'         => $matIds[$soal['mat']],
                'is_bank_soal'      => 1,
                'updated_at'        => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $db->table('soal_ujian')->where('kode_soal', $soal['kode'])->update($data);
                $updated++;
            } else {
                $data['kode_soal']  = $soal['kode'];
                $data['created_by'] = 1;
                $data['created_at'] = date('Y-m-d H:i:s');
                $db->table('soal_ujian')->insert($data);
                $inserted++;
            }
        }

        $total = $db->table('soal_ujian')->where('bank_ujian_id', $bankId)->countAllResults();
        \CodeIgniter\CLI\CLI::write("{$inserted} soal baru, {$updated} soal diperbarui (variabel/indikator/materi).");
        \CodeIgniter\CLI\CLI::write("Total soal di bank ID {$bankId}: {$total} soal.");
    }
}
