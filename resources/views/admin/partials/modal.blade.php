<div class="modal fade" id="modalLogout" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalLogoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="{{ route('api.auth.logout') }}">
            <div class="modal-header">
                <h6 class="modal-title" id="modalLogoutLabel">
                    Xác nhận
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Chắc chắn đăng xuất?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary">Thực hiện</button>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="modalDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modalDeleteLabel">
                    Xác nhận
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Chắc chắn xóa?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary">Thực hiện</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const modalLogout = document.getElementById('modalLogout');
        const modalDelete = document.getElementById('modalDelete');

        modalLogout.addEventListener('show.bs.modal', (e) => {
            const form = modalLogout.querySelector('form');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const res = await http.post(form.getAttribute('action'));
                if (res.message)
                    window.location.href = '/';
            });
        });

        modalDelete.addEventListener('show.bs.modal', (e) => {
            const modal = $(this);
            const form = modalDelete.querySelector('form');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const res = await http.delete(form.getAttribute('action'));
                if (res.message) {
                    modal.modal("hide");
                    if (onSuccessFnName && typeof window[onSuccessFnName] === 'function')
                        window[onSuccessFnName]();
                    if (!res.message) {
                        location.reload();
                    }
                }
            });
        });
    });
</script>
