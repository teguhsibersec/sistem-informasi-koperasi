7 DIAGRAM — SISTEM INFORMASI KOPERASI SEKOLAH
==============================================
Sumber: analisis kode sumber aplikasi Laravel (htdocs.zip)

Daftar file PNG:
1. use-case.png            — Use Case Diagram (5 aktor: Pengunjung, Anggota, Bendahara/Kasir, Sekretaris/Gudang, Admin/Ketua)
2. activity.png            — Activity Diagram alur pemesanan online anggota sampai selesai (termasuk cabang QRIS & upload bukti)
3. sequence.png            — Sequence Diagram checkout online via QRIS sampai webhook konfirmasi payment gateway
4. component.png           — Component Diagram modul-modul aplikasi Laravel & dependensinya
5. deployment.png          — Deployment Diagram (client, web hosting, PHP-FPM Laravel, MySQL, payment gateway QRIS)
6. erd.png                 — Entity Relationship Diagram (16 tabel: users, pengurus, anggota, pemasok, kategori, barang, penjualan, detail_penjualan, pembayaran, pembelian, detail_pembelian, keranjang, keranjang_item, riwayat_stok, pengaturan, log_aktivitas)
7. system-architecture.png — System Architecture Diagram berlapis (Presentation, HTTP/Middleware, Service, Domain, Infrastructure)

Folder sumber-mmd/ berisi file sumber Mermaid (.mmd) yang bisa diedit ulang.
Catatan hak akses: Ketua = admin penuh; Bendahara = peran kasir; Sekretaris = peran gudang; Anggota = storefront.
