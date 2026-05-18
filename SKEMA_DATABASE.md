# Skema Database `db_cat_cbt`

> **Projek:** CAT-CBT (Computer Assisted Test / Computer Based Test)  
> **DBMS:** MariaDB 10.4.32  
> **Framework:** CodeIgniter 4  
> **Total Tabel:** 21  
> **Versi Skema:** v2.0 (migration 2026-05-13)

---

## Diagram Relasi

```
┌───────────┐     ┌───────────┐     ┌───────────────┐
│  sekolah   │────▶│   kelas    │◀────│  kelas_guru   │
└───────────┘     └─────┬─────┘     └───────┬───────┘
                        │                   │
                        ▼                   ▼
┌───────────┐     ┌───────────┐     ┌───────────┐
│   users    │────▶│   siswa    │     │   guru     │◀──┐
└─────┬─────┘     └─────┬─────┘     └─────┬─────┘   │
      │                 │                 │         │
      │                 ▼                 │         │
      │          ┌──────────────┐         │         │
      │          │ peserta_ujian│◀────────┤         │
      │          └──────┬───────┘         │         │
      │                 │                 │         │
      │          ┌──────┴──────┐  ┌───────┴──────┐  │
      │          │ attempt_ujian│  │ jadwal_ujian │──┘
      │          └──────┬──────┘  └──────┬──────┘
      │                 │                │
      │          ┌──────┴──────┐         │
      │          │attempt_jawab│         │
      │          └──────┬──────┘         │
      │                 │                │
      │                 ▼                ▼
      │          ┌─────────────┐  ┌───────────┐
      │          │  soal_ujian  │  │   ujian    │
      │          └──┬──┬──┬───┘  └──┬──┬──┬──┘
      │             │  │  │         │  │  │
      │     ┌───────┘  │  └────┐    │  │  └────────┐
      │     ▼          ▼       ▼    ▼  ▼           ▼
      │ ┌────────┐┌────────┐┌──────┐┌──────┐┌──────────┐
      │ │variabel││indikator││materi││jenis ││bank_ujian│
      │ └────────┘│(FK→var) │└──────┘│ujian │└──────────┘
      │           └────────┘        └──────┘
      │
      │  ┌─────────────┐     ┌───────────┐
      ├─▶│  paket_ujian │────▶│paket_item │
      │  └──────┬──────┘     └─────┬─────┘
      │         │                  │
      │         │     ┌────────────┘
      │         │     ▼
      │         │  ┌─────────┐
      │         └─▶│  ujian   │
      │            └─────────┘
      │
      └────▶ pengumuman

  ┌──────────┐
  │ujian_bank│──▶ ujian + bank_ujian (pivot)
  └──────────┘
```

---

## Daftar Tabel

| No | Nama Tabel | Deskripsi | Status |
|----|-----------|-----------|--------|
| 1 | `users` | Data pengguna (admin, guru, siswa) | Original |
| 2 | `sekolah` | Data sekolah | Original |
| 3 | `kelas` | Data kelas per sekolah | Original |
| 4 | `siswa` | Data siswa (relasi ke users & kelas) | Original |
| 5 | `guru` | Data guru (relasi ke users & sekolah) | Original |
| 6 | `kelas_guru` | Relasi many-to-many kelas & guru | Original |
| 7 | `jenis_ujian` | Kategori/jenis ujian | Original |
| 8 | `bank_ujian` | Bank soal (koleksi ujian) | Original |
| 9 | `ujian` | Konfigurasi ujian + dual engine | **Modified** |
| 10 | `soal_ujian` | Soal + parameter IRT 3PL + metadata | **Modified** |
| 11 | `jadwal_ujian` | Penjadwalan + penugasan granular | **Modified** |
| 12 | `peserta_ujian` | Data peserta ujian per jadwal | Original |
| 13 | `hasil_ujian` | Jawaban & parameter IRT (CAT v1) | Original |
| 14 | `pengumuman` | Pengumuman dari guru/admin | Original |
| 15 | `variabel` | Variabel/kompetensi soal | **Baru** |
| 16 | `indikator` | Indikator capaian (FK ke variabel) | **Baru** |
| 17 | `materi` | Materi pelajaran | **Baru** |
| 18 | `paket_ujian` | Paket soal hasil generate | **Baru** |
| 19 | `paket_ujian_item` | Soal dalam setiap paket | **Baru** |
| 20 | `ujian_bank` | Pivot multi-bank (ujian ↔ bank_ujian) | **Baru** |
| 21 | `attempt_ujian` | Tracking attempt per siswa (1-3) | **Baru** |
| 22 | `attempt_jawaban` | Jawaban per attempt (CBT + CAT v2) | **Baru** |

