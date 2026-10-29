<div class="g-toolbar">
    <h1 class="g-headline mb-0">{{ $title }}</h1>
    @isset($createRoute)
        <a href="{{ $createRoute }}" class="btn btn-primary btn-sm">Tambah</a>
    @endisset
</div>
