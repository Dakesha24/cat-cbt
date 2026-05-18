# Alur Aplikasi CAT-CBT

> **CAT-CBT** = **Computer Adaptive Test – Computer Based Test**  
> **Versi:** v2.0 — Dual Engine (CAT IRT 3PL + CBT Fixed-Form)  
> Sistem ujian adaptif berbasis komputer yang menyesuaikan tingkat kesulitan soal dengan kemampuan peserta secara real-time menggunakan **Item Response Theory (IRT) model 3PL**, serta mendukung ujian fixed-form dengan generate paket acak.

---

## Daftar Isi

1. [Autentikasi & Role](#1-autentikasi--role)
2. [Alur Admin](#2-alur-admin)
3. [Alur Guru](#3-alur-guru)
4. [Alur Siswa (Peserta Ujian)](#4-alur-siswa-peserta-ujian)
5. [Mekanisme CAT (Computer Adaptive Test) — IRT 3PL](#5-mekanisme-cat-computer-adaptive-test)
6. [Mekanisme CBT (Fixed-Form) — Generate Paket](#6-mekanisme-cbt-fixed-form)
7. [Algoritma IRT 3PL](#7-algoritma-irt-3pl)
8. [Repeated Test (Ujian Berulang)](#8-repeated-test-ujian-berulang)
9. [Klasifikasi Kemampuan Kognitif](#9-klasifikasi-kemampuan-kognitif)
10. [Diagram Alir Lengkap](#10-diagram-alir-lengkap)

---

## 1. Autentikasi & Role

### 1.1 Registrasi

```
User → Register (POST /register)
  ↓
  Validasi: username unik, email unik, password ≥ 6 char, konfirmasi password
  ↓
  Password di-hash dengan bcrypt (PASSWORD_DEFAULT)
  ↓
  Role otomatis = 'siswa' (publik hanya bisa daftar sebagai siswa)
  ↓
  Status = 'active'
  ↓
  Redirect ke /login dengan pesan sukses
```

> **Catatan:** Admin dan Guru tidak bisa daftar sendiri. Akun admin/guru dibuat oleh **Admin** melalui dashboard.

### 1.2 Login

```
User → Login (POST /login)
  ↓
  Validasi: username + password
  ↓
  Cek user di tabel users
  ↓
  ├─ User tidak ditemukan → error "Invalid username or password"
  ├─ Password salah → error "Invalid username or password"
  ├─ Status 'inactive' → error "Akun dinonaktifkan"
  └─ Login berhasil → set session:
       user_id, username, role, logged_in = TRUE
       ↓
       Redirect berdasarkan role:
       ├─ admin → /admin/dashboard
       ├─ guru  → /guru/dashboard
       └─ siswa → /siswa/dashboard
```

### 1.3 Middleware Auth

Semua route `/admin/*`, `/guru/*`, `/siswa/*` dilindungi `AuthFilter`. Jika belum login, redirect ke `/login`.

---

## 2. Alur Admin

Admin adalah **superuser** yang mengelola seluruh sistem.

### 2.1 Dashboard Admin (`/admin/dashboard`)

Menampilkan statistik:
- Total Guru
- Total Siswa
- Total Sekolah
- Total Kelas

### 2.2 Kelola Sekolah (`/admin/sekolah`)

```
Admin → Daftar Sekolah
  ↓
  ├─ Tambah Sekolah → form (nama, alamat, telepon, email)
  ├─ Edit Sekolah   → form edit
  ├─ Hapus Sekolah   → hanya jika tidak ada guru
  └─ Lihat Kelas     → /admin/sekolah/{id}/kelas
       ↓
       ├─ Tambah Kelas → form (nama_kelas, tahun_ajaran)
       ├─ Edit Kelas   → form edit
       ├─ Hapus Kelas  → hanya jika tidak ada siswa & guru
       └─ Detail Kelas → /admin/sekolah/{id}/kelas/{id}/detail
            ↓
            ├─ Assign Guru ke Kelas   (via kelas_guru)
            ├─ Remove Guru dari Kelas
            ├─ Lihat Daftar Siswa     (filter per kelas)
            └─ Transfer Siswa         (pindah kelas/sekolah)
```

### 2.3 Kelola Guru (`/admin/guru`)

```
Admin → Daftar Guru (JOIN users, guru, sekolah, kelas_guru)
  ↓
  ├─ Tambah Guru → form lengkap:
  │     - username, email, password
  │     - nip, nama_lengkap, mata_pelajaran
  │     - pilih sekolah → filter kelas
  │     - assign ke kelas (opsional, multi-select)
  │     → insert ke users + guru + kelas_guru (transaksi)
  │
  ├─ Edit Guru → form edit + kelola kelas yang diajar
  │     - assign kelas baru
  │     - remove kelas existing
  │
  ├─ Nonaktifkan Guru → soft delete (status = 'inactive')
  └─ Aktifkan Guru    → restore (status = 'active')
```

### 2.4 Kelola Siswa (`/admin/siswa`)

```
Admin → Daftar Siswa (JOIN users, siswa, kelas, sekolah)
  ↓
  ├─ Tambah Siswa → form:
  │     - username, email, password
  │     - nama_lengkap, nomor_peserta, jenis_kelamin
  │     - pilih sekolah → filter kelas → pilih kelas
  │     → insert ke users + siswa
  │
  ├─ Batch Create Siswa → (max 50 sekaligus)
  │     - prefix username, kelas, jumlah, jenis_kelamin
  │     - auto-generate: username = prefix001, email = prefix001@sekolah.com
  │     - password default = 'password123'
  │
  ├─ Edit Siswa → form edit
  ├─ Nonaktifkan Siswa → soft delete
  └─ Aktifkan Siswa    → restore
```

### 2.5 Kelola Jenis Ujian (`/admin/jenis-ujian`)

```
Admin → Daftar Jenis Ujian (mata pelajaran/kategori)
  ↓
  ├─ Tambah → nama_jenis, deskripsi, kelas_id (opsional), created_by
  ├─ Edit
  └─ Hapus
```

### 2.6 Kelola Bank Soal (`/admin/bank-soal`)

```
Admin → Bank Soal (terstruktur 3 level)
  ↓
  Level 1: Pilih Kategori
  ↓
  Level 2: Pilih Jenis Ujian (dalam kategori)
  ↓
  Level 3: Pilih Bank Ujian (dalam jenis ujian)
  ↓
  ├─ Tambah Bank Ujian → kategori, jenis_ujian_id, nama_ujian, deskripsi
  ├─ Tambah Soal ke Bank → pertanyaan, pilihan A-E, jawaban_benar, tingkat_kesulitan, foto, pembahasan
  ├─ Edit Soal
  └─ Hapus Soal / Hapus Bank
```

### 2.7 Kelola Ujian — Paket Soal (`/admin/ujian`)

```
Admin → Daftar Ujian (paket soal yang akan diujikan)
  ↓
  ├─ Tambah Ujian → form:
  │     - jenis_ujian_id, nama_ujian, kode_ujian, deskripsi
  │     - se_awal (default 1.0000)      ← parameter IRT
  │     - se_minimum (default 0.2500)    ← stop condition
  │     - delta_se_minimum (default 0.0100) ← stop condition
  │     - maksimal_soal_tampil (default 20)
  │     - durasi (TIME)
  │     - kelas_id (opsional)
  │
  ├─ Edit Ujian
  └─ Hapus Ujian
```

### 2.8 Kelola Soal dalam Ujian (`/admin/soal/{ujian_id}`)

```
Admin → Kelola Soal Ujian tertentu
  ↓
  ├─ Tambah Soal Manual → pertanyaan, pilihan A-E, jawaban_benar, tingkat_kesulitan, foto, pembahasan
  ├─ Import Soal dari Bank → pilih soal dari bank_ujian untuk dimasukkan ke ujian
  ├─ Edit Soal
  └─ Hapus Soal
```

### 2.9 Kelola Jadwal Ujian (`/admin/jadwal-ujian`)

```
Admin → Daftar Jadwal Ujian
  ↓
  ├─ Tambah Jadwal → form:
  │     - ujian_id (paket soal)
  │     - kelas_id (target peserta)
  │     - guru_id (pengawas/penanggung jawab)
  │     - tanggal_mulai, tanggal_selesai (rentang waktu)
  │     - durasi_menit
  │     - kode_akses (kode rahasia untuk masuk ujian)
  │     - status otomatis → 'belum_mulai'
  │
  ├─ Edit Jadwal
  └─ Hapus Jadwal

Catatan: Status jadwal diperbarui otomatis oleh sistem:
  - sekarang < tanggal_mulai           → 'belum_mulai'
  - tanggal_mulai ≤ sekarang ≤ selesai → 'sedang_berlangsung'
  - sekarang > tanggal_selesai         → 'selesai'
```

### 2.10 Kelola Hasil Ujian (`/admin/hasil-ujian`)

```
Admin → Daftar Hasil Ujian (semua peserta)
  ↓
  ├─ Lihat per Jadwal → daftar siswa yang sudah selesai
  ├─ Detail per Siswa → lihat jawaban, nilai theta, SE, klasifikasi
  ├─ Download Excel / PDF
  └─ Hapus Hasil Siswa (reset)
```

### 2.11 Kelola Pengumuman (`/admin/pengumuman`)

```
Admin → Daftar Pengumuman
  ↓
  ├─ Tambah → judul, isi, tanggal_publish, tanggal_berakhir
  ├─ Edit
  ├─ Detail
  ├─ Hapus
  └─ Toggle Status (aktif/nonaktif)
```

---

## 3. Alur Guru

Guru memiliki akses terbatas pada data miliknya sendiri.

### 3.1 Dashboard Guru (`/guru/dashboard`)

Informasi pribadi guru & ringkasan.

### 3.2 Fitur Guru

| Fitur | Keterangan |
|-------|-----------|
| **Jenis Ujian** | CRUD mata pelajaran yang diajar |
| **Bank Soal** | CRUD bank soal dan soal di dalamnya |
| **Ujian** | CRUD paket ujian (seperti admin, tapi terbatas) |
| **Soal Ujian** | CRUD soal per ujian + import dari bank |
| **Jadwal Ujian** | Buat jadwal ujian untuk kelas yang diajar |
| **Hasil Ujian** | Lihat hasil ujian siswa + reset status siswa |
| **Pengumuman** | CRUD pengumuman |
| **Profil** | Edit profil pribadi (nama, NIP, mata pelajaran) |
| **Upload Gambar** | Upload via Summernote/CKEditor untuk soal |

---

## 4. Alur Siswa (Peserta Ujian)

### 4.1 Dashboard Siswa (`/siswa/dashboard`)

Halaman utama siswa setelah login.

### 4.2 Profil (`/siswa/profil`)

```
Siswa → Profil
  ↓
  ├─ Pertama kali: harus isi profil lengkap
  │     - nomor_peserta, nama_lengkap, jenis_kelamin
  │     - pilih sekolah → filter kelas → pilih kelas
  │     → simpan ke tabel siswa
  │
  └─ Selanjutnya: bisa edit profil
```

> **Penting:** Siswa **harus mengisi profil** sebelum bisa mengakses ujian. Jika belum, akan di-redirect ke halaman profil.

### 4.3 Melihat Pengumuman (`/siswa/pengumuman`)

Menampilkan daftar pengumuman aktif dari admin/guru.

### 4.4 Mulai Ujian (`/siswa/ujian`)

```
Siswa → Halaman Ujian
  ↓
  Sistem menampilkan daftar jadwal ujian untuk kelas siswa:
  - Filter: jadwal.kelas_id = siswa.kelas_id
  - Filter: tanggal_selesai >= sekarang
  - Filter: status != 'selesai'
  - LEFT JOIN peserta_ujian untuk cek status
  ↓
  Siswa memilih ujian → masukkan kode_akses
  ↓
  POST /siswa/ujian/mulai
  ↓
  Validasi:
  ├─ Kode akses salah → error
  └─ Kode akses benar →
       ↓
       Cek apakah sudah terdaftar sebagai peserta:
       ├─ Belum → insert ke peserta_ujian (status: 'belum_mulai')
       └─ Sudah → lanjut
       ↓
       Redirect ke /siswa/ujian/soal/{jadwalId}
```

### 4.5 Mengerjakan Soal CAT (`/siswa/ujian/soal/{jadwalId}`)

Ini adalah **inti dari sistem CAT**. Lihat [Mekanisme CAT](#5-mekanisme-cat-computer-adaptive-test) untuk detail lengkap.

```
Siswa masuk halaman soal
  ↓
  Cek status peserta:
  ├─ 'selesai' → error "Anda sudah menyelesaikan ujian ini"
  ├─ 'belum_mulai' → set status 'sedang_mengerjakan', catat waktu_mulai, inisialisasi CAT params
  └─ 'sedang_mengerjakan' → lanjutkan dari session CAT params
  ↓
  Ambil parameter CAT dari session:
  - theta = 0 (estimasi kemampuan awal)
  - SE = 1 (standard error awal)
  - answered_questions = []
  - current_question = null
  - total_questions = 0
  ↓
  Pilih soal pertama: cari soal dengan tingkat_kesulitan (b) terdekat ke theta=0
  ↓
  Tampilkan soal ke siswa:
  - Pertanyaan (HTML)
  - Pilihan A, B, C, D, E
  - Foto (jika ada)
  - Sisa waktu (countdown timer)
  ↓
  Siswa memilih jawaban → POST /siswa/ujian/simpan-jawaban
  ↓
  [Lihat Algoritma CAT di bawah]
```

### 4.6 Selesai Ujian (`/siswa/ujian/selesai/{jadwalId}`)

```
Ujian berhenti (kondisi terpenuhi) →
  ↓
  Update peserta_ujian: status = 'selesai', waktu_selesai = now
  ↓
  Hapus CAT params dari session
  ↓
  Tampilkan ringkasan:
  - Nama ujian
  - Total soal dijawab
  - Nilai akhir (theta terakhir)
  - Klasifikasi kemampuan kognitif
```

### 4.7 Riwayat & Hasil (`/siswa/hasil`)

```
Siswa → Riwayat Ujian
  ↓
  Tampilkan semua ujian yang sudah selesai:
  - Nama ujian, jenis ujian
  - Tanggal mulai & selesai
  - Durasi pengerjaan
  - Jumlah soal
  ↓
  Klik Detail → /siswa/hasil/detail/{pesertaUjianId}
  ↓
  Detail per soal:
  - Nomor soal, pertanyaan
  - Jawaban siswa vs jawaban benar
  - Waktu pengerjaan per soal
  - Tingkat kesulitan (parameter b)
  ↓
  Hasil akhir:
  - Total benar / salah
  - Skor kemampuan kognitif: 50 + (16.67 × theta)
  - Klasifikasi: Sangat Rendah / Rendah / Cukup / Baik / Sangat Baik
  - Statistik waktu: tercepat, terlama, rata-rata
  ↓
  Opsi: Download/Unduh hasil (PDF)
```

---

## 5. Mekanisme CAT (Computer Adaptive Test) — IRT 3PL ⚡v2

### 5.1 Perbedaan v1 (1PL) vs v2 (3PL)

| Parameter | v1 (1PL Rasch) | v2 (3PL) |
|-----------|----------------|----------|
| Parameter soal | b (tingkat kesulitan) saja | **a** (diskriminasi), **b** (kesulitan), **c** (guessing) |
| Pemilihan soal | Kedekatan b | **Item Information Ii**(θ) tertinggi |
| Probabilitas | Pi = e^(θ-b)/(1+e^(θ-b)) | Pi = c + (1-c)×e^(a(θ-b))/(1+e^(a(θ-b))) |

### 5.2 Flow Algoritma per Jawaban

```
┌─────────────────────────────────────────────────────────────┐
│               ALGORITMA CAT PER JAWABAN                       │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  1. Siswa menjawab soal                                       │
│       ↓                                                       │
│  2. Cek jawaban: benar atau salah?                            │
│       ↓                                                       │
│  3. Hitung probabilitas (IRT 3PL):                            │
│       Pi(θ) = c + (1-c) × e^(a(θ-b)) / (1 + e^(a(θ-b)))      │
│       Qi(θ) = 1 - Pi(θ)                                      │
│       Ii(θ) = a²×(Pi-c)²×Qi / ((1-c)²×Pi)   ← Item Info     │
│       ↓                                                       │
│  4. Hitung Total Information:                                 │
│       I_total = Σ Ii (semua soal yang sudah dijawab)          │
│       ↓                                                       │
│  5. Hitung SE baru:                                           │
│       SE_new = 1 / √(I_total)                                │
│       delta_SE = SE_old - SE_new                              │
│       ↓                                                       │
│  6. Update theta:                                             │
│       theta = b (tingkat kesulitan soal saat ini)             │
│       ↓                                                       │
│  7. Pilih soal berikutnya (MAXIMUM INFORMATION):              │
│       Cari soal dengan Ii(θ) tertinggi yang belum dijawab    │
│       (bukan lagi berdasarkan kedekatan b)                    │
│       ↓                                                       │
│  8. Simpan jawaban ke tabel attempt_jawaban (v2)              │
│       ↓                                                       │
│  9. Cek kondisi BERHENTI:                                     │
│       ├─ SE_new < se_minimum          → STOP (cukup presisi)  │
│       ├─ |delta_SE| < delta_se_minimum → STOP (tidak berubah) │
│       │    (SE sudah kecil & stabil)                          │
│       └─ Tidak ada soal tersedia      → STOP (soal habis)    │
│       ↓                                                       │
│ 10. Jika STOP → update status = 'selesai'                     │
│     Jika LANJUT → tampilkan soal berikutnya                   │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### 5.3 Kondisi Berhenti (Stop Condition)

Ujian CAT berhenti jika **salah satu** kondisi terpenuhi:

| Kondisi | Parameter | Default | Artinya |
|---------|-----------|---------|---------|
| SE minimum | `se_minimum` | 0.2500 | Standard Error sudah cukup kecil → kemampuan sudah terestimasi dengan baik |
| Delta SE | `delta_se_minimum` | 0.0100 | Perubahan SE antar soal sangat kecil → estimasi sudah stabil |
| Soal habis | — | — | Tidak ada soal yang sesuai dengan kriteria pemilihan |

> **Catatan:** `maksimal_soal_tampil` (default 20) diabaikan karena sistem CAT menentukan sendiri kapan berhenti berdasarkan presisi.

### 5.4 Session CAT Parameters

Selama ujian berlangsung, parameter berikut disimpan di PHP session:

| Parameter | Tipe | Deskripsi |
|-----------|------|-----------|
| `theta` | float | Estimasi kemampuan saat ini (nilai awal = 0) |
| `SE` | float | Standard Error saat ini (nilai awal = 1.0000) |
| `answered_questions` | array | Daftar `soal_id` yang sudah dijawab |
| `current_question` | array | Data soal yang sedang ditampilkan |
| `total_questions` | int | Jumlah soal yang sudah dijawab |

---

## 6. Mekanisme CBT (Fixed-Form) — Generate Paket 🆕v2

### 6.1 Prinsip Dasar

CBT **tidak** adaptif. Semua siswa dalam satu ujian mendapat paket soal yang di-generate secara acak dari bank soal.

1. Bank soal di-assign ke ujian via `ujian_bank` (multi-bank pivot)
2. Admin/Guru klik **"Generate Paket"** → soal dipilih acak `ORDER BY RAND()`
3. Hasil generate disimpan di `paket_ujian` + `paket_ujian_item`
4. Siswa pertama kali klik **"Mulai"** → sistem pilih 1 paket random → **lock** ke siswa
5. Semua soal dalam paket ditampilkan sekaligus (bisa navigasi maju-mundur)
6. Skor = jumlah benar / total soal × 100

### 6.2 Flow Generate Paket

```
Admin/Guru → Halaman Kelola Ujian → Klik "Generate Paket"
  ↓
  Input: jumlah_paket (ex: 5), jumlah_soal_per_paket (ex: 25)
  ↓
  Validasi: total soal di bank ≥ jumlah_paket × jumlah_soal_per_paket
  ↓
  Untuk i = 1 to jumlah_paket:
    SELECT soal_id FROM soal_ujian WHERE ujian_id=X ORDER BY RAND() LIMIT N
    INSERT INTO paket_ujian (nama: "Paket " + i)
    INSERT INTO paket_ujian_item (nomor_urut: 1..N)
  ↓
  Tampilkan preview tiap paket
  ↓
  Tombol: Regenerate (hapus lama + buat baru) / Hapus Semua
```

### 6.3 Flow Pengerjaan CBT

```
Siswa masuk halaman ujian (CBT)
  ↓
  Cek tipe ujian = 'CBT'
  ↓
  Attempt 1: SELECT paket_id FROM paket_ujian WHERE ujian_id=X ORDER BY RAND() LIMIT 1
  ↓
  Simpan ke attempt_ujian (nomor_attempt=1, paket_id=..., status='sedang_mengerjakan')
  ↓
  Tampilkan SEMUA soal dalam paket (navigasi maju-mundur)
  ↓
  Timer countdown
  ↓
  Submit → simpan semua jawaban ke attempt_jawaban
  ↓
  Hitung skor = (total_benar / total_soal) × 100
  ↓
  Update attempt_ujian: status='selesai', nilai_akhir=skor
```

### 6.4 Perbedaan Kunci CAT vs CBT

| Aspek | CAT | CBT |
|-------|-----|-----|
| Pemilihan soal | Adaptif per jawaban | Fixed, dari paket hasil generate |
| Jumlah soal | Variabel (stop saat SE cukup) | Fixed (sesuai paket) |
| Urutan soal | Ditentukan algoritma IRT | Sesuai nomor_urut (bisa dishuffle) |
| Model IRT | 3PL (a, b, c) | Tidak digunakan |
| Skoring | Theta → skor kognitif | Benar/Salah → persentase |
| Penyimpanan | `attempt_jawaban` + kolom IRT | `attempt_jawaban` (kolom IRT null) |
| Soal per halaman | 1 soal per halaman | Semua soal dalam 1 halaman |

---

## 7. Algoritma IRT 3PL

### 7.1 Model Matematika

Model **3PL (3-Parameter Logistic)** menggunakan 3 parameter per soal:

```
Probabilitas menjawab BENAR:

Pi(θ) = c + (1 - c) × e^(a(θ - b)) / (1 + e^(a(θ - b)))

Dimana:
  a = daya pembeda (diskriminasi), default 1.000, range 0.01–5.00
  b = tingkat kesulitan, default 0.000, range -4.00–4.00
  c = pseudo-guessing (tebakan), default 0.000, range 0.00–1.00
  θ = estimasi kemampuan siswa
  e = bilangan Euler (≈ 2.71828)
```

### 7.2 Interpretasi Parameter

| Parameter | Makna | Nilai Tinggi | Nilai Rendah |
|-----------|-------|-------------|-------------|
| **a** (diskriminasi) | Seberapa baik soal membedakan siswa pintar & kurang | Soal sangat diskriminatif | Soal kurang diskriminatif |
| **b** (kesulitan) | Tingkat kesulitan soal | Soal sulit | Soal mudah |
| **c** (guessing) | Probabilitas menjawab benar dengan menebak | Mudah ditebak | Sulit ditebak |

### 7.3 Fungsi Informasi Soal (Item Information)

```
Ii(θ) = a² × (Pi - c)² × Qi / ((1 - c)² × Pi)

Qi = 1 - Pi
```

- Maksimum Ii terjadi saat θ mendekati b
- Semakin besar **a**, semakin tinggi informasi maksimum → soal lebih baik
- Semakin besar **c**, semakin rendah informasi → guessing mengurangi kualitas

### 7.4 Standard Error (SE)

```
SE = 1 / √(Σ Ii)
```

- **SE awal** = 1.0000 (belum ada informasi)
- **SE → 0** artinya estimasi θ semakin akurat

### 7.5 Contoh Perhitungan 3PL

Misalkan:
- θ saat ini = 0
- Soal: a = 1.0, b = -0.234, c = 0.0 (sama dengan 1PL)

```
Pi = 0 + (1-0) × e^(1×(0-(-0.234))) / (1 + e^(1×(0-(-0.234))))
   = e^0.234 / (1 + e^0.234)
   = 0.5582

Qi = 1 - 0.5582 = 0.4418

Ii = 1² × (0.5582-0)² × 0.4418 / ((1-0)² × 0.5582)
   = 1 × 0.3116 × 0.4418 / 0.5582
   = 0.2466
```

Contoh dengan c > 0:
- Soal: a = 1.2, b = 0.5, c = 0.25
- θ = 1.0

```
Pi = 0.25 + 0.75 × e^(1.2×(1-0.5)) / (1 + e^(1.2×0.5))
   = 0.25 + 0.75 × e^0.6 / (1 + e^0.6)
   = 0.25 + 0.75 × 1.8221 / 2.8221
   = 0.25 + 0.75 × 0.6457
   = 0.7343
```

---

## 8. Repeated Test (Ujian Berulang) 🆕v2

### 8.1 Konfigurasi

Di form ujian, Guru/Admin mengatur:
- `pengulangan_aktif = 1`
- `maksimal_attempt = 2` atau `3`

### 8.2 Flow Attempt

```
Siswa masuk halaman ujian
  ↓
  Lihat daftar attempt:
  ┌──────────────────────────────────────────┐
  │ Attempt 1: [SELESAI] - Nilai: 78         │
  │ Attempt 2: [MULAI]          ← tombol     │
  │ Attempt 3: [TERKUNCI]       ← menunggu   │
  └──────────────────────────────────────────┘
  ↓
  Validasi:
  ├─ Attempt N hanya bisa dimulai jika N-1 selesai
  ├─ Attempt di luar jadwal → tidak bisa
  └─ Maksimal sesuai `maksimal_attempt`
```

### 8.3 Konsistensi Paket + Shuffle

- **Paket sama**: Attempt 2 & 3 menggunakan `paket_id` yang sama dengan Attempt 1
- **Urutan diacak**: `ORDER BY RAND()` saat mengambil dari `paket_ujian_item`
- **Pilihan diacak** (jika `acak_pilihan_jawaban=1`): shuffle A/B/C/D/E di frontend
- **Nilai terpisah**: Setiap attempt simpan record sendiri di `attempt_ujian`

---

## 9. Klasifikasi Kemampuan Kognitif

Hasil akhir ujian dikonversi dari **theta** menjadi **skor 0–100** dengan rumus:

```
Skor Akhir = 50 + (16.67 × theta)
```

### Klasifikasi:

| Rentang Skor | Kategori | Warna |
|-------------|----------|-------|
| < 25 | **Sangat Rendah** | 🔴 Merah |
| 25 – 41 | **Rendah** | 🟠 Orange |
| 42 – 57 | **Cukup** | 🟡 Kuning |
| 58 – 74 | **Baik** | 🔵 Biru |
| ≥ 75 | **Sangat Baik** | 🟢 Hijau |

### Interpretasi Theta terhadap Skor:

| Theta | Skor | Kategori |
|-------|------|----------|
| -1.50 | 25.0 | Rendah |
| -0.48 | 42.0 | Cukup |
| 0.00 | 50.0 | Cukup |
| +0.48 | 58.0 | Baik |
| +1.50 | 75.0 | Sangat Baik |

---

## 10. Diagram Alir Lengkap

```
┌─────────────────────────────────────────────────────────────────────┐
│                        ALUR APLIKASI CAT-CBT                         │
└─────────────────────────────────────────────────────────────────────┘

                            ┌──────────┐
                            │  LOGIN   │
                            └────┬─────┘
                                 │
                    ┌────────────┼────────────┐
                    ▼            ▼            ▼
               ┌───────┐   ┌───────┐   ┌──────────┐
               │ ADMIN │   │ GURU  │   │  SISWA   │
               └───┬───┘   └───┬───┘   └────┬─────┘
                   │            │            │
                   │            │            ▼
                   │            │     ┌────────────┐
                   │            │     │ ISI PROFIL │ (wajib pertama kali)
                   │            │     └─────┬──────┘
                   │            │           │
                   │            │           ▼
                   │            │     ┌────────────┐
                   │            │     │ LIHAT UJIAN │
                   │            │     └─────┬──────┘
                   │            │           │
                   │            │           ▼
                   │            │     ┌────────────────┐
                   │            │     │ MASUKKAN KODE  │
                   │            │     │ AKSES & MULAI  │
                   │            │     └─────┬──────────┘
                   │            │           │
                   │            │           ▼
                   │            │     ┌────────────────────────────┐
                   │            │     │      CAT LOOP              │
                   │            │     │  ┌─────────────────────┐   │
                   │            │     │  │ Pilih soal (b ≈ θ)  │   │
                   │            │     │  │ Tampilkan ke siswa  │◀──┤
                   │            │     │  └─────────┬───────────┘   │
                   │            │     │            │               │
                   │            │     │            ▼               │
                   │            │     │  ┌─────────────────────┐   │
                   │            │     │  │ Siswa menjawab      │   │
                   │            │     │  │ Hitung Pi, Qi, Ii   │   │
                   │            │     │  │ Update θ, SE        │   │
                   │            │     │  └─────────┬───────────┘   │
                   │            │     │            │               │
                   │            │     │            ▼               │
                   │            │     │  ┌─────────────────────┐   │
                   │            │     │  │ Cek Stop Condition  │   │
                   │            │     │  │ SE < min? ΔSE < min?│   │
                   │            │     │  └──────┬──────┬───────┘   │
                   │            │     │         │      │           │
                   │            │     │     YA  │      │ TIDAK     │
                   │            │     │         │      └───────────┘
                   │            │     └─────────┼──────────────────
                   │            │               │
                   │            │               ▼
                   │            │     ┌──────────────────┐
                   │            │     │  UJIAN SELESAI   │
                   │            │     │  Simpan status   │
                   │            │     │  Tampilkan skor  │
                   │            │     └────────┬─────────┘
                   │            │              │
                   │            │              ▼
                   │            │     ┌──────────────────┐
                   │            │     │  LIHAT HASIL     │
                   │            │     │  Review jawaban  │
                   │            │     │  Klasifikasi     │
                   │            │     │  Download PDF    │
                   │            │     └──────────────────┘
                   │            │
                   ▼            ▼
         ┌──────────────────────────────┐
         │         SETUP UJIAN          │
         │                              │
         │  1. Admin buat Sekolah       │
         │  2. Admin buat Kelas         │
         │  3. Admin/Guru buat Guru     │
         │  4. Admin/Guru assign Kelas  │
         │  5. Admin/Guru buat Ujian    │
         │  6. Admin/Guru buat Soal     │
         │  7. Admin/Guru buat Jadwal   │
         │                              │
         └──────────────────────────────┘
```

---

## Ringkasan Teknologi

| Komponen | Teknologi |
|----------|-----------|
| **Framework** | CodeIgniter 4 |
| **Database** | MariaDB 10.4.32 (MySQL) |
| **Bahasa** | PHP 8.2 |
| **Frontend** | Bootstrap + JavaScript (Summernote/CKEditor) |
| **Algoritma Ujian** | IRT 3PL (3-Parameter Logistic) + CBT Fixed-Form |
| **Session Management** | PHP Session (file-based) |
| **Password Hash** | bcrypt (PASSWORD_DEFAULT) |
| **Auth Middleware** | Custom AuthFilter |
