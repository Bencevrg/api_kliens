<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $writer ? $writer['name'] . ' könyvei' : 'Könyvek listája' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Vissza gomb --}}
                    <div class="mb-4">
                        <a href="{{ route('writers.index') }}" class="text-blue-500 hover:underline">← Vissza az írókhoz</a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($isAuthenticated)
                        <div class="mb-4">
                            <a href="{{ route('writers.books.create', $authorId) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                + Új könyv hozzáadása
                            </a>
                        </div>
                    @endif

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cím</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IBAN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ár</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Műfaj</th>
                                @if($isAuthenticated)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Műveletek</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($books as $book)
                                <tr>
                                    <td class="px-6 py-4">{{ $book['title'] }}</td>
                                    <td class="px-6 py-4">{{ $book['iban'] }}</td>
                                    <td class="px-6 py-4">{{ $book['price'] }} Ft</td>
                                    <td class="px-6 py-4">{{ $book['genre'] ?? '-' }}</td>
                                    @if($isAuthenticated)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('writers.books.edit', [$authorId, $book['id']]) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Szerk.</a>
                                        
                                        <form action="{{ route('writers.books.destroy', [$authorId, $book['id']]) }}" method="POST" class="inline-block" onsubmit="return confirm('Törlöd a könyvet?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Törlés</button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">Ennek az írónak nincsenek könyvei rögzítve.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>