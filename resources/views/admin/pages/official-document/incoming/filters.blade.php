@php
    $colClass ??= 'col-lg-2 col-md-6';
    $setRequired ??= false;
    $emptyOpt ??= true;
@endphp
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Loại văn bản
        </label>
        <select name="official_document_type_id" class="form-control" {{ $setRequired ? 'required' : '' }}>
            <x-select-options :items="$officialDocumentTypes" />
        </select>
    </div>
</div>
<div class="{{ $colClass }}">
    <div class="form-group">
        <label>
            Loại chương trình
        </label>
        <select name="program_type" class="form-control" {{ $setRequired ? 'required' : '' }}>
            <x-select-options :items="$programTypes" :emptyOption="$emptyOpt" keyField="original" valueFields="converted"/>
        </select>
    </div>
</div>
