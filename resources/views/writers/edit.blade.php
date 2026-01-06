<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Író szerkesztése') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('writers.update', $writer['id']) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        {{-- Név mező --}}
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Név:</label>
                            <input type="text" name="name" id="name" 
                                   value="{{ $writer['name'] }}" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        {{-- Bio mező --}}
                        <div class="mb-4">
                            <label for="bio" class="block text-gray-700 text-sm font-bold mb-2">Életrajz (Bio):</label>
                            <textarea name="bio" id="bio" rows="5" 
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ $writer['bio'] ?? '' }}</textarea>
                        </div>

                        {{-- Gombok --}}
                        <div class="flex items-center justify-between">
                            {{-- JAVÍTÁS: Inline style hozzáadva a háttérszínhez --}}
                            <button type="submit" 
                                    class="text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                    style="background-color: #3b82f6; /* Kék szín */ color: white;">
                                Mentés
                            </button>
                            
                            <a href="{{ route('writers.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                Mégse
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>