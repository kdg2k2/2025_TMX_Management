<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header">
        <a href="index" class="header-logo">
            <img src="assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo">
            <img src="assets/images/brand-logos/toggle-dark.png" alt="logo" class="toggle-dark">
            <img src="assets/images/brand-logos/desktop-dark.png" alt="logo" class="desktop-dark">
            <img src="assets/images/brand-logos/toggle-logo.png" alt="logo" class="toggle-logo">
        </a>
    </div>

    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg>
            </div>

            <ul class="main-menu">
                <li class="slide">
                    <a href="{{ route('dashboard') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-dashboard"></i>
                        <span class="side-menu__label">
                            Trang chủ
                        </span>
                    </a>
                </li>

                <li class="slide__category">
                    <span class="category-name">
                        Thầu Bè
                    </span>
                </li>
                <li class="slide">
                    <a href="{{ route('bidding.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-building"></i>
                        <span class="side-menu__label">Xây dựng gói thầu</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-certificate"></i>
                        <span class="side-menu__label">Tư cách hợp lệ</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-device-laptop"></i>
                        <span class="side-menu__label">Sở hữu phần mềm</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-file-certificate"></i>
                        <span class="side-menu__label">Hợp đồng minh chứng</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-file-certificate"></i>
                        <span class="side-menu__label">Tài liệu công chứng</span>
                    </a>
                </li>

                <li class="slide__category">
                    <span class="category-name">
                        Quản lý hợp đồng
                    </span>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-file-text"></i>
                        <span class="side-menu__label">
                            Hợp đồng
                        </span>
                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">
                                Hợp đồng
                            </a>
                        </li>
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon fs-6 ti ti-settings-2"></i>
                                Thiết lập
                                <i class="ri-arrow-right-s-line side-menu__angle"></i></a>
                            <ul class="slide-menu child2">
                                <li class="slide">
                                    <a href="{{ route('contract.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-files"></i>
                                        Loại hợp đồng
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('contract.investor.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-building-bank"></i>
                                        Nhà đầu tư
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('contract.unit.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-heart-handshake"></i>
                                        Đơn vị liên danh
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('contract.file.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-file-code-2"></i>
                                        Loại file
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('contract.scan-file.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-file-code-2"></i>
                                        Loại scan file
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="slide">
                            <a href="{{ route('contract.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon fs-6 ti ti-file-description"></i>
                                Hợp đồng
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-assembly"></i>
                        <span class="side-menu__label">
                            Sản phẩm trung gian
                        </span>
                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">
                                Sản phẩm trung gian
                            </a>
                        </li>
                        <li class="slide">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon fs-6 ti ti-progress-check"></i>
                                Kiểm tra tiến độ
                            </a>
                        </li>
                        <li class="slide">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon fs-6 ti ti-file-plus"></i>
                                Tạo biên bản bàn giao
                            </a>
                        </li>
                        <li class="slide">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon fs-6 ti ti-clipboard-check"></i>
                                Phê duyệt biên bản
                            </a>
                        </li>
                        <li class="slide">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon fs-6 ti ti-history"></i>
                                Lịch sử phê duyệt
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="slide__category">
                    <span class="category-name">
                        Nhận sự & ABC
                    </span>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-school"></i>
                        <span class="side-menu__label">
                            Bằng cấp trình độ
                        </span>
                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">
                                Bằng cấp trình độ
                            </a>
                        </li>
                        <li class="slide">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon fs-6 ti ti-building"></i>
                                Quản lý đơn vị
                            </a>
                        </li>
                        <li class="slide">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-id-badge-2"></i>
                                Quản lý nhân sự
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="slide">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-id"></i>
                        <span class="side-menu__label">
                            Thông tin nhân sự
                        </span>
                    </a>
                </li>

                <li class="slide__category">
                    <span class="category-name">
                        Khác
                    </span>
                </li>
                <li class="slide">
                    <a href="{{ route('build-software.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-code"></i>
                        <span class="side-menu__label">
                            ĐXXD Phần mềm
                        </span>
                    </a>
                </li>

                <li class="slide__category">
                    <span class="category-name">
                        Hệ thống
                    </span>
                </li>
                <li class="slide">
                    <a href="{{ route('user.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-users"></i>
                        <span class="side-menu__label">
                            Quản lý tài khoản
                        </span>
                    </a>
                </li>
            </ul>

            <div class="slide-right" id="slide-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                </svg>
            </div>
        </nav>
    </div>
</aside>
