@extends('layouts.admin.app')

@section('title', 'Record Donation')
@section('header', 'Record Donation')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.donations.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Donor Name</label>
                            <input type="text" class="form-control" name="donor_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Amount (₹)</label>
                            <input type="number" class="form-control" name="amount" min="1" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Donation Type</label>
                            <select class="form-select" name="donation_type" required>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Ref / ID (Optional)</label>
                            <input type="text" class="form-control" name="payment_reference">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.donations.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Donation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
