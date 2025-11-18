<?php
require 'config.php';

// Se já estiver logado, redirecionar para dashboard
if (isset($_SESSION['user_id'])) {
  header('Location: calendario.php');
  exit;
}

// Gerar URL de autenticação do Google
$params = [
  'client_id' => $_ENV['GOOGLE_CLIENT_ID'],
  'redirect_uri' => GOOGLE_REDIRECT_URI,
  'response_type' => 'code',
  'scope' => 'email profile',
  'access_type' => 'online'
];

$googleLoginUrl = GOOGLE_AUTH_URL . '?' . http_build_query($params);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SARA - LoginPage</title>
  <!-- <link rel="stylesheet" href="login-styles.css"> -->
</head>
<style>
  /* Complete redesign with red/coral palette and smooth animations */

:root {
  /* Primary colors from palette */
  --color-primary: #FF0000;
  --color-primary-dark: #B30000;
  --color-primary-light: #FF4444;
  --color-secondary: #FF6B5B;
  --color-accent: #FF8A7F;

  /* Neutrals */
  --color-white: #FFFFFF;
  --color-bg: #FAFAFA;
  --color-border: #E8E8E8;
  --color-text: #1A1A1A;
  --color-text-light: #666666;

  /* Spacing */
  --spacing-xs: 0.5rem;
  --spacing-sm: 1rem;
  --spacing-md: 1.5rem;
  --spacing-lg: 2rem;
  --spacing-xl: 3rem;

  /* Radius */
  --radius-sm: 0.5rem;
  --radius-md: 1rem;
  --radius-lg: 1.5rem;
  --radius-full: 50%;

  /* Transitions */
  --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  --transition-smooth: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);

  /* Shadow */
  --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
  --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.12);
  --shadow-lg: 0 16px 48px rgba(255, 0, 0, 0.15);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  width: 100%;
  height: 100%;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
}

body {
  background: linear-gradient(135deg, var(--color-bg) 0%, #FFF5F5 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: var(--spacing-md);
  overflow: hidden;
}

.login-container {
  width: 100%;
  max-width: 450px;
  position: relative;
  z-index: 1;
}

.login-content {
  position: relative;
}

/* Animated background gradient effect */
.animated-bg {
  position: absolute;
  inset: -100px;
  background: radial-gradient(circle at 30% 70%, rgba(255, 0, 0, 0.08) 0%, transparent 50%),
              radial-gradient(circle at 70% 30%, rgba(255, 107, 91, 0.06) 0%, transparent 50%);
  animation: float 20s ease-in-out infinite;
  pointer-events: none;
  z-index: -1;
}

@keyframes float {
  0%, 100% {
    transform: translate(0, 0);
  }
  50% {
    transform: translate(20px, -20px);
  }
}

/* Login box with enhanced styling */
.login-box {
  background: var(--color-white);
  border-radius: var(--radius-lg);
  padding: var(--spacing-xl);
  box-shadow: var(--shadow-lg);
  backdrop-filter: blur(20px);
  position: relative;
  z-index: 2;
  border: 1px solid rgba(255, 255, 255, 0.8);
  animation: slideUp 0.6s var(--transition-smooth);
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Logo section styling */
.logo-section {
  text-align: center;
  margin-bottom: var(--spacing-lg);
  animation: fadeIn 0.8s var(--transition-smooth) 0.1s both;
}

.logo-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--color-primary);
  margin-bottom: var(--spacing-xs);
  letter-spacing: -0.5px;
}

.logo-subtitle {
  font-size: 0.875rem;
  color: var(--color-text-light);
  font-weight: 500;
  letter-spacing: 0.3px;
}

/* Welcome section with animations */
.welcome-section {
  text-align: center;
  margin-bottom: var(--spacing-lg);
  animation: fadeIn 0.8s var(--transition-smooth) 0.2s both;
}

.welcome-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--color-text);
  margin-bottom: var(--spacing-sm);
}

.welcome-text {
  font-size: 1rem;
  color: var(--color-text-light);
  line-height: 1.5;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Enhanced Google button with interactive states */
.google-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--spacing-sm);
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  color: var(--color-white);
  border: none;
  border-radius: var(--radius-md);
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  transition: all var(--transition-smooth);
  box-shadow: 0 8px 16px rgba(255, 0, 0, 0.25);
  position: relative;
  overflow: hidden;
  animation: fadeIn 0.8s var(--transition-smooth) 0.3s both;
}

.google-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.2);
  transition: left var(--transition-fast);
}

.google-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 16px 32px rgba(255, 0, 0, 0.35);
}

.google-btn:hover::before {
  left: 100%;
}

.google-btn:active {
  transform: translateY(0);
}

