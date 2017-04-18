<div class="checkbox">
    <label>
        <input type="checkbox" name="{{ $field }}" value="{{ $value ?? 'true' }}"
               @if(old($field))
               checked
                @endif
        >
        {{ $label }}
    </label>
</div>