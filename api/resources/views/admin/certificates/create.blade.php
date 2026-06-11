@extends('layouts.admin.app')

@section('title', 'Issue Certificate')
@section('header', 'Issue Certificate')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.certificates.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Certificate Type</label>
                        <select class="form-select" name="certificate_type" required>
                            <option value="membership">Membership Certificate</option>
                            <option value="appreciation">Certificate of Appreciation</option>
                            <option value="participation">Certificate of Participation</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Issued To (Name)</label>
                        <input type="text" class="form-control" name="issued_to_name" placeholder="Enter recipient name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Purpose / Description (Optional)</label>
                        <textarea class="form-control" name="purpose" rows="3" placeholder="e.g. For outstanding contribution to social work..."></textarea>
                    </div>

                    <!-- Optional Member Link -->
                    <div class="mb-4">
                        <label class="form-label">Link to Member ID (Optional)</label>
                        <input type="number" class="form-control" name="member_id" placeholder="Enter Member ID if applicable">
                        <div class="form-text">Leave blank if issuing to a non-member or visitor.</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.certificates.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Generate Certificate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
