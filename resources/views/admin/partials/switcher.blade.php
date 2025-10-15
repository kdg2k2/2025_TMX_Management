<div class="offcanvas offcanvas-end" tabindex="-1" id="switcher-canvas" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header border-bottom d-block p-0">
        <div class="d-flex align-items-center justify-content-between p-3">
            <h5 class="offcanvas-title text-default" id="offcanvasRightLabel">Tùy chỉnh giao diện</h5>
            <x-button size="sm" variant="light" class="btn-close" data-bs-dismiss="offcanvas"
                aria-label="Close"></x-button>
        </div>
        <nav class="border-top border-block-start-dashed">
            <div class="nav nav-tabs nav-justified" id="switcher-main-tab" role="tablist">
                <x-button size="md" variant="light" :outline="true" class="nav-link active"
                    id="switcher-home-tab" data-bs-toggle="tab" data-bs-target="#switcher-home" type="button"
                    role="tab" aria-controls="switcher-home" aria-selected="true">
                    Kiểu giao diện
                </x-button>
                <x-button size="md" variant="light" :outline="true" class="nav-link" id="switcher-profile-tab"
                    data-bs-toggle="tab" data-bs-target="#switcher-profile" type="button" role="tab"
                    aria-controls="switcher-profile" aria-selected="false">
                    Màu giao diện
                </x-button>
            </div>
        </nav>
    </div>
    <div class="offcanvas-body">
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active border-0" id="switcher-home" role="tabpanel"
                aria-labelledby="switcher-home-tab" tabindex="0">
                <div class="">
                    <p class="switcher-style-head">Chế độ màu:</p>
                    <div class="row switcher-style gx-0">
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-light-theme">
                                    Sáng
                                </label>
                                <input class="form-check-input" type="radio" name="theme-style"
                                    id="switcher-light-theme" checked>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-dark-theme">
                                    Tối
                                </label>
                                <input class="form-check-input" type="radio" name="theme-style"
                                    id="switcher-dark-theme">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <p class="switcher-style-head">Hướng hiển thị:</p>
                    <div class="row switcher-style gx-0">
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-ltr">
                                    LTR
                                </label>
                                <input class="form-check-input" type="radio" name="direction" id="switcher-ltr"
                                    checked>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-rtl">
                                    RTL
                                </label>
                                <input class="form-check-input" type="radio" name="direction" id="switcher-rtl">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <p class="switcher-style-head">Kiểu điều hướng:</p>
                    <div class="row switcher-style gx-0">
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-vertical">
                                    Dọc
                                </label>
                                <input class="form-check-input" type="radio" name="navigation-style"
                                    id="switcher-vertical" checked>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-horizontal">
                                    Ngang
                                </label>
                                <input class="form-check-input" type="radio" name="navigation-style"
                                    id="switcher-horizontal">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="navigation-menu-styles">
                    <p class="switcher-style-head">Kiểu menu dọc & ngang:</p>
                    <div class="row switcher-style gx-0 pb-2 gy-2">
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-menu-click">
                                    Click menu
                                </label>
                                <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                    id="switcher-menu-click">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-menu-hover">
                                    Hover menu
                                </label>
                                <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                    id="switcher-menu-hover">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-icon-click">
                                    Click biểu tượng
                                </label>
                                <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                    id="switcher-icon-click">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-icon-hover">
                                    Hover biểu tượng
                                </label>
                                <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                    id="switcher-icon-hover">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidemenu-layout-styles">
                    <p class="switcher-style-head">Kiểu bố cục menu bên:</p>
                    <div class="row switcher-style gx-0 pb-2 gy-2">
                        <div class="col-sm-6">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-default-menu">
                                    Menu mặc định
                                </label>
                                <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                    id="switcher-default-menu" checked>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-closed-menu">
                                    Menu thu gọn
                                </label>
                                <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                    id="switcher-closed-menu">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-icontext-menu">
                                    Biểu tượng & chữ
                                </label>
                                <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                    id="switcher-icontext-menu">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-icon-overlay">
                                    Biểu tượng phủ
                                </label>
                                <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                    id="switcher-icon-overlay">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-detached">
                                    Tách rời
                                </label>
                                <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                    id="switcher-detached">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-double-menu">
                                    Menu kép
                                </label>
                                <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                    id="switcher-double-menu">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <p class="switcher-style-head">Kiểu trang:</p>
                    <div class="row switcher-style gx-0">
                        <div class="col-xl-3 col-6">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-regular">
                                    Thường
                                </label>
                                <input class="form-check-input" type="radio" name="page-styles"
                                    id="switcher-regular">
                            </div>
                        </div>
                        <div class="col-xl-3 col-6">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-classic">
                                    Cổ điển
                                </label>
                                <input class="form-check-input" type="radio" name="page-styles"
                                    id="switcher-classic">
                            </div>
                        </div>
                        <div class="col-xl-3 col-6">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-modern">
                                    Hiện đại
                                </label>
                                <input class="form-check-input" type="radio" name="page-styles"
                                    id="switcher-modern">
                            </div>
                        </div>
                        <div class="col-xl-3 col-6">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-flat">
                                    Phẳng
                                </label>
                                <input class="form-check-input" type="radio" name="page-styles" id="switcher-flat"
                                    checked>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <p class="switcher-style-head">Kiểu độ rộng bố cục:</p>
                    <div class="row switcher-style gx-0">
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-default-width">
                                    Mặc định
                                </label>
                                <input class="form-check-input" type="radio" name="layout-width"
                                    id="switcher-default-width">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-full-width">
                                    Toàn bộ chiều rộng
                                </label>
                                <input class="form-check-input" type="radio" name="layout-width"
                                    id="switcher-full-width" checked>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-boxed">
                                    Khung hộp
                                </label>
                                <input class="form-check-input" type="radio" name="layout-width"
                                    id="switcher-boxed">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <p class="switcher-style-head">Vị trí menu:</p>
                    <div class="row switcher-style gx-0">
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-menu-fixed">
                                    Cố định
                                </label>
                                <input class="form-check-input" type="radio" name="menu-positions"
                                    id="switcher-menu-fixed" checked>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-menu-scroll">
                                    Cuộn được
                                </label>
                                <input class="form-check-input" type="radio" name="menu-positions"
                                    id="switcher-menu-scroll">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <p class="switcher-style-head">Vị trí header:</p>
                    <div class="row switcher-style gx-0">
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-header-fixed">
                                    Cố định
                                </label>
                                <input class="form-check-input" type="radio" name="header-positions"
                                    id="switcher-header-fixed" checked>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-header-scroll">
                                    Cuộn được
                                </label>
                                <input class="form-check-input" type="radio" name="header-positions"
                                    id="switcher-header-scroll">
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="switcher-style-head">Tải trang:</p>
                    <div class="row switcher-style gx-0">
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-loader-enable">
                                    Bật
                                </label>
                                <input class="form-check-input" type="radio" name="page-loader"
                                    id="switcher-loader-enable" checked>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check switch-select">
                                <label class="form-check-label" for="switcher-loader-disable">
                                    Tắt
                                </label>
                                <input class="form-check-input" type="radio" name="page-loader"
                                    id="switcher-loader-disable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade border-0" id="switcher-profile" role="tabpanel"
                aria-labelledby="switcher-profile-tab" tabindex="0">
                <div>
                    <div class="theme-colors">
                        <p class="switcher-style-head">Màu menu:</p>
                        <div class="d-flex switcher-style pb-2">
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-white" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Menu sáng" type="radio" name="menu-colors"
                                    id="switcher-menu-light" checked>
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-dark" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Menu tối" type="radio" name="menu-colors"
                                    id="switcher-menu-dark">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-primary" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Menu màu" type="radio" name="menu-colors"
                                    id="switcher-menu-primary">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-gradient" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Menu gradient" type="radio" name="menu-colors"
                                    id="switcher-menu-gradient">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-transparent" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Menu trong suốt" type="radio"
                                    name="menu-colors" id="switcher-menu-transparent">
                            </div>
                        </div>
                        <div class="px-4 pb-3 text-muted fs-11">Lưu ý: Nếu muốn thay đổi màu Menu động, hãy thay đổi từ bộ chọn màu chủ đạo bên dưới</div>
                    </div>
                    <div class="theme-colors">
                        <p class="switcher-style-head">Màu header:</p>
                        <div class="d-flex switcher-style pb-2">
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-white" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Header sáng" type="radio" name="header-colors"
                                    id="switcher-header-light">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-dark" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Header tối" type="radio" name="header-colors"
                                    id="switcher-header-dark">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-primary" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Header màu" type="radio" name="header-colors"
                                    id="switcher-header-primary">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-gradient" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Header gradient" type="radio"
                                    name="header-colors" id="switcher-header-gradient">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-transparent" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Header trong suốt" type="radio"
                                    name="header-colors" id="switcher-header-transparent" checked>
                            </div>
                        </div>
                        <div class="px-4 pb-3 text-muted fs-11">Lưu ý: Nếu muốn thay đổi màu Header động, hãy thay đổi từ bộ chọn màu chủ đạo bên dưới</div>
                    </div>
                    <div class="theme-colors">
                        <p class="switcher-style-head">Màu chủ đạo:</p>
                        <div class="d-flex flex-wrap align-items-center switcher-style">
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-primary-1" type="radio"
                                    name="theme-primary" id="switcher-primary">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-primary-2" type="radio"
                                    name="theme-primary" id="switcher-primary1">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-primary-3" type="radio"
                                    name="theme-primary" id="switcher-primary2">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-primary-4" type="radio"
                                    name="theme-primary" id="switcher-primary3">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-primary-5" type="radio"
                                    name="theme-primary" id="switcher-primary4">
                            </div>
                            <div class="form-check switch-select ps-0 mt-1 color-primary-light">
                                <div class="theme-container-primary"></div>
                                <div class="pickr-container-primary" onchange="updateChartColor(this.value)"></div>
                            </div>
                        </div>
                    </div>
                    <div class="theme-colors">
                        <p class="switcher-style-head">Màu nền:</p>
                        <div class="d-flex flex-wrap align-items-center switcher-style">
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-bg-1" type="radio"
                                    name="theme-background" id="switcher-background">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-bg-2" type="radio"
                                    name="theme-background" id="switcher-background1">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-bg-3" type="radio"
                                    name="theme-background" id="switcher-background2">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-bg-4" type="radio"
                                    name="theme-background" id="switcher-background3">
                            </div>
                            <div class="form-check switch-select me-3">
                                <input class="form-check-input color-input color-bg-5" type="radio"
                                    name="theme-background" id="switcher-background4">
                            </div>
                            <div class="form-check switch-select ps-0 mt-1 tooltip-static-demo color-bg-transparent">
                                <div class="theme-container-background"></div>
                                <div class="pickr-container-background"></div>
                            </div>
                        </div>
                    </div>
                    <div class="menu-image mb-3" hidden>
                        <p class="switcher-style-head">Menu với ảnh nền:</p>
                        <div class="d-flex flex-wrap align-items-center switcher-style">
                            <div class="form-check switch-select menu-img-select m-2">
                                <input class="form-check-input bgimage-input bg-img1" type="radio"
                                    name="menu-background" id="switcher-bg-img">
                                <div class="bg-img-container">
                                    <img src="" alt="">
                                </div>
                            </div>
                            <div class="form-check switch-select menu-img-select m-2">
                                <input class="form-check-input bgimage-input bg-img2" type="radio"
                                    name="menu-background" id="switcher-bg-img1">
                                <div class="bg-img-container">
                                    <img src="" alt="">
                                </div>
                            </div>
                            <div class="form-check switch-select menu-img-select m-2">
                                <input class="form-check-input bgimage-input bg-img3" type="radio"
                                    name="menu-background" id="switcher-bg-img2">
                                <div class="bg-img-container">
                                    <img src="" alt="">
                                </div>
                            </div>
                            <div class="form-check switch-select menu-img-select m-2">
                                <input class="form-check-input bgimage-input bg-img4" type="radio"
                                    name="menu-background" id="switcher-bg-img3">
                                <div class="bg-img-container">
                                    <img src="" alt="">
                                </div>
                            </div>
                            <div class="form-check switch-select menu-img-select m-2">
                                <input class="form-check-input bgimage-input bg-img5" type="radio"
                                    name="menu-background" id="switcher-bg-img4">
                                <div class="bg-img-container">
                                    <img src="" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-block canvas-footer flex-wrap">
                <a href="javascript:void(0);" id="reset-all" class="btn btn-danger m-1 w-100">Đặt lại</a>
            </div>
        </div>
    </div>
</div>
