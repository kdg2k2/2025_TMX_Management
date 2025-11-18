<x-modal id="modal-logout" title="Xác nhận" :action="route('logout')" method="POST" size="md">
    <x-slot:body>
        <p>Bạn có chắc chắn muốn đăng xuất?</p>
    </x-slot:body>
    <x-slot:footer>
        <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
        <x-button-submit />
    </x-slot:footer>
</x-modal>

<x-modal id="modal-delete" title="Xóa dữ liệu" size="md" method="delete" nested="true">
    <x-slot:body>
        <p>Bạn có chắc chắn muốn xóa bản ghi này?</p>
    </x-slot:body>
    <x-slot:footer>
        <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
        <x-button-submit variant="danger" />
    </x-slot:footer>
</x-modal>
