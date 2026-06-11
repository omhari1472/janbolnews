@extends('layouts.admin.app')

@section('title', 'View Enquiry')
@section('header', 'View Enquiry')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-4">
                    <h5 class="card-title text-primary">Message Details</h5>
                    <span class="text-muted">{{ $enquiry->created_at->format('M d, Y h:i A') }}</span>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">From:</label>
                    <p class="mb-1">{{ $enquiry->name }}</p>
                    <p class="mb-0 text-muted"><i class="fa fa-phone me-1"></i> {{ $enquiry->phone }}</p>
                    <p class="text-muted"><i class="fa fa-envelope me-1"></i> {{ $enquiry->email }}</p>
                </div>

                <div class="mb-4">
                    <label class="fw-bold">Message:</label>
                    <div class="p-3 bg-light rounded border">
                        {{ $enquiry->message }}
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.enquiries.index') }}" class="btn btn-light"><i class="fa fa-arrow-left me-1"></i> Back</a>
                    <form action="{{ route('admin.enquiries.destroy', $enquiry) }}" method="POST" onsubmit="return confirm('Delete this enquiry?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash me-1"></i> Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
