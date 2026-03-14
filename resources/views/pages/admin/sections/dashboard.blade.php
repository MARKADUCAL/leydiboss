{{-- pages/admin/sections/dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="admin-dashboard" style="padding: 2rem;">
        <h1>Admin Dashboard</h1>
        <p style="margin-top: 0.5rem; color: #666;">Welcome back{{ $admin ? ', ' . $admin->name : '' }}.</p>
    </div>
@endsection
