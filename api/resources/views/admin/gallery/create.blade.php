@extends('layouts.admin.app')

@section('title', 'Upload Image')
@section('header', 'Upload to Gallery')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Image Title (Optional)</label>
                        <input type="text" class="form-control" name="title" placeholder="e.g. Charity Event 2026">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Select Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                        <div class="form-text">Supported formats: jpeg, png, jpg, gif. Max size: 5MB. Images are auto-converted to WebP.</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.gallery.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
