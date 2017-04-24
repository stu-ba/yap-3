<div class="text-center">
    <h3>
        @empty($message)
            There are no records <i class="fa fa-frown-o "></i>, try later.
        @else
                {{ $message }}
        @endempty
    </h3>
</div>
