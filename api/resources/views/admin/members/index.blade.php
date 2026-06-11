@extends('layouts.admin.app')

@section('title', 'Members Management')
@section('header', 'Members Management')

@section('content')
<div class="admin-table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0">All Members</h5>
        
        <div class="d-flex gap-2">
            <form action="{{ route('admin.members.index') }}" method="GET" class="d-flex align-items-center">
                <select name="status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Applied On</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr>
                    <td>#{{ $member->id }}</td>
                    <td>{{ $member->member_code ?? '-' }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($member->photo_path)
                                <img src="{{ asset('storage/' . $member->photo_path) }}" class="rounded-circle me-2" width="30" height="30">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                    <i class="fa fa-user text-secondary"></i>
                                </div>
                            @endif
                            {{ $member->name }}
                        </div>
                    </td>
                    <td>{{ $member->phone }}</td>
                    <td>{{ $member->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($member->membership_status == 'pending')
                            <span class="status-badge status-pending">Pending</span>
                        @elseif($member->membership_status == 'approved')
                            <span class="status-badge status-approved">Approved</span>
                        @else
                            <span class="status-badge status-rejected">Rejected</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <!-- View Button -->
                            <!-- <a href="{{ route('admin.members.show', $member) }}" class="btn btn-sm btn-outline-primary" title="View Details"><i class="fa fa-eye"></i></a> -->
                            
                            @if($member->membership_status === 'pending')
                                <form action="{{ route('admin.members.update-status', $member) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success" title="Approve" onclick="return confirm('Approve this member?')"><i class="fa fa-check"></i></button>
                                </form>
                                <form action="{{ route('admin.members.update-status', $member) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Reject" onclick="return confirm('Reject this member?')"><i class="fa fa-times"></i></button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No members found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $members->links() }}
    </div>
</div>
@endsection
