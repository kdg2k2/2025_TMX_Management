@php
    $colClass ??= 'col-md-6';
    $setRequired ??= true;
    $setEmptyOption ??= false;
@endphp

<div class="{{ $colClass }} my-1">
    <div class="form-group">
        <label>
            Kiểu phát hành
        </label>
        <select name="release_type" id="release-type" class="form-control" {{ $setRequired ? 'required' : '' }}>
            <x-select-options :items="$releaseTypes" :emptyOption="$setEmptyOption" keyField="original" valueFields="converted" />
        </select>
    </div>
</div>
<div class="{{ $colClass }} my-1">
    <div class="form-group">
        <label>
            Loại văn bản
        </label>
        <select name="official_document_type_id" id="official-document-type-id" class="form-control" {{ $setRequired ? 'required' : '' }}>
            <x-select-options :items="$officialDocumentTypes" />
        </select>
    </div>
</div>
<div class="{{ $colClass }} my-1">
    <div class="form-group">
        <label>
            Kiểu chương trình
        </label>
        <select name="program_type" id="program-type" class="form-control" {{ $setRequired ? 'required' : '' }}>
            <x-select-options :items="$programTypes" :emptyOption="$setEmptyOption" keyField="original" valueFields="converted" />
        </select>
    </div>
</div>
