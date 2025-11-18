    @switch($type)
        @case(0)
            Hệ thống nhận được yêu cầu phê duyệt biên bản kế hoạch chứng từ <i>(chi tiết trong file đính kèm)</i>
            <p>Tên gói thầu: <i style="color: green">{{ $name }}</i></p>
            <p>Người yêu cầu: <b style="color: red">{{ $authUser }}</b></p>
            <p>Đề nghị anh/chị truy cập hệ thống kiểm tra phê duyệt</p>
            <p>Trân trọng cảm ơn!</p>
            <p>Phòng Tổng hợp - Công ty CPTM công nghệ Xuân Mai Green.</p>
        @break

        @case(1)
            Phê duyệt yêu cầu phê duyệt biên bản kế hoạch chứng từ <i>(chi tiết trong file đính kèm)</i>
            <p>Tên gói thầu: <i style="color: green">{{ $name }}</i></p>
            <p>Người yêu cầu: <b style="color: blue">{{ $nguoilap->name }}</b></p>
            <p>Người phê duyệt: <b style="color: red">{{ $authUser }}</b></p><br>
            <p>Nhận xét phê duyệt: <b style="color: green">{{ $lydo }}</b></p>
        @break

        @case(2)
            Từ chối yêu cầu phê duyệt biên bản kế hoạch chứng từ
            <p>Tên gói thầu: <i style="color: green">{{ $name }}</i></p>
            <p>Người yêu cầu: <b style="color: blue">{{ $nguoilap->name }}</b></p>
            <p>Người từ chối: <b style="color: red">{{ $authUser }}</b></p><br>
            <p>Lý do từ chối: <b style="color: red">{{ $lydo }}</b></p>
        @break

        @case(3)
            Hệ thống nhận được yêu cầu phê duyệt biên bản nhận bàn giao chứng từ: <i>(chi tiết trong file đính kèm)</i>
            <p>Tên gói thầu: <i style="color: green">{{ $name }}</i></p>
            <p>Người yêu cầu: <b style="color: red">{{ $authUser }}</b></p><br>
            <p>Đề nghị anh/chị truy cập hệ thống kiểm tra phê duyệt</p>
        @break

        @case(4)
            Phê duyệt yêu cầu phê duyệt biên bản nhận bàn giao chứng từ <i>(chi tiết trong file đính kèm)</i>
            <p>Tên gói thầu: <i style="color: green">{{ $name }}</i></p>
            <p>Người yêu cầu: <b style="color: blue">{{ $nguoilap->name }}</b></p>
            <p>Người phê duyệt: <b style="color: red">{{ $authUser }}</b></p><br>
            <p>Nhận xét phê duyệt: <b style="color: green">{{ $lydo }}</b></p>
        @break

        @case(5)
            Từ chối yêu cầu phê duyệt biên bản nhận bàn giao chứng từ
            <p>Tên gói thầu: <i style="color: green">{{ $name }}</i></p>
            <p>Người yêu cầu: <b style="color: blue">{{ $nguoilap->name }}</b></p>
            <p>Người từ chối: <b style="color: red">{{ $authUser }}</b></p><br>
            <p>Lý do từ chối: <b style="color: red">{{ $lydo }}</b></p>
        @break

        @case(6)
            <p>Anh/chị <b style="color: red">{{ $name }}</b> có đăng ký chứng từ <i>(chi tiết trong file đính
                    kèm)</i></p>
            <p>Sử dụng cho chương trình: <i style="color: green">{{ $sd_cho }}</i></p>
            <p>Ngày nhận: {{ $ngaybangiao }}</p>
            <p>Đề nghị Phòng Tổng hợp truy cập hệ thống xác nhận, phê duyệt.</p>
        @break

        @case(7)
            <p>Đăng ký sử dụng chứng từ đã được phê duyệt <i>(chi tiết trong file đính kèm)</i></p>
            <p>Gói thầu đăng kí: <b>{{ $name }}</b></p>
            <p>Người đăng kí: <b style="color: red">{{ $nguoidangky }}</b></p>
            <p>Ngày đăng kí: <b>{{ $ngaydangky }}</b></p><br>
            <p>Người phê duyệt: <b style="color: red">{{ $authUser }}</b></p>
            <p>Nhận xét phê duyệt: <b style="color: green">{{ $lydo }}</b></p>
        @break

        @case(8)
            Đăng ký sử dụng chứng từ đã bị từ chối.
            <p>Gói thầu đăng kí: <b>{{ $name }}</b></p>
            <p>Người đăng kí: <b style="color: red">{{ $nguoidangky }}</b></p>
            <p>Ngày đăng kí: <b>{{ $ngaydangky }}</b></p><br>
            <p>Người từ chối: <b style="color: red">{{ $authUser }}</b></p>
            <p>Lý do từ chối: <b style="color: red">{{ $lydo }}</b></p>
        @break

        @case(9)
            Không đủ dữ liệu kho để duyệt biên bản kế hoạch:
            <p>Gói thầu đăng kí: <b>{{ $name }}</b></p>
            <p>Chi tiết đang thiếu:</p>
            <ul>
                @foreach ($messages as $index => $item)
                    @if ($index != 0)
                        <li>{{ $item }}</li>
                    @endif
                @endforeach
            </ul>
        @break

        @case(10)
            <p>Chi tiết cần bổ sung:</p>
            <ul>
                @foreach ($messages as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @break

        @default
    @endswitch
