@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Create Section</h1>
  <div class="ms-auto">
    <a href="{{ route('teacher.sections') }}" class="btn btn-outline-secondary">Back</a>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Section Information</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('teacher.sections.store') }}" method="POST">
          @csrf
          
          <div class="mb-3">
            <label for="name" class="form-label">Section Name *</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required maxlength="255">
            @error('name')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Create Section</button>
            <a href="{{ route('teacher.sections') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap');

:root {
  --violet:      #845EC2;
  --violet-soft: #EDE4FF;
  --violet-bg:   #F8F4FF;
  --coral:       #FF6B8A;
  --coral-soft:  #FFE0E8;
  --mint:        #52C27B;
  --mint-soft:   #D4F5E2;
  --text:        #2D2040;
  --text-muted:  #8A7A99;
  --radius:      14px;
  --shadow:      0 4px 20px rgba(100,60,160,0.09);
}

body { font-family: 'Nunito', sans-serif !important; background: var(--violet-bg); color: var(--text); }

/* ── PAGE HEADER ── */
.h3.fw-bold {
  font-family: 'Baloo 2', cursive !important; font-size: 1.6rem !important;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.page-subhead { font-size: 0.82rem; font-weight: 700; color: var(--text-muted); margin-top: 2px; }

/* ── BACK BUTTON ── */
.btn-ghost-back {
  font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.8rem;
  background: white; color: var(--text-muted); border: 1.5px solid #E8E0F0;
  border-radius: 10px; padding: 6px 14px; text-decoration: none;
  display: inline-flex; align-items: center; transition: all 0.18s;
}
.btn-ghost-back:hover { background: var(--violet-soft); color: var(--violet); border-color: var(--violet-soft); }

/* ── CARD ── */
.card {
  border: none !important; border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important; overflow: hidden;
  animation: fadeUp 0.4s ease both;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }

.card-header {
  padding: 13px 18px !important; display: flex; align-items: center; gap: 10px;
  border-bottom: 2px solid #F0E8FF !important;
}
.card-header h5 {
  font-family: 'Baloo 2', cursive !important;
  font-size: 0.95rem !important; font-weight: 800 !important; color: var(--text) !important; margin: 0 !important;
}
.header-violet { background: var(--violet-bg) !important; border-left: 4px solid var(--violet) !important; }

.section-icon {
  width: 30px; height: 30px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;
}
.si-violet { background: var(--violet-soft); }

.card-body { padding: 22px 22px 24px !important; }

/* ── FORM ── */
.form-label-custom {
  font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
  letter-spacing: 0.07em; color: var(--text-muted);
  display: block; margin-bottom: 7px;
}
.req { color: var(--coral); }

.form-control-custom {
  width: 100%; padding: 10px 14px;
  font-family: 'Nunito', sans-serif; font-size: 0.9rem; font-weight: 700; color: var(--text);
  background: #FDFBFF; border: 1.5px solid #E8E0F0; border-radius: 10px;
  outline: none; transition: border-color 0.18s, box-shadow 0.18s;
  box-sizing: border-box;
}
.form-control-custom::placeholder { color: #C0B0D8; font-weight: 600; }
.form-control-custom:focus {
  border-color: var(--violet);
  box-shadow: 0 0 0 3px rgba(132,94,194,0.12);
}
.form-control-custom.is-invalid { border-color: var(--coral) !important; }

.field-error {
  margin-top: 6px; font-size: 0.78rem; font-weight: 700; color: #c0294a;
}

/* ── BUTTONS ── */
.btn-primary-grad {
  font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.88rem;
  background: #845EC2;
  color: white; border: none; border-radius: 10px; padding: 9px 22px;
  box-shadow: 0 3px 12px rgba(132,94,194,0.28);
  display: inline-flex; align-items: center; transition: all 0.18s; cursor: pointer;
}
.btn-primary-grad:hover { background: #6e4aab; transform: translateY(-1px); box-shadow: 0 5px 16px rgba(132,94,194,0.38); color: white; }

.btn-ghost-cancel {
  font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.85rem;
  background: #F0E8FF; color: var(--text-muted); border: none;
  border-radius: 10px; padding: 9px 18px; text-decoration: none;
  display: inline-flex; align-items: center; transition: all 0.18s;
}
.btn-ghost-cancel:hover { background: var(--violet-soft); color: var(--violet); }
</style>
@endsection
