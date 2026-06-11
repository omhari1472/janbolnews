@extends('layouts.admin.app')

@section('title', 'Gallery Management')
@section('header', 'Gallery')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
        <h5 class="m-0">Gallery Images</h5>
        <a href="{{ route('admin.gallery.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-upload me-1"></i> Upload Image</a>
    </div>

    <div class="row g-4">
        @forelse($images as $image)
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="ratio ratio-4x3">
                    <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top object-fit-cover" alt="{{ $image->title }}">
                </div>
                <div class="card-body d-flex justify-content-between align-items-center p-2">
                    <small class="text-truncate" style="max-width: 150px;">{{ $image->title ?? 'Untitled' }}</small>
                    <form action="{{ route('admin.gallery.destroy', $image) }}" method="POST" onsubmit="return confirm('Delete this image?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <p class="text-muted mb-0">No images found in gallery.</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $images->links() }}
    </div>
</div>
@endsection
