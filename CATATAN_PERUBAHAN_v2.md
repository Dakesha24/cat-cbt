# CATATAN PERUBAHAN — v2.0 Migration

> **Tanggal:** 13 Mei 2026
> **Perubahan:** Migration v2.0 + Models + CRUD Variabel/Indikator/Materi (Admin & Guru)
> **Status:** ✅ Selesai

---

## Ringkasan

Menjalankan 5 file migration dan mengupdate 3 Model existing + membuat 8 Model baru sebagai fondasi schema v2.0.

---

## 1. Migration Dijalankan (5/5)

```bash
php spark migrate --all
# Output: "Migrations complete."
```

| # | Migration File | Hasil |
|---|---------------|-------|
| 1 | `2026-05-13-000001_TambahTabelVariabelIndikatorMateri` | ✅ 3 tabel baru |
| 2 | `2026-05-13-000002_ModifySoalUjianDanUjian` | ✅ 3 tabel dimodifikasi |
| 3 | `2026-05-13-000003_TambahTabelPaketUjian` | ✅ 2 tabel baru |
| 4 | `2026-05-13-000004_TambahTabelAttemptUjian` | ✅ 2 tabel baru |
| 5 | `2026-05-13-000005_TambahTabelUjianBankDanIndeks` | ✅ 1 tabel + 3 index |

---

## 2. Tabel Baru (8 tabel)

| Tabel | Fields | Model |
|-------|--------|-------|
| `variabel` | variabel_id, nama_variabel, deskripsi, timestamps | `VariabelModel.php` |
| `indikator` | indikator_id, variabel_id (FK), nama_indikator, deskripsi, timestamps | `IndikatorModel.php` |
| `materi` | materi_id, nama_materi, deskripsi, timestamps | `MateriModel.php` |
| `paket_ujian` | paket_id, ujian_id (FK), nama_paket, nomor_paket, created_at | `PaketUjianModel.php` |
| `paket_ujian_item` | paket_item_id, paket_id (FK), soal_id (FK), nomor_urut, created_at | `PaketUjianItemModel.php` |
| `attempt_ujian` | attempt_id, peserta_ujian_id (FK), nomor_attempt, paket_id, status, waktu_mulai, waktu_selesai, nilai_akhir, created_at | `AttemptUjianModel.php` |
| `attempt_jawaban` | jawaban_id, attempt_id (FK), soal_id (FK), nomor_tampil, jawaban_siswa, is_correct, waktu_menjawab, IRT params | `AttemptJawabanModel.php` |
| `ujian_bank` | ujian_bank_id, ujian_id (FK), bank_ujian_id (FK), created_at | `UjianBankModel.php` |

---

## 3. Tabel Dimodifikasi (3 tabel)

### `soal_ujian` (18 → 23 kolom)

| Kolom Baru | Tipe | Default |
|-----------|------|---------|
| `a` | `DECIMAL(5,3)` | `1.000` |
| `c` | `DECIMAL(5,3)` | `0.000` |
| `variabel_id` | `INT(11) UNSIGNED` FK nullable | `NULL` |
| `indikator_id` | `INT(11) UNSIGNED` FK nullable | `NULL` |
| `materi_id` | `INT(11) UNSIGNED` FK nullable | `NULL` |
| `media` | `VARCHAR(255)` (rename dari `foto`) | `NULL` |

### `ujian` (14 → 21 kolom)

| Kolom Baru | Tipe | Default |
|-----------|------|---------|
| `tipe_ujian` | `ENUM('CAT','CBT')` | `'CAT'` |
| `tampilkan_pembahasan` | `TINYINT(1)` | `0` |
| `visibilitas` | `ENUM('terbuka','tertutup')` | `'terbuka'` |
| `pengulangan_aktif` | `TINYINT(1)` | `0` |
| `maksimal_attempt` | `TINYINT(1)` | `1` |
| `acak_urutan_soal` | `TINYINT(1)` | `0` |
| `acak_pilihan_jawaban` | `TINYINT(1)` | `0` |

### `jadwal_ujian` (9 → 11 kolom)

| Kolom Baru | Tipe | Default |
|-----------|------|---------|
| `tipe_penugasan` | `ENUM('kelas','individu')` | `'kelas'` |
| `siswa_ids` | `TEXT` nullable | `NULL` |

---

## 4. Model Diperbarui (3 file)

