<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Absensi Optik Melati | Dashboard</title>
  
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @if(session('success'))
      <meta name="success-message" content="{{ session('success') }}">
  @endif
   <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/jqvmap/jqvmap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/summernote/summernote-bs4.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  {{-- Custom CSS for responsive tables --}}
  <style>
    /* Responsive table improvements */
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    
    .table-responsive table {
      min-width: 800px; /* Ensure minimum width for readability */
    }
    
    /* Mobile-first responsive design */
    @media (max-width: 768px) {
      .card-body {
        padding: 0.75rem;
      }
      
      .table-responsive {
        margin: 0 -0.75rem;
      }
      
      .table-responsive table {
        font-size: 0.875rem;
      }
      
      .table-responsive th,
      .table-responsive td {
        padding: 0.5rem 0.25rem;
        white-space: nowrap;
      }
      
      /* Hide less important columns on mobile */
      .table-responsive .d-none-mobile {
        display: none !important;
      }
      
      /* Make action buttons smaller on mobile */
      .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
      }
    }
    
    @media (max-width: 576px) {
      .table-responsive table {
        font-size: 0.75rem;
      }
      
      .table-responsive th,
      .table-responsive td {
        padding: 0.25rem 0.125rem;
      }
      
      /* Stack filter controls on mobile */
      .filter-row .col-md-3 {
        margin-bottom: 0.5rem;
      }
    }
    
    /* DataTables responsive improvements */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
      margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
      .dataTables_wrapper .dataTables_length,
      .dataTables_wrapper .dataTables_filter,
      .dataTables_wrapper .dataTables_info,
      .dataTables_wrapper .dataTables_paginate {
        text-align: center;
        margin-bottom: 0.5rem;
      }
      
      .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
      }
    }
    
    /* Improve table header visibility */
    .table thead th {
      background-color: #f8f9fa;
      border-bottom: 2px solid #dee2e6;
      font-weight: 600;
      position: sticky;
      top: 0;
      z-index: 10;
    }
    
    /* Better hover effects */
    .table-hover tbody tr:hover {
      background-color: rgba(0, 123, 255, 0.05);
    }
    
    /* Status badges responsive */
    .badge {
      font-size: 0.75em;
      padding: 0.25em 0.5em;
    }
    
    @media (max-width: 576px) {
      .badge {
        font-size: 0.7em;
        padding: 0.2em 0.4em;
      }
    }
  </style>

  @stack('styles') {{-- Untuk CSS spesifik halaman --}}
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ asset('image/optik-melati.png') }}" alt="AdminLTELogo" height="60" width="60">
  </div>

  @include('layouts.partials.admin_navbar')
  @include('layouts.partials.admin_sidebar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
          </div><div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">@yield('breadcrumb_item', 'Dashboard')</li>
            </ol>
          </div></div></div></div>
    <section class="content">
      <div class="container-fluid">
        @yield('content') {{-- Konten utama halaman akan dimasukkan di sini --}}
      </div></section>
    </div>
  @include('layouts.partials.admin_footer')

  <aside class="control-sidebar control-sidebar-dark">
    </aside>
  </div> 

  
<!-- jQuery -->
<script src="{{ asset('AdminLTE-3.0.1/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('AdminLTE-3.0.1/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('AdminLTE-3.0.1/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{ asset('AdminLTE-3.0.1/plugins/chart.js/Chart.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{ asset('AdminLTE-3.0.1/plugins/sparklines/sparkline.js')}}"></script>
<!-- JQVMap -->
<script src="{{ asset('AdminLTE-3.0.1/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{ asset('AdminLTE-3.0.1/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('AdminLTE-3.0.1/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{ asset('AdminLTE-3.0.1/plugins/moment/moment.min.js')}}"></script>
<script src="{{ asset('AdminLTE-3.0.1/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('AdminLTE-3.0.1/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{ asset('AdminLTE-3.0.1/plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('AdminLTE-3.0.1/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('AdminLTE-3.0.1/dist/js/adminlte.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('AdminLTE-3.0.1/dist/js/pages/dashboard.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('AdminLTE-3.0.1/dist/js/demo.js')}}"></script>

{{-- Custom JavaScript for responsive tables --}}
<script>
  $(document).ready(function() {
    // Initialize responsive behavior for all tables
    $('.table-responsive').each(function() {
      var $table = $(this);
      var $tableElement = $table.find('table');
      
      // Add responsive classes to DataTables
      if ($tableElement.hasClass('dataTable')) {
        $tableElement.addClass('table-responsive');
      }
      
      // Handle mobile view adjustments
      function adjustTableForMobile() {
        if (window.innerWidth <= 768) {
          // Hide less important columns on mobile
          $tableElement.find('th, td').each(function(index) {
            var $cell = $(this);
            var columnIndex = $cell.index();
            
            // Hide specific columns on mobile (adjust as needed)
            if (columnIndex >= 6) { // Hide columns after index 5
              $cell.addClass('d-none-mobile');
            }
          });
        } else {
          // Show all columns on desktop
          $tableElement.find('th, td').removeClass('d-none-mobile');
        }
      }
      
      // Call on load and resize
      adjustTableForMobile();
      $(window).resize(adjustTableForMobile);
    });
    
    // Improve DataTables responsive behavior
    if ($.fn.DataTable) {
      $.extend(true, $.fn.dataTable.defaults, {
        responsive: true,
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true
      });
    }
  });
</script>

{{-- Notification System JavaScript --}}
<script>
$(document).ready(function() {
    // Load notifications on page load
    loadNotifications();
    
    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
    
    // Load notifications function
    function loadNotifications() {
        $.ajax({
            url: '{{ route("admin.notifications.latest") }}',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                updateNotificationBadge(response.unread_count);
                updateNotificationList(response.notifications);
            },
            error: function(xhr, status, error) {
                console.error('Error loading notifications:', error);
            }
        });
    }
    
    // Update notification badge
    function updateNotificationBadge(count) {
        var $badge = $('#notificationBadge');
        if (count > 0) {
            $badge.text(count).show();
        } else {
            $badge.hide();
        }
    }
    
    // Update notification list
    function updateNotificationList(notifications) {
        var $list = $('#notificationList');
        $list.empty();
        
        if (notifications.length === 0) {
            $list.append('<span class="dropdown-item text-muted">Tidak ada notifikasi baru</span>');
            return;
        }
        
        notifications.forEach(function(notification) {
            let link = '#';
            if (notification.type === 'leave_request' && notification.related_id) {
                link = '/admin/leaves/' + notification.related_id;
            } else if (notification.type === 'permission_request' && notification.related_id) {
                link = '/admin/permissions/' + notification.related_id;
            }
            var notificationHtml = `
                <a href="${link}" class="dropdown-item notification-item" data-id="${notification.id}">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h6 class="dropdown-item-title mb-1">${notification.title}</h6>
                            <p class="dropdown-item-text text-muted mb-0">${notification.message}</p>
                            <small class="text-muted">${formatDate(notification.created_at)}</small>
                        </div>
                        <div class="ml-2">
                            <span class="badge badge-warning badge-sm">Baru</span>
                        </div>
                    </div>
                </a>
            `;
            $list.append(notificationHtml);
        });
    }
    
    // Format date
    function formatDate(dateString) {
        var date = new Date(dateString);
        var now = new Date();
        var diffTime = Math.abs(now - date);
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 1) {
            return 'Hari ini';
        } else if (diffDays === 2) {
            return 'Kemarin';
        } else if (diffDays <= 7) {
            return diffDays - 1 + ' hari yang lalu';
        } else {
            return date.toLocaleDateString('id-ID');
        }
    }
    
    // Handle notification click
    $(document).on('click', '.notification-item', function(e) {
        e.preventDefault();
        var notificationId = $(this).data('id');
        
        // Mark as read
        $.ajax({
            url: '{{ route("admin.notifications.read", ":id") }}'.replace(':id', notificationId),
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Reload notifications to update badge
                loadNotifications();
            },
            error: function(xhr, status, error) {
                console.error('Error marking notification as read:', error);
            }
        });
    });
});
</script>

@stack('scripts') {{-- Untuk JS spesifik halaman --}}
</body>
</html>