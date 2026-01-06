<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class WriterController extends Controller
{
    /**
     * Írók listázása
     */
    public function index()
    {
        try {
            $response = Http::api()->get('/writers');

            if ($response->failed()) {
                return view('writers.index', [
                    'writers' => [],
                    'error' => 'Nem sikerült betölteni az írókat.'
                ]);
            }

            $data = $response->json();
            $writers = $data['writers'] ?? [];

            return view('writers.index', [
                'writers' => $writers,
                'isAuthenticated' => $this->isAuthenticated()
            ]);

        } catch (\Exception $e) {
            return view('writers.index', [
                'writers' => [],
                'error' => 'Hiba történt: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Exportálás választó
     */
    public function export($type)
    {
        try {
            $response = Http::api()->get('/writers');
            
            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült lekérni az adatokat az exporthoz.');
            }

            $data = $response->json();
            $writers = $data['writers'] ?? [];

            if (empty($writers)) {
                return back()->with('error', 'Nincs exportálható adat.');
            }

            if ($type === 'csv') {
                return $this->exportCsv($writers);
            } elseif ($type === 'pdf') {
                return $this->exportPdf($writers);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba az exportálás során: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * JAVÍTOTT CSV Export (CP1250 kódolással)
     */
    private function exportCsv($writers)
    {
        // 1. Tartalom összeállítása
        $csvContent = "sep=;\n";
        $csvContent .= "ID;Név;Bio\n";

        foreach ($writers as $writer) {
            $id = $writer['id'];
            $name = $this->cleanCsvField($writer['name']);
            $bio = $this->cleanCsvField($writer['bio'] ?? '');

            $csvContent .= "{$id};{$name};{$bio}\n";
        }

        // 2. Konvertálás UTF-8-ról CP1250-re (Windows Central European)
        // Ha a CP1250 sem működne, akkor ISO-8859-2 -t próbálnánk, de ez általában jó.
        // Ellenőrizzük, hogy létezik-e a mb_convert_encoding, ha nem, marad az UTF-8 BOM-mal.
        if (function_exists('mb_convert_encoding')) {
             // Próbáljuk a szabványos CP1250-et
             try {
                $csvContentEncoded = mb_convert_encoding($csvContent, 'CP1250', 'UTF-8');
             } catch (\Throwable $e) {
                // Ha a CP1250 nem ismert, próbáljuk meg UTF-16LE-vel (Excel szereti)
                $csvContentEncoded = chr(255) . chr(254) . mb_convert_encoding($csvContent, 'UTF-16LE', 'UTF-8');
             }
        } else {
            $csvContentEncoded = $csvContent;
        }

        // 3. Válasz küldése
        return response($csvContentEncoded)
                ->header('Content-Type', 'text/csv; charset=windows-1250')
                ->header('Content-Disposition', 'attachment; filename="irok_lista.csv"');
    }

    /**
     * Segédfüggvény a CSV mezők tisztítására
     */
    private function cleanCsvField($field)
    {
        // Sortörések cseréje szóközre
        $field = str_replace(["\r", "\n"], ' ', $field);
        
        // Ha a mező tartalmaz pontosvesszőt vagy idézőjelet, akkor idézőjelbe kell tenni
        if (preg_match('/[;";]/', $field)) {
            $field = '"' . str_replace('"', '""', $field) . '"';
        }
        
        return $field;
    }

    private function exportPdf($writers)
    {
        $pdf = Pdf::loadView('writers.pdf', ['writers' => $writers]);
        return $pdf->download('irok_lista.pdf');
    }

    // --- CRUD Műveletek ---

    public function create()
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login');
        }
        return view('writers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
        ]);

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('/writers', $validated);

            if ($response->successful()) {
                return redirect()->route('writers.index')->with('success', 'Író sikeresen létrehozva!');
            }
            return back()->with('error', 'Hiba történt a mentéskor: ' . $response->body());
        } catch (\Exception $e) {
            return back()->with('error', 'Kommunikációs hiba: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login');
        }

        $response = Http::api()->get('/writers');
        
        if ($response->failed()) {
             return redirect()->route('writers.index')->with('error', 'Nem sikerült betölteni az adatokat.');
        }

        $data = $response->json();
        $writers = $data['writers'] ?? [];
        $writer = collect($writers)->firstWhere('id', $id);

        if (!$writer) {
            return redirect()->route('writers.index')->with('error', 'Az író nem található.');
        }

        return view('writers.edit', ['writer' => $writer]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
        ]);

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->patch("/writers/{$id}", $validated);

            if ($response->successful()) {
                return redirect()->route('writers.index')->with('success', 'Író sikeresen frissítve!');
            }
            return back()->with('error', 'Hiba a frissítés során: ' . $response->body());
        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->delete("/writers/{$id}");

            if ($response->successful()) {
                return redirect()->route('writers.index')->with('success', 'Író sikeresen törölve!');
            }
            return back()->with('error', 'Nem sikerült törölni az írót.');
        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }
}