| File | Perubahan |
|------|-----------|
| `app/Models/SoalUjianModel.php` | `allowedFields`: +`a`, +`c`, +`variabel_id`, +`indikator_id`, +`materi_id`, `foto`→`media` |
| `app/Models/UjianModel.php` | `allowedFields`: +`tipe_ujian`, +`tampilkan_pembahasan`, +`visibilitas`, +`pengulangan_aktif`, +`maksimal_attempt`, +`acak_urutan_soal`, +`acak_pilihan_jawaban`, +`maksimal_soal_tampil` |
| `app/Models/JadwalUjianModel.php` | `allowedFields`: +`durasi_menit`, +`tipe_penugasan`, +`siswa_ids` |

---

## 5. Model Baru (8 file)

| File | Nama Model | Method Kunci |
|------|-----------|-------------|
| `VariabelModel.php` | VariabelModel | `getWithCounts()` — variabel + jumlah indikator & soal |
| `IndikatorModel.php` | IndikatorModel | `getByVariabel()`, `getAllWithVariabel()` — indikator per variabel |
| `MateriModel.php` | MateriModel | `getWithCount()` — materi + jumlah soal |
| `UjianBankModel.php` | UjianBankModel | `getBanksByUjian()`, `syncBanks()`, `getSoalFromBanks()` |
| `PaketUjianModel.php` | PaketUjianModel | `getByUjian()`, `getSoalByPaket()`, `deleteByUjian()` |
| `PaketUjianItemModel.php` | PaketUjianItemModel | `getByPaket()` — soal dalam paket urut nomor |
| `AttemptUjianModel.php` | AttemptUjianModel | `getByPeserta()`, `attemptExists()`, `getActiveAttempt()` |
| `AttemptJawabanModel.php` | AttemptJawabanModel | `getByAttempt()`, `countCorrect()`, `getAnsweredSoalIds()`, `saveBatchJawaban()` |

---

## 6. Verifikasi Database

```
Total tabel: 14 → 22 (+8 baru)
soal_ujian: 18 fields → 23 fields ✅
ujian: 14 fields → 21 fields ✅
jadwal_ujian: 9 fields → 11 fields ✅
```

---

## 7. File yang Berubah / Baru

```
File Diubah (3):
  app/Models/SoalUjianModel.php
  app/Models/UjianModel.php
  app/Models/JadwalUjianModel.php

File Baru (8):
  app/Models/VariabelModel.php
  app/Models/IndikatorModel.php
  app/Models/MateriModel.php
  app/Models/UjianBankModel.php
  app/Models/PaketUjianModel.php
  app/Models/PaketUjianItemModel.php
  app/Models/AttemptUjianModel.php
  app/Models/AttemptJawabanModel.php

Migration (5, sudah ada):
  app/Database/Migrations/2026-05-13-000001_*.php
  app/Database/Migrations/2026-05-13-000002_*.php
  app/Database/Migrations/2026-05-13-000003_*.php
  app/Database/Migrations/2026-05-13-000004_*.php
  app/Database/Migrations/2026-05-13-000005_*.php

Dokumentasi (4, sudah ada):
  SKEMA_DATABASE.md
  RANCANGAN_CAT-CBT_v2.md
  STRUKTUR_PROJEK.md
  ALUR_APLIKASI.md
```

---

---

## 8. Fase 3: CRUD Variabel, Indikator, Materi ✅

### Routes Ditambahkan

**Admin:** 12 route (4 per entitas: list, tambah, edit, hapus) + 1 API route

```
GET  admin/variabel
POST admin/variabel/tambah
POST admin/variabel/edit/(:num)
GET  admin/variabel/hapus/(:num)
GET  admin/api/indikator-by-variabel/(:num)  ← AJAX cascade
GET  admin/indikator
POST admin/indikator/tambah
POST admin/indikator/edit/(:num)
GET  admin/indikator/hapus/(:num)
GET  admin/materi
POST admin/materi/tambah
POST admin/materi/edit/(:num)
GET  admin/materi/hapus/(:num)
```

**Guru:** 12 route + 1 API route (pola sama, prefix `guru/`)

### Controller

| File | Method Ditambahkan |
|------|-------------------|
| `Admin/Admin.php` | `variabel()`, `tambahVariabel()`, `editVariabel()`, `hapusVariabel()`, `indikator()`, `tambahIndikator()`, `editIndikator()`, `hapusIndikator()`, `materi()`, `tambahMateri()`, `editMateri()`, `hapusMateri()`, `getIndikatorByVariabel()` |
| `Guru/Guru.php` | Sama + 3 helper private method (`_handleVariabelCrud`, `_handleIndikatorCrud`, `_handleMateriCrud`) |

