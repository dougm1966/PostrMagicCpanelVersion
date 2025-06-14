<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PosterMagic – AI Event Poster to Social Media Magic</title>
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
<body class="font-inter bg-white text-gray-800 antialiased overflow-x-hidden">
  <div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="animate-fade-in-down animate-delay-100 flex items-center justify-between px-4 sm:px-6 lg:px-8 xl:px-12 py-4 lg:py-6 border-b border-gray-200/50 bg-white/80 backdrop-blur-sm sticky top-0 z-50">
      <div class="flex items-center text-gray-600">
        <a href="#" class="w-full text-left text-xl font-semibold text-gray-900 py-6 flex justify-between items-center cursor-pointer hover:text-brand-400 transition-colors duration-300">PosterMagic</a>
      </div>
      
      <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-sm font-medium">
        <a href="#how-it-works" class="hover:text-brand-400 text-gray-500 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-100/30 px-3 py-2 rounded-lg">
          <i data-lucide="wand-2" class="w-4 h-4"></i>
          <span class="hidden lg:inline">How It Works</span>
        </a>
        <a href="#benefits" class="hover:text-brand-400 text-gray-500 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-100/30 px-3 py-2 rounded-lg">
          <i data-lucide="sparkles" class="w-4 h-4"></i>
          <span class="hidden lg:inline">Benefits</span>
        </a>
        <a href="#testimonials" class="hover:text-brand-400 text-gray-500 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-100/30 px-3 py-2 rounded-lg">
          <i data-lucide="message-circle" class="w-4 h-4"></i>
          <span class="hidden lg:inline">Testimonials</span>
        </a>
        <a href="#pricing" class="hover:text-brand-400 text-gray-500 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-100/30 px-3 py-2 rounded-lg">
          <i data-lucide="credit-card" class="w-4 h-4"></i>
          <span class="hidden lg:inline">Pricing</span>
        </a>
        <a href="#faq" class="hover:text-brand-400 text-gray-500 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-100/30 px-3 py-2 rounded-lg">
          <i data-lucide="help-circle" class="w-4 h-4"></i>
          <span class="hidden lg:inline">FAQ</span>
        </a>
      </nav>
      
      <div class="flex items-center gap-2 lg:gap-4">
        <button class="hidden sm:flex items-center gap-2 text-sm font-medium text-gray-400 hover:text-white transition-all duration-300 px-3 py-2 rounded-lg hover:bg-gray-800/50 hover:scale-105">
          <i data-lucide="log-in" class="w-4 h-4"></i>
          Sign in
        </button>
        <button class="flex items-center gap-2 text-xs lg:text-sm font-semibold px-3 lg:px-5 py-2 lg:py-2.5 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-brand-500/50 hover:scale-105 hover:-translate-y-1">
          <i data-lucide="play-circle" class="w-4 h-4"></i>
          <span class="hidden sm:inline">Try PosterMagic Free</span>
          <span class="sm:hidden">Try Free</span>
        </button>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="flex-1 appearance-none bg-transparent border-0 text-gray-900 placeholder-gray-400 focus:ring-0 sm:text-sm"6 lg:px-8 pt-12 pb-16 lg:py-24">
      <!-- Background Elements -->
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-brand-900/20 via-transparent to-transparent"></div>
      <div class="absolute top-0 left-1/4 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
      
      <div class="relative mt-10 max-w-2xl mx-auto animate-fade-in-up animate-delay-600 bg-white/60 ring-1 ring-gray-200/50 backdrop-blur-lg rounded-xl p-2 flex items-center gap-2 shadow-2xl shadow-brand-500/10" border border-brand-800/50 text-brand-300 rounded-full text-sm font-medium mb-8 backdrop-blur-sm hover:bg-brand-900/70 hover:border-brand-700/70 transition-all duration-300 hover:scale-105">
          <i data-lucide="trending-up" class="w-4 h-4"></i>
          <span class="hidden sm:inline">Trusted by 125,000+ teams at</span>
          <span class="sm:hidden">Used by 125k+ teams</span>
          <span class="font-semibold">Microsoft, Spotify, Stripe</span>
        </div>
        <!-- Event Poster Content -->
        <h1 class="animate-blur-in animate-delay-300 max-w-5xl mx-auto text-3xl sm:text-4xl lg:text-6xl xl:text-7xl font-bold tracking-tight font-satoshi text-white leading-tight lg:leading-[1.1]">
          Transform Your Event Posters Into<br class="hidden sm:block">
          <span class="bg-clip-text text-transparent bg-clip-text bg-gradient-to-br from-gray-900 via-gray-700 to-gray-800 hover:from-indigo-400 hover:to-purple-500 transition-all duration-500">Social Media Magic</span>
        </h1>

        <!-- Subheadline -->
        <p class="animate-fade-in-up animate-delay-400 mx-auto mt-6 lg:mt-8 max-w-3xl text-base leading-relaxed text-gray-300">
          Upload any event poster and get ready to produce stunning social media content. Our AI analyzes your poster to create engaging posts that drive attendance.
        </p>

        <!-- Tag Buttons -->
        <div class="animate-fade-in-up animate-delay-500 mt-6 flex items-center justify-center gap-3">
          <span class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium bg-brand-50 text-brand-600 ring-1 ring-inset ring-brand-200 animate-fade-in-up"digo-300 bg-indigo-500/10">
            <i data-lucide="facebook" class="w-3 h-3"></i>
            Facebook
          </span>
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full border border-pink-500/40 text-pink-300 bg-pink-500/10">
            <i data-lucide="instagram" class="w-3 h-3"></i>
            Instagram
          </span>
          <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors"ky-300 bg-sky-500/10">
            <i data-lucide="twitter" class="w-3 h-3"></i>
            Twitter
          </span>
        </div>

        <!-- Poster Upload Form -->
        <div class="animate-slide-up animate-delay-600 mx-auto mt-10 lg:mt-12 max-w-md lg:max-w-2xl">
          <form class="flex flex-col sm:flex-row gap-3 lg:gap-4 items-center justify-center" enctype="multipart/form-data" method="post" action="#">
            <label for="poster-upload" class="flex flex-col items-center justify-center w-full sm:w-auto gap-2 cursor-pointer border-2 border-dclass="flex flex-col p-8 bg-white rounded-2xl shadow-2xl border-gray-200 border hover-lift hover-glow"ray-800/50 transition-colors duration-300 hover:scale-105">
              <i data-lucide="upload" class="w-8 h-8 text-brand-400"></i>
              <span class="mt-2 text-sm text-gray-500">Drag & drop or click to upload poster (PNG/JPG/PDF)</span>
              <input id="poster-upload" name="poster" type="file" accept=".png,.jpg,.jpeg,.pdf" class="hidden" required>
            </label>
            <button type="submit" class="flex gap-2 lg:px-8 lg:py-4 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 hover:shadow-xl hover:shadow-brand-500/25 lg:text-base group text-sm font-semibold text-white rounded-xl pt-3 pr-6 pb-3 pl-6 items-center justify-center hover:scale-105 hover:-translate-y-1">
              <span>Upload & See The Magic</span>
              <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300"></i>
            </button>
          </form>
          <p class="mt-4 text-xs lg:text-sm text-gray-500 flex flex-wrap items-center justify-center gap-4">
            <span class="flex items-center gap-1 hover:text-green-400 transition-colors duration-300">
              <i data-lucide="check" class="w-3 h-3 text-green-400"></i>
              No credit card required
            </span>
            <span class="flex items-center gap-1 hover:text-blue-400 transition-colors duration-300">
              <i data-lucide="gift" class="w-3 h-3 text-blue-400"></i>
              Three free posts
            </span>
            <span class="flex items-center gap-1 hover:text-purple-400 transition-colors duration-300">
              <i data-lucide="target" class="w-3 h-3 text-purple-400"></i>
              Design for marketing teams but made for you
            </span>
          </p>
        </div>
      </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="min-h-screen flex items-center py-20 lg:py-32 bg-gray-900">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- How PostrMagic Works Title & Subtitle -->
        <div class="mb-12 lg:mb-16 text-center">
          <h2 class="text-3xl sm:text-4xl font-bold font-satoshi mb-4 text-white">How PostrMagic Works</h2>
          <p class="mt-4 max-w-xl mx-auto text-lg text-gray-600">Transform your poster into engaging social content in 3 simple steps.</p>
        </div>
        <!-- Step Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
            <!-- Step 1: Upload Poster Card -->
            <div class="animate-fade-in-left animate-delay-700 group hover-lift hover:border-green-500/30 transition-all duration-500 hover:shadow-2xl hover:shadow-green-500/20 text-center border border-gray-700/50 rounded-2xl p-8 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 flex flex-col min-h-[26rem]">
              <div class="flex items-center justify-center w-16 h-16 rounded-full bg-brand-100 text-brand-600 mb-4"green-500 to-green-600 mb-4 icon-animate">
                <i data-lucide="file-up" class="w-8 h-8 text-white"></i>
              </div>
              <h3 class="text-lg font-semibold text-white mb-2">1. Upload Your Poster</h3>
              <p class="mt-2 text-sm text-gray-500">Simply drag &amp; drop your event poster (PNG, JPG, or PDF) or select it from your device.</p>
              <div class="mt-auto pt-6">
                <div class="w-32 h-12 flex items-center justify-center bg-gray-100/50 rounded-lg"r border-green-500/20 rounded-full py-1.5 px-4 max-w-max mx-auto">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-400 mr-2"></i>
                    <span class="text-xs text-green-300 font-medium">Works with any image quality</span>
                </div>
              </div>
            </div>
            <!-- Step 2: AI Magic Card -->
            <div class="animate-slide-up animclass="flex flex-col p-8 bg-white rounded-2xl shadow-2xl border-brand-500 border-2 hover-lift hover-glow relative"r:shadow-brand-500/30 transition-all duration-500 text-center bg-gradient-to-t from-indigo-500/20 to-purple-600/10 hover:from-indigo-500/30 hover:to-purple-600/20 border rounded-2xl p-8 backdrop-blur-sm group flex flex-col min-h-[26rem]">
              <div class="flex items-center justify-center w-16 h-16 rounded-full bg-brand-100 text-brand-600 mb-4"indigo-500 to-purple-600 mb-4 icon-animate" style="animation-delay: 0.2s;">
                <i data-lucide="wand-2" class="w-8 h-8 text-white"></i>
              </div>
              <h3 class="text-lg font-semibold text-white mb-2">2. AI Magic Happens</h3>
              <p class="mt-2 text-sm text-gray-500">Our intelligent system analyzes your design, extracts key info, and crafts compelling social media content.</p>
              <div class="mt-auto pt-6">
                <div class="flex items-center justify-center bg-indigo-900/30 border border-indigo-500/20 rounded-full py-1.5 px-4 max-w-max mx-auto">
                    <i data-lucide="check-circle" class="w-4 h-4 text-indigo-400 mr-2"></i>
                    <span class="text-xs text-indigo-300 font-medium">Event analysis &amp; copywriting</span>
                </div>
              </div>
            </div>
            <!-- Step 3: Get Your Content Card -->
            <div class="animate-fade-in-right animate-delay-700 group hover-lift hover:border-pink-500/30 transition-all duration-500 hover:shadow-2xl hover:shadow-pink-500/20 text-center border border-gray-700/50 rounded-2xl p-8 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 flex flex-col min-h-[26rem]">
              <div class="flex items-center justify-center w-16 h-16 rounded-full bg-brand-100 text-brand-600 mb-4"pink-500 to-rose-500 mb-4 icon-animate" style="animation-delay: 0.4s;">
                <i data-lucide="download-cloud" class="w-8 h-8 text-white"></i>
              </div>
              <h3 class="text-lg font-semibold text-white mb-2">3. Get Your Content</h3>
              <p class="mt-2 text-sm text-gray-500">Instantly download perfectly sized posts and captions for Facebook, Instagram, Twitter, LinkedIn, and more.</p>
              <div class="mt-auto pt-6">
                <div class="flex items-center justify-center bg-pink-900/30 border border-pink-500/20 rounded-full py-1.5 px-4 max-w-max mx-auto">
                    <i data-lucide="check-circle" class="w-4 h-4 text-pink-400 mr-2"></i>
                    <span class="text-xs text-pink-300 font-medium">Social-ready assets</span>
                </div>
              </div>
            </div>
          </div>
      </div>
    </section>

    <!-- Why Event Organizers Love Us -->
    <section id="benefits" class="min-h-screen flex flex-col justify-center py-20 lg:py-32 bg-gray-950 px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl font-satoshi" mb-12">Why Event Organizers Love Us</h2>
      <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto" text-gray-400 mb-10">Go from poster to promotion in minutes. Here's how PosterMagic empowers you:</p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 max-w-6xl mx-auto">
        <!-- Benefit card -->
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-brand-500/10 hover-lift">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="zap" class="w-6 h-6 text-brand-400"></i>
            <h3 class="font-bold text-gray-900">Save Hours, Not Minutes</h3>
          </div>
          <p class="mt-2 text-sm text-gray-500">Get your social campaigns live in record time, freeing you to focus on your event.</p>
        </div>
        <!-- Benefit card -->
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-indigo-500/10 hover-lift">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="layout-template" class="w-6 h-6 text-indigo-400"></i>
            <h3 class="font-bold text-gray-900">Perfectly Formatted Posts</h3>
          </div>
          <p class="mt-2 text-sm text-gray-500">No more resizing headaches. Get assets optimized for all major platforms instantly.</p>
        </div>
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-green-500/10 hover-lift">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="search-check" class="w-6 h-6 text-green-400"></i>
            <h3 class="font-bold text-gray-900">Boost Your Reach</h3>
          </div>
          <p class="mt-2 text-sm text-gray-500">AI-powered keyword and hashtag suggestions to maximize visibility and engagement.</p>
        </div>
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-pink-500/10 hover-lift">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="smartphone-device" class="w-6 h-6 text-pink-400"></i>
            <h3 class="font-bold text-gray-900">Engage Everywhere</h3>
          </div>
          <p class="mt-2 text-sm text-gray-500">Content that looks stunning and performs beautifully on any device, desktop or mobile.</p>
        </div>
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-yellow-500/10 hover-lift">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="trending-up" class="w-6 h-6 text-yellow-400"></i>
            <h3 class="font-bold text-gray-900">Maximize Your ROI</h3>
          </div>
          <p class="mt-2 text-sm text-gray-500">Affordable plans designed to fit any event budget, delivering maximum impact for your spend.</p>
        </div>
        <div class="border border-gray-700/50 rounded-2xl p-6 backdrop-blur-sm bg-gray-900/30 hover:bg-gray-900/50 transition-all duration-300 hover:shadow-xl hover:shadow-purple-500/10 hover-lift">
          <div class="flex items-center gap-3 mb-3">
            <i data-lucide="target" class="w-6 h-6 text-purple-400"></i>
            <h3 class="font-bold text-gray-900">Drive Real Results</h3>
          </div>
          <p class="mt-2 text-sm text-gray-500">Create compelling content that captivates your audience and measurably boosts attendance.</p>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="min-h-screen flex items-center py-20 lg:py-32 bg-slate-800 dark:bg-white">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl font-satoshi" mb-12 text-white dark:text-gray-900">Hear From Our Happy Users</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Testimonial 1 -->
          <div class="bg-gray-900/50 dark:bg-gray-50 border borderclass="p-8 bg-gray-50 border border-gray-100 rounded-2xl shadow-lg animate-slide-up">
            <div class="flex items-center mb-4">
              <img src="https://via.placeholder.com/40x40.png?text=SL" alt="User Sarah L." class="w-10 h-10 rounded-full mr-3 border-2 border-brand-500">
              <div>
                <p class="font-semibold text-white dark:text-gray-800">Sarah L.</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">Music Festival Organizer</p>
              </div>
            </div>
            <p class="text-gray-300 dark:text-gray-600 text-sm italic">"PosterMagic saved us countless hours! What used to take days now takes minutes. The AI-generated content is spot on and boosted our engagement significantly."</p>
          </div>
          <!-- Testimonial 2 -->
          <div class="bg-gray-900/50 dark:bg-gray-50 border borderclass="p-8 bg-gray-50 border border-gray-100 rounded-2xl shadow-lg animate-slide-up">
            <div class="flex items-center mb-4">
              <img src="https://via.placeholder.com/40x40.png?text=MP" alt="User Mike P." class="w-10 h-10 rounded-full mr-3 border-2 border-brand-500">
              <div>
                <p class="font-semibold text-white dark:text-gray-800">Mike P.</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">Conference Manager</p>
              </div>
            </div>
            <p class="text-gray-300 dark:text-gray-600 text-sm italic">"The platform variety is incredible. We launched campaigns across 5 social networks with perfectly tailored content. Highly recommend!"</p>
          </div>
          <!-- Testimonial 3 -->
          <div class="bg-gray-900/50 dark:bg-gray-50 border borderclass="p-8 bg-gray-50 border border-gray-100 rounded-2xl shadow-lg animate-slide-up">
            <div class="flex items-center mb-4">
              <img src="https://via.placeholder.com/40x40.png?text=CW" alt="User Chen W." class="w-10 h-10 rounded-full mr-3 border-2 border-brand-500">
              <div>
                <p class="font-semibold text-white dark:text-gray-800">Chen W.</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">Startup Event Lead</p>
{{ ... }}
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="min-h-screen flex flex-col justify-center py-20 lg:py-32 bg-gray-900/20 px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl font-satoshi" mb-12 text-white">Choose Your Magic Plan</h2>
      <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto" text-gray-400 mb-10">Flexible plans for every event size and need. Get started for free!</p>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
        <!-- Free Plan -->
        <div class="border border-gray-700/50 rounded-2xl p-8 backdrop-blur-sm bg-gray-900/40 flex flex-col hover-lift">
          <h3 class="text-xl font-semibold text-white mb-2">Free Start</h3>
          <p class="text-sm text-gray-400 mb-1">Test the Waters</p>
          <div class="mt-4 text-xl font-bold text-gray-900" my-4">$0 <span class="text-base font-normal text-gray-400">/ month</span></div>
          <ul class="space-y-2 text-sm text-gray-400 mb-6 flex-1">
            <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>1 poster processing / month</li>
            <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>Standard AI analysis</li>
            <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>Watermarked outputs</li>
            <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>Community support</li>
          </ul>
          <button class="mt-auto w-full py-3 rounded-lg bg-gray-700 hover:bg-gray-600 text-white font-semibold transition-colors hover:scale-105 transform">Start for Free</button>
        </div>
        <!-- Viral Plan (Most Popular) -->
        <div class="border-2 border-brand-500 rounded-2xl p-8 backdrop-blur-sm bg-gray-900/60 flex flex-col shadow-2xl shadow-brand-500/30 transform scale-105 hover-lift">
          <div class="flex justify-between items-center mb-2">
            <h3 class="text-2xl font-semibold leading-6 text-gray-900">Viral Campaign</h3>
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-brand-600 text-white">Most Popular</span>
          </div>
          <p class="text-sm text-gray-400 mb-1">Amplify Your Reach</p>
          <div class="text-4xl font-bold text-brand-400 my-4">$29 <span class="text-base font-normal text-gray-400">/ month</span></div>
          <ul class="space-y-2 text-sm text-gray-400 mb-6 flex-1">
{{ ... }}
        </div>
        <!-- Event Plan -->
        <div class="border border-gray-700/50 rounded-2xl p-8 backdrop-blur-sm bg-gray-900/40 flex flex-col hover-lift">
          <h3 class="text-xl font-semibold text-white mb-2">Event Campaign</h3>
          <p class="text-sm text-gray-400 mb-1">Dominate Your Event</p>
          <div class="mt-4 text-xl font-bold text-gray-900" my-4">$49 <span class="text-base font-normal text-gray-400">/ month</span></div>
          <ul class="space-y-2 text-sm text-gray-400 mb-6 flex-1">
            <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>Unlimited posters</li>
            <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>Advanced AI features & customization</li>
            <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>Priority rendering queue</li>
            <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 text-green-500 mr-2"></i>Dedicated success manager</li>
{{ ... }}

    <!-- CTA Section -->
    <!-- FAQ & CTA Wrapper -->
    <div id="faq">
        <!-- FAQ Section -->
        <section class="py-16 lg:py-24 bg-gray-50" dark:bg-white">
          <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl font-satoshi" mb-12 text-white dark:text-gray-900">Frequently Asked Questions</h2>
            <div class="grid md:grid-cols-2 gap-6">
              <!-- FAQ Item 1 -->
              <details class="p-8 rounded-2xl bg-white/70 backdrop-blur-sm border border-gray-200 flex flex-col items-center text-center animate-slide-up"lg p-6 cursor-pointer hover:bg-gray-800/50 dark:hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-white dark:text-gray-800 list-none">
                  What file types can I upload?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="pb-6 text-gray-600" dark:text-gray-600 mt-4 text-sm">PosterMagic currently supports PNG, JPG/JPEG, and PDF files. We recommend using high-resolution images for the best results.</p>
              </details>
              <!-- FAQ Item 2 -->
              <details class="p-8 rounded-2xl bg-white/70 backdrop-blur-sm border border-gray-200 flex flex-col items-center text-center animate-slide-up"lg p-6 cursor-pointer hover:bg-gray-800/50 dark:hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-white dark:text-gray-800 list-none">
                  How does the AI work?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="pb-6 text-gray-600" dark:text-gray-600 mt-4 text-sm">Our AI analyzes the visual elements and text on your poster to understand key information like event title, date, time, location, and theme. It then uses this to generate contextually relevant social media posts, including captions, hashtags, and calls to action, formatted for different platforms.</p>
              </details>
              <!-- FAQ Item 3 -->
              <details class="p-8 rounded-2xl bg-white/70 backdrop-blur-sm border border-gray-200 flex flex-col items-center text-center animate-slide-up"lg p-6 cursor-pointer hover:bg-gray-800/50 dark:hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-white dark:text-gray-800 list-none">
                  Can I customize the generated content?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="pb-6 text-gray-600" dark:text-gray-600 mt-4 text-sm">Yes! While our AI provides excellent starting points, you'll have the ability to review and edit all generated text and select from different visual styles before finalizing your posts. We aim to give you both speed and control.</p>
              </details>
              <!-- FAQ Item 4 -->
              <details class="p-8 rounded-2xl bg-white/70 backdrop-blur-sm border border-gray-200 flex flex-col items-center text-center animate-slide-up"lg p-6 cursor-pointer hover:bg-gray-800/50 dark:hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-white dark:text-gray-800 list-none">
                  What social media platforms are supported?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="pb-6 text-gray-600" dark:text-gray-600 mt-4 text-sm">We generate content optimized for major platforms including Facebook, Instagram (Feed, Stories, Reels), Twitter/X, LinkedIn, and Pinterest. We're always working to add support for more!</p>
              </details>

              <!-- FAQ Item 5 -->
              <details class="p-8 rounded-2xl bg-white/70 backdrop-blur-sm border border-gray-200 flex flex-col items-center text-center animate-slide-up"lg p-6 cursor-pointer hover:bg-gray-800/50 dark:hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-white dark:text-gray-800 list-none">
                  Is there a free trial available?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="pb-6 text-gray-600" dark:text-gray-600 mt-4 text-sm">Yes, we offer a free trial that allows you to generate content for one poster. This is a great way to see the magic for yourself before committing to a paid plan.</p>
              </details>

              <!-- FAQ Item 6 -->
              <details class="p-8 rounded-2xl bg-white/70 backdrop-blur-sm border border-gray-200 flex flex-col items-center text-center animate-slide-up"lg p-6 cursor-pointer hover:bg-gray-800/50 dark:hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-white dark:text-gray-800 list-none">
                  What if I'm not happy with the results?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="pb-6 text-gray-600" dark:text-gray-600 mt-4 text-sm">We offer a 7-day money-back guarantee on all our plans. If you're not satisfied with PosterMagic, just contact our support team within 7 days of your purchase for a full refund.</p>
              </details>
            </div>
          </div>
        </section>

