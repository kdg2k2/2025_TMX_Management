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
                        Quản Lý Đấu Thầu
                    </span>
                </li>
                <li class="slide">
                    <a href="{{ route('bidding.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-building"></i>
                        <span class="side-menu__label">Xây dựng gói thầu</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('eligibilities.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-certificate"></i>
                        <span class="side-menu__label">Tư cách hợp lệ</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('software_ownerships.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-device-laptop"></i>
                        <span class="side-menu__label">Sở hữu phần mềm</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('proof_contracts.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-file-certificate"></i>
                        <span class="side-menu__label">Hợp đồng minh chứng</span>
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
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
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
                        Công tác & Nghỉ phép
                    </span>
                </li>
                <li class="slide">
                    <a href="{{ route('work-schedule.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-calendar-event"></i>
                        <span class="side-menu__label">Lịch công tác</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('leave-request.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-calendar-pause"></i>
                        <span class="side-menu__label">Nghỉ phép</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('user.timetable.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-calendar-time"></i>
                        <span class="side-menu__label">Thời gian biểu</span>
                    </a>
                </li>

                <li class="slide__category">
                    <span class="category-name">
                        Nhận sự & ABC
                    </span>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-user-scan"></i>
                        <span class="side-menu__label">
                            Dữ liệu nhân sự
                        </span>
                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">
                                Dữ liệu nhân sự
                            </a>
                        </li>
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon fs-6 ti ti-settings-2"></i>
                                Thiết lập
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child2">
                                <li class="slide">
                                    <a href="{{ route('personnels.custom-field.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Cột thông tin nhân sự bổ sung
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('personnels.units.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-building"></i>
                                        Đơn vị
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('personnels.file.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-file-code-2"></i>
                                        Loại file bằng cấp
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="slide">
                            <a href="{{ route('personnels.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-id-badge-2"></i>
                                Thông tin nhân sự
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('personnels.file.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-id-badge-2"></i>
                                Bằng cấp trình độ
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-report-analytics"></i>
                        <span class="side-menu__label">
                            Tổng hợp ABC
                        </span>
                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">
                                Tổng hợp ABC
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('work-timesheet.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-calendar-check"></i>
                                Xuất lưới
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('work-timesheet.overtime.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-cloud-upload"></i>
                                Nộp bảng chấm công
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('payroll.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-currency-dollar"></i>
                                Bảng lương
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="slide__category">
                    <span class="category-name">
                        Hồ sơ - Biên bản - Công văn
                    </span>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i
                            class="side-menu__icon ti ti-ticket"></i>
                        <span
                            class="side-menu__label">
                            Vé máy bay/tàu xe
                        </span>
                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">
                                Vé máy bay/tàu xe
                            </a>
                        </li>
                        <li class="slide">
                            <a href="javascript::void(0);" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-plane"></i>
                                Vé máy bay
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('train-and-bus-ticket.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-train"></i>
                                Vé tàu xe
                            </a>
                        </li>
                    </ul>
                </li>
                @php
                    $pendingDossierMinuteFlag =
                        app(\App\Models\DossierMinute::class)->where('status', 'pending_approval')->count() > 0
                            ? 'text-danger'
                            : '';
                    $pendingProfessionalRecordMinuteFlag =
                        app(\App\Models\ProfessionalRecordMinute::class)->where('status', 'pending_approval')->count() >
                        0
                            ? 'text-danger'
                            : '';
                @endphp
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i
                            class="side-menu__icon ti ti-user-check {{ $pendingDossierMinuteFlag }} {{ $pendingProfessionalRecordMinuteFlag }}"></i>
                        <span
                            class="side-menu__label {{ $pendingDossierMinuteFlag }} {{ $pendingProfessionalRecordMinuteFlag }}">
                            HSNN/HSCM
                        </span>
                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">
                                HSNN/HSCM
                            </a>
                        </li>
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item {{ $pendingDossierMinuteFlag }}">
                                <i class="side-menu-doublemenu__icon fs-6 ti ti-settings-2"></i>
                                HS ngoại nghiệp
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child2">
                                <li class="slide">
                                    <a href="{{ route('dossier.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Kho loại hồ sơ
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('dossier.plan.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Lập kế hoạch
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('dossier.handover.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Bàn giao
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('dossier.usage_register.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Đăng ký sử dụng
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('dossier.minute.index') }}"
                                        class="side-menu__item {{ $pendingDossierMinuteFlag }}">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Phê duyệt biên bản
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('dossier.synthetic.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Tổng hợp chứng từ
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="slide has-sub">
                            <a href="javascript:void(0);"
                                class="side-menu__item {{ $pendingProfessionalRecordMinuteFlag }}">
                                <i class="side-menu-doublemenu__icon fs-6 ti ti-settings-2"></i>
                                HS chuyên môn
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child2">
                                <li class="slide">
                                    <a href="{{ route('professional-record.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Kho loại hồ sơ
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('professional-record.plan.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Lập kế hoạch
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('professional-record.handover.index') }}"
                                        class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Bàn giao
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('professional-record.usage_register.index') }}"
                                        class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Đăng ký sử dụng
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('professional-record.minute.index') }}"
                                        class="side-menu__item {{ $pendingProfessionalRecordMinuteFlag }}">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Phê duyệt biên bản
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('professional-record.synthetic.index') }}"
                                        class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Tổng hợp chứng từ
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="slide">
                            <a href="{{ route('unit.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-id-badge-2"></i>
                                Quản lý đơn vị
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-user-check"></i>
                        <span class="side-menu__label">
                            Biên bản họp - Bảng tin
                        </span>
                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">
                                Biên bản họp - Bảng tin
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('internal-meeting-minute.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-id-badge-2"></i>
                                Giao ban
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('board-meeting-minute.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-id-badge-2"></i>
                                Hội đồng quản trị
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('shareholder-meeting-minute.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-id-badge-2"></i>
                                Cổ đông
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('internal-bulletin.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-id-badge-2"></i>
                                Bảng tin
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-user-check"></i>
                        <span class="side-menu__label">
                            HS Lao Động/Bổ Nhiệm
                        </span>
                        <i class="ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">
                                HS Lao Động/Bổ Nhiệm
                            </a>
                        </li>
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon fs-6 ti ti-settings-2"></i>
                                Thiết lập
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child2">
                                <li class="slide">
                                    <a href="{{ route('employment-contract-personnel.custom-field.index') }}"
                                        class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon fs-6 ti ti-input-search"></i>
                                        Cột thông tin nhân sự bổ sung
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="slide">
                            <a href="{{ route('employment-contract-personnel.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon ti ti-id-badge-2"></i>
                                Thông tin nhân sự
                            </a>
                        </li>
                    </ul>
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
                <li class="slide">
                    <a href="{{ route('task-schedule.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-calendar-clock"></i>
                        <span class="side-menu__label">
                            Mail tự động
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
