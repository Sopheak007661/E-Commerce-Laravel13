<x-app-layout>
    <div class="mt-2 shadow-lg   transition-all duration-300 z-50">
            <form id="form-filter" method="GET" action="{{route('shop.index')}}" >
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <form>
                            <input type="text" name="search" value="{{request('search')}}" placeholder="Search product..." autofocus autocomplete="search" class="w-full flex border p-3 rounded focus:outline-none focus:border-primary pr-10"> 
                        </form>
                    </div>
            </form>
    </div>
</x-app-layout>