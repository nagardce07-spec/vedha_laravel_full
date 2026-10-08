{{-- resources/views/admin/privacy/edit.blade.php --}}
@extends('layouts.admin')
@section('title', 'Privacy Policy')

@section('content')
<div class="card">
    <div class="card-header"><div class="card-title"><span class="dot">•</span> Privacy Policy <span class="dot">•</span></div></div>

    <form action="{{ route('admin.privacy.update') }}" method="POST">
        @csrf @method('PUT')
        <div id="editor-toolbar" style="border:1px solid #E5E7EB; border-bottom:none; border-radius:10px 10px 0 0; padding:8px 12px; display:flex; gap:8px; background:#FAFAFC;">
            <button type="button" class="ql-bold" style="border:none; background:none; cursor:pointer;">B</button>
            <button type="button" class="ql-italic" style="border:none; background:none; cursor:pointer;">I</button>
            <button type="button" class="ql-underline" style="border:none; background:none; cursor:pointer;">U</button>
            <button type="button" class="ql-link" style="border:none; background:none; cursor:pointer;">🔗</button>
            <button type="button" class="ql-list" value="ordered" style="border:none; background:none; cursor:pointer;">1.</button>
            <button type="button" class="ql-list" value="bullet" style="border:none; background:none; cursor:pointer;">•</button>
            <button type="button" class="ql-clean" style="border:none; background:none; cursor:pointer;">Tx</button>
        </div>
        <div id="editor" style="height:500px; border:1px solid #E5E7EB; border-radius:0 0 10px 10px; padding:16px;">{!! $page->content !!}</div>
        <textarea name="content" id="hiddenContent" style="display:none;"></textarea>

        <button type="submit" class="btn btn-primary" style="margin-top:20px;" onclick="document.getElementById('hiddenContent').value = quill.root.innerHTML;">Save</button>
    </form>
</div>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script>
    const quill = new Quill('#editor', { theme: 'snow', modules: { toolbar: '#editor-toolbar' } });
</script>
@endsection
