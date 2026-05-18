# Struktur Folder & File Projek CAT-CBT

> **Framework:** CodeIgniter 4  
> **Pattern:** MVC (Model-View-Controller)  
> **PHP:** 7.4+ / 8.0+  
> **Database:** MariaDB/MySQL  
> **Editor Teks Soal:** CKEditor 4 + Summernote  

---

## Pohon Direktori (Tree View)

```
cat-cbt/
├── 📁 app/                          # Aplikasi utama (MVC CodeIgniter)
│   ├── 📁 Config/                   # Konfigurasi aplikasi
│   │   ├── App.php                  # Pengaturan dasar aplikasi
│   │   ├── Autoload.php             # Autoload namespace
│   │   ├── Cache.php                # Konfigurasi cache
│   │   ├── Constants.php            # Konstanta global
│   │   ├── ContentSecurityPolicy.php
│   │   ├── Cookie.php
│   │   ├── CURLRequest.php
│   │   ├── Database.php             # Koneksi database
│   │   ├── Email.php                # Konfigurasi email
│   │   ├── Encryption.php
│   │   ├── Events.php
│   │   ├── Exceptions.php
│   │   ├── Feature.php
│   │   ├── Filters.php              # ⭐ Middleware (AuthFilter)
│   │   ├── ForeignCharacters.php
│   │   ├── Format.php
│   │   ├── Generators.php
│   │   ├── Honeypot.php
│   │   ├── Images.php
│   │   ├── Kint.php
│   │   ├── Logger.php
│   │   ├── Migrations.php
│   │   ├── Mimes.php
│   │   ├── Modules.php
│   │   ├── Pager.php
│   │   ├── Paths.php                # Path sistem
│   │   ├── Publisher.php
│   │   ├── Routes.php               # ⭐ Routing URL aplikasi
│   │   ├── Routing.php              # Konfigurasi auto-routing
│   │   ├── Security.php
│   │   ├── Services.php
│   │   ├── Session.php              # Konfigurasi session
│   │   ├── Toolbar.php
│   │   ├── UserAgents.php
│   │   ├── Validation.php
│   │   ├── View.php
│   │   └── Boot/                    # Bootstrap per environment
│   │       ├── development.php
│   │       ├── production.php
│   │       └── testing.php
│   │
│   ├── 📁 Controllers/              # Controller (logika bisnis)
│   │   ├── Auth.php                 # ⭐ Login, Register, Logout
│   │   ├── BaseController.php       # Base controller
│   │   ├── Home.php                 # Halaman depan publik
│   │   ├── User.php                 # (kosong/minimal)
│   │   ├── 📁 Admin/
│   │   │   ├── Admin.php            # ⭐ Semua fitur admin (1316+ baris)
│   │   │   └── Feedback.php         # Feedback controller
│   │   ├── 📁 Guru/
│   │   │   └── Guru.php             # ⭐ Semua fitur guru
│   │   └── 📁 Siswa/
│   │       └── Siswa.php            # ⭐ CAT engine + hasil ujian
│   │
│   ├── 📁 Database/
│   │   ├── 📁 Migrations/           # ⭐ Migrasi database v2
│   │   │   ├── 2026-05-13-000001_TambahTabelVariabelIndikatorMateri.php
│   │   │   ├── 2026-05-13-000002_ModifySoalUjianDanUjian.php
│   │   │   ├── 2026-05-13-000003_TambahTabelPaketUjian.php
│   │   │   ├── 2026-05-13-000004_TambahTabelAttemptUjian.php
│   │   │   └── 2026-05-13-000005_TambahTabelUjianBankDanIndeks.php
│   │   └── 📁 Seeds/               # Database seeder (kosong)
│   │
│   ├── 📁 Filters/
│   │   └── AuthFilter.php           # ⭐ Filter autentikasi (middleware)
│   │
│   ├── 📁 Helpers/                  # Helper functions (kosong)
│   │
│   ├── 📁 Language/                 # File bahasa
│   │   └── en/
│   │       └── Validation.php       # Pesan validasi bahasa Inggris
│   │
│   ├── 📁 Libraries/                # Library kustom (kosong)
│   │
│   ├── 📁 Models/                   # Model (database layer)
│   │   ├── GuruModel.php            # Model tabel guru
│   │   ├── HasilUjianModel.php      # ⭐ Model hasil_ujian (IRT)
│   │   ├── JadwalUjianModel.php     # Model jadwal_ujian
│   │   ├── JenisUjianModel.php      # Model jenis_ujian
│   │   ├── KelasModel.php           # Model kelas
│   │   ├── PengumumanModel.php      # Model pengumuman
│   │   ├── PesertaUjianModel.php    # Model peserta_ujian
│   │   ├── SekolahModel.php         # Model sekolah
│   │   ├── SiswaModel.php           # Model siswa
│   │   ├── SoalUjianModel.php       # ⭐ Model soal_ujian + IRT
│   │   ├── UjianModel.php           # Model ujian (konfigurasi CAT)
│   │   └── UserModel.php            # ⭐ Model users + soft delete
│   │
│   ├── 📁 ThirdParty/               # Library pihak ketiga (kosong)
│   │
│   └── 📁 Views/                    # View (tampilan UI)
│       ├── welcome_message.php      # Halaman default CI4
│       ├── 📁 admin/
│       │   ├── dashboard.php        # Dashboard admin
│       │   ├── feedback.php         # Feedback admin
│       │   ├── jenis_ujian.php      # CRUD jenis ujian
│       │   ├── 📁 bank_soal/
│       │   │   ├── index.php        # Bank soal utama
│       │   │   ├── kategori.php     # Pilih kategori
│       │   │   ├── jenis_ujian.php  # Pilih jenis ujian
│       │   │   └── ujian.php        # Kelola soal bank
│       │   ├── 📁 guru/
│       │   │   ├── daftar.php       # Daftar guru
│       │   │   ├── tambah.php       # Form tambah guru
│       │   │   └── edit.php         # Form edit guru
│       │   ├── 📁 hasil/
│       │   │   ├── daftar.php       # Daftar hasil ujian
│       │   │   ├── detail.php       # Detail hasil siswa
│       │   │   ├── siswa.php        # Hasil per siswa
│       │   │   ├── download_excel.php
│       │   │   └── download_pdf.php
│       │   ├── 📁 jadwal/
│       │   │   ├── jadwal_ujian.php # CRUD jadwal
│       │   │   └── detail.php       # Detail jadwal
│       │   ├── 📁 kelas/
│       │   │   ├── daftar.php
│       │   │   ├── detail.php
│       │   │   ├── tambah.php
│       │   │   └── edit.php
│       │   ├── 📁 pengumuman/
│       │   │   ├── daftar.php
│       │   │   ├── detail.php
│       │   │   ├── tambah.php
│       │   │   └── edit.php
│       │   ├── 📁 sekolah/
│       │   │   ├── daftar.php       # Daftar sekolah
│       │   │   ├── tambah.php       # Form tambah sekolah
│       │   │   ├── edit.php         # Edit sekolah
│       │   │   ├── kelas.php        # Daftar kelas per sekolah
│       │   │   ├── detail_kelas.php # Detail kelas (guru + siswa)
│       │   │   ├── tambah_kelas.php # Tambah kelas
│       │   │   ├── edit_kelas.php   # Edit kelas
│       │   │   └── transfer_siswa.php
│       │   ├── 📁 siswa/
│       │   │   ├── daftar.php       # Daftar siswa
│       │   │   ├── tambah.php       # Form tambah + batch create
│       │   │   └── edit.php         # Form edit siswa
│       │   └── 📁 ujian/
│       │       ├── daftar.php       # Daftar ujian
│       │       ├── detail.php       # Detail ujian
│       │       └── kelola_soal.php  # Kelola soal per ujian
│       │
│       ├── 📁 auth/
│       │   ├── login.php            # Form login
│       │   └── register.php         # Form register
│       │
│       ├── 📁 errors/               # Halaman error CI4
│       │   ├── cli/
│       │   │   ├── error_404.php
│       │   │   ├── error_exception.php
│       │   │   └── production.php
│       │   └── html/
│       │       ├── debug.css
│       │       ├── debug.js
│       │       ├── error_404.php
│       │       ├── error_exception.php
│       │       └── production.php
│       │
│       ├── 📁 guru/
│       │   ├── dashboard.php
│       │   ├── profil.php
│       │   ├── jenis_ujian.php
│       │   ├── ujian.php
│       │   ├── kelola_soal.php
│       │   ├── kelola_soal_ck4.php  # CKEditor 4 integration
│       │   ├── daftar_soal.php
│       │   ├── jadwal_ujian.php
│       │   ├── hasil_ujian.php
│       │   ├── daftar_siswa.php
│       │   ├── detail_hasil.php
│       │   ├── hasil_ujian_excel.php
│       │   ├── hasil_ujian_pdf.php
│       │   ├── pengumuman.php
│       │   └── 📁 bank_soal/
│       │       ├── index.php
│       │       ├── kategori.php
│       │       ├── jenis_ujian.php
│       │       └── ujian.php
│       │
│       ├── 📁 pages/                # Halaman publik
│       │   ├── home.php             # Landing page
│       │   ├── about.php
│       │   ├── contact.php
│       │   ├── faq.php
│       │   ├── bantuan.php
│       │   ├── guide.php
│       │   └── profile.php
│       │
│       ├── 📁 siswa/
│       │   ├── dashboard.php        # Dashboard siswa
│       │   ├── profil.php           # Edit profil
│       │   ├── pengumuman.php       # Lihat pengumuman
│       │   ├── ujian.php            # ⭐ Daftar ujian tersedia
│       │   ├── soal.php             # ⭐ Halaman pengerjaan soal CAT
│       │   ├── selesai_ujian.php    # ⭐ Halaman selesai ujian
│       │   ├── hasil.php            # Riwayat hasil ujian
│       │   ├── detail_hasil.php     # ⭐ Detail + klasifikasi kognitif
│       │   └── cetak_hasil_ujian.php# Halaman cetak/PDF
│       │
│       └── 📁 templates/            # Template layout
│           ├── header.php           # Header umum
│           ├── footer.php           # Footer umum
│           ├── navbar.php           # Navigasi umum
│           ├── user_header.php      # Header untuk halaman user
│           ├── 📁 admin/
│           │   └── admin_template.php   # ⭐ Template admin
│           ├── 📁 guru/
│           │   └── guru_template.php    # ⭐ Template guru
│           └── 📁 siswa/
│               └── siswa_template.php   # ⭐ Template siswa
│
├── 📁 public/                       # Web root (public access)
│   ├── index.php                    # ⭐ Entry point aplikasi
│   ├── .htaccess                    # Rewrite rules
│   ├── 📁 assets/
│   │   └── 📁 images/
│   │       ├── hero.webp
│   │       ├── icon-cat.png
│   │       ├── phyfacat.png
│   │       ├── phyfacat.webp
│   │       └── 📁 profil/
│   │           └── albert.webp
│   ├── 📁 ckeditor/                 # CKEditor 4 (editor teks rich)
│   │   ├── ckeditor.js             # Core editor
│   │   ├── config.js               # Konfigurasi editor
│   │   ├── contents.css
│   │   ├── 📁 adapters/
│   │   ├── 📁 lang/                # 60+ bahasa
│   │   ├── 📁 plugins/             # 30+ plugin (image, table, link, dll)
│   │   ├── 📁 samples/             # Contoh implementasi
│   │   ├── 📁 skins/
│   │   └── 📁 vendor/
│   └── 📁 uploads/
│       ├── 📁 soal/                # Upload gambar soal
│       └── 📁 editor-images/       # Upload gambar dari CKEditor
│
├── 📁 tests/                        # Unit testing
│   ├── 📁 _support/
│   │   ├── 📁 Database/
│   │   │   ├── 📁 Migrations/
│   │   │   └── 📁 Seeds/
│   │   ├── 📁 Libraries/
│   │   └── 📁 Models/
│   ├── 📁 database/
│   ├── 📁 session/
│   └── 📁 unit/
│
├── 📁 vendor/                       # Dependency Composer (auto-generated)
│   └── codeigniter4/framework/
│
├── 📁 writable/                     # File writable (cache, logs, uploads)
│
├── 🔧 Konfigurasi Root:
│   ├── .env                         # ⭐ Environment variables (DB, URL, dsb)
│   ├── .env.example                 # Template .env
│   ├── .gitignore                   # File yang diabaikan Git
│   ├── .htaccess                    # Apache config (root)
│   ├── composer.json                # ⭐ Dependency Composer
│   ├── composer.lock                # Versi dependency terkunci
│   ├── package-lock.json            # (npm)
│   ├── phpunit.xml.dist             # Konfigurasi PHPUnit
│   ├── LICENSE                      # MIT License
│   └── README.md                    # Dokumentasi dasar
│
├── 🚀 Entry Points:
│   ├── spark                        # ⭐ CLI tool CodeIgniter
│   ├── preload.php                  # PHP preload
│   └── builds                       # Build script
│
├── 🗄️ Database:
│   └── db_cat_cbt.sql               # ⭐ Dump database lengkap
│
├── 🛠️ Tools:
│   ├── generate_password.php        # Generator password hash
│   └── ck.html                      # (testing/reference)
│
└── 📄 Dokumentasi Buatan:
    ├── SKEMA_DATABASE.md             # Skema database + relasi
    └── ALUR_APLIKASI.md              # Alur aplikasi + algoritma CAT
```

