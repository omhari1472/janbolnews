@extends('layouts.admin.app')

@section('title', 'Donations Management')
@section('header', 'Donations')

@section('content')
<div class="admin-table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0">All Donations</h5>
        <a href="{{ route('admin.donations.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus me-1"></i> Record Donation</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Receipt No.</th>
                    <th>Donor</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donations as $donation)
                <tr>
                    <td>{{ $donation->receipt_number }}</td>
                    <td>
                        {{ $donation->donor_name }}<br>
                        <small class="text-muted">{{ $donation->phone }}</small>
                    </td>
                    <td>₹{{ number_format($donation->amount, 2) }}</td>
                    <td>
                        @if($donation->donation_type == 'online')
                            <span class="badge bg-success">Online</span>
                        @else
                            <span class="badge bg-secondary">Cash</span>
                        @endif
                    </td>
                    <td>{{ $donation->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.donations.receipt', $donation) }}" class="btn btn-sm btn-outline-danger" title="Download Receipt"><i class="fa fa-file-pdf"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No donations recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $donations->links() }}
    </div>
</div>
@endsection
