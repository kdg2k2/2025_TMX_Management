@props([
    'items' => [],
    'selected' => null,
    'keyField' => 'id',
    'valueFields' => 'name',
    'emptyOption' => true,
    'emptyText' => 'Chọn',
    'optionAttributes' => [], // Custom: ['data-email' => 'email']
    'recordAttribute' => null, // Tên attribute để chứa toàn bộ record: 'data-record', 'data-item', etc.
    'recordFields' => null, // Chỉ lấy một số field: ['id', 'name', 'email'] hoặc null để lấy tất cả
])

@php
    $valueFieldsArray = is_array($valueFields) ? $valueFields : [$valueFields];

    $getDisplayValue = function($item) use ($valueFieldsArray) {
        $values = array_map(fn($field) => $item[$field] ?? '', $valueFieldsArray);
        return implode(' - ', array_filter($values));
    };

    $getKeyValue = function($item) use ($keyField) {
        return is_array($keyField)
            ? ($item[$keyField[0]] ?? '')
            : ($item[$keyField] ?? '');
    };

    $isSelected = function($item) use ($selected, $getKeyValue) {
        return $getKeyValue($item) == $selected;
    };

    // Function build attributes
    $getAllAttributes = function($item) use ($optionAttributes, $recordAttribute, $recordFields) {
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
            $data = $recordFields
                ? array_intersect_key($item, array_flip($recordFields))
                : $item;
            $jsonData = json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT);
            $attributes .= ' ' . $recordAttribute . '="' . htmlspecialchars($jsonData, ENT_QUOTES) . '"';
        }

        return $attributes;
    };
@endphp

@if($emptyOption)
    <option value="">{{ $emptyText }}</option>
@endif

@foreach($items as $item)
    <option
        value="{{ $getKeyValue($item) }}"
        {{ $isSelected($item) ? 'selected' : '' }}
        {!! $getAllAttributes($item) !!}>
        {{ $getDisplayValue($item) }}
    </option>
@endforeach