---

## Penjelasan per Folder

### 📁 `app/` — Aplikasi Utama

Folder ini berisi seluruh kode aplikasi mengikuti arsitektur MVC CodeIgniter 4.

#### `app/Config/` — Konfigurasi

| File Penting | Fungsi |
|-------------|--------|
| **`Routes.php`** | Mendefinisikan semua route URL aplikasi (admin, guru, siswa, auth) |
| **`Filters.php`** | Mendaftarkan `AuthFilter` sebagai middleware untuk route terlindungi |
| **`Database.php`** | Konfigurasi koneksi database (host, user, password) — meng-override `.env` |
| **`Session.php`** | Konfigurasi session (file-based) |
| **`App.php`** | Pengaturan dasar: `$baseURL`, `$indexPage`, environment |

#### `app/Controllers/` — Controller

| File | Baris | Fungsi |
|------|-------|--------|
| **`Auth.php`** | ~120 | Login, register, logout + role-based redirect |
| **`Admin/Admin.php`** | ~3800+ | ⭐ Controller TERBESAR — seluruh fitur admin |
| **`Guru/Guru.php`** | ~1800+ | Seluruh fitur guru |
| **`Siswa/Siswa.php`** | ~700+ | ⭐ **CAT Engine** — pilih soal, hitung IRT, simpan jawaban |
| **`Home.php`** | ~20 | Controller halaman publik |
| **`BaseController.php`** | ~40 | Base class untuk semua controller |

