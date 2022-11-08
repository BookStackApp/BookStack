<div class="flex-container-row justify-space-between items-center gap-m item-list-row">
    <label for="shortcut-{{ $label }}" class="bold flex px-m py-xs">{{ $label }}</label>
    <div class="px-m py-xs">
        <input type="text"
               class="small flex-none shortcut-input px-s py-xs"
               id="shortcut-{{ $id }}"
               name="shortcut[{{ $id }}]"
               readonly
               value="{{ $shortcuts->getShortcut($id) }}">
    </div>
</div>