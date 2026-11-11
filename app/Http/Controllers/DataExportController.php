<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataExportController extends Controller
{
    public function exportJournals()
    {
        $companyId = session('company_id');
        $journals = Journal::with('entries.account')
            ->where('company_id', $companyId)
            ->orderBy('date', 'asc')
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=jurnal_ledgernusa_" . date('Ymd') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Tanggal', 'No Referensi', 'Deskripsi', 'Akun', 'Debit', 'Kredit'];

        $callback = function() use($journals, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($journals as $journal) {
                foreach ($journal->entries as $entry) {
                    fputcsv($file, [
                        $journal->date->format('Y-m-d'),
                        $journal->reference,
                        $journal->description,
                        $entry->account->code . ' - ' . $entry->account->name,
                        $entry->debit,
                        $entry->credit,
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}