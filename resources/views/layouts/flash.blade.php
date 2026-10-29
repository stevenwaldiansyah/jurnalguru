@if(session('success'))
    <div class="g-alert g-alert--success alert alert-success alert-dismissible fade show border-0" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="g-alert g-alert--error alert alert-danger alert-dismissible fade show border-0" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('info'))
    <div class="g-alert g-alert--info alert alert-info alert-dismissible fade show border-0" role="alert">{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(isset($errors) && $errors->any())
    <div class="g-alert g-alert--error" role="alert"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
