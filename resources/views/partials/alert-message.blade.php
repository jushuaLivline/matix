@foreach (['success', 'primary', 'info', 'warning', 'danger', 'error'] as $alert)
    @if(session($alert))
        <div class="alert alert-{{ $alert }} alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ __('messages.' . session($alert)) }}
        </div>
    @endif
@endforeach
