<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Írók listája') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Export Gombok --}}
                    <div class="flex justify-end gap-2 mb-4">
                        <a href="{{ route('writers.export', 'csv') }}" 
                           class="text-white font-bold py-2 px-4 rounded text-sm"
                           style="background-color: #6b7280; display: inline-block; text-decoration: none;">
                            CSV Export
                        </a>
                        
                        <a href="{{ route('writers.export', 'pdf') }}" 
                           class="text-white font-bold py-2 px-4 rounded text-sm"
                           style="background-color: #ef4444; display: inline-block; text-decoration: none;">
                            PDF Export
                        </a>
                    </div>

                    {{-- Üzenetek megjelenítése --}}
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- Új hozzáadása gomb - JAVÍTOTT STÍLUS --}}
                    @if(session('api_token'))
                        <div class="mb-4">
                            <a href="{{ route('writers.create') }}" 
                               class="text-white font-bold py-2 px-4 rounded"
                               style="background-color: #3b82f6; /* Kék */ display: inline-block; text-decoration: none; color: white;">
                                + Új író felvétele
                            </a>
                        </div>
                    @endif

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Név</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Műveletek</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($writers as $writer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $writer['id'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $writer['name'] }}</td>
                                    <td class="px-6 py-4">{{ Str::limit($writer['bio'] ?? '', 50) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('writers.books.index', $writer['id']) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Könyvei</a>

                                        @if(session('api_token'))
                                            <a href="{{ route('writers.edit', $writer['id']) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Szerkesztés</a>
                                            
                                            <form action="{{ route('writers.destroy', $writer['id']) }}" method="POST" class="inline-block" onsubmit="return confirm('Biztosan törölni szeretnéd?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Törlés</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center">Nincsenek írók rögzítve.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>