#### `app/Models/` — Model (Database Layer)

| Model | Tabel | Fungsi Utama |
|-------|-------|-------------|
| **`UserModel.php`** | `users` | CRUD user, soft delete (activate/deactivate), statistik dashboard |
| **`GuruModel.php`** | `guru` | CRUD guru |
| **`SiswaModel.php`** | `siswa` | CRUD siswa, cek profil |
| **`SekolahModel.php`** | `sekolah` | CRUD sekolah |
| **`KelasModel.php`** | `kelas` | CRUD kelas |
| **`UjianModel.php`** | `ujian` | CRUD ujian (paket soal + parameter IRT) |
| **`SoalUjianModel.php`** | `soal_ujian` | CRUD soal + query pemilihan soal CAT |
| **`JenisUjianModel.php`** | `jenis_ujian` | CRUD jenis ujian |
| **`JadwalUjianModel.php`** | `jadwal_ujian` | CRUD jadwal + query per kelas guru |
| **`PesertaUjianModel.php`** | `peserta_ujian` | CRUD peserta ujian |
| **`HasilUjianModel.php`** | `hasil_ujian` | ⭐ Simpan jawaban + parameter IRT |
| **`PengumumanModel.php`** | `pengumuman` | CRUD pengumuman + join user |

#### `app/Views/` — Template Tampilan

