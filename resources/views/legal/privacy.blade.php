<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kebijakan Privasi — LedgerNusa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 p-6 md:p-12">
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-2xl shadow-sm border">
        <h1 class="text-3xl font-bold mb-6">Kebijakan Privasi</h1>
        <p class="mb-4 text-sm text-gray-500">Terakhir diperbarui: {{ date('d F Y') }}</p>

        <div class="space-y-4 leading-relaxed">
            <p>Kebijakan Privasi ini menjelaskan bagaimana <b>Mangata Nusantara Teknologi</b> ("Kami") mengumpulkan, menggunakan, dan melindungi data Anda dalam penggunaan aplikasi LedgerNusa.</p>
            
            <h2 class="text-xl font-bold mt-6">1. Data yang Kami Kumpulkan</h2>
            <p>Kami mengumpulkan data identitas (nama, email), data perusahaan (NPWP, alamat), dan seluruh data transaksi keuangan yang Anda input ke dalam sistem.</p>

            <h2 class="text-xl font-bold mt-6">2. Penggunaan Data</h2>
            <p>Data Anda sepenuhnya milik Anda. Kami <b>tidak akan pernah</b> mengakses, membaca, menjual, atau membagikan data keuangan perusahaan Anda kepada pihak ketiga, kecuali diwajibkan oleh hukum yang berlaku di Indonesia.</p>

            <h2 class="text-xl font-bold mt-6">3. Penyimpanan Data</h2>
            <p>Data keuangan Anda disimpan secara aman. Sesuai dengan kesepakatan, jika masa langganan berakhir, data Anda <b>tidak akan dihapus secara otomatis</b>, melainkan dibekukan (read-only). Anda dapat mengunduh data Anda kapan saja.</p>

            <h2 class="text-xl font-bold mt-6">4. Hak Anda (UU PDP)</h2>
            <p>Anda berhak meminta salinan data (ekspor), memperbaiki data, dan meminta penghapusan data secara permanen dengan menghubungi tim support kami.</p>
        </div>
        <div class="mt-10 pt-6 border-t">
            <a href="{{ url()->previous() }}" class="text-blue-600 font-semibold hover:underline">&larr; Kembali</a>
        </div>
    </div>
</body>
</html>