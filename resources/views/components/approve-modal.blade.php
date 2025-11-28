@props([
    'id' => null,
    'title' => 'Thực hiện',
    'size' => 'sm',
    'method' => null,
    'noteName' => null,
    'buttonVariant' => 'primary',
])

<x-modal :id="$id" :title="$title" :size="$size" :method="$method" nested="true">
    <x-slot:body>
        <div class="form-group">
            <label>Nhận xét</label>
            <input type="text" class="form-control" name="{{ $noteName }}" required>
        </div>
    </x-slot:body>
    <x-slot:footer>
        <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
        <x-button-submit :variant="$buttonVariant" />
    </x-slot:footer>
</x-modal>
