@props([
    'items' => [],
    'selected' => null,
    'keyField' => 'id',
    'valueFields' => 'name',
    'emptyOption' => true,
    'emptyText' => 'Chọn',
    'optionAttributes' => [],
    'recordAttribute' => null,
    'recordFields' => null,
])


@php
    $valueFieldsArray = is_array($valueFields) ? $valueFields : [$valueFields];

    $getDisplayValue = function ($item) use ($valueFieldsArray) {
        // Nếu item là scalar (string, number), return luôn
        if (!is_array($item)) {
            return $item;
        }

        $values = array_map(fn($field) => $item[$field] ?? '', $valueFieldsArray);
        return implode(' - ', array_filter($values));
    };

    $getKeyValue = function ($item, $key) use ($keyField, $items) {
        // Nếu item là scalar (string/number)
        if (!is_array($item) && !is_object($item)) {
            // Check xem key có phải là string hoặc là số không liên tiếp không
            // Ví dụ: [2024 => 'Năm 2024'] thì key = 2024
            $firstKey = array_key_first($items);
            if (!is_numeric($firstKey) || $firstKey !== 0) {
                return $key; // Associative array, dùng key
            }
            return $item; // Indexed array, dùng item
        }

        // Logic cũ cho array/object
        return is_array($keyField) ? $item[$keyField[0]] ?? '' : $item[$keyField] ?? '';
    };

    $isSelected = function ($item, $key) use ($selected, $getKeyValue) {
        return $getKeyValue($item, $key) == $selected;
    };

    // Function build attributes
    $getAllAttributes = function ($item) use ($optionAttributes, $recordAttribute, $recordFields) {
        // Nếu item là scalar, không có attributes
        if (!is_array($item)) {
            return '';
        }

        $attributes = '';

        // Custom attributes từ optionAttributes
        foreach ($optionAttributes as $attrName => $fieldName) {
            // Nếu fieldName là '@record', serialize toàn bộ item
            if ($fieldName === '@record') {
                $data = $recordFields ? array_intersect_key($item, array_flip($recordFields)) : $item;
                $value = json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT);
            } else {
                $value = $item[$fieldName] ?? '';
            }
            $attributes .= ' ' . $attrName . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
        }

        // Record attribute riêng (nếu có)
        if ($recordAttribute) {
            $data = $recordFields ? array_intersect_key($item, array_flip($recordFields)) : $item;
            $jsonData = json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT);
            $attributes .= ' ' . $recordAttribute . '="' . htmlspecialchars($jsonData, ENT_QUOTES) . '"';
        }

        return $attributes;
    };
@endphp

@if ($emptyOption)
    <option value="">{{ $emptyText }}</option>
@endif

@foreach ($items as $key => $item)
    <option value="{{ $getKeyValue($item, $key) }}" {{ $isSelected($item, $key) ? 'selected' : '' }}
        {!! $getAllAttributes($item) !!}>
        {{ $getDisplayValue($item) }}
    </option>
@endforeach
