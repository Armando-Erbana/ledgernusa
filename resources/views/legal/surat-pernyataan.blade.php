<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Kerahasiaan Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 p-6 md:p-12">
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-2xl shadow-sm border">
        <h1 class="text-2xl font-bold text-center mb-8">SURAT PERNYATAAN KERAHASIAAN DATA PENGGUNA</h1>
        
        <div class="space-y-4 leading-relaxed text-justify">
            <p>Kami yang bertanda tangan di bawah ini, pengelola platform <b>LedgerNusa</b>, dengan ini menyatakan bahwa:</p>
            <ol class="list-decimal pl-5 space-y-3">
                <li>Seluruh data yang diinput oleh pengguna (perusahaan maupun perorangan) ke dalam sistem LedgerNusa adalah <b>milik pengguna sepenuhnya</b>.</li>
                <li>Kami, <b>Mangata Nusantara Teknologi</b>, <b>tidak akan mengakses, membaca, menyalin, atau menggunakan</b> data pengguna untuk kepentingan apa pun di luar penyediaan layanan, kecuali diperintahkan oleh peraturan perundang-undangan yang berlaku atau mendapat persetujuan tertulis dari pengguna.</li>
                <li>Kami <b>tidak akan menjual, menyewakan, atau membagikan</b> data pengguna kepada pihak ketiga mana pun.</li>
                <li>Dalam hal terjadi kebocoran data, kami akan memberitahu pengguna dalam waktu maksimal 3x24 jam dan melaporkannya ke otoritas terkait sesuai UU PDP.</li>
            </ol>
            <br>
            <p>Pernyataan ini berlaku sejak tanggal pendaftaran pengguna dan selama data pengguna tersimpan di sistem LedgerNusa.</p>
            
            <div class="mt-12 text-right">
                <p>Hormat kami,</p>
                <br><br><br>
                <p class="font-bold">Mangata Nusantara Teknologi</p>
            </div>
        </div>
        <div class="mt-10 pt-6 border-t">
            <a href="{{ url()->previous() }}" class="text-blue-600 font-semibold hover:underline">&larr; Kembali</a>
        </div>
    </div>
</body>
</html>