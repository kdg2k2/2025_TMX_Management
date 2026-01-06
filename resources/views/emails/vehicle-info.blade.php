<p>
    Hãng xe:
    <strong>
        {{ $data['brand'] ?? '' }}
    </strong>
</p>
<p>
    Biển số xe:
    <strong>
        {{ $data['license_plate'] ?? '' }}
    </strong>
</p>
<p>
    Số km hiện trạng:
    <strong>
        {{ number_format($data['current_km'] ?? 0, 0, ',', '.') }} km
    </strong>
</p>
<p>
    Số km đến hạn bảo dưỡng:
    <strong style="color: red;">
        {{ number_format($data['maintenance_km'] ?? 0, 0, ',', '.') }} km
    </strong>
</p>
<p>
    Hạn đăng kiểm:
    <strong style="color: red;">
        {{ $data['inspection_expired_at'] ?? '' }}
    </strong>
</p>
<p>
    Hạn bảo hiểm trách nhiệm dân sự:
    <strong style="color: red;">
        {{ $data['liability_insurance_expired_at'] ?? '' }}
    </strong>
</p>
<p>
    Hạn bảo hiểm thân vỏ:
    <strong style="color: red;">
        {{ $data['body_insurance_expired_at'] ?? '' }}
    </strong>
</p>
<p>
    Trạng thái xe:
    <strong style="color: green;">
        {{ $data['status']['converted'] ?? '' }}
    </strong>
</p>
