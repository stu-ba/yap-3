@if(($type === 'participants' || $type === 'leaders') && $project->{$type}->count() > 0)
    @php
        $text = '';
    @endphp
    @foreach($project->{$type} as $member)
        @if($loop->count > 1)
            @if($loop->last)
                @php
                    $text .= $member->username
                @endphp
                @continue
            @endif
            @php
                $text .= $member->username . ', '
            @endphp
        @else
            {{ $member->username }}
        @endif
    @endforeach
    @if(isset($text) && $text != '')
        <span rel="tooltip" data-html="true" class="hover-tip" data-placement="top" title="{{ $text }}">{{ str_limit($text,
                10) }}<sup class="fa font-size-half text-muted fa-asterisk"></sup></span>
        @php
            unset($text);
        @endphp
    @endif
@else
    {{ config('yap.placeholders.no_one') }}
@endif
