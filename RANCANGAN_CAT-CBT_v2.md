# RANCANGAN PENGEMBANGAN CAT-CBT v2.0

> **Status:** Perencanaan  
> **Target:** Platform Asesmen Dual-Engine (CAT + CBT)  
> **Framework:** CodeIgniter 4  
> **DBMS:** MariaDB/MySQL  

---

## 📋 Daftar Isi

- [Fase 1: Database — Skema Baru](#fase-1-database--skema-baru)
- [Fase 2: Engine CBT (Fixed-Form)](#fase-2-engine-cbt-fixed-form)
- [Fase 3: Engine CAT (IRT 3-PL)](#fase-3-engine-cat-irt-3-pl)
- [Fase 4: Bank Soal & Metadata](#fase-4-bank-soal--metadata)
- [Fase 5: Pool Paket & Generate](#fase-5-pool-paket--generate)
- [Fase 6: Repeated Test (Ujian Berulang)](#fase-6-repeated-test-ujian-berulang)
- [Fase 7: Manajemen Peserta & Opsi Ujian](#fase-7-manajemen-peserta--opsi-ujian)
- [Fase 8: Modul Analisis Data](#fase-8-modul-analisis-data)
- [Fase 9: UI/UX & Integrasi](#fase-9-uiux--integrasi)
- [Fase 10: Testing & Deployment](#fase-10-testing--deployment)

---

## Fase 1: Database — Skema Baru

### 1.1 Tabel Baru yang Dibutuhkan

#### `variabel` — Variabel/kompetensi soal
- [ ] Buat tabel `variabel`
  - `variabel_id` INT(11) PK AUTO_INCREMENT
  - `nama_variabel` VARCHAR(100)
  - `deskripsi` TEXT NULL
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP

#### `indikator` — Indikator capaian (dependen ke variabel)
- [ ] Buat tabel `indikator`
  - `indikator_id` INT(11) PK AUTO_INCREMENT
  - `variabel_id` INT(11) FK → `variabel.variabel_id`
  - `nama_indikator` VARCHAR(200)
  - `deskripsi` TEXT NULL
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP

#### `materi` — Materi pelajaran
- [ ] Buat tabel `materi`
  - `materi_id` INT(11) PK AUTO_INCREMENT
  - `nama_materi` VARCHAR(200)
  - `deskripsi` TEXT NULL
  - `created_at` TIMESTAMP
  - `updated_at` TIMESTAMP

#### `paket_ujian` — Paket soal hasil generate
- [ ] Buat tabel `paket_ujian`
  - `paket_id` INT(11) PK AUTO_INCREMENT
  - `ujian_id` INT(11) FK → `ujian.id_ujian`
  - `nama_paket` VARCHAR(50) — ex: "Paket A", "Paket B", ...
  - `nomor_paket` INT(11) — 1, 2, 3, ...
  - `created_at` TIMESTAMP

#### `paket_ujian_item` — Soal-soal dalam satu paket
- [ ] Buat tabel `paket_ujian_item`
  - `paket_item_id` INT(11) PK AUTO_INCREMENT
  - `paket_id` INT(11) FK → `paket_ujian.paket_id` ON DELETE CASCADE
  - `soal_id` INT(11) FK → `soal_ujian.soal_id`
  - `nomor_urut` INT(11) — urutan soal dalam paket (1, 2, 3, ...)
  - UNIQUE KEY (`paket_id`, `soal_id`)

#### `attempt_ujian` — Tracking attempt per siswa
- [ ] Buat tabel `attempt_ujian`
  - `attempt_id` INT(11) PK AUTO_INCREMENT
  - `peserta_ujian_id` INT(11) FK → `peserta_ujian.peserta_ujian_id`
  - `nomor_attempt` TINYINT(1) — 1, 2, 3
  - `paket_id` INT(11) FK → `paket_ujian.paket_id`
  - `status` ENUM('belum_mulai','sedang_mengerjakan','selesai')
  - `waktu_mulai` DATETIME NULL
  - `waktu_selesai` DATETIME NULL
  - `nilai_akhir` DECIMAL(5,3) NULL
  - UNIQUE KEY (`peserta_ujian_id`, `nomor_attempt`)

#### `attempt_jawaban` — Jawaban per attempt (menggantikan `hasil_ujian` untuk CBT)
- [ ] Buat tabel `attempt_jawaban`
  - `jawaban_id` INT(11) PK AUTO_INCREMENT
  - `attempt_id` INT(11) FK → `attempt_ujian.attempt_id`
  - `soal_id` INT(11) FK → `soal_ujian.soal_id`
  - `nomor_tampil` INT(11) — nomor urut yang ditampilkan ke siswa (hasil shuffle)
  - `jawaban_siswa` ENUM('A','B','C','D','E')
  - `is_correct` TINYINT(1) NULL
  - `waktu_menjawab` TIMESTAMP
  - `theta_saat_ini` DECIMAL(5,3) NULL — hanya untuk CAT
  - `se_saat_ini` DECIMAL(5,3) NULL — hanya untuk CAT
  - `delta_se_saat_ini` DECIMAL(5,3) NULL — hanya untuk CAT
  - `pi_saat_ini` DECIMAL(5,3) NULL — hanya untuk CAT
  - `qi_saat_ini` DECIMAL(5,3) NULL — hanya untuk CAT
  - `ii_saat_ini` DECIMAL(5,3) NULL — hanya untuk CAT

### 1.2 Modifikasi Tabel Existing

#### `soal_ujian` — Tambah kolom metadata
- [ ] Tambah `a` DECIMAL(5,3) DEFAULT 1.000 — parameter diskriminasi IRT 3PL
- [ ] Tambah `c` DECIMAL(5,3) DEFAULT 0.000 — parameter pseudo-guessing IRT 3PL
- [ ] Tambah `variabel_id` INT(11) NULL FK → `variabel.variabel_id`
- [ ] Tambah `indikator_id` INT(11) NULL FK → `indikator.indikator_id`
- [ ] Tambah `materi_id` INT(11) NULL FK → `materi.materi_id`
- [ ] Rename/refactor `foto` → `media` VARCHAR(255) NULL (bisa gambar, tabel, formula)

#### `ujian` — Tambah kolom mekanisme & opsi
- [ ] Tambah `tipe_ujian` ENUM('CAT','CBT') NOT NULL — dual engine
- [ ] Tambah `tampilkan_pembahasan` TINYINT(1) DEFAULT 0
- [ ] Tambah `visibilitas` ENUM('terbuka','tertutup') DEFAULT 'terbuka'
- [ ] Tambah `pengulangan_aktif` TINYINT(1) DEFAULT 0
- [ ] Tambah `maksimal_attempt` TINYINT(1) DEFAULT 1 — 1-3
- [ ] Tambah `acak_urutan_soal` TINYINT(1) DEFAULT 0 — shuffle tiap attempt
- [ ] Tambah `acak_pilihan_jawaban` TINYINT(1) DEFAULT 0 — shuffle A/B/C/D/E

#### `bank_ujian` — Tambah relasi multi-bank
- [ ] Tambah `is_active` TINYINT(1) DEFAULT 1

#### `jadwal_ujian` — Tambah penugasan granular
- [ ] Tambah `tipe_penugasan` ENUM('kelas','individu') DEFAULT 'kelas'
- [ ] Tambah `siswa_ids` TEXT NULL — JSON array of siswa_id untuk tipe individu

---

## Fase 2: Engine CBT (Fixed-Form)

### 2.1 Konfigurasi Ujian CBT
- [ ] Tambah form pemilihan tipe ujian (CAT/CBT) di halaman tambah ujian
- [ ] Jika CBT: disable parameter IRT (se_awal, se_minimum, delta_se_minimum)
- [ ] Tentukan jumlah soal per paket
- [ ] Tentukan jumlah paket yang akan di-generate

### 2.2 Alur Generate Paket
- [ ] Tombol "Generate Paket" di halaman kelola soal ujian
- [ ] Input: jumlah_paket (ex: 5), jumlah_soal_per_paket (ex: 25)
- [ ] Logic:
  - [ ] Ambil semua soal dari bank_ujian yang di-assign ke ujian ini
  - [ ] Validasi: total soal ≥ jumlah_paket × jumlah_soal_per_paket
  - [ ] Untuk setiap paket: `SELECT soal_id FROM soal_ujian WHERE ujian_id=X ORDER BY RAND() LIMIT N`
  - [ ] Insert ke `paket_ujian` + `paket_ujian_item` dengan nomor_urut 1..N
  - [ ] Tampilkan preview setiap paket (soal apa saja yang terisi)

### 2.3 Pengerjaan Ujian CBT
- [ ] Siswa masuk ujian → cek tipe CBT
- [ ] Attempt 1: pilih 1 paket secara random dari tabel `paket_ujian`
- [ ] Simpan `paket_id` ke `attempt_ujian`, lock permanen
- [ ] Tampilkan semua soal dalam paket (bisa navigasi maju-mundur)
- [ ] Timer countdown sesuai durasi
- [ ] Submit: simpan semua jawaban ke `attempt_jawaban`
- [ ] Hitung skor: jumlah benar / total soal × 100

### 2.4 Review CBT
- [ ] Setelah selesai, tampilkan hasil
- [ ] Jika `tampilkan_pembahasan = 1`: tampilkan pembahasan per soal
- [ ] Tampilkan jawaban benar vs jawaban siswa
- [ ] Status per soal: benar (hijau) / salah (merah)

---

## Fase 3: Engine CAT (IRT 3-PL)

### 3.1 Upgrade Model IRT dari 1PL ke 3PL
- [ ] Update perhitungan probabilitas:

```
Rumus 3PL:
Pi(θ) = c + (1 - c) × e^(a(θ - b)) / (1 + e^(a(θ - b)))

Dimana:
  a = daya pembeda (diskriminasi), default 1
  b = tingkat kesulitan, default 0
  c = pseudo-guessing (tebakan), default 0
  θ = estimasi kemampuan siswa
```

- [ ] Update fungsi informasi soal:

```
Rumus 3PL Information Function:
Ii(θ) = a² × (Pi - c)² × Qi / ((1 - c)² × Pi)

Qi = 1 - Pi
```

- [ ] Update pemilihan soal berikutnya:
  - Cari soal dengan `Ii(θ)` tertinggi (bukan lagi berdasarkan kedekatan b)
  - Filter: soal belum dijawab

### 3.2 Parameter Soal per Level
- [ ] Form input parameter `a`, `b`, `c` saat tambah/edit soal
- [ ] Default: `a = 1.000`, `b = 0.000`, `c = 0.000`
- [ ] Validasi range:
  - `a`: 0.01 – 5.00 (diskriminasi positif)
  - `b`: -4.00 – 4.00 (tingkat kesulitan)
  - `c`: 0.00 – 1.00 (probabilitas tebakan)

### 3.3 Estimasi Theta (Maximum Likelihood)
- [ ] Implementasi MLE untuk update theta setelah setiap jawaban
- [ ] Fallback: jika MLE gagal konvergen, gunakan pendekatan sederhana (θ = b)

### 3.4 Stop Condition CAT
- [ ] SE < se_minimum
- [ ] |delta_SE| < delta_se_minimum
- [ ] Maksimal soal tampil (opsional, bisa diaktifkan)
- [ ] Tidak ada soal tersisa yang memenuhi kriteria Ii

---

## Fase 4: Bank Soal & Metadata

### 4.1 CRUD Variabel
- [ ] Halaman kelola variabel (admin & guru)
- [ ] Tambah / Edit / Hapus variabel
- [ ] List: nama_variabel, deskripsi, jumlah soal terkait

### 4.2 CRUD Indikator
- [ ] Halaman kelola indikator
- [ ] Dropdown filter variabel → tampilkan indikator-nya
- [ ] Tambah indikator: pilih variabel, isi nama, deskripsi

### 4.3 CRUD Materi
- [ ] Halaman kelola materi
- [ ] Tambah / Edit / Hapus materi

### 4.4 Integrasi dengan Form Soal
- [ ] Tambah dropdown/select2: Variabel, Indikator, Materi
- [ ] Indikator difilter berdasarkan variabel yang dipilih (AJAX cascade)
- [ ] Simpan `variabel_id`, `indikator_id`, `materi_id` ke `soal_ujian`

### 4.5 Media Support
- [ ] Upgrade CKEditor 4 ke CKEditor 5 (atau tetap CKEditor 4 + plugin math)
- [ ] Support upload gambar → `public/uploads/soal/`
- [ ] Support input tabel
- [ ] Support formula matematika (MathJax / KaTeX / CKEditor math plugin)
- [ ] Validasi ukuran file upload (max 2MB)

### 4.6 Multi-Bank Assignment
- [ ] Ujian dapat meng-assign beberapa bank soal sekaligus
- [ ] Buat tabel pivot: `ujian_bank` (ujian_id, bank_ujian_id)
- [ ] Halaman kelola ujian: checklist bank soal yang diikutsertakan
- [ ] Generate paket mengambil soal dari SEMUA bank terpilih

---

## Fase 5: Pool Paket & Generate

### 5.1 UI Generate Paket
- [ ] Halaman `/admin/ujian/{id}/generate-paket`
- [ ] Input:
  - Jumlah paket (1-20)
  - Jumlah soal per paket (1-100)
- [ ] Tombol "Generate & Simpan"
- [ ] Validasi:
  - Total soal di bank ≥ jumlah_paket × jumlah_soal_per_paket
  - Jika tidak cukup: tampilkan warning + kurangi otomatis

### 5.2 Logic Random Selection
- [ ] Ambil semua soal_id dari bank_ujian terkait
- [ ] Untuk i = 1 to jumlah_paket:
  - [ ] `SELECT soal_id FROM soal_ujian WHERE ujian_id=X ORDER BY RAND() LIMIT N`
  - [ ] Insert ke `paket_ujian`: nama = "Paket " + i
  - [ ] Insert ke `paket_ujian_item`: tiap soal dengan nomor_urut
- [ ] Transaksi database (rollback jika gagal)

### 5.3 Preview & Regenerasi
- [ ] Tampilkan tabel paket yang sudah di-generate
- [ ] Klik nama paket → lihat daftar soal di dalamnya
- [ ] Tombol "Regenerate" → hapus paket lama + generate ulang
- [ ] Tombol "Hapus Semua Paket" → clear semua paket

### 5.4 Assignment ke Siswa (First Attempt Locking)
- [ ] Saat siswa pertama kali klik "Mulai" untuk ujian CBT:
  - [ ] Cek `attempt_ujian` untuk siswa ini
  - [ ] Jika attempt 1 belum ada:
    - [ ] `SELECT paket_id FROM paket_ujian WHERE ujian_id=X ORDER BY RAND() LIMIT 1`
    - [ ] Simpan ke `attempt_ujian`
    - [ ] **Lock**: paket_id sudah terikat ke siswa, tidak akan berubah
  - [ ] Jika attempt 1 sudah ada: gunakan paket yang sama
- [ ] Tampilkan soal sesuai `nomor_urut` di `paket_ujian_item`

---

## Fase 6: Repeated Test (Ujian Berulang)

### 6.1 Konfigurasi Pengulangan
- [ ] Di form tambah/edit ujian:
  - [ ] Toggle `pengulangan_aktif` (ON/OFF)
  - [ ] Jika ON: pilih `maksimal_attempt` (1, 2, atau 3)
  - [ ] Tentukan jadwal per attempt (tanggal_mulai_1, tanggal_selesai_1, ...)
- [ ] Alternatif: satu rentang waktu, siswa bebas memilih kapan attempt

### 6.2 Flow Attempt
- [ ] Siswa masuk halaman ujian → lihat daftar attempt:
  ```
  Attempt 1: [SELESAI] - Nilai: 78
  Attempt 2: [MULAI]           ← tombol mulai
  Attempt 3: [TERKUNCI]         ← baru bisa setelah attempt 2 selesai
  ```
- [ ] Validasi attempt:
  - [ ] Attempt N hanya bisa dimulai jika attempt N-1 sudah selesai
  - [ ] Attempt tidak bisa dimulai jika di luar jadwal
  - [ ] Maksimal 3 attempt

### 6.3 Konsistensi Paket + Shuffle
- [ ] **Paket sama**: untuk attempt 2 dan 3, gunakan `paket_id` yang sama dengan attempt 1
- [ ] **Urutan diacak**: saat generate tampilan untuk attempt 2/3:
  - [ ] Ambil soal dari `paket_ujian_item` (soal sama)
  - [ ] `ORDER BY RAND()` saat mengambil dari database
  - [ ] Simpan `nomor_tampil` di `attempt_jawaban` (urutan yang dilihat siswa)
- [ ] **Pilihan diacak** (jika `acak_pilihan_jawaban = 1`):
  - [ ] Shuffle A/B/C/D/E di frontend
  - [ ] Simpan mapping jawaban asli → jawaban tampil

### 6.4 Penyimpanan Nilai per Attempt
- [ ] Setiap attempt simpan record terpisah di `attempt_ujian`
- [ ] `nilai_akhir` dihitung saat attempt selesai
- [ ] Tampilkan **3 nilai terpisah** di halaman hasil:
  ```
  Attempt 1: 78 (Baik)
  Attempt 2: 85 (Sangat Baik)
  Attempt 3: 90 (Sangat Baik)
  ```

---

## Fase 7: Manajemen Peserta & Opsi Ujian

### 7.1 Tipe Penugasan
- [ ] Di form tambah/edit jadwal:
  - [ ] Radio: "Per Kelas" vs "Per Individu"
  - [ ] Jika "Per Kelas": dropdown pilih kelas (existing)
  - [ ] Jika "Per Individu": multi-select siswa (searchable, AJAX)
- [ ] Simpan `tipe_penugasan` dan `siswa_ids` (JSON) ke `jadwal_ujian`

### 7.2 Auto-Enroll Peserta
- [ ] Jika tipe "Per Kelas":
  - [ ] Ambil semua `siswa_id` dari `kelas_id` terkait
  - [ ] Auto-insert ke `peserta_ujian` saat jadwal dibuat
- [ ] Jika tipe "Per Individu":
  - [ ] Parse JSON `siswa_ids`
  - [ ] Insert hanya siswa yang dipilih ke `peserta_ujian`

### 7.3 Tampilkan Pembahasan
- [ ] Toggle di form ujian: `tampilkan_pembahasan`
- [ ] Jika ON: setelah ujian selesai, siswa bisa lihat pembahasan per soal
- [ ] Jika OFF: hanya tampilkan benar/salah, tanpa pembahasan
- [ ] Note: untuk repeated test, pembahasan baru muncul setelah attempt TERAKHIR selesai

### 7.4 Visibilitas Ujian
- [ ] Toggle di form ujian: `visibilitas`
- [ ] `terbuka`: semua siswa eligible bisa melihat ujian di dashboard
- [ ] `tertutup`: hanya siswa yang sudah di-assign (di `peserta_ujian`) yang bisa melihat

---

## Fase 8: Modul Analisis Data

### 8.1 Backend — Query Analytics
- [ ] Buat `AnalisisModel.php`
- [ ] Method: `getGlobalStats(ujian_id)` — statistik seluruh peserta
- [ ] Method: `getKelasStats(ujian_id, kelas_id)` — statistik per kelas
- [ ] Method: `getIndividuStats(peserta_ujian_id)` — statistik per siswa
- [ ] Method: `getPerVariabel(ujian_id)` — performa per variabel
- [ ] Method: `getPerIndikator(ujian_id, variabel_id)` — performa per indikator
- [ ] Method: `getPerMateri(ujian_id)` — performa per materi

### 8.2 Dashboard Analisis
- [ ] Halaman `/admin/hasil-ujian/analisis/{ujian_id}`
- [ ] Tabs: Global | Per Kelas | Per Individu

#### Tab Global:
- [ ] Statistik ringkasan: total peserta, rata-rata nilai, nilai tertinggi, terendah
- [ ] Distribusi nilai (histogram/bar chart)
- [ ] Klasifikasi kognitif: pie chart (Sangat Rendah – Sangat Baik)
- [ ] Performa per Variabel: bar chart
- [ ] Performa per Indikator: bar chart (filterable by variabel)
- [ ] Performa per Materi: bar chart

#### Tab Per Kelas:
- [ ] Dropdown pilih kelas
- [ ] Tabel: nama siswa, nilai per attempt, rata-rata
- [ ] Grafik perbandingan antar kelas

#### Tab Per Individu:
- [ ] Search box cari siswa
- [ ] Detail: nama, kelas, nilai per attempt
- [ ] Radar chart: kekuatan per variabel
- [ ] Timeline pengerjaan soal (urutan, waktu per soal, benar/salah)
- [ ] Rekomendasi: variabel/indikator mana yang perlu diperbaiki

### 8.3 Klasifikasi Kategori Kemampuan
- [ ] Tampilkan klasifikasi per dimensi:
  - Per Variabel: mana yang kuat, mana yang lemah
  - Per Indikator: mana yang tercapai, mana yang belum
  - Per Materi: mana yang dikuasai, mana yang perlu review

### 8.4 Export
- [ ] Export ke Excel (reuse PhpSpreadsheet atau buat CSV)
- [ ] Export ke PDF (reuse existing template)
- [ ] Export grafik sebagai PNG (Chart.js → canvas.toBlob)

---

## Fase 9: UI/UX & Integrasi

### 9.1 Sidebar Navigasi Baru
- [ ] Update sidebar admin: tambah menu Analisis Data
- [ ] Update sidebar guru: tambah menu Analisis Data (terbatas)
- [ ] Update sidebar siswa: tampilkan attempt history

### 9.2 Form Builder Ujian
- [ ] Wizard / step form untuk membuat ujian:
  - Step 1: Info Dasar (nama, kode, deskripsi)
  - Step 2: Pilih Engine (CAT / CBT)
  - Step 3: Konfigurasi (parameter IRT / jumlah paket)
  - Step 4: Pilih Bank Soal (multi-select)
  - Step 5: Opsi (pembahasan, visibilitas, pengulangan)
  - Step 6: Generate Paket (otomatis setelah save)

### 9.3 Soal Editor Upgrade
- [ ] Integrasi MathJax/KaTeX untuk formula LaTeX
- [ ] Preview soal sebelum simpan
- [ ] Bulk import soal dari Excel/CSV

### 9.4 Timer & Auto-Submit
- [ ] Countdown timer real-time di halaman soal
- [ ] Auto-submit saat waktu habis
- [ ] Konfirmasi sebelum submit manual
- [ ] Warning saat waktu ≤ 5 menit

---

## Fase 10: Testing & Deployment

### 10.1 Unit Testing
- [ ] Test model: CRUD semua tabel baru
- [ ] Test CAT 3PL: verifikasi perhitungan Pi, Ii, SE
- [ ] Test CBT paket: generate, assignment, shuffle
- [ ] Test attempt: konsistensi paket, urutan soal
- [ ] Test analisis: akurasi query per variabel/indikator/materi

### 10.2 Integration Testing
- [ ] Flow admin: buat ujian CBT → generate paket → assign ke kelas
- [ ] Flow admin: buat ujian CAT → atur parameter → assign individu
- [ ] Flow siswa: login → lihat ujian → attempt 1 → attempt 2 → lihat hasil
- [ ] Flow analisis: akses dashboard → filter → export

### 10.3 Migrasi Data Lama
- [ ] Script migrasi: isi parameter `a`, `c` untuk soal existing (default 1, 0)
- [ ] Script migrasi: set `tipe_ujian = 'CAT'` untuk ujian existing
- [ ] Backup database sebelum migrasi

### 10.4 Deployment
- [ ] Update `.env` production
- [ ] Run migration
- [ ] Clear cache
- [ ] Smoke test semua fitur

---

## 📊 Estimasi Waktu

| Fase | Deskripsi | Estimasi | Prioritas |
|------|-----------|----------|-----------|
| Fase 1 | Database skema baru | 3-4 hari | 🔴 Tinggi |
| Fase 2 | Engine CBT | 5-7 hari | 🔴 Tinggi |
| Fase 3 | Engine CAT 3PL | 5-7 hari | 🔴 Tinggi |
| Fase 4 | Bank Soal & Metadata | 3-4 hari | 🟡 Medium |
| Fase 5 | Pool Paket & Generate | 3-4 hari | 🔴 Tinggi |
| Fase 6 | Repeated Test | 4-5 hari | 🟡 Medium |
| Fase 7 | Manajemen Peserta | 2-3 hari | 🟡 Medium |
| Fase 8 | Modul Analisis | 5-7 hari | 🟢 Rendah |
| Fase 9 | UI/UX & Integrasi | 4-5 hari | 🟢 Rendah |
| Fase 10 | Testing & Deploy | 3-4 hari | 🟢 Rendah |
| **Total** | | **37-50 hari** | |

---

## 📝 Catatan Teknis

### Perbedaan Kunci CAT vs CBT

| Aspek | CAT | CBT |
|-------|-----|-----|
| Pemilihan soal | Adaptif per jawaban | Fixed, dari paket yang di-generate |
| Jumlah soal | Variabel (tergantung SE) | Fixed (sesuai paket) |
| Urutan soal | Ditentukan algoritma | Sesuai nomor_urut (bisa dishuffle) |
| Model IRT | 3PL (a, b, c) | Tidak digunakan |
| Skoring | Theta → skor kognitif | Benar/Salah → persentase |
| Parameter | a, b, c, θ, SE, Ii | Tidak ada |

### Rumus IRT 3PL

```
Probabilitas menjawab benar:
Pi(θ) = c + (1 - c) / (1 + e^(-a(θ - b)))

Item Information Function:
Ii(θ) = a² × (Pi - c)² × (1 - Pi) / ((1 - c)² × Pi)

Standard Error:
SE = 1 / √(Σ Ii)

Skor Kognitif:
Skor = 50 + (16.67 × θ)
```

### Klasifikasi Kognitif (sama dengan v1)

| Skor | Kategori |
|------|----------|
| < 25 | Sangat Rendah |
| 25-41 | Rendah |
| 42-57 | Cukup |
| 58-74 | Baik |
| ≥ 75 | Sangat Baik |

---
