@include('emails.contract-product-minute-base-content')
<hr>

<p style="text-align: center; margin-top: 20px;">
    <a href="{{ $signUrl }}"
        style="
            display: inline-block;
            padding: 12px 30px;
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        ">
        🖊️ Nhấn vào đây để ký biên bản
    </a>
</p>

<p style="text-align: center; color: #6c757d; font-size: 12px; margin-top: 10px;">
    Hoặc sao chép link sau vào trình duyệt:
    <br>
    <a href="{{ $signUrl }}" style="color: #0d6efd;">{{ $signUrl }}</a>
</p>