### Views (6 file baru)

| View | Deskripsi |
|------|-----------|
| `admin/variabel.php` | Tabel variabel + modal tambah/edit (admin) |
| `admin/indikator.php` | Tabel indikator per variabel + modal tambah/edit (admin) |
| `admin/materi.php` | Tabel materi + modal tambah/edit (admin) |
| `guru/variabel.php` | Sama, untuk guru |
| `guru/indikator.php` | Sama, untuk guru |
| `guru/materi.php` | Sama, untuk guru |

### Sidebar Diupdate (2 file)

| File | Yang Ditambah |
|------|--------------|
| `templates/admin/admin_template.php` | Section "Metadata Soal" + 3 menu: Variabel, Indikator, Materi |
| `templates/guru/guru_template.php` | 3 menu: Variabel, Indikator, Materi (di bawah Bank Soal) |

---

---

## 9. Fase 4: Update Form Soal ✅

### Controller

| Method | Perubahan |
|--------|-----------|
| `Admin::kelolaSoal()` | Pass `$variabel`, `$indikator`, `$materi` ke view |
| `Admin::tambahSoal()` | +`a`, +`c`, +`variabel_id`, +`indikator_id`, +`materi_id` ke validasi & data; rename `foto`→`media` |
| `Admin::editSoal()` | Sama; fallback `$soal['media'] ?? $soal['foto']`; `hapus_foto`→`hapus_media` |
| `Guru::kelolaSoal()` | Sama |
| `Guru::tambahSoal()` | Sama |
| `Guru::editSoal()` | Sama |

### Views (2 file diubah)

| File | Perubahan |
|------|-----------|
| `admin/ujian/kelola_soal.php` | Table: +kolom a, c, Metadata. Modal tambah/edit: +Parameter IRT 3PL & Metadata section, rename foto→media |
| `guru/kelola_soal.php` | Sama |

### Fitur Baru di Form

- **Parameter IRT 3PL:** input `a` (diskriminasi, 0.01-5.00) dan `c` (guessing, 0.00-1.00)
- **Dropdown Metadata:** Variabel → Indikator (cascade AJAX) + Materi
- **Rename:** `foto` → `media` di semua tempat (form, upload, hapus)
- **AJAX Cascade:** `loadIndikator()` dan `loadIndikatorEdit()` via API endpoint

---

---

## 10. Fase 5: Update Form Ujian ✅

### Controller

| Method | Perubahan |
|--------|-----------|
| `Admin::tambahUjian()` | +`tipe_ujian`, +`tampilkan_pembahasan`, +`visibilitas`, +`pengulangan_aktif`, +`maksimal_attempt`, +`acak_urutan_soal`, +`acak_pilihan_jawaban`, +`maksimal_soal_tampil` ke validasi & data |
| `Admin::editUjian()` | Sama |
| `Guru::tambahUjian()` | Sama |
| `Guru::editUjian()` | Sama |

### Views (2 file diubah)

| File | Perubahan |
|------|-----------|
| `admin/ujian/daftar.php` | Tambah/Edit modal: +Konfigurasi Ujian section (tipe radio, toggles, maks\_soal, visibilitas, pengulangan). Card: +badge CAT/CBT. JS: `toggleIRTSection()` |
| `guru/ujian.php` | Sama; JS: `toggleIRTSectionGuru()` |

### Fitur Baru di Form Ujian

- **Tipe Ujian:** Radio CAT (Adaptif) / CBT (Fixed-Form) — mempengaruhi tampilan parameter IRT
- **Toggle-togle:** Tampilkan Pembahasan, Acak Urutan Soal, Acak Pilihan Jawaban (checkbox)
- **Pengulangan Aktif:** Checkbox → munculkan dropdown Maksimal Attempt (1-3)
- **Visibilitas:** Dropdown Terbuka/Tertutup
- **Maksimal Soal Tampil:** Input number
- **Parameter IRT:** Sembunyi otomatis jika pilih CBT
- **Badge CAT/CBT:** Di kartu ujian, biru-info (CAT) / hijau (CBT)

---

---

## 11. Fase 6: Multi-Bank Assignment + Generate Paket ✅

### Routes (12 baru)

