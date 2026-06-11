@extends('layouts.admin.app')

@section('title', 'Certificate Management')
@section('header', 'Certificates')

@section('content')
<div class="admin-table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0">Issued Certificates</h5>
        <a href="{{ route('admin.certificates.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus me-1"></i> Issue New</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Cert. No.</th>
                    <th>Issued To</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificates as $certificate)
                <tr>
                    <td>{{ $certificate->certificate_number }}</td>
                    <td>
                        {{ $certificate->issued_to_name }}
                        @if($certificate->member_id)
                            <br><small class="text-muted">Member ID: #{{ $certificate->member_id }}</small>
                        @endif
                    </td>
                    <td><span class="badge bg-info text-dark">{{ ucfirst($certificate->certificate_type) }}</span></td>
                    <td>{{ $certificate->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.certificates.download', $certificate) }}" class="btn btn-sm btn-outline-danger" title="Download PDF"><i class="fa fa-file-pdf"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No certificates issued yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $certificates->links() }}
    </div>
</div>
@endsection