---

## Detail Tabel

### 1. `users` — Pengguna

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `user_id` | `INT(11)` | PK, Auto Increment |
| `username` | `VARCHAR(50)` | Username unik |
| `email` | `VARCHAR(100)` | Email unik |
| `password` | `VARCHAR(255)` | Hash bcrypt |
| `role` | `ENUM('admin','siswa','guru')` | Default: `siswa` |
| `status` | `ENUM('active','inactive')` | Default: `active` |
| `created_at` | `TIMESTAMP` | Default: `CURRENT_TIMESTAMP` |
| `updated_at` | `TIMESTAMP` | Auto update |

---

### 2. `sekolah` — Sekolah

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `sekolah_id` | `INT(11)` | PK, Auto Increment |
| `nama_sekolah` | `VARCHAR(100)` | |
| `alamat` | `TEXT` | Nullable |
| `telepon` | `VARCHAR(20)` | Nullable |
| `email` | `VARCHAR(100)` | Nullable |
| `created_at` | `TIMESTAMP` | Default: `CURRENT_TIMESTAMP` |

---

### 3. `kelas` — Kelas

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `kelas_id` | `INT(11)` | PK, Auto Increment |
| `sekolah_id` | `INT(11)` | FK → `sekolah.sekolah_id` |
| `nama_kelas` | `VARCHAR(20)` | Contoh: `XII IPA` |
| `tahun_ajaran` | `VARCHAR(10)` | Contoh: `2024/2025` |

---

### 4. `siswa` — Siswa

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `siswa_id` | `INT(11)` | PK, Auto Increment |
| `user_id` | `INT(11)` | FK → `users.user_id` |
| `kelas_id` | `INT(11)` | FK → `kelas.kelas_id` (nullable, ON DELETE SET NULL) |
| `nomor_peserta` | `VARCHAR(20)` | Nomor peserta ujian |
| `nama_lengkap` | `VARCHAR(100)` | |
| `jenis_kelamin` | `ENUM('Laki-laki','Perempuan')` | Nullable |
| `created_at` | `TIMESTAMP` | Default: `CURRENT_TIMESTAMP` |
| `updated_at` | `TIMESTAMP` | Auto update |

---

### 5. `guru` — Guru

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `guru_id` | `INT(11)` | PK, Auto Increment |
| `user_id` | `INT(11)` | FK → `users.user_id` |
| `sekolah_id` | `INT(11)` | FK → `sekolah.sekolah_id` |
| `nip` | `VARCHAR(20)` | Nomor Induk Pegawai |
| `nama_lengkap` | `VARCHAR(100)` | |
| `mata_pelajaran` | `VARCHAR(50)` | |

---

### 6. `kelas_guru` — Relasi Kelas & Guru

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `kelas_guru_id` | `INT(11)` | PK, Auto Increment |
| `kelas_id` | `INT(11)` | FK → `kelas.kelas_id` |
| `guru_id` | `INT(11)` | FK → `guru.guru_id` |
| `created_at` | `TIMESTAMP` | Default: `CURRENT_TIMESTAMP` |
| `updated_at` | `TIMESTAMP` | Default: `CURRENT_TIMESTAMP` |

---

### 7. `jenis_ujian` — Jenis / Kategori Ujian

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `jenis_ujian_id` | `INT(11)` | PK, Auto Increment |
| `nama_jenis` | `VARCHAR(100)` | Contoh: `Fisika`, `UTS kelas 10` |
| `deskripsi` | `TEXT` | |
| `kelas_id` | `INT(11)` | FK → `kelas.kelas_id` (nullable) |
| `created_by` | `INT(11)` | FK → `users.user_id` (nullable) |
| `created_at` | `DATETIME` | Default: `CURRENT_TIMESTAMP` |
| `updated_at` | `DATETIME` | Default: `CURRENT_TIMESTAMP` |

---

