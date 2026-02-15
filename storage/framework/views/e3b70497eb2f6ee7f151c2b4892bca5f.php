<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin · KiddieCheck</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    :root{
      --bg:#4747b9;
      --pill:#5855df;
      --text:#f5f5f5;
      --text-soft:rgba(249,217,118,.85);
      --avatar:#7b78f9;
      --hover:#5855df;
      --yellow:#f9d976;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{margin:0;background:var(--bg);color:var(--text);font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji"}
    .admin-header{display:flex;align-items:center;gap:18px;padding:10px 16px;position:relative;z-index:10}
    .admin-logo{height:44px;display:block}
    .pill{
      display:inline-block;text-decoration:none;
      background:#4346b7;color:#f5f5f5;
      padding:2px 16px;border-radius:12px;font-weight:700;
      letter-spacing:.025rem;
      font-size:1.55rem;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
      
    }
    .pill:hover{opacity:.95;color:#f9d976}
    .pill-italic{font-style:italic}
    .check{margin-left:8px; opacity:.8}
    .spacer{flex:1}
    .avatar{
      width:40px;height:40px;border-radius:12px; background:#5855df;
      display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700; text-decoration:none;
      overflow:hidden;    border: none;
    }
    .profile{position:relative}
    .menu{position:absolute; right:0; top:calc(100% + 8px); background: #5855df; color:var(--text); border-radius:12px;  padding:8px; min-width:200px; display:none; z-index:9999}
    .profile.open .menu{display:block}
    .menu-item{display:block; width:100%; text-align:left; background:transparent; color:var(--text); font-weight:700; border:0; padding:10px 12px; border-radius:10px; cursor:pointer; text-decoration:none}
    .menu-item:hover{background: #504ec8}
    .main{padding:16px}
    .card{background:#fff;color:#333;border-radius:12px;padding:12px;}

    td:last-child {
      text-align: center;
    justify-content: center; 
    align-items: center;     
    height: 100%;        
    padding: 50px; 
    
}

.btn-outline-secondary {
    --bs-btn-color: var(--text-soft);
    --bs-btn-border-color: var(--text-soft);
    --bs-btn-hover-color: black;
    --bs-btn-hover-bg: var(--text-soft);
    --bs-btn-hover-border-color: var(--text-soft);
    --bs-btn-active-color: black;
    --bs-btn-active-bg: white;
    --bs-btn-active-border-color: white;
  
    --bs-btn-disabled-color: #6c757d;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color: #6c757d;
    --bs-gradient: none;
}

.btn-outline-primary {
    --bs-btn-color: #ffffffc3;
    --bs-btn-border-color: #ffffffc3;
    --bs-btn-bg: none;
    --bs-btn-hover-color: black;
    --bs-btn-hover-bg: var(--text-soft);
    --bs-btn-hover-border-color: var(--text-soft);
    --bs-btn-focus-shadow-rgb: 13, 110, 253;
    --bs-btn-active-color: black;
    --bs-btn-active-bg: white;
    --bs-btn-active-border-color: white;
    --bs-btn-disabled-color: #0d6efd;
    --bs-btn-disabled-bg: transparent;
    --bs-btn-disabled-border-color: #0d6efd;
    --bs-gradient: none;
}

.table-sm>:not(caption)>*>* {
    padding: .3rem .6rem;
    vertical-align: middle;
}

.table-hover>tbody>tr:hover>* {
    --bs-table-color-state: var(--text-soft);
    --bs-table-bg-state: var(--bs-table-hover-bg);
}

  </style>
</head>
<body>
  

  
</body>
</html>
<?php /**PATH C:\xamppkiddiecheck\htdocs\kiddiecheck\resources\views\admin\layout.blade.php ENDPATH**/ ?>