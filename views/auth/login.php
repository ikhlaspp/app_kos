<?php
// views/auth/login.php
// Assumes $pageTitle and $appConfig are available from BaseController.
// Assumes $email is available if the form was submitted with errors.

// Define custom color palette for consistency
$paletteWhite = '#FFFFFF';
$paletteLightBlue = '#E9F1F7';
$paletteMediumBlue = '#4A90E2'; // Main accent color
$paletteDarkBlue = '#1A3A5B';   // Darker accent color, used for primary elements/text
$paletteTextPrimary = '#0D2A57'; // Main text color
$paletteTextSecondary = '#555555'; // Secondary text color

// Define a primary button color that matches the app's theme
$btnPrimaryBg = $paletteMediumBlue;
$btnPrimaryHoverBg = $paletteDarkBlue;
?>

<style>
    /* Custom style for the form container */
    .login-form-container {
        background-color: <?php echo htmlspecialchars($paletteWhite); ?>;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-top: 30px; /* Add some margin from the top */
        margin-bottom: 30px; /* Add some margin from the bottom */
    }

    /* Override Bootstrap primary button to match your palette */
    .btn-primary {
        background-color: <?php echo htmlspecialchars($btnPrimaryBg); ?>;
        border-color: <?php echo htmlspecialchars($btnPrimaryBg); ?>;
        font-weight: 600; /* Make text bolder */
    }

    .btn-primary:hover {
        background-color: <?php echo htmlspecialchars($btnPrimaryHoverBg); ?>;
        border-color: <?php echo htmlspecialchars($btnPrimaryHoverBg); ?>;
    }

    /* Style for form labels */
    .form-label {
        color: <?php echo htmlspecialchars($paletteTextPrimary); ?>;
        font-weight: 500;
    }

    /* Link color consistency */
    a {
        color: <?php echo htmlspecialchars($paletteMediumBlue); ?>;
        text-decoration: none;
    }

    a:hover {
        color: <?php echo htmlspecialchars($paletteDarkBlue); ?>;
        text-decoration: underline;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7"> <div class="login-form-container">
                <h2 class="mb-4 text-center"><?php echo htmlspecialchars($pageTitle ?? 'Login'); ?></h2>

                <form action="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'auth/login'); ?>" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        <div class="invalid-feedback">
                            Email wajib diisi dan formatnya harus valid.
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <div class="invalid-feedback">
                            Password wajib diisi.
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <p class="mt-3 text-center">
                    Belum punya akun? <a href="<?php echo htmlspecialchars($appConfig['BASE_URL'] . 'auth/register'); ?>">Registrasi di sini</a>.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
  'use strict'

  var forms = document.querySelectorAll('.needs-validation')

  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})()
</script>