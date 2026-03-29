{{-- pages/admin/sections/dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="admin-dashboard" style="padding: 2rem;">
        <h1>Admin Dashboard</h1>
        <p style="margin-top: 0.5rem; color: #666;">Welcome back{{ $admin ? ', ' . $admin->name : '' }}.</p>

        {{-- Dashboard Stats Cards --}}
        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
            {{-- Total Bookings --}}
            <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div
                        style="background: #2563eb; color: white; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        📅
                    </div>
                    <div>
                        <p style="margin: 0; font-size: 0.875rem; color: #666; text-transform: uppercase; font-weight: 600;">
                            Total Bookings</p>
                        {{-- <p style="margin: 0.5rem 0 0 0; font-size: 2rem; font-weight: bold; color: #1f2937;">
                            {{ $totalBookings }}</p> --}}
                        <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: #999;">All time bookings</p>
                    </div>
                </div>
            </div>

            {{-- Pending Bookings --}}
            <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div
                        style="background: #f59e0b; color: white; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        ⏳
                    </div>
                    <div>
                        <p
                            style="margin: 0; font-size: 0.875rem; color: #666; text-transform: uppercase; font-weight: 600;">
                            Pending Bookings</p>
                        {{-- <p style="margin: 0.5rem 0 0 0; font-size: 2rem; font-weight: bold; color: #1f2937;">
                            {{ $pendingBookings }}</p> --}}
                        <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: #999;">Awaiting confirmation</p>
                    </div>
                </div>
            </div>

            {{-- Completed Bookings --}}
            <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div
                        style="background: #10b981; color: white; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        ✅
                    </div>
                    <div>
                        <p
                            style="margin: 0; font-size: 0.875rem; color: #666; text-transform: uppercase; font-weight: 600;">
                            Completed Bookings</p>
                        {{-- <p style="margin: 0.5rem 0 0 0; font-size: 2rem; font-weight: bold; color: #1f2937;">
                            {{ $completedBookings }}</p> --}}
                        <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: #999;">Finished bookings</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
