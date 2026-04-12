# ⚡ VoltSpace - Sprint 1 Development Guide (Final)

Dokumen ini berisi panduan pengerjaan Sprint 1 VoltSpace, termasuk alur kerja Git, pembagian tugas, serta aturan pengembangan.

---

# 📅 Sprint 1 Timeline

| Tahap       | Periode             |
|------------|--------------------|
| Development | Week 6 – Week 9     |
| Testing     | Week 10 (1 minggu)  |

---

# 🎯 Sprint Goal

Mengembangkan fitur inti sistem VoltSpace:

- Manajemen ruangan  
- Manajemen pengguna  
- Jadwal listrik  
- Dashboard monitoring  
- Integrasi IoT dasar  

---

# 🌳 Branch Structure


main
├── rizki/ruangan
├── mufid/user
├── helmi/dashboard
├── aydin/jadwal-listrik
└── cheisya/iot-device


❌ Tidak ada `dev` branch  
❌ Tidak membuat branch baru  
✅ Semua pekerjaan dilakukan di branch masing-masing  

---

# 🚀 Setup Awal (CLONE MANUAL)

## 1. Masuk folder kerja
```bash
cd folder-kerja-kalian
2. Clone repository
git clone https://github.com/VoltSpace-PPL/VoltSpace-Web.git
3. Masuk project
cd VoltSpace-Web
4. Install Laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh
php artisan serve
🔁 Workflow Pengerjaan
1. Masuk branch masing-masing
git checkout nama/branch

Contoh:

git checkout rizki/ruangan
2. Kerjakan fitur

Lakukan development sesuai task masing-masing.

3. Commit perubahan
git add .
git commit -m "feat: deskripsi perubahan"

Contoh:

feat: create CRUD ruangan
fix: validation input user
4. Push ke GitHub
git push origin nama/branch

Contoh:

git push origin rizki/ruangan
5. Pull Request (PR)
nama/branch → main

Contoh:

rizki/ruangan → main
⚠️ Aturan Penting

❌ Tidak boleh push langsung ke main
❌ Tidak boleh kerja di branch orang lain
❌ Tidak boleh membuat branch baru

✅ Semua fitur sudah disediakan branch
✅ Semua perubahan wajib melalui Pull Request ke main
✅ Commit harus jelas dan deskriptif

👥 Pembagian Tugas Sprint 1
Nama	Branch	Tugas
M. Rizki Aulia Wibowo	rizki/ruangan	CRUD ruangan + validasi input
Muhammad Aydin Yusuf	aydin/jadwal-listrik	CRUD jadwal listrik + logika penjadwalan
Cheisya Valda Wibawaningrum	cheisya/iot-device	CRUD IoT device + integrasi data
Muhammad Mufid Taqiyuddin	mufid/user	CRUD user + role management + validasi akun
M. Helmi Afriza	helmi/dashboard	Dashboard monitoring + visualisasi energi
🧪 Testing (Week 10)
Integration testing antar fitur
Testing IoT system
Bug fixing
End-to-end testing
🎯 Output Sprint 1
CRUD utama selesai
Dashboard berjalan
IoT terintegrasi
Sistem siap Sprint 2
⚡ Workflow Singkat
git checkout nama/branch
git add .
git commit -m "feat: ..."
git push origin nama/branch

# lalu PR ke main
