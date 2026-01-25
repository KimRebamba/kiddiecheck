<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>.form-signin{max-width:420px;margin:auto}</style>
	</head>
<body class="container py-5">
	<h1 class="mb-4">Welcome back</h1>
	<form method="POST" action="{{ route('login') }}" class="form-signin">
		@csrf
		<div class="mb-3">
			<label class="form-label">Email</label>
			<input class="form-control" type="email" name="email" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Password</label>
			<input class="form-control" type="password" name="password" required>
		</div>
		<div class="mb-3 form-check">
			<input class="form-check-input" type="checkbox" name="remember" id="remember">
			<label class="form-check-label" for="remember">Remember me</label>
		</div>
		<button class="btn btn-primary w-100" type="submit">Login</button>
		<div class="mt-3">
			<a class="btn btn-link" href="{{ route('register') }}">Create an account</a>
		</div>
	</form>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
