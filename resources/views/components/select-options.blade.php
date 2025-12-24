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
    'optionCallback' => null,
])

@php
    $valueFieldsArray = is_array($valueFields) ? $valueFields : [$valueFields];

    $dotGet = function ($array, $path) {
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (!is_array($array) || !array_key_exists($key, $array)) {
                return null;
            }
            $array = $array[$key];
        }
        return $array;
    };

    $getDisplayValue = function ($item) use ($valueFieldsArray, $dotGet) {
        if (!is_array($item)) {
            return $item;
        }

        $values = array_map(fn($field) => $dotGet($item, $field), $valueFieldsArray);

        return implode(' - ', array_filter($values));
    };

    $getKeyValue = function ($item, $key) use ($keyField, $items, $dotGet) {
        if (!is_array($item) && !is_object($item)) {
            $firstKey = array_key_first($items);
            if (!is_numeric($firstKey) || $firstKey !== 0) {
                return $key;
            }
            return $item;
        }

        return $dotGet((array) $item, $keyField);
    };

    $isSelected = function ($item, $key) use ($selected, $getKeyValue) {
        return $getKeyValue($item, $key) == $selected;
    };

    $getAllAttributes = function ($item) use ($optionAttributes, $recordAttribute, $recordFields, $optionCallback) {
        if (!is_array($item)) {
            $extra = $optionCallback ? call_user_func($optionCallback, $item) : '';
            return $extra ? ' ' . trim($extra) : '';
        }

        $attributes = '';

        foreach ($optionAttributes as $attrName => $fieldName) {
            if ($fieldName === '@record') {
                $data = $recordFields ? array_intersect_key($item, array_flip($recordFields)) : $item;
                $value = json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT);
            } else {
                $value = $item[$fieldName] ?? '';
            }
            $attributes .= ' ' . $attrName . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
        }

        if ($recordAttribute) {
            $data = $recordFields ? array_intersect_key($item, array_flip($recordFields)) : $item;
            $jsonData = json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT);
            $attributes .= ' ' . $recordAttribute . '="' . htmlspecialchars($jsonData, ENT_QUOTES) . '"';
        }

        if ($optionCallback && is_callable($optionCallback)) {
            $callbackAttrs = call_user_func($optionCallback, $item);
            if ($callbackAttrs) {
                $attributes .= ' ' . trim($callbackAttrs);
            }
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
