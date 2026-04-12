# ⚡ VoltSpace — Sprint 1 Development Guide

> Panduan resmi pengerjaan Sprint 1 VoltSpace: alur kerja Git, pembagian tugas, dan aturan pengembangan tim.

---

## 📋 Daftar Isi

- [Sprint Timeline](#-sprint-1-timeline)
- [Sprint Goal](#-sprint-goal)
- [Branch Structure](#-branch-structure)
- [Setup Awal](#-setup-awal)
- [Workflow Pengerjaan](#-workflow-pengerjaan)
- [Aturan Penting](#️-aturan-penting)
- [Pembagian Tugas](#-pembagian-tugas-sprint-1)
- [Testing](#-testing-week-10)
- [Output Sprint 1](#-output-sprint-1)

---

## 📅 Sprint 1 Timeline

| Tahap       | Periode              |
|-------------|----------------------|
| Development | Week 6 – Week 9      |
| Testing     | Week 10 (1 minggu)   |

---

## 🎯 Sprint Goal

Mengembangkan fitur inti sistem VoltSpace:

| #  | Fitur                    |
|----|--------------------------|
| 1  | Manajemen Ruangan        |
| 2  | Manajemen Pengguna       |
| 3  | Jadwal Listrik           |
| 4  | Dashboard Monitoring     |
| 5  | Manajemen IoT            |
| 6  | Integrasi IoT            |

---

## 🌳 Branch Structure

```
main
├── rizki/ruangan
├── mufid/user
├── helmi/dashboard
├── aydin/jadwal-listrik
└── cheisya/iot-device
```

| Aturan | Keterangan |
|--------|------------|
| ❌ | Jangan membuat branch baru (karena sudah dibikin) |
| ✅ | Semua pekerjaan dilakukan di branch masing-masing |

---

## 🚀 Setup Awal

Lakukan langkah berikut **satu kali** saat pertama kali memulai.

### 1. Masuk ke Folder Kerja

```bash
cd folder-kerja-kalian
```

### 2. Clone Repository

```bash
git clone https://github.com/VoltSpace-PPL/VoltSpace-Web.git
```

### 3. Masuk ke Folder Project

```bash
cd VoltSpace-Web
```

### 4. Install & Konfigurasi Laravel

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh
php artisan serve
```

---

## 🔁 Workflow Pengerjaan

Ikuti langkah ini **setiap kali** mengerjakan fitur.

### Step 1 — Masuk ke Branch Masing-Masing

```bash
git checkout nama/branch
```

> **Contoh:**
> ```bash
> git checkout rizki/ruangan
> ```

---

### Step 2 — Kerjakan Fitur

Lakukan development sesuai task masing-masing (lihat [Pembagian Tugas](#-pembagian-tugas-sprint-1)).

---

### Step 3 — Commit Perubahan

```bash
git add .
git commit -m "feat: deskripsi perubahan"
```

> **Contoh commit message yang benar:**
> ```
> feat: create CRUD ruangan
> fix: validation input user
> refactor: simplify dashboard query
> ```

---

### Step 4 — Push ke GitHub

```bash
git push origin nama/branch
```

> **Contoh:**
> ```bash
> git push origin rizki/ruangan
> ```

---

### Step 5 — Buat Pull Request (PR)

Buka GitHub dan buat PR dari branch kamu ke `main`:

```
nama/branch  →  main
```

> **Contoh:**
> ```
> rizki/ruangan  →  main
> ```

---

## ⚠️ Aturan Penting

### ❌ Dilarang

- Push langsung ke `main`
- Bekerja di branch orang lain
- Membuat branch baru di luar yang sudah ditentukan

### ✅ Wajib Dilakukan

- Semua perubahan harus melalui **Pull Request ke `main`**
- Commit message harus **jelas dan deskriptif**
- Selalu bekerja di **branch masing-masing**

---

## 👥 Pembagian Tugas Sprint 1

| Nama | Branch | Tugas |
|------|--------|-------|
| M. Rizki Aulia Wibowo | `rizki/ruangan` | CRUD Ruangan + validasi input |
| Muhammad Aydin Yusuf | `aydin/jadwal-listrik` | CRUD Jadwal Listrik + logika penjadwalan |
| Cheisya Valda Wibawaningrum | `cheisya/iot-device` | CRUD IoT Device + integrasi data |
| Muhammad Mufid Taqiyuddin | `mufid/user` | CRUD User + role management + validasi akun |
| M. Helmi Afriza | `helmi/dashboard` | Dashboard Monitoring + visualisasi energi |

---

## 🧪 Testing (Week 10)

- [ ] Integration testing antar fitur
- [ ] Testing IoT system
- [ ] Bug fixing
- [ ] End-to-end testing

---

## 🎯 Output Sprint 1

- [ ] CRUD utama selesai
- [ ] Dashboard berjalan
- [ ] IoT terintegrasi
- [ ] Sistem siap untuk Sprint 2

---

## ⚡ Quick Reference — Workflow Singkat

```bash
# 1. Masuk ke branch kamu
git checkout nama/branch

# 2. Selesai ngoding, simpan perubahan
git add .
git commit -m "feat: deskripsi perubahan"

# 3. Push ke GitHub
git push origin nama/branch

# 4. Buat Pull Request di GitHub: nama/branch → main
```

---

*VoltSpace · Sprint 1 · PPL Team*
