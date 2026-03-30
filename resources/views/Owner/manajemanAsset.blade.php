@extends('layouts.Owner')

@section('content')
<div class="p-2 sm:p-5 flex flex-col h-full bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ mode: '{{ $mode }}' }">
    
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Manajemen Aset</h1>
                <p class="text-xs text-gray-400 font-medium italic">Kelola kurikulum dan sumber daya kursus Smartin</p>
            </div>
            
            <div class="flex bg-gray-100 p-1 rounded-xl">
                <a href="{{ route('owner.manajemenAsset', ['mode' => 'mapel']) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $mode == 'mapel' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    Mata Pelajaran
                </a>
                <a href="{{ route('owner.manajemenAsset', ['mode' => 'paket']) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $mode == 'paket' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    Paket Bundling
                </a>
                <a href="{{ route('owner.manajemenAsset', ['mode' => 'mentors']) }}" 
                   class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $mode == 'mentors' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    Mentor
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('owner.manajemenAsset') }}" class="flex flex-wrap gap-3 p-4 bg-gray-50/50 rounded-2xl border border-gray-100">
            <input type="hidden" name="mode" value="{{ $mode }}">

            @if($mode == 'mapel')
            <select name="category_id" onchange="this.form.submit()" class="text-xs border border-gray-200 bg-white rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                @endforeach
            </select>
            @endif

            @if($mode == 'mentors')
            <select name="filter_subject_id" onchange="this.form.submit()" class="text-xs border border-gray-200 bg-white rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                <option value="">Semua Mapel</option>
                @foreach($allSubjects as $sub)
                    <option value="{{ $sub->id }}" {{ request('filter_subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->mapel_name }}</option>
                @endforeach
            </select>
            @endif

            <div class="relative flex-1 min-w-[200px]">
                @if($mode == 'mapel')
                    <input type="text" name="q_mapel" value="{{ request('q_mapel') }}" placeholder="Cari mata pelajaran..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                @elseif($mode == 'paket')
                    <input type="text" name="q_bundling" value="{{ request('q_bundling') }}" placeholder="Cari bundling..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                @elseif($mode == 'mentors')
                    <input type="text" name="q_mentor" value="{{ request('q_mentor') }}" placeholder="Cari nama mentor atau mapel..." class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                @endif
                <x-akar-search class="absolute left-3 top-2.5 w-4 text-gray-400"/>
                <button type="submit" class="hidden"></button>
            </div>

            @if(request('category_id') || request('q_mapel') || request('q_bundling') || request('q_mentor') || request('filter_subject_id'))
                <a href="{{ route('owner.manajemenAsset', ['mode' => $mode]) }}" class="inline-flex items-center px-3 py-2 text-xs text-red-500 hover:text-red-700 transition-all font-medium">
                    <x-akar-circle-x class="w-4 h-4 mr-1" /> Reset
                </a>
            @endif
            
            {{-- <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-all">
                + Tambah Baru
            </button> --}}
        </form>
    </div>

    <div class="flex-1 overflow-y-auto custom-scroll pr-1 min-h-[400px]">
        
        @if($mode == 'mapel')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($subjects as $subject)
                <div class="p-4 border border-gray-100 bg-white rounded-2xl flex flex-col hover:border-blue-200 hover:shadow-md transition-all group">
                    <div class="flex justify-between items-start mb-3">
                        <div class="text-blue-500 bg-blue-50 p-2 rounded-xl group-hover:bg-blue-500 group-hover:text-white transition-all">
                            <x-akar-book class="w-5"/>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-900">Rp{{ number_format($subject->monthly_price,0,',','.') }}</p>
                            <span class="text-[9px] text-gray-400 uppercase tracking-tighter">per bulan</span>
                        </div>
                    </div>
                    <h4 class="text-[13px] font-semibold text-gray-800 mb-1 leading-tight">{{ $subject->mapel_name }}</h4>
                    <p class="text-[10px] text-gray-400 mb-4 line-clamp-2 italic">{{ $subject->categories->category_name ?? 'Tanpa Kategori' }}</p>
                </div>
                @empty
                    @include('Owner.partials.empty_state')
                @endforelse
            </div>

        @elseif($mode == 'paket')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @forelse($bundlings as $bundle)
                <div class="p-5 border border-gray-100 bg-white rounded-2xl flex flex-col md:flex-row gap-5 hover:border-emerald-200 transition-all">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="text-emerald-500 bg-emerald-50 p-2 rounded-xl"><x-eos-packages-o class="w-5"/></div>
                            <h4 class="text-sm font-bold text-gray-800">{{ $bundle->bundling_name }}</h4>
                        </div>
                        <p class="text-xs text-gray-400 mb-4 line-clamp-2">{{ $bundle->description }}</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($bundle->details as $detail)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded text-[9px] font-medium border border-gray-200">{{ $detail->subject->mapel_name ?? '-' }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="md:w-40 flex flex-col justify-between items-end border-l border-gray-50 pl-5">
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 line-through">Rp{{ number_format($bundle->details->sum(fn($d) => $d->subject->monthly_price ?? 0),0,',','.') }}</p>
                            <p class="text-sm font-black text-emerald-600 leading-none">Rp{{ number_format($bundle->bundling_price,0,',','.') }}</p>
                        </div>
                        {{-- <button class="w-full mt-4 py-2 bg-gray-900 text-white rounded-xl text-[10px] font-bold uppercase tracking-wider hover:bg-blue-600 transition-all">Detail Paket</button> --}}
                    </div>
                </div>
                @empty
                    @include('Owner.partials.empty_state')
                @endforelse
            </div>

        @elseif($mode == 'mentors')
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($mentors as $mentor)
                <div class="p-4 bg-white border border-gray-100 rounded-2xl hover:border-blue-100 transition-all flex items-center gap-4 group">
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 font-bold group-hover:bg-blue-500 group-hover:text-white transition-all shadow-sm">
                        {{ substr($mentor->mentor_name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-bold text-gray-800 leading-none mb-1">{{ $mentor->mentor_name }}</h4>
                        <p class="text-[10px] text-gray-400">{{ $mentor->phone_number }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 {{ $mentor->is_active ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-gray-50 text-gray-400 border-gray-100' }} border rounded-full text-[9px] font-bold">
                            {{ $mentor->is_active ? 'Online' : 'Offline' }}
                        </span>
                        <p class="text-[10px] text-blue-500 font-bold mt-1">{{ $mentor->schedules_count }} Kelas</p>
                    </div>
                </div>
                @empty
                    @include('Owner.partials.empty_state')
                @endforelse
            </div>
        @endif

    </div>
</div>

<style>
    .custom-scroll::-webkit-scrollbar { width: 4px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #D1D5DB; }
</style>
@endsection