### 8. `bank_ujian` — Bank Soal

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `bank_ujian_id` | `INT(11)` | PK, Auto Increment |
| `kategori` | `VARCHAR(50)` | Kategori bank soal |
| `jenis_ujian_id` | `INT(11)` | FK → `jenis_ujian.jenis_ujian_id` (ON DELETE CASCADE) |
| `nama_ujian` | `VARCHAR(100)` | |
| `deskripsi` | `TEXT` | Nullable |
| `created_by` | `INT(11)` | FK → `users.user_id` (ON DELETE CASCADE) |
| `created_at` | `TIMESTAMP` | Default: `CURRENT_TIMESTAMP` |
| `updated_at` | `TIMESTAMP` | Auto update |

> **Unique Key:** (`kategori`, `jenis_ujian_id`, `nama_ujian`, `created_by`)

---

### 9. `ujian` — Konfigurasi Ujian ⚡ (MODIFIED v2)

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id_ujian` | `INT(11)` | PK, Auto Increment |
| `jenis_ujian_id` | `INT(11)` | FK → `jenis_ujian.jenis_ujian_id` |
| `nama_ujian` | `VARCHAR(100)` | |
| `kode_ujian` | `VARCHAR(50)` | Nullable |
| `deskripsi` | `TEXT` | |
| 🆕 `tipe_ujian` | `ENUM('CAT','CBT')` | **Default: `CAT`** — dual engine |
| 🆕 `tampilkan_pembahasan` | `TINYINT(1)` | **Default: `0`** |
| 🆕 `visibilitas` | `ENUM('terbuka','tertutup')` | **Default: `terbuka`** |
| 🆕 `pengulangan_aktif` | `TINYINT(1)` | **Default: `0`** |
| 🆕 `maksimal_attempt` | `TINYINT(1)` | **Default: `1`** (maks 3) |
| 🆕 `acak_urutan_soal` | `TINYINT(1)` | **Default: `0`** — shuffle per attempt |
| 🆕 `acak_pilihan_jawaban` | `TINYINT(1)` | **Default: `0`** — shuffle A/B/C/D/E |
| `se_awal` | `DECIMAL(6,4)` | Standard Error awal (default `1.0000`) |
| `se_minimum` | `DECIMAL(6,4)` | SE minimum untuk berhenti (default `0.2500`) |
| `delta_se_minimum` | `DECIMAL(6,4)` | Delta SE minimum (default `0.0100`) |
| `maksimal_soal_tampil` | `INT(11)` | Default `20` |
| `durasi` | `TIME` | Durasi ujian |
| `kelas_id` | `INT(11)` | FK → `kelas.kelas_id` (nullable) |
| `created_by` | `INT(11)` | FK → `users.user_id` (nullable) |
| `created_at` | `DATETIME` | Default: `CURRENT_TIMESTAMP` |
| `updated_at` | `DATETIME` | Default: `CURRENT_TIMESTAMP` |

> 🆕 = kolom baru dari migration v2

---

### 10. `soal_ujian` — Soal Ujian ⚡ (MODIFIED v2)

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `soal_id` | `INT(11)` | PK, Auto Increment |
| `kode_soal` | `VARCHAR(50)` | Nullable |
| `ujian_id` | `INT(11)` | FK → `ujian.id_ujian` (nullable) |
| `bank_ujian_id` | `INT(11)` | FK → `bank_ujian.bank_ujian_id` (nullable, ON DELETE CASCADE) |
| `pertanyaan` | `TEXT` | Isi pertanyaan (bisa HTML) |
| `pilihan_a` | `TEXT` | Pilihan jawaban A (bisa HTML) |
| `pilihan_b` | `TEXT` | Pilihan jawaban B (bisa HTML) |
| `pilihan_c` | `TEXT` | Pilihan jawaban C (bisa HTML) |
| `pilihan_d` | `TEXT` | Pilihan jawaban D (bisa HTML) |
| `pilihan_e` | `TEXT` | Pilihan jawaban E (bisa HTML) |
| `pembahasan` | `TEXT` | Pembahasan soal (nullable) |
| ✏️ `media` | `VARCHAR(255)` | Nama file gambar/tabel/formula (nullable) — rename dari `foto` |
| `jawaban_benar` | `ENUM('A','B','C','D','E')` | Kunci jawaban |
| `tingkat_kesulitan` | `DECIMAL(5,3)` | Parameter **b** IRT (tingkat kesulitan), default `0.000` |
| 🆕 `a` | `DECIMAL(5,3)` | **Default: `1.000`** — parameter diskriminasi IRT 3PL |
| 🆕 `c` | `DECIMAL(5,3)` | **Default: `0.000`** — parameter pseudo-guessing IRT 3PL |
| 🆕 `variabel_id` | `INT(11) UNSIGNED` | FK → `variabel.variabel_id` (nullable, ON DELETE SET NULL) |
| 🆕 `indikator_id` | `INT(11) UNSIGNED` | FK → `indikator.indikator_id` (nullable, ON DELETE SET NULL) |
| 🆕 `materi_id` | `INT(11) UNSIGNED` | FK → `materi.materi_id` (nullable, ON DELETE SET NULL) |
| `is_bank_soal` | `TINYINT(1)` | `1` = soal bank, `0` = soal ujian langsung |
| `created_by` | `INT(11)` | Nullable |
| `created_at` | `TIMESTAMP` | Default: `CURRENT_TIMESTAMP` |
| `updated_at` | `TIMESTAMP` | Default: `CURRENT_TIMESTAMP` |

> 🆕 = kolom baru | ✏️ = kolom diubah

---

### 11. `jadwal_ujian` — Jadwal Ujian ⚡ (MODIFIED v2)

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `jadwal_id` | `INT(11)` | PK, Auto Increment |
| `ujian_id` | `INT(11)` | FK → `ujian.id_ujian` |
| `kelas_id` | `INT(11)` | FK → `kelas.kelas_id` |
| `guru_id` | `INT(11)` | FK → `guru.guru_id` |
| `tanggal_mulai` | `DATETIME` | Waktu mulai ujian |
| `tanggal_selesai` | `DATETIME` | Waktu selesai ujian |
| `durasi_menit` | `INT(11)` | Durasi dalam menit |
| `kode_akses` | `VARCHAR(20)` | Kode akses untuk masuk ujian |
| `status` | `ENUM('belum_mulai','sedang_berlangsung','selesai')` | Default: `belum_mulai` |
| 🆕 `tipe_penugasan` | `ENUM('kelas','individu')` | **Default: `kelas`** |
| 🆕 `siswa_ids` | `TEXT` | JSON array ID siswa (untuk tipe individu) — nullable |

> 🆕 = kolom baru

---

### 12. `peserta_ujian` — Peserta Ujian

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `peserta_ujian_id` | `INT(11)` | PK, Auto Increment |
| `jadwal_id` | `INT(11)` | FK → `jadwal_ujian.jadwal_id` |
| `siswa_id` | `INT(11)` | FK → `siswa.siswa_id` |
| `status` | `ENUM('belum_mulai','sedang_mengerjakan','selesai')` | Default: `belum_mulai` |
| `waktu_mulai` | `DATETIME` | Nullable |
| `waktu_selesai` | `DATETIME` | Nullable |

---

### 13. `hasil_ujian` — Jawaban & Hasil Ujian (CAT v1)

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `jawaban_id` | `INT(11)` | PK, Auto Increment |
| `peserta_ujian_id` | `INT(11)` | FK → `peserta_ujian.peserta_ujian_id` |
| `soal_id` | `INT(11)` | FK → `soal_ujian.soal_id` |
| `jawaban_siswa` | `ENUM('A','B','C','D','E')` | Pilihan jawaban siswa |
| `is_correct` | `TINYINT(1)` | `1` = benar, `0` = salah (nullable) |
| `waktu_menjawab` | `TIMESTAMP` | Default: `CURRENT_TIMESTAMP` |
| `theta_saat_ini` | `DECIMAL(5,3)` | Estimasi kemampuan (θ) saat ini (nullable) |
| `se_saat_ini` | `DECIMAL(5,3)` | Standard Error saat ini (nullable) |
| `delta_se_saat_ini` | `DECIMAL(5,3)` | Perubahan SE |
| `pi_saat_ini` | `DECIMAL(5,3)` | Probabilitas menjawab benar (nullable) |
| `qi_saat_ini` | `DECIMAL(5,3)` | Probabilitas menjawab salah = 1 - Pi (nullable) |
| `ii_saat_ini` | `DECIMAL(5,3)` | Item Information function (nullable) |

> **CAT v1:** Tabel ini tetap ada untuk backward compatibility. Ke depan, jawaban akan disimpan di `attempt_jawaban`.

---

### 14. `pengumuman` — Pengumuman

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `pengumuman_id` | `INT(11)` | PK, Auto Increment |
| `judul` | `VARCHAR(200)` | Judul pengumuman |
| `isi_pengumuman` | `TEXT` | Isi pengumuman |
| `tanggal_publish` | `DATETIME` | Default: `CURRENT_TIMESTAMP` |
| `tanggal_berakhir` | `DATETIME` | Nullable |
| `created_by` | `INT(11)` | FK → `users.user_id` |

---

### 15. `variabel` — Variabel/Kompetensi 🆕

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `variabel_id` | `INT(11) UNSIGNED` | PK, Auto Increment |
| `nama_variabel` | `VARCHAR(100)` | Contoh: "Pemahaman Konsep", "Analisis Data" |
| `deskripsi` | `TEXT` | Nullable |
| `created_at` | `TIMESTAMP` | Nullable |
| `updated_at` | `TIMESTAMP` | Nullable |

---

### 16. `indikator` — Indikator Capaian 🆕

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `indikator_id` | `INT(11) UNSIGNED` | PK, Auto Increment |
| `variabel_id` | `INT(11) UNSIGNED` | FK → `variabel.variabel_id` (CASCADE) |
| `nama_indikator` | `VARCHAR(200)` | Contoh: "Menyebutkan definisi", "Mengaplikasikan rumus" |
| `deskripsi` | `TEXT` | Nullable |
| `created_at` | `TIMESTAMP` | Nullable |
| `updated_at` | `TIMESTAMP` | Nullable |

---

### 17. `materi` — Materi Pelajaran 🆕

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `materi_id` | `INT(11) UNSIGNED` | PK, Auto Increment |
| `nama_materi` | `VARCHAR(200)` | Contoh: "Hukum Newton", "Gerak Parabola" |
| `deskripsi` | `TEXT` | Nullable |
| `created_at` | `TIMESTAMP` | Nullable |
| `updated_at` | `TIMESTAMP` | Nullable |

---

### 18. `paket_ujian` — Paket Soal (Generate) 🆕

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `paket_id` | `INT(11)` | PK, Auto Increment |
| `ujian_id` | `INT(11)` | FK → `ujian.id_ujian` (CASCADE) |
| `nama_paket` | `VARCHAR(50)` | Contoh: "Paket A", "Paket B" |
| `nomor_paket` | `INT(11)` | 1, 2, 3, ... |
| `created_at` | `TIMESTAMP` | Nullable |

---

### 19. `paket_ujian_item` — Isi Paket Soal 🆕

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `paket_item_id` | `INT(11)` | PK, Auto Increment |
| `paket_id` | `INT(11)` | FK → `paket_ujian.paket_id` (CASCADE) |
| `soal_id` | `INT(11)` | FK → `soal_ujian.soal_id` (CASCADE) |
| `nomor_urut` | `INT(11)` | Urutan soal dalam paket (1, 2, 3, ...) |
| `created_at` | `TIMESTAMP` | Nullable |

> **Unique Key:** (`paket_id`, `soal_id`) — satu soal tidak boleh duplikat dalam satu paket

---

### 20. `ujian_bank` — Pivot Multi-Bank 🆕

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `ujian_bank_id` | `INT(11)` | PK, Auto Increment |
| `ujian_id` | `INT(11)` | FK → `ujian.id_ujian` (CASCADE) |
| `bank_ujian_id` | `INT(11)` | FK → `bank_ujian.bank_ujian_id` (CASCADE) |
| `created_at` | `TIMESTAMP` | Nullable |

> **Unique Key:** (`ujian_id`, `bank_ujian_id`) — satu bank hanya bisa di-assign sekali per ujian

---

### 21. `attempt_ujian` — Tracking Attempt 🆕

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `attempt_id` | `INT(11)` | PK, Auto Increment |
| `peserta_ujian_id` | `INT(11)` | FK → `peserta_ujian.peserta_ujian_id` (CASCADE) |
| `nomor_attempt` | `TINYINT(1)` | 1, 2, atau 3 |
| `paket_id` | `INT(11)` | FK → `paket_ujian.paket_id` (SET NULL) — NULL untuk CAT |
| `status` | `ENUM('belum_mulai','sedang_mengerjakan','selesai')` | Default: `belum_mulai` |
| `waktu_mulai` | `DATETIME` | Nullable |
| `waktu_selesai` | `DATETIME` | Nullable |
| `nilai_akhir` | `DECIMAL(5,3)` | Nullable — skor akhir attempt |
| `created_at` | `TIMESTAMP` | Nullable |

> **Unique Key:** (`peserta_ujian_id`, `nomor_attempt`)

---

### 22. `attempt_jawaban` — Jawaban per Attempt 🆕

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `jawaban_id` | `INT(11)` | PK, Auto Increment |
| `attempt_id` | `INT(11)` | FK → `attempt_ujian.attempt_id` (CASCADE) |
| `soal_id` | `INT(11)` | FK → `soal_ujian.soal_id` (CASCADE) |
| `nomor_tampil` | `INT(11)` | Nomor urut yang ditampilkan (hasil shuffle) — nullable |
| `jawaban_siswa` | `ENUM('A','B','C','D','E')` | Pilihan jawaban |
| `is_correct` | `TINYINT(1)` | 1 = benar, 0 = salah (nullable) |
| `waktu_menjawab` | `TIMESTAMP` | |
| `theta_saat_ini` | `DECIMAL(5,3)` | Nullable — hanya CAT |
| `se_saat_ini` | `DECIMAL(5,3)` | Nullable — hanya CAT |
| `delta_se_saat_ini` | `DECIMAL(5,3)` | Nullable — hanya CAT |
| `pi_saat_ini` | `DECIMAL(5,3)` | Nullable — hanya CAT |
| `qi_saat_ini` | `DECIMAL(5,3)` | Nullable — hanya CAT |
| `ii_saat_ini` | `DECIMAL(5,3)` | Nullable — hanya CAT |

---

## Index Tambahan (Migration v2)

| Index | Tabel | Kolom | Fungsi |
|-------|-------|-------|--------|
| `idx_hasil_peserta_soal` | `hasil_ujian` | (`peserta_ujian_id`, `soal_id`) | Query hasil per peserta |
| `idx_soal_ujian_b` | `soal_ujian` | (`ujian_id`, `tingkat_kesulitan`) | Pemilihan soal CAT |
| `idx_jadwal_tanggal` | `jadwal_ujian` | (`tanggal_mulai`, `tanggal_selesai`) | Filter jadwal aktif |

---

## Alur Bisnis v2

1. **Sekolah** didaftarkan → memiliki banyak **Kelas**
2. **Guru** terdaftar di **Sekolah**, di-assign ke **Kelas** via `kelas_guru`
3. **Siswa** terdaftar di **Kelas**
4. Admin/Guru membuat **Variabel** → **Indikator** → **Materi** (metadata soal)
5. Guru membuat **Jenis Ujian**, **Bank Ujian**, dan **Ujian** (pilih tipe: CAT atau CBT)
6. Soal ditambahkan dengan metadata lengkap (variabel, indikator, materi) + parameter IRT 3PL (a, b, c)
7. Satu ujian bisa mengambil soal dari **beberapa bank** via `ujian_bank`
8. Untuk CBT: Generate **Paket Soal** → soal random masuk ke `paket_ujian_item`
9. Guru membuat **Jadwal Ujian** (bisa per kelas atau per individu)
10. Siswa masuk → **Attempt 1**: sistem pilih paket random (CBT) atau adaptif (CAT)
11. Jika pengulangan aktif: **Attempt 2 & 3** paket sama, urutan dishuffle
12. Setiap jawaban disimpan di `attempt_jawaban` (CBT + CAT v2) atau `hasil_ujian` (CAT v1)
13. Nilai dihitung per attempt (tidak saling menimpa)
14. Guru/Admin bisa melihat **Analisis** per variabel, indikator, materi

---

## Perbandingan Skema v1 vs v2

| Aspek | v1 (Original) | v2 (Migration) |
|-------|---------------|----------------|
| Total tabel | 14 | 22 |
| Engine ujian | CAT only (IRT 1PL) | CAT (IRT 3PL) + CBT (Fixed-Form) |
| Parameter IRT | b saja | a (diskriminasi), b (kesulitan), c (guessing) |
| Metadata soal | Tidak ada | Variabel → Indikator → Materi |
| Bank soal | 1 bank per ujian | Multi-bank via pivot `ujian_bank` |
| Paket soal | Tidak ada | Generate random + locking |
| Attempt | 1× | Maks 3× (opsional) |
| Penugasan | Per kelas | Per kelas atau per individu |
| Jawaban | `hasil_ujian` | `hasil_ujian` (v1) + `attempt_jawaban` (v2) |
| Shuffle | Tidak ada | Urutan soal & pilihan jawaban |
| Visibilitas | Selalu terbuka | Terbuka / Tertutup |
| Pembahasan | Selalu tampil | Toggle on/off |

---

## Engine: MySQL InnoDB, Charset: utf8mb4, Collation: utf8mb4_general_ci
