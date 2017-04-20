<noscript>
    <div class="alert alert-warning alert-with-icon" data-notify="container">
        <i data-notify="icon" class="fa fa-2x fa-exclamation-triangle"></i>
        <span data-notify="message">Turn on <strong>JavaScript</strong> for full experience. You can read <a href="#" class="alert-link">more</a> about usage of JavaScript in <a href="{{route('docs')}}" class="alert-link">user guide</a>.</span>
    </div>
    @if(Alert::any())
        @foreach (Alert::getMessages() as $type => $messages)
            @foreach ($messages as $message)
                <div class="alert alert-{{ $type }} alert-with-icon" data-notify="container">
                    <i data-notify="icon" class="fa fa-2x fa-{{ $type }}"></i>
                    <span data-notify="message">{!! $message !!}</span>
                </div>
            @endforeach
        @endforeach
    @endif
</noscript>
@if(Alert::any())
    @push('alerts')
        <script>
            @foreach (Alert::getMessages() as $type => $messages)
                @foreach ($messages as $message)
                    $.notify({
                    message: "{!! $message !!}"
                    }, {
                        type: '{{ $type }}'
                    });
                @endforeach
            @endforeach
        </script>
    @endpush
@endif
