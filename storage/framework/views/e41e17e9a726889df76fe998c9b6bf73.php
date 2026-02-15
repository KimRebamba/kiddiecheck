<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Kiddie • Login</title>
	<style>
		:root{
			--bg:#4747b9; /* deep purple */
			--panel:#6d69f6; /* lighter purple panel */
			--slot:#5855df; /* input slot */
			--accent:#f9d976; /* warm yellow */
			--accent-soft:rgba(249,217,118,.75);
			--white: #ffffff;
		}
		*{box-sizing:border-box}
		html,body{height:100%}
		body{
			margin:0;
			background:var(--bg);
			color:var(--accent);
			font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
		}
		.wrap{
			min-height:100vh;
			display:grid;
			grid-template-columns: 1fr 1fr;
			place-items:center;
			gap:4rem;
			padding:4rem 6rem;
		}
		.brand{
			max-width:720px;
		}
		.brand .logo{display:block; max-width:520px; width:100%; height:auto; margin-bottom:1.25rem}
		.brand .tagline{
			font-size:1.75rem;
			line-height:1.35;
			color:var(--accent);
			text-shadow:0 1px 0 rgba(0,0,0,.08);
		}

		.panel{
			width:100%;
			max-width:640px;
			background:var(--panel);
			border-radius:42px;
			padding:2.25rem 2.5rem;
			
		}
		.slot{background:var(--slot); border-radius:22px; padding:.4rem .6rem; margin-bottom:1.1rem}
		.field{width:100%; background:transparent; border:0; outline:none; color:white; padding:1.1rem 1.25rem; font-size:1.15rem}
		.field::placeholder{color:white; font-style:italic}
		.actions{margin-top:.5rem}
		.btn{
			display:block; width:100%;
			background:var(--accent);
			color:#daae41; 
			font-weight:700; font-size:1.25rem;
			border:0; border-radius:26px;
			padding:1.05rem 1.25rem;
			cursor:pointer;

		}
		.btn:active{transform:translateY(1px)}
		.errors{background:#ff6b6b; color:#fff; padding:.75rem 1rem; border-radius:12px; margin-bottom:1rem}

		/* Responsive */
		@media (max-width: 1024px){
			.wrap{grid-template-columns:1fr; padding:3rem 2rem}
			.brand{order:2; text-align:center}
			.brand .logo{margin-left:auto;margin-right:auto}
		}
	</style>
</head>
<body>
	<div class="wrap">
		<section class="brand">
			<img class="logo" src="<?php echo e(asset('main-logo.svg')); ?>" alt="Kiddie logo">
			<p class="tagline">Your No. 1 Child Screening System for Schools, Teachers and Families.</p>
		</section>

		<section class="panel" aria-label="Login form">
			<?php if($errors->any()): ?>
				<div class="errors"><?php echo e($errors->first()); ?></div>
			<?php endif; ?>

			<form method="POST" action="<?php echo e(route('login')); ?>">
				<?php echo csrf_field(); ?>
				<div class="slot">
					<input class="field" type="email" name="email" placeholder="Email Address" required>
				</div>
				<div class="slot">
					<input class="field" type="password" name="password" placeholder="Password" required>
				</div>

				<div class="actions">
					<button class="btn" type="submit">Login Account</button>
				</div>
			</form>
		</section>
	</div>
</body>
</html>
<?php /**PATH C:\xamppkiddiecheck\htdocs\kiddiecheck\resources\views/auth/login.blade.php ENDPATH**/ ?>