.google-icon {
  transition: transform var(--transition-fast);
  animation: slideInLeft 0.8s var(--transition-smooth) 0.4s both;
}

.google-btn:hover .google-icon {
  transform: scale(1.1);
}

.btn-text {
  animation: slideInRight 0.8s var(--transition-smooth) 0.45s both;
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Helper text styling */
.helper-text {
  text-align: center;
  font-size: 0.875rem;
  color: var(--color-text-light);
  margin-top: var(--spacing-md);
  animation: fadeIn 0.8s var(--transition-smooth) 0.5s both;
}

/* Decorative elements for visual appeal */
.decoration {
  position: absolute;
  border-radius: var(--radius-full);
  pointer-events: none;
  opacity: 0.6;
  animation: pulse 4s ease-in-out infinite;
}

.decoration-1 {
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, rgba(255, 107, 91, 0.3) 0%, rgba(255, 138, 127, 0.1) 100%);
  top: -50px;
  right: -50px;
  animation: pulse 6s ease-in-out infinite;
}

.decoration-2 {
  width: 150px;
  height: 150px;
  background: linear-gradient(135deg, rgba(255, 0, 0, 0.1) 0%, rgba(255, 68, 68, 0.05) 100%);
  bottom: -75px;
  left: -75px;
  animation: pulse 8s ease-in-out infinite 1s;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

/* Responsive design for mobile and tablet */
@media (max-width: 640px) {
  .login-box {
    padding: var(--spacing-lg);
  }

  .logo-title {
    font-size: 2rem;
  }

  .welcome-title {
    font-size: 1.5rem;
  }

  .google-btn {
    padding: 0.875rem;
    font-size: 0.95rem;
  }

  body {
    padding: var(--spacing-sm);
  }

  .decoration-1 {
    width: 80px;
    height: 80px;
    top: -40px;
    right: -40px;
  }

  .decoration-2 {
    width: 120px;
    height: 120px;
    bottom: -60px;
    left: -60px;
  }
}

@media (max-width: 480px) {
  .login-box {
    padding: var(--spacing-md);
  }

  .logo-title {
    font-size: 1.75rem;
  }

  .logo-subtitle {
    font-size: 0.8rem;
  }

  .welcome-title {
    font-size: 1.25rem;
  }

  .welcome-text {
    font-size: 0.9rem;
  }

  .google-btn {
    gap: var(--spacing-xs);
    padding: 0.75rem;
  }

  .google-icon {
    width: 18px;
    height: 18px;
  }
}

</style>
<body>
  <div class="login-container">
    <div class="login-content">
      <!-- Added animated background gradient -->
      <div class="animated-bg"></div>

      <div class="login-box">
        <!-- Added logo section with smooth animations -->
        <div class="logo-section">
          <h1 class="logo-title">SARA</h1>
          <p class="logo-subtitle">Sistema de Agendamento e Reserva de Aparelhos</p>
        </div>

        <!-- Added welcome message with fade-in animation -->
        <div class="welcome-section">
          <h2 class="welcome-title">Bem-vindo</h2>
          <p class="welcome-text">Faça login com sua conta Google</p>
        </div>

        <!-- Enhanced Google button with hover animations -->
        <a href="<?php echo htmlspecialchars($googleLoginUrl); ?>" class="google-btn">
          <svg width="20" height="20" viewBox="0 0 18 18" class="google-icon">
            <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" />
            <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z" />
            <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.175 0 7.55 0 9s.348 2.825.957 4.039l3.007-2.332z" />
            <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" />
          </svg>
          <span class="btn-text">Entrar com Google</span>
        </a>

        <!-- Added helper text with animation -->
        <p class="helper-text">Seguro e rápido</p>
      </div>

      <!-- Added decorative elements -->
      <div class="decoration decoration-1"></div>
      <div class="decoration decoration-2"></div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
  const googleBtn = document.querySelector('.google-btn');

  if (googleBtn) {
    googleBtn.addEventListener('click', function(e) {
      const ripple = document.createElement('span');
      ripple.classList.add('ripple');

      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;

      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = x + 'px';
      ripple.style.top = y + 'px';

      this.appendChild(ripple);

      setTimeout(() => ripple.remove(), 600);
    });
  }

  const elements = document.querySelectorAll('a, button');
  elements.forEach(el => {
    el.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        this.click();
      }
    });
  });
});

const style = document.createElement('style');
style.textContent = `
  .google-btn {
    position: relative;
  }

  .ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: rippleEffect 0.6s ease-out;
    pointer-events: none;
  }

  @keyframes rippleEffect {
    to {
      transform: scale(4);
      opacity: 0;
    }
  }
`;
document.head.appendChild(style);

  </script>
</body>
</html>
