<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FlowSync Pro – AI-Powered Workspace Integration Platform</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,600,700,800,900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            inter: ['Inter', 'sans-serif'],
            satoshi: ['Satoshi', 'sans-serif']
          },
          colors: {
            brand: {
              50: '#f0f4ff',
              100: '#e0e7ff', 
              200: '#c7d2fe',
              300: '#a5b4fc',
              400: '#818cf8',
              500: '#6366f1',
              600: '#4f46e5',
              700: '#4338ca',
              800: '#3730a3',
              900: '#312e81'
            }
          },
          animation: {
            'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
            'fade-in-down': 'fadeInDown 0.8s ease-out forwards',
            'fade-in-left': 'fadeInLeft 0.8s ease-out forwards',
            'fade-in-right': 'fadeInRight 0.8s ease-out forwards',
            'blur-in': 'blurIn 1s ease-out forwards',
            'slide-up': 'slideUp 0.6s ease-out forwards',
            'glow': 'glow 2s ease-in-out infinite alternate'
          },
          keyframes: {
            fadeInUp: {
              '0%': { opacity: '0', transform: 'translateY(40px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' }
            },
            fadeInDown: {
              '0%': { opacity: '0', transform: 'translateY(-40px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' }
            },
            fadeInLeft: {
              '0%': { opacity: '0', transform: 'translateX(-40px)' },
              '100%': { opacity: '1', transform: 'translateX(0)' }
            },
            fadeInRight: {
              '0%': { opacity: '0', transform: 'translateX(40px)' },
              '100%': { opacity: '1', transform: 'translateX(0)' }
            },
            blurIn: {
              '0%': { opacity: '0', filter: 'blur(10px)' },
              '100%': { opacity: '1', filter: 'blur(0px)' }
            },
            slideUp: {
              '0%': { opacity: '0', transform: 'translateY(60px) scale(0.95)' },
              '100%': { opacity: '1', transform: 'translateY(0) scale(1)' }
            },
            glow: {
              '0%': { boxShadow: '0 0 20px rgba(99, 102, 241, 0.3)' },
              '100%': { boxShadow: '0 0 40px rgba(99, 102, 241, 0.6)' }
            }
          }
        }
      }
    }
  </script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <style>
    .animate-delay-100 { animation-delay: 0.1s; }
    .animate-delay-200 { animation-delay: 0.2s; }
    .animate-delay-300 { animation-delay: 0.3s; }
    .animate-delay-400 { animation-delay: 0.4s; }
    .animate-delay-500 { animation-delay: 0.5s; }
    .animate-delay-600 { animation-delay: 0.6s; }
    .animate-delay-700 { animation-delay: 0.7s; }
    .animate-delay-800 { animation-delay: 0.8s; }
    
    [class*="animate-"] {
      opacity: 0;
    }
    
    .hover-lift {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .hover-lift:hover {
      transform: translateY(-8px) scale(1.02);
    }
    
    .hover-glow:hover {
      box-shadow: 0 20px 40px rgba(99, 102, 241, 0.3);
    }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=IBM+Plex+Serif:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@300;400;500;600;700&family=Inter&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=IBM+Plex+Serif:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@300;400;500;600;700&family=Inter&display=swap" rel="stylesheet">
</head>
<body class="font-inter bg-gray-950 text-white antialiased overflow-x-hidden">
  <div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="animate-fade-in-down animate-delay-100 flex items-center justify-between px-4 sm:px-6 lg:px-8 xl:px-12 py-4 lg:py-6 border-b border-gray-800/50 bg-gray-950/80 backdrop-blur-sm sticky top-0 z-50">
      <div class="flex items-center gap-3">
        <a href="#" class="lg:text-2xl text-xl font-bold text-white tracking-tight font-satoshi hover:text-brand-400 transition-colors duration-300">FlowSync</a>
      </div>
      
      <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-sm font-medium">
        <a href="#features" class="hover:text-brand-400 text-gray-400 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-800/30 px-3 py-2 rounded-lg">
          <i data-lucide="layers" class="w-4 h-4"></i>
          <span class="hidden lg:inline">Integrations</span>
        </a>
        <a href="#enterprise" class="hover:text-brand-400 text-gray-400 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-800/30 px-3 py-2 rounded-lg">
          <i data-lucide="building-2" class="w-4 h-4"></i>
          <span class="hidden lg:inline">Enterprise</span>
        </a>
        <a href="#pricing" class="hover:text-brand-400 text-gray-400 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-800/30 px-3 py-2 rounded-lg">
          <i data-lucide="credit-card" class="w-4 h-4"></i>
          <span class="hidden lg:inline">Pricing</span>
        </a>
        <a href="#docs" class="hover:text-brand-400 text-gray-400 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-800/30 px-3 py-2 rounded-lg">
          <i data-lucide="file-text" class="w-4 h-4"></i>
          <span class="hidden lg:inline">API Docs</span>
        </a>
      </nav>
      
      <div class="flex items-center gap-2 lg:gap-4">
        <button class="hidden sm:flex items-center gap-2 text-sm font-medium text-gray-400 hover:text-white transition-all duration-300 px-3 py-2 rounded-lg hover:bg-gray-800/50 hover:scale-105">
          <i data-lucide="log-in" class="w-4 h-4"></i>
          Sign in
        </button>
        <button class="flex items-center gap-2 text-xs lg:text-sm font-semibold px-3 lg:px-5 py-2 lg:py-2.5 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-brand-500/50 hover:scale-105 hover:-translate-y-1">
          <i data-lucide="play-circle" class="w-4 h-4"></i>
          <span class="hidden sm:inline">Start Free Trial</span>
          <span class="sm:hidden">Try Free</span>
        </button>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-gray-950 via-gray-900 to-gray-950 px-4 sm:px-6 lg:px-8 pt-12 pb-16 lg:py-24">
      <!-- Background Elements -->
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-brand-900/20 via-transparent to-transparent"></div>
      <div class="absolute top-0 left-1/4 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
      
      <div class="relative z-10 max-w-7xl mx-auto text-center">
        <!-- Event Poster Content -->
        <h1 class="animate-blur-in animate-delay-300 max-w-5xl mx-auto text-3xl sm:text-4xl lg:text-6xl xl:text-7xl font-bold tracking-tight font-satoshi text-white leading-tight lg:leading-[1.1]">
          Transform Your Event Posters Into<br class="hidden sm:block">
          <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-400 hover:to-purple-500 transition-all duration-500">Social Media Magic</span>
        </h1>

        <!-- Subheadline -->
        <p class="animate-fade-in-up animate-delay-400 mx-auto mt-6 lg:mt-8 max-w-3xl text-base leading-relaxed text-gray-300">
          Upload any event poster and get ready to produce stunning social media content. Our AI analyzes your poster to create engaging posts that drive attendance.
        </p>

        <!-- Tag Buttons -->
        <div class="animate-fade-in-up animate-delay-500 mt-6 flex items-center justify-center gap-3">
          <span class="px-3 py-1.5 text-xs font-medium rounded-full border border-indigo-500/40 text-indigo-300 bg-indigo-500/10">In-Person</span>
          <span class="px-3 py-1.5 text-xs font-medium rounded-full border border-green-500/40 text-green-300 bg-green-500/10">Business</span>
          <span class="px-3 py-1.5 text-xs font-medium rounded-full border border-purple-500/40 text-purple-300 bg-purple-500/10">In-Frame</span>
        </div>

        <!-- Stats -->
        <div class="animate-fade-in-up animate-delay-500 flex flex-wrap items-center justify-center gap-4 lg:gap-8 mt-8 text-sm text-gray-400">
          <div class="flex items-center gap-2 hover:text-green-400 transition-colors duration-300">
            <i data-lucide="shield-check" class="w-4 h-4 text-green-400"></i>
            <span>SOC 2 Type II</span>
          </div>
          <div class="flex items-center gap-2 hover:text-blue-400 transition-colors duration-300">
            <i data-lucide="globe" class="w-4 h-4 text-blue-400"></i>
            <span>99.9% uptime SLA</span>
          </div>
          <div class="flex items-center gap-2 hover:text-purple-400 transition-colors duration-300">
            <i data-lucide="clock" class="w-4 h-4 text-purple-400"></i>
            <span>5-min setup</span>
          </div>
        </div>

        <!-- CTA Form -->
        <div class="animate-slide-up animate-delay-600 mx-auto mt-10 lg:mt-12 max-w-md lg:max-w-2xl">
          <form class="flex flex-col sm:flex-row gap-3 lg:gap-4 items-center justify-center" enctype="multipart/form-data" method="post" action="#">
            <label for="poster-upload" class="flex flex-col items-center justify-center w-full sm:w-auto gap-2 cursor-pointer border-2 border-dashed border-gray-600 rounded-xl p-6 bg-gray-800/30 hover:bg-gray-800/50 transition-colors duration-300 hover:scale-105">
              <i data-lucide="upload" class="w-8 h-8 text-brand-400"></i>
              <span class="text-sm text-gray-400">Drag & drop or click to upload poster (PNG/JPG/PDF)</span>
              <input id="poster-upload" name="poster" type="file" accept=".png,.jpg,.jpeg,.pdf" class="hidden" required>
            </label>
            <button type="submit" class="flex gap-2 lg:px-8 lg:py-4 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 hover:shadow-xl hover:shadow-brand-500/25 lg:text-base group text-sm font-semibold text-white rounded-xl pt-3 pr-6 pb-3 pl-6 items-center justify-center hover:scale-105 hover:-translate-y-1">
              <span>Generate Social Posts</span>
              <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300"></i>
            </button>
          </form>
          <p class="mt-4 text-xs lg:text-sm text-gray-500 flex flex-wrap items-center justify-center gap-4">
            <span class="flex items-center gap-1 hover:text-green-400 transition-colors duration-300">
              <i data-lucide="check" class="w-3 h-3 text-green-400"></i>
              No credit card required
            </span>
            <span class="flex items-center gap-1 hover:text-blue-400 transition-colors duration-300">
              <i data-lucide="calendar" class="w-3 h-3 text-blue-400"></i>
              14-day free trial
            </span>
            <span class="flex items-center gap-1 hover:text-purple-400 transition-colors duration-300">
              <i data-lucide="users" class="w-3 h-3 text-purple-400"></i>
              Free for teams under 10
            </span>
          </p>
        </div>

        <!-- Interactive Demo Cards -->
        <div class="mt-16 lg:mt-24 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 max-w-6xl mx-auto">
          
          <!-- Upload Poster Card -->
          <div class="animate-fade-in-left animate-delay-700 group hover-lift hover:border-green-500/30 transition-all duration-500 hover:shadow-2xl hover:shadow-green-500/20 text-left border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50">
            <div class="space-y-4 text-center">
              <div class="flex items-center justify-center w-16 h-16 mx-auto rounded-xl bg-gradient-to-br from-green-500 to-green-600">
                <i data-lucide="upload" class="w-8 h-8 text-white"></i>
              </div>
              <p class="text-sm text-gray-300">Drop your poster file or choose one from your device. We accept PNG, JPG, or PDF.</p>
            </div>
          </div>

          <!-- AI Magic Card -->
          <div class="animate-slide-up animate-delay-800 border-brand-500/30 hover:border-brand-500/60 hover-lift hover:shadow-2xl hover:shadow-brand-500/30 transition-all duration-500 text-center bg-gradient-to-t from-indigo-500/20 to-purple-600/10 hover:from-indigo-500/30 hover:to-purple-600/20 border rounded-2xl p-6 backdrop-blur-sm group">
            <div class="space-y-4 text-center">
              <div class="flex items-center justify-center w-16 h-16 mx-auto rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600">
                <i data-lucide="sparkles" class="w-8 h-8 text-white"></i>
              </div>
              <p class="text-sm text-gray-300">Our AI extracts event details and drafts engaging captions tailored to each network.</p>
            </div>
          </div>

          <!-- Get Your Content Card -->
          <div class="animate-fade-in-right animate-delay-700 relative overflow-hidden hover-lift hover:border-green-500/30 transition-all duration-500 hover:shadow-2xl hover:shadow-green-500/20 border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 group">
            <div class="space-y-4 text-center relative z-40">
              <div class="flex items-center gap-4">
                <i data-lucide="facebook" class="w-8 h-8 text-blue-500"></i>
                <i data-lucide="instagram" class="w-8 h-8 text-pink-500"></i>
                <i data-lucide="twitter" class="w-8 h-8 text-sky-400"></i>
              </div>
              <p class="text-sm text-gray-300">Download ready-to-post images & captions for Facebook, Instagram, and Twitter.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Why Event Organizers Love Us -->
    <section id="benefits" class="py-16 lg:py-24 bg-gray-950 px-4 sm:px-6 lg:px-8">
      <h2 class="text-center text-3xl sm:text-4xl font-bold font-satoshi mb-12">Why Event Organizers Love Us</h2>
      <p class="text-center max-w-2xl mx-auto text-gray-400 mb-10">Everything you need to promote your event.</p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 max-w-6xl mx-auto">
        <!-- Benefit card -->
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-brand-500/10">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="zap" class="w-6 h-6 text-brand-400"></i>
            <h3 class="font-semibold">Lightning Fast</h3>
          </div>
          <p class="text-sm text-gray-400">Generate social-ready assets in seconds, not hours.</p>
        </div>
        <!-- Benefit card -->
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-brand-500/10">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="layout" class="w-6 h-6 text-indigo-400"></i>
            <h3 class="font-semibold">Platform Ready</h3>
          </div>
          <p class="text-sm text-gray-400">Perfect sizes and ratios for every major network.</p>
        </div>
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-brand-500/10">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="search" class="w-6 h-6 text-green-400"></i>
            <h3 class="font-semibold">Smart Research</h3>
          </div>
          <p class="text-sm text-gray-400">AI analyzes keywords to boost discoverability.</p>
        </div>
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-brand-500/10">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="smartphone" class="w-6 h-6 text-pink-400"></i>
            <h3 class="font-semibold">Mobile Ready</h3>
          </div>
          <p class="text-sm text-gray-400">Assets look crisp on every screen size.</p>
        </div>
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-brand-500/10">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="wallet" class="w-6 h-6 text-yellow-400"></i>
            <h3 class="font-semibold">Affordable</h3>
          </div>
          <p class="text-sm text-gray-400">Cost-effective plans for any event budget.</p>
        </div>
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-brand-500/10">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="activity" class="w-6 h-6 text-purple-400"></i>
            <h3 class="font-semibold">True Performance</h3>
          </div>
          <p class="text-sm text-gray-400">Content optimized to drive clicks and attendance.</p>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-16 lg:py-24 bg-gray-900/20 px-4 sm:px-6 lg:px-8">
      <h2 class="text-center text-3xl sm:text-4xl font-bold font-satoshi mb-12">Simple, Fair Pricing</h2>
      <p class="text-center max-w-2xl mx-auto text-gray-400 mb-10">Just pay for what you need most.</p>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
        <!-- Free -->
        <div class="border border-gray-700/50 rounded-2xl p-8 backdrop-blur-sm bg-gray-900/40 flex flex-col">
          <h3 class="text-xl font-semibold mb-2">Free Start</h3>
          <div class="text-4xl font-bold mb-4">$0</div>
          <ul class="space-y-2 text-sm text-gray-400 mb-6 flex-1">
            <li>1 poster / month</li>
            <li>Watermarked outputs</li>
            <li>Community support</li>
          </ul>
          <button class="mt-auto w-full py-3 rounded-lg bg-gray-800 hover:bg-gray-700 transition-colors">Get Started</button>
        </div>
        <!-- Pro -->
        <div class="border-2 border-brand-500 rounded-2xl p-8 backdrop-blur-sm bg-gray-900/60 flex flex-col shadow-lg shadow-brand-500/25 transform hover:scale-105 transition-transform">
          <h3 class="text-xl font-semibold mb-2 flex items-center justify-center gap-2">Viral Campaign <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-brand-600 text-white">Most Popular</span></h3>
          <div class="text-4xl font-bold mb-4 text-brand-400">$29</div>
          <ul class="space-y-2 text-sm text-gray-400 mb-6 flex-1">
            <li>15 posters / month</li>
            <li>No watermarks</li>
            <li>HD exports</li>
            <li>Email support</li>
          </ul>
          <button class="mt-auto w-full py-3 rounded-lg bg-brand-600 hover:bg-brand-700 transition-colors">Choose Plan</button>
        </div>
        <!-- Premium -->
        <div class="border border-gray-700/50 rounded-2xl p-8 backdrop-blur-sm bg-gray-900/40 flex flex-col">
          <h3 class="text-xl font-semibold mb-2">Event Campaign</h3>
          <div class="text-4xl font-bold mb-4">$49</div>
          <ul class="space-y-2 text-sm text-gray-400 mb-6 flex-1">
            <li>Unlimited posters</li>
            <li>Priority rendering</li>
            <li>Dedicated manager</li>
          </ul>
          <button class="mt-auto w-full py-3 rounded-lg bg-gray-800 hover:bg-gray-700 transition-colors">Contact Sales</button>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600 text-center text-white px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl sm:text-4xl font-bold font-satoshi mb-4">Ready to Transform Your Event Marketing?</h2>
      <p class="max-w-2xl mx-auto mb-8 text-white/90">Join thousands of event organizers who are leveraging PostrMagic to create better content.</p>
      <button class="px-8 py-4 bg-white text-gray-900 font-semibold rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all hover:scale-105">Start My Free Trial</button>
    </section>

    <!-- Close main container -->
    </div>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-gray-800/50 px-4 sm:px-6 lg:px-8 py-12 text-gray-400">
      <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <a href="#" class="text-2xl font-bold font-satoshi text-white">PostrMagic</a>
          <p class="mt-4 text-sm">Transform any poster into a social media campaign in seconds.</p>
        </div>
        <div>
          <h4 class="font-semibold text-white mb-3">Product</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="#features" class="hover:text-white">Features</a></li>
            <li><a href="#pricing" class="hover:text-white">Pricing</a></li>
            <li><a href="#docs" class="hover:text-white">API Docs</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold text-white mb-3">Company</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="#about" class="hover:text-white">About</a></li>
            <li><a href="#careers" class="hover:text-white">Careers</a></li>
            <li><a href="#contact" class="hover:text-white">Contact</a></li>
          </ul>
        </div>
      </div>
      <div class="mt-10 text-center text-xs text-gray-500"> 2025 PostrMagic. Built with by your design team.</div>
    </footer>

    <script>
      // Initialize Lucide icons
      lucide.createIcons();
      
      // Intersection Observer for animations
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      };
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
          }
        });
      }, observerOptions);
      
      // Observe all animated elements
      document.addEventListener('DOMContentLoaded', function() {
        const animatedElements = document.querySelectorAll('[class*="animate-"]');
        animatedElements.forEach(el => observer.observe(el));
        
        // Enhanced card interactions
        const cards = document.querySelectorAll('.hover-lift');
        cards.forEach(card => {
          card.addEventListener('mouseenter', function() {
            this.style.zIndex = '50';
          });
          card.addEventListener('mouseleave', function() {
            this.style.zIndex = 'auto';
          });
        });
      });
    </script>
  </body>
</html>