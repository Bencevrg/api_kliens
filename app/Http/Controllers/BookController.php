<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    /**
     * Egy adott író könyveinek listázása
     * URL: /writers/{authorId}/books
     */
    public function index($authorId)
    {
        try {
            // 1. Lekérjük a könyveket az íróhoz
            $response = Http::api()->get("/writers/{$authorId}/books");
            
            // 2. Lekérjük az író adatait is (hogy kiírhassuk a nevét a fejlécben)
            $writersResponse = Http::api()->get('/writers');
            
            // JAVÍTÁS: Kicsomagoljuk a 'writers' kulcs alól az adatokat
            $writersData = $writersResponse->json();
            $writersList = $writersData['writers'] ?? [];
            
            // Megkeressük az aktuális írót
            $writer = collect($writersList)->firstWhere('id', $authorId);

            if ($response->failed()) {
                return back()->with('error', 'Nem sikerült betölteni a könyveket.');
            }

            // JAVÍTÁS: Kicsomagoljuk a 'books' kulcs alól az adatokat
            $data = $response->json();
            $books = $data['books'] ?? [];

            return view('books.index', [
                'books' => $books,
                'writer' => $writer,
                'authorId' => $authorId,
                'isAuthenticated' => $this->isAuthenticated()
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }

    public function create($authorId)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login');
        }
        return view('books.create', ['authorId' => $authorId]);
    }

    public function store(Request $request, $authorId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'iban' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
        ]);

        $validated['author_id'] = $authorId;

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post("/writers/{$authorId}/books", $validated);

            if ($response->successful()) {
                return redirect()->route('writers.books.index', $authorId)
                    ->with('success', 'Könyv sikeresen hozzáadva!');
            }

            return back()->with('error', 'Hiba: ' . $response->body());

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }

    public function edit($authorId, $bookId)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login');
        }

        // Könyv adatainak lekérése a szerkesztéshez
        $response = Http::api()->get("/writers/{$authorId}/books");
        
        if ($response->failed()) {
             return back()->with('error', 'Nem sikerült betölteni a könyveket.');
        }

        // JAVÍTÁS: Itt is kicsomagoljuk a 'books' kulcsot
        $data = $response->json();
        $books = $data['books'] ?? [];
        
        $book = collect($books)->firstWhere('id', $bookId);

        if (!$book) {
            return back()->with('error', 'A könyv nem található.');
        }

        return view('books.edit', [
            'book' => $book,
            'authorId' => $authorId
        ]);
    }

    public function update(Request $request, $authorId, $bookId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'iban' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
        ]);

        try {
            // Az API útvonalat igazítjuk a backendhez.
            // A backend BookController update metódusa most már kezeli a ($id, $bookId) sorrendet.
            $response = Http::api()
                ->withToken($this->token)
                ->patch("/writers/{$authorId}/books/{$bookId}", $validated);

            if ($response->successful()) {
                return redirect()->route('writers.books.index', $authorId)
                    ->with('success', 'Könyv sikeresen frissítve!');
            }

            return back()->with('error', 'Hiba: ' . $response->body());

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }

    public function destroy($authorId, $bookId)
    {
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->delete("/writers/{$authorId}/books/{$bookId}");

            if ($response->successful()) {
                return redirect()->route('writers.books.index', $authorId)
                    ->with('success', 'Könyv törölve!');
            }

            return back()->with('error', 'Nem sikerült törölni a könyvet.');

        } catch (\Exception $e) {
            return back()->with('error', 'Hiba: ' . $e->getMessage());
        }
    }
}