@extends('teacher.layout')

@section('content')

<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0 fw-bold">Families</h1>
</div>

@if($families->isEmpty())
  <div class="empty-state">
    <div class="empty-icon">👨‍👩‍👧‍👦</div>
    <h5>No Families Found</h5>
    <p>No families with assigned students have been added yet.</p>
  </div>
@else
  <div class="row g-3">
    @foreach($families as $family)
      <div class="col-md-6 col-lg-4">
        <div class="family-card card" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#familyModal{{ $family->user_id }}">
          <div class="card-body">

            <div class="family-card-top">
              <div>
                <h5 class="card-title mb-0">{{ $family->family_name }}</h5>
                <span class="student-pill">
                  <i class="fas fa-user-friends me-1"></i>
                  {{ $family->students->count() }} {{ Str::plural('student', $family->students->count()) }}
                </span>
              </div>
            </div>

            <div class="family-info-grid">
              <div class="info-item">
                <span class="info-label"><i class="fas fa-map-marker-alt me-1"></i>Address</span>
                <span class="info-value">{{ $family->home_address }}</span>
              </div>
              <div class="info-item">
                <span class="info-label"><i class="fas fa-phone-alt me-1"></i>Emergency</span>
                <span class="info-value">{{ $family->emergency_contact }} · {{ $family->emergency_phone }}</span>
              </div>
            </div>

            <button class="btn btn-sm btn-outline-primary mt-3"
              onclick="event.stopPropagation();"
              data-bs-toggle="modal"
              data-bs-target="#familyModal{{ $family->user_id }}">
              <i class="fas fa-eye me-1"></i>View Details
            </button>

          </div>
        </div>
      </div>

      <!-- Family Modal -->
      <div class="modal fade" id="familyModal{{ $family->user_id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <div>
                <h5 class="modal-title">{{ $family->family_name }}</h5>
                <span class="modal-subtitle">{{ $family->students->count() }} {{ Str::plural('student', $family->students->count()) }} assigned</span>
              </div>
              <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

              <!-- Family Info -->
              <div class="modal-section">
                <div class="modal-section-title">
                  <i class="fas fa-home me-2"></i>Family Information
                </div>
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="info-block">
                      <div class="info-block-label">Family Name</div>
                      <div class="info-block-value">{{ $family->family_name }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-block">
                      <div class="info-block-label">Home Address</div>
                      <div class="info-block-value">{{ $family->home_address }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-block">
                      <div class="info-block-label">Emergency Contact</div>
                      <div class="info-block-value">{{ $family->emergency_contact }}</div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-block">
                      <div class="info-block-label">Emergency Phone</div>
                      <div class="info-block-value">{{ $family->emergency_phone }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="modal-divider"></div>

              <!-- Assigned Students -->
              <div class="modal-section">
                <div class="modal-section-title">
                  <i class="fas fa-child me-2"></i>Assigned Students
                </div>
                @if($family->students->isEmpty())
                  <p class="text-muted" style="font-size:0.88rem; font-weight:600; font-style:italic;">No students assigned to this family.</p>
                @else
                  <div class="table-responsive">
                    <table class="table table-sm">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Age</th>
                          <th>Section</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($family->students as $student)
                          @php
                            $dob = is_string($student->date_of_birth) ? \Carbon\Carbon::parse($student->date_of_birth) : $student->date_of_birth;
                            $age = $dob ? (int)$dob->diffInYears(now()) : 'N/A';
                          @endphp
                          <tr>
                            <td>
                              <span class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</span>
                            </td>
                            <td>{{ $age }} yrs</td>
                            <td><span class="badge bg-primary">{{ optional($student->section)->name ?? 'N/A' }}</span></td>
                            <td>
                              <a href="{{ route('teacher.student', $student->student_id) }}" class="btn btn-xs btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View
                              </a>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endif
              </div>

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Close</button>
              <a href="{{ route('teacher.reports') }}" class="btn btn-primary">
                <i class="fas fa-chart-bar me-1"></i>View Reports
              </a>
            </div>

          </div>
        </div>
      </div>

    @endforeach
  </div>
@endif

<style>
@import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap');

:root {
  --violet:      #845EC2;
  --violet-soft: #EDE4FF;
  --violet-bg:   #F8F4FF;
  --teal:        #2EC4B6;
  --teal-soft:   #C8F4F1;
  --coral:       #FF6B8A;
  --coral-soft:  #FFE0E8;
  --mint:        #52C27B;
  --mint-soft:   #D4F5E2;
  --lemon:       #F9C74F;
  --lemon-soft:  #FFF6CC;
  --sky:         #4EA8DE;
  --sky-soft:    #D6EEFF;
  --peach:       #FF9A76;
  --text:        #2D2040;
  --text-muted:  #8A7A99;
  --radius:      14px;
  --shadow:      0 4px 20px rgba(100,60,160,0.09);
}

body { font-family: 'Nunito', sans-serif !important; background: var(--violet-bg); color: var(--text); }

/* ── PAGE TITLE ── */
.h3.fw-bold {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1.7rem !important;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* ── FAMILY CARDS ── */
.family-card {
  border: none !important;
  border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important;
  transition: transform 0.2s, box-shadow 0.2s;
  overflow: hidden;
  animation: fadeUp 0.4s ease both;
  background: white !important;
}
.family-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 32px rgba(100,60,160,0.14) !important;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }

/* top accent bar */
.family-card::before {
  content: ''; display: block; height: 5px; width: 100%;
}
.col-md-6:nth-child(6n+1) .family-card::before { background: linear-gradient(90deg, var(--violet), var(--coral)); }
.col-md-6:nth-child(6n+2) .family-card::before { background: linear-gradient(90deg, var(--teal),   var(--sky));   }
.col-md-6:nth-child(6n+3) .family-card::before { background: linear-gradient(90deg, var(--coral),  var(--peach)); }
.col-md-6:nth-child(6n+4) .family-card::before { background: linear-gradient(90deg, var(--mint),   var(--teal));  }
.col-md-6:nth-child(6n+5) .family-card::before { background: linear-gradient(90deg, var(--lemon),  var(--peach)); }
.col-md-6:nth-child(6n+6) .family-card::before { background: linear-gradient(90deg, var(--sky),    var(--violet));}

/* stagger */
.col-md-6:nth-child(1) .family-card { animation-delay: 0.05s; }
.col-md-6:nth-child(2) .family-card { animation-delay: 0.10s; }
.col-md-6:nth-child(3) .family-card { animation-delay: 0.15s; }
.col-md-6:nth-child(4) .family-card { animation-delay: 0.20s; }
.col-md-6:nth-child(5) .family-card { animation-delay: 0.25s; }
.col-md-6:nth-child(6) .family-card { animation-delay: 0.30s; }

/* ── CARD TOP ── */
.family-card-top {
  display: flex; align-items: center; gap: 14px; margin-bottom: 14px;
}
.family-avatar {
  width: 48px; height: 48px; border-radius: 13px;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Baloo 2', cursive; font-size: 1.3rem; font-weight: 800;
  color: white; flex-shrink: 0;
}
.col-md-6:nth-child(6n+1) .family-avatar { background: linear-gradient(135deg, var(--violet), var(--coral)); }
.col-md-6:nth-child(6n+2) .family-avatar { background: linear-gradient(135deg, var(--teal),   var(--sky));   }
.col-md-6:nth-child(6n+3) .family-avatar { background: linear-gradient(135deg, var(--coral),  var(--peach)); }
.col-md-6:nth-child(6n+4) .family-avatar { background: linear-gradient(135deg, var(--mint),   var(--teal));  }
.col-md-6:nth-child(6n+5) .family-avatar { background: linear-gradient(135deg, var(--lemon),  var(--peach)); }
.col-md-6:nth-child(6n+6) .family-avatar { background: linear-gradient(135deg, var(--sky),    var(--violet));}

.card-title {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1rem !important; font-weight: 800 !important; color: var(--text) !important;
}
.student-pill {
  display: inline-block; margin-top: 3px;
  font-size: 0.73rem; font-weight: 700; color: var(--text-muted);
}

/* ── INFO GRID ── */
.family-info-grid { display: flex; flex-direction: column; gap: 6px; }
.info-item { display: flex; flex-direction: column; gap: 1px; }
.info-label { font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); }
.info-value { font-size: 0.82rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* ── BUTTONS ── */
.btn {
  font-family: 'Nunito', sans-serif !important;
  font-weight: 800 !important; border-radius: 10px !important;
  transition: all 0.18s !important; font-size: 0.78rem !important;
}
.btn-primary {
  background: linear-gradient(135deg, var(--violet), var(--coral)) !important;
  border: none !important; color: white !important;
  box-shadow: 0 3px 10px rgba(132,94,194,0.25) !important;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(132,94,194,0.35) !important; }
.btn-outline-primary {
  color: var(--violet) !important; border: 1.5px solid var(--violet-soft) !important; background: white !important;
}
.btn-outline-primary:hover { background: var(--violet-soft) !important; }
.btn-ghost {
  background: #F0E8FF !important; color: var(--text-muted) !important; border: none !important;
}
.btn-ghost:hover { background: var(--violet-soft) !important; color: var(--violet) !important; }
.btn-xs { padding: 4px 11px !important; font-size: 0.74rem !important; }

/* ── BADGES ── */
.badge { font-size: 0.7rem !important; font-weight: 800 !important; padding: 4px 10px !important; border-radius: 20px !important; }
.badge.bg-primary { background: var(--violet-soft) !important; color: #5a3e8a !important; }

/* ── MODAL ── */
.modal-content {
  border: none !important;
  border-radius: 18px !important;
  box-shadow: 0 20px 60px rgba(100,60,160,0.18) !important;
  overflow: hidden;
}
.modal-header {
  background: linear-gradient(135deg, #EDE4FF 0%, #D6EEFF 100%) !important;
  border-bottom: 2px solid #F0E8FF !important;
  padding: 20px 24px !important;
  display: flex; align-items: center; gap: 14px;
}
.modal-avatar {
  width: 48px; height: 48px; border-radius: 13px; flex-shrink: 0;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  display: flex; align-items: center; justify-content: center;
  font-family: 'Baloo 2', cursive; font-size: 1.3rem; font-weight: 800; color: white;
  box-shadow: 0 4px 12px rgba(132,94,194,0.3);
}
.modal-title {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1.1rem !important; font-weight: 800 !important; color: var(--text) !important;
  margin-bottom: 0 !important;
}
.modal-subtitle {
  font-size: 0.76rem; font-weight: 700; color: var(--text-muted); display: block; margin-top: 2px;
}
.modal-body { padding: 24px !important; }
.modal-footer {
  background: #FDFBFF !important;
  border-top: 2px solid #F0E8FF !important;
  padding: 14px 24px !important; gap: 8px;
}

/* ── MODAL SECTIONS ── */
.modal-section { margin-bottom: 4px; }
.modal-section-title {
  font-family: 'Baloo 2', cursive;
  font-size: 0.9rem; font-weight: 800; color: var(--violet);
  margin-bottom: 14px;
  display: flex; align-items: center;
}
.modal-divider {
  height: 2px; background: #F0E8FF; border-radius: 2px; margin: 20px 0;
}

/* ── INFO BLOCKS ── */
.info-block {
  background: #FDFBFF; border: 1.5px solid #F0E8FF;
  border-radius: 10px; padding: 12px 14px;
}
.info-block-label {
  font-size: 0.68rem; font-weight: 800; text-transform: uppercase;
  letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 4px;
}
.info-block-value { font-size: 0.9rem; font-weight: 700; color: var(--text); }

/* ── MODAL TABLE ── */
.table { margin-bottom: 0 !important; }
.table thead th {
  font-size: 0.7rem !important; font-weight: 800 !important;
  text-transform: uppercase; letter-spacing: 0.07em;
  color: var(--text-muted) !important; background: #FDFBFF !important;
  border-bottom: 2px solid #F0E8FF !important;
  padding: 9px 12px !important; white-space: nowrap;
}
.table tbody tr { border-bottom: 1px solid #F9F5FF !important; transition: background 0.15s; }
.table tbody tr:last-child { border-bottom: none !important; }
.table tbody tr:hover { background: #FDFBFF !important; }
.table tbody td { padding: 10px 12px !important; font-size: 0.85rem; vertical-align: middle !important; border: none !important; }
.fw-semibold { font-weight: 800 !important; font-size: 0.87rem; color: var(--text); }

.table-avatar {
  width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-weight: 900; font-size: 0.8rem; color: white;
  background: linear-gradient(135deg, var(--violet), var(--coral));
}

/* ── EMPTY STATE ── */
.empty-state {
  text-align: center; padding: 60px 20px;
  background: white; border-radius: var(--radius);
  box-shadow: var(--shadow); animation: fadeUp 0.4s ease;
}
.empty-icon { font-size: 3rem; margin-bottom: 12px; }
.empty-state h5 {
  font-family: 'Baloo 2', cursive; font-size: 1.2rem; font-weight: 800;
  color: var(--text); margin-bottom: 6px;
}
.empty-state p { color: var(--text-muted); font-size: 0.9rem; font-weight: 600; }
</style>

@endsection