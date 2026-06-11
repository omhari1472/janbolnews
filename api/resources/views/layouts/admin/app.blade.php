<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Admin Dashboard') - Drishika Foundation</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="{{ asset('img/logo.png') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: var(--bg-light);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--dark-blue);
            color: #fff;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }
        
        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand h3 {
            color: #fff;
            margin: 0;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        
        .sidebar-menu li {
            width: 100%;
        }
        
        .sidebar-menu a, .sidebar-menu button {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: 0.3s;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active, .sidebar-menu button:hover {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            border-left: 4px solid var(--primary-green);
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            flex-grow: 1;
            padding: 20px;
            width: calc(100% - 250px);
        }
        
        /* Top Header */
        .admin-header {
            background: #fff;
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        /* Cards */
        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            height: 100%;
            border-left: 5px solid var(--primary-blue);
        }
        
        .stat-card .icon {
            width: 60px;
            height: 60px;
            background: var(--bg-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary-blue);
            margin-right: 20px;
        }
        
        /* Tables */
        .admin-table-container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .table thead th {
            border-top: none;
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-weight: 600;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-pending { background-color: #fff3cd; color: #ffc107; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }

    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h3>Drishika<span style="color: var(--primary-green);">Seva</span></h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.members.index') }}" class="{{ request()->routeIs('admin.members.*') ? 'active' : '' }}"><i class="fa fa-users"></i> Members</a></li>
            <li><a href="#"><i class="fa fa-hand-holding-usd"></i> Donations</a></li>
            <li><a href="#"><i class="fa fa-certificate"></i> Certificates</a></li>
            <li><a href="#"><i class="fa fa-images"></i> Gallery</a></li>
            <li><a href="#"><i class="fa fa-envelope"></i> Enquiries</a></li>
            <li>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit"><i class="fa fa-sign-out-alt"></i> Logout</button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Header -->
        <div class="admin-header">
            <h4 class="m-0">@yield('header', 'Dashboard')</h4>
            <div class="user-profile d-flex align-items-center">
                <div class="text-end me-3">
                    <span class="d-block fw-bold">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <small class="text-muted">Super Admin</small>
                </div>
                <!-- Placeholder for Admin Image -->
                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
            </div>
        </div>

        <!-- Content -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')

    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>