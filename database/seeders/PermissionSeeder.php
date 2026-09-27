<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['code' => 'coa.view','name' => 'Lihat COA','group' => 'COA'],
            ['code' => 'coa.manage','name' => 'Kelola COA','group' => 'COA'],
            ['code' => 'journal.view','name' => 'Lihat Jurnal','group' => 'Jurnal'],
            ['code' => 'journal.create','name' => 'Buat Jurnal','group' => 'Jurnal'],
            ['code' => 'journal.post','name' => 'Posting Jurnal','group' => 'Jurnal'],
            ['code' => 'cash.view','name' => 'Lihat Kas & Bank','group' => 'Kas & Bank'],
            ['code' => 'cash.manage','name' => 'Kelola Kas & Bank','group' => 'Kas & Bank'],
            ['code' => 'report.view','name' => 'Lihat Laporan','group' => 'Laporan'],
            ['code' => 'contact.view','name' => 'Lihat Kontak','group' => 'Kontak'],
            ['code' => 'contact.manage','name' => 'Kelola Kontak','group' => 'Kontak'],
            ['code' => 'user.view','name' => 'Lihat User','group' => 'User'],
            ['code' => 'user.manage','name' => 'Kelola User','group' => 'User'],
            ['code' => 'setting.manage','name' => 'Kelola Setting','group' => 'Setting'],
        ];
        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(['code' => $p['code']], [
                'name' => $p['name'], 'group' => $p['group'],
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}