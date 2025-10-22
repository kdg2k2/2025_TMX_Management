var contractData = [];
var selectedItems = [];
var currentPage = 1;
var selectedPage = 1;
var perPage = 10;
var searchTerm = '';
var selectedSearchTerm = '';

const loadContractData = async (page = 1, search = '') => {
    const res = await http.get(listContractUrl, {
        paginate: 1,
        page: page,
        per_page: perPage,
        search: search,
    });

    if (res.data) {
        contractData = res.data;
        renderOriginalList();
        createPagination(res.data, 'original', changePage);
    }
};

const renderOriginalList = () => {
    const list = document.getElementById('original-list-contractor-experience');
    list.innerHTML = '';

    if (contractData.data && contractData.data.length > 0) {
        contractData.data.forEach(item => {
            const isChecked = selectedItems.some(selected => selected.id === item.id);
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.innerHTML = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           value="${item.id}"
                           id="check-${item.id}"
                           ${isChecked ? 'checked' : ''}>
                    <label class="form-check-label" for="check-${item.id}">
                        ${item.name}
                    </label>
                </div>
            `;

            // Add event listener
            const checkbox = li.querySelector(`#check-${item.id}`);
            checkbox.addEventListener('change', (e) => {
                handleCheckbox(item.id, e.target.checked);
            });

            list.appendChild(li);
        });
    } else {
        list.innerHTML = '<li class="list-group-item text-center text-muted">Không có dữ liệu</li>';
    }
};

const renderSelectedList = () => {
    const list = document.getElementById('selected-list-contractor-experience');
    list.innerHTML = '';

    const start = (selectedPage - 1) * perPage;
    const end = start + perPage;
    const filteredItems = selectedItems.filter(item =>
        item.name.toLowerCase().includes(selectedSearchTerm.toLowerCase())
    );
    const paginatedItems = filteredItems.slice(start, end);

    if (paginatedItems.length > 0) {
        paginatedItems.forEach(item => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';

            const span = document.createElement('span');
            span.textContent = item.name;
            li.appendChild(span);

            const deleteBtn = createBtn(
                'danger',
                'Xóa',
                false,
                {},
                'ri-close-line',
                null
            );
            deleteBtn.addEventListener('click', () => removeItem(item.id));

            li.appendChild(deleteBtn);
            list.appendChild(li);
        });
    } else {
        list.innerHTML = '<li class="list-group-item text-center text-muted">Chưa có mục nào được chọn</li>';
    }

    createPagination({
        current_page: selectedPage,
        last_page: Math.ceil(filteredItems.length / perPage),
        total: filteredItems.length
    }, 'selected', changeSelectedPage);
};

const changePage = (page) => {
    currentPage = page;
    loadContractData(page, searchTerm);
};

const changeSelectedPage = (page) => {
    selectedPage = page;
    renderSelectedList();
};

const handleCheckbox = (id, checked) => {
    if (checked) {
        const item = contractData.data.find(item => item.id === id);
        if (item && !selectedItems.some(selected => selected.id === id)) {
            selectedItems.push(item);
        }
    } else {
        selectedItems = selectedItems.filter(item => item.id !== id);
    }
    renderSelectedList();
};

const removeItem = (id) => {
    selectedItems = selectedItems.filter(item => item.id !== id);
    renderSelectedList();
    renderOriginalList();
};

document.addEventListener("DOMContentLoaded", () => {
    loadContractData();

    // Search left
    document.querySelector('.search-left').addEventListener('input', (e) => {
        searchTerm = e.target.value;
        currentPage = 1;
        loadContractData(currentPage, searchTerm);
    });

    // Search right
    document.querySelector('.search-right').addEventListener('input', (e) => {
        selectedSearchTerm = e.target.value;
        selectedPage = 1;
        renderSelectedList();
    });
});
