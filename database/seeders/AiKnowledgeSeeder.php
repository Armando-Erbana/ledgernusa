<?php

namespace Database\Seeders;

use App\Models\AiKnowledge;
use Illuminate\Database\Seeder;

class AiKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'keywords' => ['cara', 'input', 'jurnal', 'buat', 'baru'],
                'question' => 'Cara input jurnal?',
                'answer' => "Berikut langkahnya:\n1. Buka menu Jurnal di bawah\n2. Klik tombol + Jurnal\n3. Isi tanggal & referensi\n4. Pilih akun, isi debit/kredit\n5. Klik Simpan Draft\n6. Setelah yakin, klik Posting",
                'action_url' => '/journals/create',
                'action_label' => 'Buka Jurnal',
                'category' => 'jurnal',
            ],
            [
                'keywords' => ['posting', 'jurnal', 'finalisasi'],
                'question' => 'Cara posting jurnal?',
                'answer' => "Setelah jurnal disimpan sebagai draft:\n1. Buka detail jurnal\n2. Klik tombol Posting Jurnal\n\nSetelah diposting, jurnal tidak bisa diedit lagi. Pastikan sudah benar sebelum posting.",
                'action_url' => '/journals',
                'action_label' => 'Lihat Jurnal',
                'category' => 'jurnal',
            ],
            [
                'keywords' => ['balance', 'debit', 'kredit', 'tidak', 'sama'],
                'question' => 'Kenapa debit kredit harus balance?',
                'answer' => "Prinsip dasar akuntansi (double-entry): setiap transaksi harus memiliki total debit = total kredit. Ini menjaga persamaan akuntansi:\n\nAset = Kewajiban + Ekuitas\n\nKalau tidak balance, sistem tidak akan menyimpan jurnal.",
                'category' => 'jurnal',
            ],
            [
                'keywords' => ['coa', 'chart', 'akun', 'accounts'],
                'question' => 'Apa itu COA?',
                'answer' => "COA (Chart of Accounts) adalah daftar semua akun akuntansi perusahaan Anda. Terbagi 5 tipe:\n- Aset: kas, bank, piutang\n- Kewajiban: hutang\n- Ekuitas: modal\n- Pendapatan\n- Beban\n\nSemua jurnal menggunakan akun dari COA ini.",
                'action_url' => '/accounts',
                'action_label' => 'Buka COA',
                'category' => 'coa',
            ],
            [
                'keywords' => ['tambah', 'akun', 'coa', 'buat', 'baru'],
                'question' => 'Cara tambah akun COA?',
                'answer' => "1. Buka menu COA\n2. Klik + Akun\n3. Isi kode (misal 101)\n4. Isi nama (misal Kas)\n5. Pilih tipe (asset/liability/dll)\n6. Centang 'Kas' atau 'Bank' jika perlu\n7. Klik Simpan",
                'action_url' => '/accounts/create',
                'action_label' => 'Tambah Akun',
                'category' => 'coa',
            ],
            [
                'keywords' => ['import', 'coa', 'csv', 'upload', 'massal'],
                'question' => 'Cara import COA dari CSV?',
                'answer' => "1. Siapkan file CSV dengan format:\n   code,name,type\n   101,Kas,asset\n   102,Bank,asset\n2. Buka menu COA → Import CSV\n3. Upload file\n4. Klik Import Sekarang\n\nTipe valid: asset, liability, equity, revenue, expense.",
                'action_url' => '/accounts/import',
                'action_label' => 'Import COA',
                'category' => 'coa',
            ],
            [
                'keywords' => ['laba', 'rugi', 'lihat', 'laporan', 'profit'],
                'question' => 'Cara lihat laba rugi?',
                'answer' => "1. Buka menu Laporan\n2. Pilih Laba Rugi\n3. Pilih periode (dari - sampai)\n4. Klik Tampilkan\n\nAkan muncul total pendapatan, beban, dan laba/rugi bersih periode tersebut.",
                'action_url' => '/reports/income-statement',
                'action_label' => 'Lihat Laba Rugi',
                'category' => 'laporan',
            ],
            [
                'keywords' => ['neraca', 'balance', 'sheet', 'lihat'],
                'question' => 'Apa itu neraca?',
                'answer' => "Neraca (Balance Sheet) menunjukkan posisi keuangan pada satu tanggal tertentu:\n\nAset = Kewajiban + Ekuitas\n\nBerbeda dengan Laba Rugi yang menunjukkan periode, Neraca menunjukkan snapshot pada 1 tanggal.",
                'action_url' => '/reports/balance-sheet',
                'action_label' => 'Lihat Neraca',
                'category' => 'laporan',
            ],
            [
                'keywords' => ['buku', 'besar', 'ledger', 'lihat'],
                'question' => 'Cara lihat buku besar?',
                'answer' => "1. Buka menu Laporan\n2. Pilih Buku Besar\n3. Pilih akun yang ingin dilihat\n4. Pilih periode\n5. Klik Tampilkan\n\nAnda akan lihat saldo awal, mutasi, dan saldo akhir akun tersebut.",
                'action_url' => '/reports/ledger',
                'action_label' => 'Lihat Buku Besar',
                'category' => 'laporan',
            ],
            [
                'keywords' => ['neraca', 'saldo', 'trial', 'balance'],
                'question' => 'Apa itu neraca saldo?',
                'answer' => "Neraca Saldo (Trial Balance) adalah daftar semua akun dengan saldo debit/kredit pada periode tertentu. Fungsinya untuk memastikan total debit = total kredit.\n\nKalau tidak balance, ada kesalahan input jurnal.",
                'action_url' => '/reports/trial-balance',
                'action_label' => 'Lihat Neraca Saldo',
                'category' => 'laporan',
            ],
            [
                'keywords' => ['ganti', 'company', 'switch', 'pindah', 'perusahaan'],
                'question' => 'Cara ganti company?',
                'answer' => "1. Klik menu Setting (kanan bawah) atau tombol 'Company' di header\n2. Cari company yang ingin dipakai\n3. Klik 'Pilih'\n\nAnda akan langsung masuk ke company tersebut.",
                'action_url' => '/companies',
                'action_label' => 'Ganti Company',
                'category' => 'company',
            ],
            [
                'keywords' => ['buat', 'company', 'baru', 'perusahaan'],
                'question' => 'Cara buat company baru?',
                'answer' => "1. Buka menu Setting/Company\n2. Klik + Baru\n3. Isi nama company\n4. Isi currency (default IDR)\n5. Isi alamat (opsional)\n6. Klik Simpan\n\nAnda otomatis jadi owner company tersebut.",
                'action_url' => '/companies/create',
                'action_label' => 'Buat Company',
                'category' => 'company',
            ],
            [
                'keywords' => ['trial', 'gratis', 'free', 'berapa', 'lama'],
                'question' => 'Berapa lama trial?',
                'answer' => "Trial gratis 14 hari untuk setiap company baru. Selama trial, Anda bisa akses semua fitur paket Professional.\n\nSetelah 14 hari, Anda perlu berlangganan untuk lanjut menggunakan.",
                'action_url' => '/subscription',
                'action_label' => 'Lihat Langganan',
                'category' => 'subscription',
            ],
            [
                'keywords' => ['perpanjang', 'langganan', 'bayar', 'subscription'],
                'question' => 'Cara perpanjang langganan?',
                'answer' => "1. Buka menu Langganan\n2. Pilih paket yang diinginkan\n3. Hubungi admin via WhatsApp untuk pembayaran\n4. Setelah pembayaran dikonfirmasi, akses Anda aktif kembali",
                'action_url' => '/subscription',
                'action_label' => 'Buka Langganan',
                'category' => 'subscription',
            ],
            [
                'keywords' => ['paket', 'harga', 'plan', 'biaya', 'berapa'],
                'question' => 'Apa saja paket langganan?',
                'answer' => "Tersedia 3 paket:\n\n• Starter — Rp 149rb/bulan\n  3 user, 500 jurnal/bulan\n\n• Professional — Rp 399rb/bulan\n  10 user, 5.000 jurnal/bulan\n\n• Enterprise — Rp 1,5jt/bulan\n  Unlimited user & jurnal",
                'action_url' => '/subscription',
                'action_label' => 'Lihat Paket',
                'category' => 'subscription',
            ],
            [
                'keywords' => ['install', 'pwa', 'hp', 'mobile', 'aplikasi'],
                'question' => 'Cara install ke HP?',
                'answer' => "LedgerNusa bisa diinstall seperti aplikasi native:\n\nAndroid (Chrome):\n1. Buka LedgerNusa di Chrome\n2. Klik menu (3 titik)\n3. Pilih 'Add to Home screen'\n\niOS (Safari):\n1. Buka di Safari\n2. Klik tombol Share\n3. Pilih 'Add to Home Screen'",
                'category' => 'pwa',
            ],
            [
                'keywords' => ['undang', 'staff', 'user', 'tambah', 'team'],
                'question' => 'Cara undang staff?',
                'answer' => "Fitur undang staff sedang dikembangkan. Sementara ini, Anda bisa:\n1. Minta staff register sendiri\n2. Hubungi admin untuk attach ke company Anda\n\nSegera hadir: fitur undang via email langsung dari aplikasi.",
                'category' => 'user',
            ],
            [
                'keywords' => ['kas', 'bank', 'saldo', 'lihat'],
                'question' => 'Cara lihat saldo kas & bank?',
                'answer' => "Saldo kas & bank terlihat di Dashboard. Untuk detail:\n1. Buka COA\n2. Cari akun dengan label 'Kas' atau 'Bank'\n3. Lihat kolom Saldo\n\nAtau buka Buku Besar untuk melihat mutasi detail.",
                'action_url' => '/dashboard',
                'action_label' => 'Ke Dashboard',
                'category' => 'kas',
            ],
            [
                'keywords' => ['jurnal', 'draft', 'edit', 'hapus', 'ubah'],
                'question' => 'Cara edit jurnal yang belum diposting?',
                'answer' => "1. Buka menu Jurnal\n2. Cari jurnal dengan status 'draft'\n3. Klik Edit\n4. Ubah data\n5. Klik Update\n\nJurnal yang sudah diposting (status 'posted') tidak bisa diedit.",
                'action_url' => '/journals',
                'action_label' => 'Lihat Jurnal',
                'category' => 'jurnal',
            ],
            [
                'keywords' => ['lupa', 'password', 'reset', 'ganti'],
                'question' => 'Lupa password, bagaimana?',
                'answer' => "1. Di halaman login, klik 'Lupa password?'\n2. Masukkan email Anda\n3. Cek email untuk link reset password\n4. Klik link, isi password baru\n\nKalau tidak menerima email, hubungi admin.",
                'category' => 'auth',
            ],
        ];

        foreach ($items as $i => $item) {
            AiKnowledge::updateOrCreate(
                ['question' => $item['question']],
                array_merge($item, ['sort_order' => $i])
            );
        }

        $this->command->info('✅ ' . count($items) . ' knowledge base dibuat.');
    }
}