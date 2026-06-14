@php
    $userRole = strtolower(auth()->user()->role);
    $isChapterView = ($userRole === 'chapter' || $userRole === 'reseller' || (in_array($userRole, ['administrator', 'operasional']) && request('view_type') === 'chapter'));
    $isAdminCSView = ($userRole === 'administrator' && request('view_type') !== 'chapter');
    $isCSMBCView = ($userRole === 'cs-mbc');
@endphp

@if($isChapterView)
    @include('admin.database.partials.row_chapter', ['item' => $item, 'loop' => $loop, 'kelas' => $kelas])
@elseif($isAdminCSView)
    @include('admin.database.partials.row_cs', ['item' => $item, 'loop' => $loop, 'kelas' => $kelas])
@elseif($isCSMBCView)
    @include('admin.database.partials.row_mbc', ['item' => $item, 'loop' => $loop, 'kelas' => $kelas])
@else
    {{-- Fallback for marketing or other roles --}}
    @include('admin.database.partials.row_mbc', ['item' => $item, 'loop' => $loop, 'kelas' => $kelas])
@endif