```
# Admin + Guru (masing-masing):
GET  ujian/(:num)/bank                     → assignBank (halaman assign + daftar paket)
POST ujian/(:num)/bank/sync                → syncBanks (simpan checklist bank)
GET  ujian/(:num)/generate-paket           → generatePaket (form generate)
POST ujian/(:num)/generate-paket/proses    → prosesGeneratePaket (eksekusi generate)
GET  ujian/(:num)/paket/(:num)/hapus       → hapusPaket
GET  ujian/(:num)/paket/hapus-semua        → hapusSemuaPaket
```

### Controller — Admin & Guru

| Method | Fungsi |
|--------|--------|
| `assignBank()` | Halaman split: kiri=checklist bank, kanan=daftar paket |
| `syncBanks()` | Hapus semua lalu insert batch bank terpilih via `UjianBankModel::syncBanks()` |
| `generatePaket()` | Halaman form: jumlah_paket + soal_per_paket + info total soal |
| `prosesGeneratePaket()` | Validasi → hapus paket lama → random pick soal dari bank ter-assign → insert `paket_ujian` + `paket_ujian_item` (transaksi DB) |
| `hapusPaket()` | Hapus satu paket via CASCADE |
| `hapusSemuaPaket()` | Hapus semua paket ujian |

### Views (4 baru)

| File | Deskripsi |
|------|-----------|
| `admin/ujian/assign_bank.php` | Split layout: checklist bank + tabel paket |
| `admin/ujian/generate_paket.php` | Stat cards (bank, soal, paket) + form generate dengan kalkulator kebutuhan |
| `guru/assign_bank.php` | Sama, untuk guru |
| `guru/generate_paket.php` | Sama, untuk guru |

### Fitur

- **Multi-bank:** Checkbox list semua bank → simpan → satu ujian bisa punya banyak sumber soal
- **Generate paket:** Random pick dari SEMUA bank terpilih, tanpa duplikasi soal per paket
- **Transaksi DB:** Jika gagal di tengah generate, semua rollback
- **Kalkulator real-time:** Jumlah paket × soal per paket = total kebutuhan
- **Validasi:** Cek total soal tersedia ≥ yang dibutuhkan
- **Link baru di dropdown kartu ujian:** "Assign Bank & Paket"

---

---

## 12. Fase 7: Jadwal Individu + Penugasan Granular ✅

### Controller

| Method | Perubahan |
|--------|-----------|
| `Admin::jadwalUjian()` | +pass `$siswa` list untuk multi-select |
| `Admin::tambahJadwal()` | +`tipe_penugasan`, +`siswa_ids` (JSON) ke validasi & data |
| `Admin::editJadwal()` | Sama |
| `Guru::jadwalUjian()` | +pass `$siswa` (filter by kelas guru) |
| `Guru::tambahJadwal()` | +`tipe_penugasan`, +`siswa_ids` (JSON) |
| `Guru::editJadwal()` | Sama |

### Views (2 file diubah)

| File | Perubahan |
|------|-----------|
| `admin/jadwal/jadwal_ujian.php` | Tambah/Edit modal: +Penugasan Peserta section (radio kelas/individu + multi-select siswa) |
| `guru/jadwal_ujian.php` | Sama |

### Fitur

- **Tipe Penugasan:** Radio "Per Kelas" (auto-enroll semua siswa) / "Per Individu" (pilih manual)
- **Multi-select Siswa:** Tahan Ctrl/Cmd untuk memilih beberapa siswa
- **Pre-populated di edit:** Siswa yang sudah dipilih tetap checked
- **Toggle interaktif:** Pilih "Per Individu" → multi-select muncul; "Per Kelas" → sembunyi
- **Data disimpan:** `siswa_ids` sebagai JSON array di database

### Ringkasan Semua Fase

| Fase | Status |
|------|:----:|
| 1. Migration (8 tabel, 3 modifikasi) | ✅ |
| 2. Models (3 update, 8 baru) | ✅ |
| 3. CRUD Variabel, Indikator, Materi | ✅ |
| 4. Update Form Soal (a,c, metadata, cascade) | ✅ |
| 5. Update Form Ujian (CAT/CBT, toggles) | ✅ |
| 6. Multi-Bank + Generate Paket | ✅ |
| 7. Jadwal Individu + Penugasan | ✅ |

---

## Fase Selanjutnya

Semua fase v2 selesai. Yang belum:
- [ ] Integrasi attempt system di flow siswa (start ujian, answer, review)
- [ ] Shuffle soal & pilihan di frontend
- [ ] Engine CAT 3PL (upgrade dari 1PL)
- [ ] Modul Analisis Data
- [ ] Testing menyeluruh
