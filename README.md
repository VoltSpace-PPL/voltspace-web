# ⚡ VoltSpace - Sprint 1 Development Guide

Dokumen ini berisi panduan pengerjaan Sprint 1 untuk tim VoltSpace, termasuk alur kerja Git, pembagian tugas, serta aturan pengembangan.

---

# 📅 Sprint 1 Timeline

* **Development:** Week 6 – Week 9 (6 April – 2 Mei)
* **Testing:** Week 10 (1 minggu)

---

# 🎯 Sprint Goal

Mengembangkan fitur inti sistem VoltSpace yang mencakup manajemen ruangan, pengguna, jadwal listrik, dashboard monitoring, serta integrasi IoT dasar.

---

# 🌳 Branch Structure

```bash
main   → versi stabil
dev    → integrasi semua fitur
nama/fitur → development masing-masing anggota
```

---

# 🚀 Setup Awal (Wajib Semua Anggota)

```bash
git clone https://github.com/VoltSpace-PPL/VoltSpace-Web.git
cd VoltSpace-Web

composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

---

# 🔁 Workflow Pengerjaan

## 1. Ambil update terbaru dari dev

```bash
git checkout dev
git pull origin dev
```

---

## 2. Masuk ke branch masing-masing

Contoh:

```bash
git checkout rizki/ruangan
```

---

## 3. Kerjakan fitur

Lakukan coding sesuai tugas masing-masing.

---

## 4. Commit perubahan

```bash
git add .
git commit -m "feat: deskripsi perubahan"
```

Contoh:

```bash
feat: create data ruangan
fix: validasi input user
```

---

## 5. Push ke GitHub

```bash
git push origin nama-branch
```

---

## 6. Pull Request (PR)

* Lakukan PR ke:

```
nama/fitur → dev
```

---

## 7. Update dari dev (WAJIB sebelum lanjut kerja)

```bash
git checkout dev
git pull origin dev
git checkout nama/fitur
git merge dev
```

---

# ⚠️ Aturan Penting

❌ Tidak boleh push ke `main`
❌ Tidak boleh langsung kerja di `dev`
✅ Semua fitur harus melalui Pull Request ke `dev`
✅ Commit harus jelas dan terstruktur

---

# 👥 Pembagian Tugas Sprint 1

## 👤 M. Rizki Aulia Wibowo

**Branch:** `rizki/ruangan`
**Tugas:**

* Mengembangkan fitur CRUD data ruangan
* Validasi input data ruangan

---

## 👤 Muhammad Aydin Yusuf

**Branch:** `aydin/jadwal-listrik`
**Tugas:**

* Mengembangkan CRUD jadwal listrik
* Mengatur logika penjadwalan listrik otomatis

---

## 👤 Cheisya Valda Wibawaningrum

**Branch:** `cheisya/iot-device`
**Tugas:**

* Mengelola CRUD perangkat IoT
* Menyimpan data device ke sistem

---

## 👤 Muhammad Mufid Taqiyuddin

**Branch:** `mufid/user`
**Tugas:**

* Mengembangkan CRUD pengguna
* Mengatur role (admin/mahasiswa)
* Validasi dan manajemen akun

---

## 👤 M. Helmi Afriza

**Branch:** `helmi/dashboard`
**Tugas:**

* Mengembangkan dashboard monitoring energi
* Menampilkan data penggunaan listrik
* Visualisasi data (grafik/summary)

---

# 🧪 Testing (Week 10)

Pengujian dilakukan selama 1 minggu mencakup:

* Integration testing antar fitur
* Pengujian IoT dengan sistem web
* Bug fixing
* Validasi fungsionalitas end-to-end

---

# 🎯 Output Sprint 1

* Sistem CRUD utama berjalan
* Dashboard monitoring aktif
* IoT terintegrasi dengan backend
* Sistem siap dikembangkan ke Sprint 2

---

# ⚡ Catatan

* Pastikan selalu sync dengan `dev`
* Gunakan commit message yang jelas
* Koordinasi aktif antar tim

---
