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
                <!-- Dashboard -->
                <li class="slide">
                    <a href="{{ route('dashboard') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-home-2"></i>
                        <span class="side-menu__label">
                            Trang chủ
                        </span>
                    </a>
                </li>

                <!-- Quản Lý Đấu Thầu -->
                <li class="slide__category">
                    <span class="category-name">
                        Quản Lý Đấu Thầu
                    </span>
                </li>
                <li class="slide">
                    <a href="{{ route('bidding.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-briefcase"></i>
                        <span class="side-menu__label">Xây dựng gói thầu</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('eligibilities.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-rosette"></i>
                        <span class="side-menu__label">Tư cách hợp lệ</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('software_ownerships.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-brand-windows"></i>
                        <span class="side-menu__label">Sở hữu phần mềm</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('proof_contracts.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-file-check"></i>
                        <span class="side-menu__label">Hợp đồng minh chứng</span>
                    </a>
                </li>

                <!-- Quản lý hợp đồng -->
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
                                <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-settings"></i>
                                Thiết lập
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child2">
                                <li class="slide">
                                    <a href="{{ route('contract.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-category"></i>
                                        Loại hợp đồng
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('contract.investor.index') }}" class="side-menu__item">
                                        <i
                                            class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-building-skyscraper"></i>
                                        Nhà đầu tư
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('contract.unit.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-affiliate"></i>
                                        Đơn vị liên danh
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('contract.file.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-file-settings"></i>
                                        Loại file
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('contract.scan-file.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-scan"></i>
                                        Loại scan file
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="slide">
                            <a href="{{ route('contract.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-file-invoice"></i>
                                Hợp đồng
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Công tác & Nghỉ phép -->
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
                        <i class="side-menu__icon ti ti-calendar-off"></i>
                        <span class="side-menu__label">Nghỉ phép</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('user.timetable.index') }}" class="side-menu__item">
                        <i class="side-menu__icon ti ti-calendar-stats"></i>
                        <span class="side-menu__label">Thời gian biểu</span>
                    </a>
                </li>

                <!-- Nhận sự & ABC -->
                <li class="slide__category">
                    <span class="category-name">
                        Nhận sự & ABC
                    </span>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-users-group"></i>
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
                                <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-settings"></i>
                                Thiết lập
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child2">
                                <li class="slide">
                                    <a href="{{ route('personnels.custom-field.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-columns"></i>
                                        Cột thông tin nhân sự bổ sung
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('personnels.units.index') }}" class="side-menu__item">
                                        <i
                                            class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-building-community"></i>
                                        Đơn vị
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('personnels.file.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-certificate"></i>
                                        Loại file bằng cấp
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="slide">
                            <a href="{{ route('personnels.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-user-circle"></i>
                                Thông tin nhân sự
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('personnels.file.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-certificate-2"></i>
                                Bằng cấp trình độ
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-report-money"></i>
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
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-table-export"></i>
                                Xuất lưới
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('work-timesheet.overtime.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-file-upload"></i>
                                Nộp bảng chấm công
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('payroll.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-cash"></i>
                                Bảng lương
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Hồ sơ - Biên bản - Công văn -->
                <li class="slide__category">
                    <span class="category-name">
                        Hồ sơ - Biên bản - Công văn
                    </span>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-plane"></i>
                        <span class="side-menu__label">
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
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item ">
                                <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-plane-departure"></i>
                                Vé máy bay
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child2">
                                <li class="slide">
                                    <a href="{{ route('airport.index') }}" class="side-menu__item">
                                        <i
                                            class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-building-airport"></i>
                                        Sân bay
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('airline.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-plane-tilt"></i>
                                        Hãng bay
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('plane-ticket-class.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-medal"></i>
                                        Hạng vé máy bay
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="javascript::void(0);" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-ticket"></i>
                                        Vé máy bay
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="slide">
                            <a href="{{ route('train-and-bus-ticket.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-bus"></i>
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
                            class="side-menu__icon ti ti-folders {{ $pendingDossierMinuteFlag }} {{ $pendingProfessionalRecordMinuteFlag }}"></i>
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
                                <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-folder"></i>
                                HS ngoại nghiệp
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child2">
                                <li class="slide">
                                    <a href="{{ route('dossier.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-file-stack"></i>
                                        Kho loại hồ sơ
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('dossier.plan.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-checklist"></i>
                                        Lập kế hoạch
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('dossier.handover.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-file-export"></i>
                                        Bàn giao
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('dossier.usage_register.index') }}" class="side-menu__item">
                                        <i
                                            class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-clipboard-text"></i>
                                        Đăng ký sử dụng
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('dossier.minute.index') }}"
                                        class="side-menu__item {{ $pendingDossierMinuteFlag }}">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-file-check"></i>
                                        Phê duyệt biên bản
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('dossier.synthetic.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-files"></i>
                                        Tổng hợp chứng từ
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="slide has-sub">
                            <a href="javascript:void(0);"
                                class="side-menu__item {{ $pendingProfessionalRecordMinuteFlag }}">
                                <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-folder-share"></i>
                                HS chuyên môn
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child2">
                                <li class="slide">
                                    <a href="{{ route('professional-record.type.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-file-stack"></i>
                                        Kho loại hồ sơ
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('professional-record.plan.index') }}" class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-checklist"></i>
                                        Lập kế hoạch
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('professional-record.handover.index') }}"
                                        class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-file-export"></i>
                                        Bàn giao
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('professional-record.usage_register.index') }}"
                                        class="side-menu__item">
                                        <i
                                            class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-clipboard-text"></i>
                                        Đăng ký sử dụng
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('professional-record.minute.index') }}"
                                        class="side-menu__item {{ $pendingProfessionalRecordMinuteFlag }}">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-file-check"></i>
                                        Phê duyệt biên bản
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{ route('professional-record.synthetic.index') }}"
                                        class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-files"></i>
                                        Tổng hợp chứng từ
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="slide">
                            <a href="{{ route('unit.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-building"></i>
                                Quản lý đơn vị
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-notebook"></i>
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
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-user-check"></i>
                                Giao ban
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('board-meeting-minute.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-users"></i>
                                Hội đồng quản trị
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('shareholder-meeting-minute.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-chart-pie"></i>
                                Cổ đông
                            </a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('internal-bulletin.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-news"></i>
                                Bảng tin
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="side-menu__icon ti ti-file-description"></i>
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
                                <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-settings"></i>
                                Thiết lập
                                <i class="ri-arrow-right-s-line side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child2">
                                <li class="slide">
                                    <a href="{{ route('employment-contract-personnel.custom-field.index') }}"
                                        class="side-menu__item">
                                        <i class="side-menu-doublemenu__icon me-2 d-block fs-6 ti ti-columns"></i>
                                        Cột thông tin nhân sự bổ sung
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="slide">
                            <a href="{{ route('employment-contract-personnel.index') }}" class="side-menu__item">
                                <i class="side-menu-doublemenu__icon me-2 d-block ti ti-user-circle"></i>
                                Thông tin nhân sự
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Khác -->
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

                <!-- Hệ thống -->
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
                        <i class="side-menu__icon ti ti-mail-cog"></i>
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
