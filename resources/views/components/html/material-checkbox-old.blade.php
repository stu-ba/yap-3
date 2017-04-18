<div class="checkbox">
    <label>
        <noscript>
            <input type="checkbox" name="{{ $field }}" value="{{ $value ?? 'true' }}"
                   @if(old($field))
                   checked
                    @endif
            >
            <span class="checkbox-material"><span class="check"></span></span>
        </noscript>
        <input type="checkbox" name="{{ $field }}" value="{{ $value ?? 'true' }}"
               @if(old($field))
               checked
                @endif
        >
        {{ $label }}
    </label>
</div>