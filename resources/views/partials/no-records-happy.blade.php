<div class="text-center">
    <h3>
        @empty($message)
            There are no records <i class="fa fa-smile-o"></i>.
        @else
            {!! $message !!}
        @endempty
    </h3>
</div>