**Struktur 3 role** dengan template berbeda:

| Role | Template | Views |
|------|----------|-------|
| **Admin** | `templates/admin/admin_template.php` | 36 file di `admin/` |
| **Guru** | `templates/guru/guru_template.php` | 18 file di `guru/` |
| **Siswa** | `templates/siswa/siswa_template.php` | 9 file di `siswa/` |
| **Publik** | Header + footer umum | `pages/`, `auth/` |

#### `app/Filters/`

| File | Fungsi |
|------|--------|
| **`AuthFilter.php`** | Middleware — cek apakah user sudah login. Jika belum, redirect ke `/login`. Melindungi route `/admin/*`, `/guru/*`, `/siswa/*` |

---

### 📁 `public/` — Web Root

Folder yang di-expose ke publik via web server (Apache/Nginx).

| Subfolder | Isi |
|-----------|-----|
| `index.php` | **Entry point** — semua request masuk lewat sini (Front Controller pattern) |
| `.htaccess` | Rewrite rules Apache — redirect semua request ke `index.php` |
| `assets/images/` | Gambar statis (logo, hero, icon profil) |
| `ckeditor/` | **CKEditor 4** — rich text editor untuk membuat soal (pertanyaan, pilihan) |
| `uploads/soal/` | Upload gambar untuk soal |
| `uploads/editor-images/` | Upload gambar dari CKEditor/summernote dalam soal |

