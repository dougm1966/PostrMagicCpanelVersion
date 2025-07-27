<?php
require_once __DIR__ . '/includes/auth.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

// Handle form submission  
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($login) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $user = authenticateUser($login, $password, $remember);
        if ($user) {
            // Redirect to requested page or dashboard
            $redirect = $_GET['redirect'] ?? 'dashboard.php';
            header('Location: ' . $redirect);
            exit();
        } else {
            $error = 'Invalid email/username or password';
            // Debug: Add this temporarily to see what's happening
            if (APP_DEBUG) {
                $error .= " (Debug: login='$login', password='$password')";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - PostrMagic</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    .input-glow:focus {
      box-shadow: 0 0 10px 0px rgba(0,123,255,0.12) inset !important;
      border-color: #60a5fa !important;
      outline: none;
      background-color: #f5faff;
    }
    .error-anim {
      animation: shake .37s cubic-bezier(.36,.07,.19,.97) both;
      border-color: #dc2626 !important;
    }
    @keyframes shake {
      0%,100% { transform: translateX(0);}
      20% { transform: translateX(-8px);}
      40% { transform: translateX(8px);}
      60% { transform: translateX(-6px);}
      80% { transform: translateX(6px);}
    }
    .stagger-item { opacity:0; transform:translateY(44px); transition:opacity .68s cubic-bezier(.23,.77,.52,1.01),transform .68s cubic-bezier(.23,.77,.52,1.01);}
    .stagger-item.visible { opacity:1; transform:translateY(0);}
    .hover\:inner-glow:hover {
      box-shadow: 0 0 10px 0px rgba(0,123,255,0.12) inset;
      transform: scale(1.06);
      transition: box-shadow 0.22s cubic-bezier(.4,0,.2,1), transform 0.22s cubic-bezier(.4,0,.2,1);
    }
    .transition-shadow-transform {
      transition: box-shadow 0.22s cubic-bezier(.4,0,.2,1), transform 0.22s cubic-bezier(.4,0,.2,1);
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-50">
  <div class="w-[402px] min-h-screen flex flex-col items-center justify-center relative">
    <div class="bg-white rounded-[28px] shadow-xl py-8 px-8 mt-[54px] mb-[34px] w-[354px] flex flex-col items-center relative">
      <div class="flex flex-col items-start gap-2 w-full stagger-item">
        <span class="text-[58px] font-extrabold font-sans text-black leading-tight tracking-tight">Welcome!</span>
        <span class="text-base font-normal text-black/70 leading-6">Sign in and turn your creativity into income.</span>
      </div>
      <div class="mt-6 flex w-full justify-between gap-4 stagger-item">
        <button aria-label="Sign up with Facebook" class="flex-1 bg-blue-50 border border-black/20 rounded-2xl h-11 flex justify-center items-center max-w-[91px] transition-shadow-transform hover:inner-glow active:scale-95">
          <svg width="16" height="22" viewBox="0 0 12 22" fill="none"><path d="M11.19 12.38l.6-3.91H8.07V6.15c0-1.07.53-2.1 2.2-2.1h1.7V.31A20.7 20.7 0 0 0 8.67 0c-3.21 0-5.31 1.94-5.31 5.45v3.02H0V12.38h3.37v9.62h4.07v-9.62h3.75z" fill="#1877F3"/></svg>
        </button>
        <button aria-label="Sign up with Google" class="flex-1 bg-blue-50 border border-black/20 rounded-2xl h-11 flex justify-center items-center max-w-[91px] transition-shadow-transform hover:inner-glow active:scale-95">
          <svg width="24" height="24" viewBox="0 0 24 24"><g><path fill="#4285F4" d="M23.52 12.27c0-.84-.08-1.67-.23-2.47H12v4.69h6.48a5.54 5.54 0 0 1-2.4 3.64v3h3.87c2.26-2.09 3.57-5.18 3.57-8.86z"/><path fill="#34A853" d="M12 24c3.24 0 5.96-1.07 7.95-2.93l-3.87-3a6.17 6.17 0 0 1-9.24-3.59h-4v3.08c1.99 3.92 6.09 6.44 9.16 6.44z"/><path fill="#FBBC05" d="M5.84 14.48A6.11 6.11 0 0 1 5.54 13c0-.5.09-.99.15-1.48V8.44h-4a11.98 11.98 0 0 0 0 7.12l4-3.08z"/><path fill="#EA4335" d="M12 7.54c1.76 0 3.33.61 4.57 1.8l3.43-3.4C17.96 2.99 15.24 2 12 2 8.93 2 4.83 4.52 2.84 8.44l4 3.09A6.17 6.17 0 0 1 12 7.54z"/></g></svg>
        </button>
        <button aria-label="Sign up with Apple" class="flex-1 bg-blue-50 border border-black/20 rounded-2xl h-11 flex justify-center items-center max-w-[91px] transition-shadow-transform hover:inner-glow active:scale-95">
          <svg width="20" height="24" viewBox="0 0 20 24" fill="#000"><path d="M16.66 12.77c-.01-2.38 2.07-3.5 2.16-3.56-1.18-1.73-3.01-1.97-3.66-1.99-1.56-.16-3.06.92-3.87.92-.8 0-2.04-.9-3.36-.88-1.75.03-3.35 1.02-4.24 2.6-1.8 3.12-.46 7.75 1.3 10.28.87 1.25 1.91 2.65 3.27 2.6 1.33-.05 1.83-.85 3.44-.85 1.61 0 2.04.85 3.37.83 1.4-.02 2.28-1.27 3.15-2.52.62-.89.87-1.35 1.36-2.37-3.57-1.36-3.46-4.53-.68-5.16zm-2.27-6.21c.71-.86 1.2-2.08 1.07-3.27-1.03.04-2.28.68-3.02 1.53-.66.76-1.25 1.98-1.03 3.14 1.13.09 2.28-.57 2.98-1.4z"/></svg>
        </button>
      </div>
      <div class="w-full flex items-center justify-center my-6 relative stagger-item">
        <div class="absolute w-full h-[1px] bg-gradient-to-r from-transparent via-black/20 to-transparent"></div>
        <span class="bg-white z-10 px-4 py-0 text-xs uppercase tracking-tight text-black/70">Or</span>
      </div>
      
      <?php if ($error): ?>
      <div class="w-full mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm stagger-item">
        <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>
      
      <form id="signin-form" method="POST" autocomplete="off" class="flex flex-col gap-4 w-full">
        <div class="stagger-item">
          <label for="email" class="text-[13px] font-semibold text-black">Email or Username</label>
          <div class="mt-2 relative flex items-center">
            <input type="text" id="email" name="email" required
              placeholder="Enter your email or username"
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              autocomplete="username"
              class="input-glow signin-field w-full h-11 px-3 border border-black/20 rounded-lg text-[16px] placeholder-black/70 placeholder:font-normal"/>
          </div>
        </div>
        <div class="stagger-item">
          <label for="password" class="text-[13px] font-semibold text-black">Password</label>
          <div class="mt-2 relative flex items-center">
            <input type="password" id="password" name="password" required
              placeholder="Enter your password"
              autocomplete="current-password"
              class="input-glow signin-field w-full h-11 px-3 border border-black/20 rounded-lg text-[16px] placeholder-black/70 placeholder:font-normal"/>
            <div class="absolute right-3 cursor-pointer" id="togglePassword">
              <svg width="22" height="14" fill="none" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="opacity-50"><ellipse cx="11" cy="7" rx="10" ry="6"/><circle cx="11" cy="7" r="3"/></svg>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-between pt-2 stagger-item">
          <label class="flex items-center gap-2">
            <input type="checkbox" name="remember" class="form-checkbox border-black/20 rounded-sm w-3 h-3"/>
            <span class="text-xs text-black/70 font-normal">Remember me</span>
          </label>
          <a href="#" class="text-xs font-medium text-black hover:underline">Forget Password?</a>
        </div>
        <button type="submit" id="continue-btn" class="mt-4 w-full h-11 bg-black hover:bg-gray-900 text-white rounded-2xl shadow-lg text-[16px] font-semibold transition flex items-center justify-center stagger-item
          hover:scale-105 hover:shadow-xl active:scale-95 active:shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-200
          ">
          Continue
        </button>
        <div id="signin-success-msg" class="text-center text-green-500 font-semibold mt-1 hidden stagger-item">
          Signed in! (This is a demo)
        </div>
      </form>
    </div>
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full flex justify-center mb-4">
      <div class="bg-black rounded-full w-36 h-1.5"></div>
    </div>
  </div>
  <script>
    // Staggered animation
    window.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.stagger-item').forEach((el, i) => {
        setTimeout(() => el.classList.add('visible'), 190 + i * 120);
      });
    });
    // Password toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
      let inp = document.getElementById('password');
      inp.type = inp.type === 'password' ? 'text' : 'password';
    });
  </script>
</body>
</html>