{{ ... }}

    <!-- Close main container -->
    </div>

    <!-- Footer -->
    <footer class="p-8 rounded-xl bg-white border border-gray-200 hover-lift hover-glow"6 lg:px-8 py-12 text-gray-400">
      <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <a href="#" class="text-2xl font-bold font-satoshi text-white">PostrMagic</a>
          <p class="mt-4 text-sm">Transform any poster into a social media campaign in seconds.</p>
        </div>
{{ ... }}
          <h4 class="font-semibold text-gray-900 mb-3">Product</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="#how-it-works" class="hover:text-gray-900">How It Works</a></li>
            <li><a href="#benefits" class="hover:text-gray-900">Benefits</a></li>
            <li><a href="#testimonials" class="hover:text-gray-900">Testimonials</a></li>
            <li><a href="#pricing" class="hover:text-gray-900">Pricing</a></li>
            <li><a href="#faq" class="hover:text-gray-900">FAQ</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold text-gray-900 mb-3">Company</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="#about" class="hover:text-gray-900">About</a></li>
            <li><a href="#careers" class="hover:text-gray-900">Careers</a></li>
            <li><a href="#contact" class="hover:text-gray-900">Contact</a></li>
          </ul>
        </div>
      </div>
      <div class="mt-10 text-center text-xs text-gray-500"> 2025 PostrMagic. Built with by your design team.</div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTopBtn" class="fixed bottom-8 right-8 bg-brand-600 hover:bg-brand-700 text-white p-3 rounded-full shadow-lg transition-opacity duration-300 opacity-0 pointer-events-none z-50 hover:scale-110 transform">
      <i data-lucide="arrow-up" class="w-6 h-6"></i>
    </button>

    <script>
      // Initialize Lucide icons
      lucide.createIcons();
      
      document.addEventListener('DOMContentLoaded', function() {
        // Intersection Observer for general animations
        const observerOptions = {
          threshold: 0.1,
          rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.style.opacity = '1';
              // Add specific animation classes if needed or trigger them here
              if (entry.target.classList.contains('icon-animate')) {
                entry.target.classList.add('animate-fade-in-up'); // Example, adjust as needed
              }
            }
          });
        }, observerOptions);
        
        document.querySelectorAll('.animate-fade-in-up, .animate-fade-in-down, .animate-fade-in-left, .animate-fade-in-right, .animate-blur-in, .animate-slide-up, .icon-animate').forEach(el => {
          observer.observe(el);
        });

        // Enhanced card interactions (hover-lift already has CSS, this is for z-index if complex overlaps occur)
        const cards = document.querySelectorAll('.hover-lift');
        cards.forEach(card => {
          card.addEventListener('mouseenter', function() {
            // this.style.zIndex = '10'; // Usually not needed if layout is simple
          });
          card.addEventListener('mouseleave', function() {
            // this.style.zIndex = '1';
          });
        });

        // FAQ Accordion
        const detailsElements = document.querySelectorAll('#faq details');
        detailsElements.forEach(details => {
          details.addEventListener('toggle', function() {
            if (this.open) {
              detailsElements.forEach(otherDetails => {
                if (otherDetails !== this && otherDetails.open) {
                  // otherDetails.open = false; // Optional: close other FAQs when one opens
                }
              });
            }
          });
        });

        // Back to Top Button
        const backToTopBtn = document.getElementById('backToTopBtn');
        window.addEventListener('scroll', function() {
          if (window.pageYOffset > 300) {
            backToTopBtn.style.opacity = '1';
            backToTopBtn.style.pointerEvents = 'auto';
          } else {
            backToTopBtn.style.opacity = '0';
            backToTopBtn.style.pointerEvents = 'none';
          }
        });

        backToTopBtn.addEventListener('click', function() {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        });

      });
    </script>
  </body>
</html>