---

### 📁 `writable/` — File Writable

Folder untuk file yang dibuat saat runtime:
- Cache
- Log aplikasi
- Session file
- Upload sementara

---

### 🔧 File Konfigurasi Root

| File | Fungsi |
|------|--------|
| **`.env`** | Environment variables: `CI_ENVIRONMENT`, `app.baseURL`, konfigurasi database |
| **`composer.json`** | Dependensi: `codeigniter4/framework ^4.0`, `phpunit`, `faker` |
| **`spark`** | CLI tool CodeIgniter (migrate, seed, routes list, dll) |
| **`db_cat_cbt.sql`** | Dump database lengkap (struktur + data) |

---

## Alur Request HTTP

```
Browser Request (contoh: /admin/guru)
  │
  ▼
public/.htaccess → Rewrite ke public/index.php
  │
  ▼
public/index.php → Bootstrap CodeIgniter
  │
  ▼
app/Config/Routes.php → Cocokkan route
  │
  ▼
app/Config/Filters.php → Cek AuthFilter (GET /admin/*)
  │
  ├─ Belum login → Redirect /login
  └─ Sudah login → Lanjut
       │
       ▼
app/Controllers/Admin/Admin.php::daftarGuru()
  │
  ├─ Panggil Model (app/Models/GuruModel.php, UserModel.php)
  ├─ Query database
  └─ Return data
       │
       ▼
app/Views/templates/admin/admin_template.php
  │
  └─ Load app/Views/admin/guru/daftar.php
       │
       ▼
HTML Response → Browser
```

---

## Ringkasan Statistik

| Kategori | Jumlah |
|----------|--------|
| Controller | 7 file |
| Model | 12 file |
| View (admin) | 36 file |
| View (guru) | 18 file |
| View (siswa) | 9 file |
| View (publik) | 10 file |
| Template | 6 file |
| Config | 35 file |
| **Migration** | **5 file (v2)** |
| Total file projek (tanpa vendor) | ~300+ file |
| Baris kode controller | ~6500+ baris |
| Database | 22 tabel (v2: 14 original + 8 baru) |

---

## Dokumentasi

| File | Deskripsi |
|------|-----------|
| `SKEMA_DATABASE.md` | Skema 22 tabel + relasi + RBAC + perbandingan v1/v2 |
| `ALUR_APLIKASI.md` | Alur aplikasi + algoritma CAT/IRT |
| `RANCANGAN_CAT-CBT_v2.md` | Rancangan pengembangan v2 (TODO list 10 fase) |
| `STRUKTUR_PROJEK.md` | Struktur folder & file + alur HTTP request |
