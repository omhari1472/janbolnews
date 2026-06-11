@extends('layouts.admin.app')

@section('title', 'Enquiries')
@section('header', 'Enquiries')

@section('content')
<div class="admin-table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0">All Messages</h5>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enquiries as $enquiry)
                <tr>
                    <td>{{ $enquiry->name }}</td>
                    <td>
                        {{ $enquiry->phone }}<br>
                        <small class="text-muted">{{ $enquiry->email }}</small>
                    </td>
                    <td><span class="d-inline-block text-truncate" style="max-width: 250px;">{{ $enquiry->message }}</span></td>
                    <td>{{ $enquiry->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye"></i></a>
                        <form action="{{ route('admin.enquiries.destroy', $enquiry) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this enquiry?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No enquiries found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $enquiries->links() }}
    </div>
</div>
@endsection
