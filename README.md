# 💧 Aplikasi Kelola Penagihan Air  
Pamsimas adalah aplikasi berbasis PHP Slim yang digunakan untuk mengelola pembayaran penggunaan air per meter kubik bagi warga Desa Panembangan. Aplikasi ini bertujuan untuk mempermudah pencatatan konsumsi air, penghitungan tagihan, serta memfasilitasi pembayaran secara efisien dan transparan.

## 🚀 Fitur
- **Manajemen Pelanggan**: Tambah, edit, dan hapus data pelanggan.  
- **Pencatatan Pemakaian Air**: Input jumlah penggunaan air berdasarkan meteran.  
- **Perhitungan Tagihan**: Menghitung biaya tagihan berdasarkan tarif per meter kubik.  
- **Pembuatan & Pengelolaan Tagihan**: Generate tagihan bulanan dan status pembayaran.  
- **Laporan**: Rekap pemakaian dan pembayaran dalam bentuk laporan.  
- **Kelola Pegawai**: Tambah, edit, dan hapus data pegawai yang berwenang mengelola sistem.  
- **Kelola Admin**: Sistem multi-user dengan peran admin dan pegawai untuk akses yang lebih aman.  
- **API RESTful**: Endpoint untuk mengintegrasikan dengan frontend atau aplikasi lain.  

## 🛠️ Teknologi yang Digunakan
- **Backend**: [PHP Slim](https://www.slimframework.com/)  
- **Database**: MySQL  
- **Template Engine**: Twig  
- **Autentikasi**: JWT (JSON Web Token)  

## 📌 Instalasi  
1. **Klon repositori**  
   ```sh
   git clone https://github.com/ceceprokani/Pamsimas-Panembangan-BE.git  
   cd Pamsimas-Panembangan-BE