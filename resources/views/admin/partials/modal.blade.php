<x-modal id="modalLogout" title="Xác nhận" :action="route('logout')" method="POST" size="md">

    <x-slot:body>
        <p>Bạn có chắc chắn muốn đăng xuất?</p>
    </x-slot:body>

    <x-slot:footer>
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-primary">Thực hiện</button>
    </x-slot:footer>
</x-modal>

<x-modal id="modalDelete" title="Xóa dữ liệu" size="md">

    <x-slot:body>
        <p>Bạn có chắc chắn muốn xóa bản ghi này?</p>
    </x-slot:body>

    <x-slot:footer>
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-danger">Xóa</button>
    </x-slot:footer>
</x-modal>
