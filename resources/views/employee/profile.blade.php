@extends('layouts.employee')

@section('title', 'Profil Saya')

@push('styles')
<style>
    /* Apply Poppins font */
    body {
        font-family: 'Poppins', sans-serif;
    }
    
    .nav-tabs .nav-link {
        color: #2c3e50 !important;
        background-color: #ecf0f1;
        border: 1px solid #bdc3c7;
        border-bottom: none;
        cursor: pointer;
        padding: 0.5rem 1rem;
        margin-right: 0.25rem;
        font-weight: 500;
        text-decoration: none;
        font-family: 'Poppins', sans-serif;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: #bdc3c7 #bdc3c7 #bdc3c7;
        background-color: #d5dbdb;
        color: #2c3e50;
    }
    
    .nav-tabs .nav-link.active {
        color: #fff !important;
        background-color: #2c3e50 !important;
        border-color: #2c3e50 #2c3e50 #fff;
        border-bottom: 1px solid #fff;
        font-weight: 600;
    }
    
    .tab-content {
        border: 1px solid #bdc3c7;
        border-top: none;
        padding: 1rem;
        background-color: #fff;
    }
    
    .tab-pane {
        display: none;
    }
    
    .tab-pane.show.active {
        display: block;
    }
    
    /* Ensure tab text is visible */
    .nav-tabs .nav-link {
        color: #2c3e50 !important;
        font-size: 0.9rem;
    }
    
    .nav-tabs .nav-link.active {
        color: #fff !important;
    }
    
    .nav-tabs .nav-link i {
        margin-right: 0.5rem;
        font-size: 1rem;
    }
    
    /* Font Awesome styling */
    .fas {
        display: inline-block;
        font-style: normal;
        font-variant: normal;
        text-rendering: auto;
        line-height: 1;
    }
    
    /* Form elements with Poppins */
    .form-control, .form-select, .btn, .card-title, .card-header {
        font-family: 'Poppins', sans-serif;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Profil Saya
                    </h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                                <i class="fas fa-user me-2"></i>Edit Profil
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                                <i class="fas fa-lock me-2"></i>Ubah Password
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Edit Profil Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <form action="{{ route('employee.profile.update') }}" method="POST" class="mt-4" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 text-center mb-3">
                                        <div class="mb-2">
                                            @if($user->profile_photo)
                                                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Foto Profil" class="img-thumbnail rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120" alt="Foto Profil" class="img-thumbnail rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                            @endif
                                        </div>
                                        <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" name="profile_photo" id="profile_photo" accept="image/*">
                                        <small class="text-muted">Format: jpg, jpeg, png. Maks 2MB.</small>
                                        @error('profile_photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nama Lengkap</label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="branch_id" class="form-label">Cabang</label>
                                                    <select class="form-select @error('branch_id') is-invalid @enderror" 
                                                            id="branch_id" name="branch_id">
                                                        <option value="">Pilih Cabang</option>
                                                        @foreach($branches as $branch)
                                                            <option value="{{ $branch->id }}" 
                                                                    {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                                                {{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('branch_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="role" class="form-label">Role</label>
                                                    <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" readonly>
                                                    <small class="text-muted">Role tidak dapat diubah</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Ubah Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                            <form action="{{ route('employee.profile.password') }}" method="POST" class="mt-4">
                                @csrf
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Password Saat Ini</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">Password Baru</label>
                                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                                   id="new_password" name="new_password" required>
                                            <small class="text-muted">Minimal 8 karakter</small>
                                            @error('new_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                            <input type="password" class="form-control" 
                                                   id="new_password_confirmation" name="new_password_confirmation" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key me-2"></i>Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Akun -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID Karyawan:</strong> {{ $user->id }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Cabang:</strong> {{ $user->branch->name ?? 'Belum Ditentukan' }}</p>
                            <p><strong>Bergabung Sejak:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                            <p><strong>Terakhir Update:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Debug: Log tab functionality
    console.log('Profile page loaded');
    
    // Test tab functionality
    $('#password-tab').on('click', function() {
        console.log('Password tab clicked');
    });

    // Manual tab switching as fallback
    $('.nav-tabs .nav-link').on('click', function(e) {
        e.preventDefault();
        var target = $(this).data('bs-target');
        
        // Remove active class from all tabs and content
        $('.nav-tabs .nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');
        
        // Add active class to clicked tab
        $(this).addClass('active');
        
        // Show target content
        $(target).addClass('show active');
        
        console.log('Tab switched to:', target);
    });

    // Check if tab text is visible
    setTimeout(function() {
        var profileTab = $('#profile-tab');
        var passwordTab = $('#password-tab');
        
        console.log('Profile tab text:', profileTab.text());
        console.log('Password tab text:', passwordTab.text());
        
        // Force text visibility
        profileTab.css('color', '#2c3e50');
        passwordTab.css('color', '#2c3e50');
        
        // Check if Font Awesome is loaded
        var testIcon = $('<i class="fas fa-user"></i>');
        $('body').append(testIcon);
        var iconWidth = testIcon.width();
        testIcon.remove();
        
        if (iconWidth > 0) {
            console.log('Font Awesome loaded successfully');
        } else {
            console.log('Font Awesome not loaded, using fallback');
            // Apply fallback icons
            $('.fas.fa-user').html('👤');
            $('.fas.fa-lock').html('🔒');
            $('.fas.fa-save').html('💾');
            $('.fas.fa-key').html('🔑');
            $('.fas.fa-info-circle').html('ℹ️');
        }
    }, 500);

    // Password strength indicator
    $('#new_password').on('input', function() {
        var password = $(this).val();
        var strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        var strengthText = '';
        var strengthClass = '';
        
        switch(strength) {
            case 0:
            case 1:
                strengthText = 'Sangat Lemah';
                strengthClass = 'text-danger';
                break;
            case 2:
                strengthText = 'Lemah';
                strengthClass = 'text-warning';
                break;
            case 3:
                strengthText = 'Sedang';
                strengthClass = 'text-info';
                break;
            case 4:
                strengthText = 'Kuat';
                strengthClass = 'text-success';
                break;
            case 5:
                strengthText = 'Sangat Kuat';
                strengthClass = 'text-success';
                break;
        }
        
        // Remove existing strength indicator
        $('.password-strength').remove();
        
        // Add new strength indicator
        if (password.length > 0) {
            $(this).after('<small class="password-strength ' + strengthClass + '">Kekuatan: ' + strengthText + '</small>');
        }
    });
});
</script>
@endpush 