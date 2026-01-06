<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Új könyv hozzáadása') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('writers.books.store', $authorId) }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Cím:</label>
                                <input type="text" name="title" class="border rounded w-full py-2 px-3" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">IBAN:</label>
                                <input type="text" name="iban" class="border rounded w-full py-2 px-3" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Ár (Ft):</label>
                                <input type="number" step="0.01" name="price" class="border rounded w-full py-2 px-3" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Zsáner:</label>
                                <input type="text" name="genre" class="border rounded w-full py-2 px-3">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Leírás:</label>
                            <textarea name="description" rows="3" class="border rounded w-full py-2 px-3"></textarea>
                        </div>

                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Mentés
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>