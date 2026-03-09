<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Family · KiddieCheck</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; }

    body {
      margin: 0;
      background: #f5f5f5;
      color: #333;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .family-header {
      background: white;
      border-bottom: 2px solid #e5e5e5;
      padding: 0;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .header-container {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      padding: 0 20px;
      height: 70px;
    }

    /* ── Brand ── */
    .brand {
      font-size: 1.5rem;
      font-weight: 800;
      text-decoration: none;
      margin-right: 3rem;
      letter-spacing: -0.02em;
      flex-shrink: 0;
    }
    .brand span:nth-child(4n+1) { color: #3B82F6; }
    .brand span:nth-child(4n+2) { color: #F59E0B; }
    .brand span:nth-child(4n+3) { color: #EF4444; }
    .brand span:nth-child(4n+0) { color: #10B981; }

    /* ── Nav Links ── */
    .nav-links {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      flex: 1;
    }

    .nav-link {
      text-decoration: none;
      color: #6b7280;
      font-weight: 600;
      font-size: 0.95rem;
      padding: 0.6rem 1.2rem;
      border-radius: 8px;
      transition: all 0.2s;
    }
    .nav-link:hover  { color: #667eea; background: #f3f4f6; }
    .nav-link.active { color: #667eea; background: transparent; font-weight: 700; }

    /* ── Profile Section ── */
    .profile-section {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-left: auto;
    }

    .profile-menu { position: relative; }

    .profile-button {
      background: transparent;
      border: none;
      color: #6b7280;
      font-weight: 600;
      font-size: 0.9rem;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
    }
    .profile-button:hover { border-color: #667eea; color: #667eea; background: #f9fafb; }

    /* Avatar circle */
    .profile-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: linear-gradient(135deg, #ff6b9d, #ffb3d1);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: 900;
      color: white;
      flex-shrink: 0;
    }

    /* ── Custom Dropdown (NOT .dropdown-menu to avoid Bootstrap conflict) ── */
    .profile-dropdown {
      position: absolute;
      right: 0;
      top: calc(100% + 8px);
      background: white;
      border: 2px solid #e5e5e5;
      border-radius: 12px;
      padding: 0.5rem;
      min-width: 210px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: opacity 0.2s, transform 0.2s, visibility 0.2s;
      box-shadow: 0 10px 25px rgba(0,0,0,0.12);
      z-index: 9999;
    }

    .profile-dropdown.open {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .pd-divider {
      border: none;
      border-top: 1px solid #f3f4f6;
      margin: 0.3rem 0;
    }

    .pd-label {
      font-size: 0.72rem;
      font-weight: 700;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      padding: 0.4rem 1rem 0.2rem;
      display: block;
    }

    .pd-item {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      width: 100%;
      text-align: left;
      background: transparent;
      border: none;
      color: #374151;
      font-weight: 600;
      font-size: 0.88rem;
      padding: 0.65rem 1rem;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.15s, color 0.15s;
    }
    .pd-item:hover { background: #f3f4f6; color: #667eea; }
    .pd-item.danger { color: #ef4444; }
    .pd-item.danger:hover { background: #fef2f2; color: #dc2626; }

    .pd-item svg {
      width: 16px;
      height: 16px;
      flex-shrink: 0;
      opacity: 0.65;
    }

    /* ── Main ── */
    .main { max-width: 1400px; margin: 0 auto; padding: 2rem 20px; }

    .alert { background: white; border: 2px solid #e5e5e5; border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; font-weight: 500; }
    .alert-success { background: #f0fdf4; border-color: #86efac; color: #166534; }
    .alert-error   { background: #fef2f2; border-color: #fca5a5; color: #991b1b; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .header-container { padding: 15px; height: auto; flex-wrap: wrap; gap: 0.5rem; }
      .brand { margin-right: auto; font-size: 1.2rem; }
      .nav-links { width: 100%; justify-content: flex-start; margin-top: 4px; gap: 0.3rem; overflow-x: auto; }
      .nav-link { font-size: 0.85rem; padding: 0.5rem 0.9rem; white-space: nowrap; }
      .profile-section { margin-left: auto; }
      .profile-button { font-size: 0.85rem; padding: 0.4rem 0.8rem; }
    }
  </style>
</head>
<body>

@php
  $navFamily  = DB::table('families')->where('user_id', Auth::id())->first();
  $navStudent = null;

  if ($navFamily) {
    $students = DB::table('students')
      ->where('family_id', $navFamily->user_id)
      ->orderBy('date_of_birth', 'desc')
      ->get();

    $now = \Carbon\Carbon::now();

    // Prefer a child who currently has an active assessment window
    foreach ($students as $s) {
      $hasActivePeriod = DB::table('assessment_periods')
        ->where('student_id', $s->student_id)
        ->where('status', '!=', 'completed')
        ->where('start_date', '<=', $now)
        ->where('end_date', '>=', $now)
        ->exists();

      if ($hasActivePeriod) {
        $navStudent = $s;
        break;
      }
    }

    // Fallback: first child if none currently has an active assessment
    if (!$navStudent) {
      $navStudent = $students->first();
    }
  }
@endphp

  <header class="family-header">
    <div class="header-container">

      {{-- Brand --}}
      <a class="brand" href="{{ route('family.index') }}">
        @foreach(str_split('KiddieCheck') as $l)<span>{{ $l }}</span>@endforeach
      </a>

      {{-- Nav Links --}}
      <div class="nav-links">
        <a class="nav-link {{ request()->routeIs('family.index') ? 'active' : '' }}" href="{{ route('family.index') }}">Home</a>
        <a class="nav-link {{ request()->routeIs('family.index') ? 'active' : '' }}" href="{{ route('family.index') }}#family-children">Children</a>
        <a class="nav-link {{ request()->routeIs('family.tests.*') ? 'active' : '' }}"
           href="{{ $navStudent && $navStudent->student_id ? route('family.tests.start.show', $navStudent->student_id) : route('family.index') }}">Current Test</a>
        <a class="nav-link {{ request()->routeIs('family.help') ? 'active' : '' }}" href="{{ route('family.help') }}">Help</a>

      {{-- Profile Dropdown --}}
      <div class="profile-section">
        <div class="profile-menu">
          <a href="{{ $navStudent && $navStudent->student_id ? route('family.student.profile', $navStudent->student_id) : route('family.index') }}" class="profile-button" tabindex="0" id="profileBtn">
            <span>{{ optional(Auth::user())->username ?? 'Account' }}</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </a>

          {{-- Uses .profile-dropdown NOT .dropdown-menu to avoid Bootstrap override --}}
          <div class="profile-dropdown" id="profileDropdown">

            <span class="pd-label">Account</span>
            <a href="{{ route('family.profile') }}" class="pd-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              Family Profile
            </a>

            <hr class="pd-divider">
            <a href="{{ route('logout') }}" class="pd-item danger">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
              Logout
            </a>

          </div>
        </div>
      </div>

    </div>
  </header>

  <main class="main">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @yield('content')
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var btn      = document.getElementById('profileBtn');
      var dropdown = document.getElementById('profileDropdown');

      if (btn && dropdown) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          dropdown.classList.toggle('open');
        });

        document.addEventListener('click', function (e) {
          if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
          }
        });
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>