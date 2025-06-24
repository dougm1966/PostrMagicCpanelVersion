<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PostrMagic – AI Event Poster to Social Media Magic</title>
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
<body class="font-inter bg-white text-gray-900 antialiased overflow-x-hidden">
  <div class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="animate-fade-in-down animate-delay-100 flex items-center justify-between px-4 sm:px-6 lg:px-8 xl:px-12 py-4 lg:py-6 border-b border-gray-200/50 bg-white/80 backdrop-blur-sm sticky top-0 z-50">
      <div class="flex items-center gap-3">
        <a href="#" class="lg:text-2xl text-xl font-bold text-gray-900 tracking-tight font-satoshi hover:text-brand-400 transition-colors duration-300">PostrMagic</a>
      </div>
      
      <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-sm font-medium">
        <a href="#how-it-works" class="hover:text-brand-400 text-gray-600 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-100/50 px-3 py-2 rounded-lg">
          <i data-lucide="wand-2" class="w-4 h-4"></i>
          <span class="hidden lg:inline">How It Works</span>
        </a>
        <a href="#benefits" class="hover:text-brand-400 text-gray-600 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-100/50 px-3 py-2 rounded-lg">
          <i data-lucide="sparkles" class="w-4 h-4"></i>
          <span class="hidden lg:inline">Benefits</span>
        </a>
        <a href="#testimonials" class="hover:text-brand-400 text-gray-600 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-100/50 px-3 py-2 rounded-lg">
          <i data-lucide="message-circle" class="w-4 h-4"></i>
          <span class="hidden lg:inline">Testimonials</span>
        </a>
        <a href="#pricing" class="hover:text-brand-400 text-gray-600 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-100/50 px-3 py-2 rounded-lg">
          <i data-lucide="credit-card" class="w-4 h-4"></i>
          <span class="hidden lg:inline">Pricing</span>
        </a>
        <a href="#faq" class="hover:text-brand-400 text-gray-600 transition-all duration-300 flex items-center gap-2 hover:scale-105 hover:bg-gray-100/50 px-3 py-2 rounded-lg">
          <i data-lucide="help-circle" class="w-4 h-4"></i>
          <span class="hidden lg:inline">FAQ</span>
        </a>
      </nav>
      
      <div class="flex items-center gap-2 lg:gap-4">
        
        <a href="login.php" class="hidden sm:flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-all duration-300 px-3 py-2 rounded-lg hover:bg-gray-100/50 hover:scale-105">
          <i data-lucide="log-in" class="w-4 h-4"></i>
          Sign in
        </a>
        <button class="flex items-center gap-2 text-xs lg:text-sm font-semibold px-3 lg:px-5 py-2 lg:py-2.5 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-brand-500/50 hover:scale-105 hover:-translate-y-1">
          <i data-lucide="play-circle" class="w-4 h-4"></i>
          <span class="hidden sm:inline">Try PostrMagic Free</span>
          <span class="sm:hidden">Try Free</span>
        </button>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-white via-gray-50 to-white px-4 sm:px-6 lg:px-8 pt-12 pb-16 lg:py-24">
      <!-- Background Elements -->
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-brand-900/20 via-transparent to-transparent"></div>
      <div class="absolute top-0 left-1/4 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl animate-pulse"></div>
      <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
      
      <div class="relative z-10 max-w-7xl mx-auto text-center">
        <!-- Badge -->
        <div class="animate-fade-in-up animate-delay-200 inline-flex items-center gap-2 px-4 py-2 bg-brand-50/80 border border-brand-200/50 text-brand-700 rounded-full text-sm font-medium mb-8 backdrop-blur-sm hover:bg-brand-100/70 hover:border-brand-300/70 transition-all duration-300 hover:scale-105">
          <i data-lucide="trending-up" class="w-4 h-4"></i>
          <span class="hidden sm:inline">Trusted by 125,000+ teams at</span>
          <span class="sm:hidden">Used by 125k+ teams</span>
          <span class="font-semibold">Microsoft, Spotify, Stripe</span>
        </div>
        <!-- Event Poster Content -->
        <h1 class="animate-blur-in animate-delay-300 max-w-5xl mx-auto text-3xl sm:text-4xl lg:text-6xl xl:text-7xl font-bold tracking-tight font-satoshi text-gray-900 leading-tight lg:leading-[1.1]">
          Transform Your Event Posters Into<br class="hidden sm:block">
          <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-400 hover:to-purple-500 transition-all duration-500">Social Media Magic</span>
        </h1>

        <!-- Subheadline -->
        <p class="animate-fade-in-up animate-delay-400 mx-auto mt-6 lg:mt-8 max-w-3xl text-base leading-relaxed text-gray-700">
          Upload any event poster and get ready to produce stunning social media content. Our AI analyzes your poster to create engaging posts that drive attendance.
        </p>

        <!-- Tag Buttons -->
        <div class="animate-fade-in-up animate-delay-500 mt-6 flex items-center justify-center gap-4">
          <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-full border-2 border-sky-500 text-white bg-gradient-to-r from-sky-500 to-blue-600 shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300">
            <i data-lucide="twitter" class="w-4 h-4"></i>
            Twitter
          </span>
          <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-full border-2 border-pink-600 text-white bg-gradient-to-r from-pink-600 to-rose-600 shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300">
            <i data-lucide="instagram" class="w-4 h-4"></i>
            Instagram
          </span>
          <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-full border-2 border-blue-600 text-white bg-gradient-to-r from-blue-600 to-blue-700 shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300">
            <i data-lucide="facebook" class="w-4 h-4"></i>
            Facebook
          </span>
        </div>

        <!-- Poster Upload Form -->
        <div class="animate-slide-up animate-delay-600 mx-auto mt-10 lg:mt-12 max-w-md lg:max-w-2xl">
          <form class="flex flex-col sm:flex-row gap-3 lg:gap-4 items-center justify-center" enctype="multipart/form-data" method="post" action="#">
            <label for="poster-upload" class="flex flex-col items-center justify-center w-full sm:w-auto gap-2 cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50/50 hover:bg-gray-100/50 transition-colors duration-300 hover:scale-105">
              <i data-lucide="upload" class="w-8 h-8 text-brand-400"></i>
              <span class="text-sm text-gray-600">Drag & drop or click to upload poster (PNG/JPG/PDF)</span>
              <input id="poster-upload" name="poster" type="file" accept=".png,.jpg,.jpeg,.pdf" class="hidden" required>
            </label>
            <button type="submit" class="flex gap-2 lg:px-8 lg:py-4 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 hover:shadow-xl hover:shadow-brand-500/25 lg:text-base group text-sm font-semibold text-white rounded-xl pt-3 pr-6 pb-3 pl-6 items-center justify-center hover:scale-105 hover:-translate-y-1">
              <span>Upload & See The Magic</span>
              <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300"></i>
            </button>
          </form>
          <p class="mt-4 text-xs lg:text-sm text-gray-600 flex flex-wrap items-center justify-center gap-4">
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

    <!-- Poster Showcase Section -->
    <section id="testimonials" class="min-h-screen flex items-center py-20 lg:py-32 bg-gradient-to-br from-slate-50 to-blue-50/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl sm:text-4xl font-bold font-satoshi mb-4 text-gray-900">See PostrMagic in Action</h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">Real posters, real results. See how our AI transforms event posters into engaging social media content.</p>
        </div>
        
        <!-- Showcase Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
          
          <!-- Example 1: Community Festival -->
          <div class="group">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
              <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                    <i data-lucide="music" class="w-4 h-4 text-white"></i>
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-gray-900">Community Festival</h3>
                  </div>
                </div>
                
                <!-- Clickable Poster -->
                <div class="mb-4 cursor-pointer" onclick="openModal('Test_posters/Community-Fest-Poster.png', 'Community Festival Poster')">
                  <div class="aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img src="Test_posters/Community-Fest-Poster.png" alt="Community Festival Poster" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                  </div>
                  <p class="text-xs text-gray-500 text-center mt-2">Click to view poster</p>
                </div>
                
                <!-- Generated Content Preview -->
                <div class="space-y-3">
                  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="console.log('clicked!'); openSocialModal('facebook', 'community-festival')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="facebook" class="w-3 h-3 text-blue-600"></i>
                      <span class="text-xs font-semibold text-blue-800">Facebook</span>
                    </div>
                    <p class="text-xs text-gray-700">🎵 Ultimate community celebration! Live music, local arts...</p>
                  </div>
                  
                  <div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="openSocialModal('instagram', 'community-festival')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="instagram" class="w-3 h-3 text-pink-600"></i>
                      <span class="text-xs font-semibold text-pink-800">Instagram</span>
                    </div>
                    <p class="text-xs text-gray-700">✨ Weekend vibes! Local artists & community spirit...</p>
                  </div>
                  
                  <div class="bg-gradient-to-r from-sky-50 to-blue-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="openSocialModal('twitter', 'community-festival')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="twitter" class="w-3 h-3 text-sky-600"></i>
                      <span class="text-xs font-semibold text-sky-800">Twitter/X</span>
                    </div>
                    <p class="text-xs text-gray-700">🎵 Join us for the ultimate community celebration...</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Example 2: Pool Tournament -->
          <div class="group">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
              <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center">
                    <i data-lucide="target" class="w-4 h-4 text-white"></i>
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-gray-900">Pool Tournament</h3>
                  </div>
                </div>
                
                <!-- Clickable Poster -->
                <div class="mb-4 cursor-pointer" onclick="openModal('Test_posters/Pool tournament.jpg', 'Pool Tournament Poster')">
                  <div class="aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img src="Test_posters/Pool tournament.jpg" alt="Pool Tournament Poster" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                  </div>
                  <p class="text-xs text-gray-500 text-center mt-2">Click to view poster</p>
                </div>
                
                <!-- Generated Content Preview -->
                <div class="space-y-3">
                  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="openSocialModal('facebook', 'pool-tournament')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="facebook" class="w-3 h-3 text-blue-600"></i>
                      <span class="text-xs font-semibold text-blue-800">Facebook</span>
                    </div>
                    <p class="text-xs text-gray-700">🎱 Think you've got what it takes? Join our championship...</p>
                  </div>
                  
                  <div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="openSocialModal('instagram', 'pool-tournament')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="instagram" class="w-3 h-3 text-pink-600"></i>
                      <span class="text-xs font-semibold text-pink-800">Instagram</span>
                    </div>
                    <p class="text-xs text-gray-700">🎱 Championship vibes! Ready to sink some shots...</p>
                  </div>
                  
                  <div class="bg-gradient-to-r from-sky-50 to-blue-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="openSocialModal('twitter', 'pool-tournament')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="twitter" class="w-3 h-3 text-sky-600"></i>
                      <span class="text-xs font-semibold text-sky-800">Twitter/X</span>
                    </div>
                    <p class="text-xs text-gray-700">🎱 Think you've got what it takes? Join our championship...</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Example 3: CMF Lineup -->
          <div class="group">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
              <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center">
                    <i data-lucide="mic-2" class="w-4 h-4 text-white"></i>
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-gray-900">Music Festival</h3>
                  </div>
                </div>
                
                <!-- Clickable Poster -->
                <div class="mb-4 cursor-pointer" onclick="openModal('Test_posters/CMF-lineup-poster.jpg', 'CMF Lineup Poster')">
                  <div class="aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img src="Test_posters/CMF-lineup-poster.jpg" alt="CMF Lineup Poster" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                  </div>
                  <p class="text-xs text-gray-500 text-center mt-2">Click to view poster</p>
                </div>
                
                <!-- Generated Content Preview -->
                <div class="space-y-3">
                  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="openSocialModal('facebook', 'music-festival')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="facebook" class="w-3 h-3 text-blue-600"></i>
                      <span class="text-xs font-semibold text-blue-800">Facebook</span>
                    </div>
                    <p class="text-xs text-gray-700">🔥 The wait is over! Festival lineup that will blow your mind...</p>
                  </div>
                  
                  <div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="openSocialModal('instagram', 'music-festival')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="instagram" class="w-3 h-3 text-pink-600"></i>
                      <span class="text-xs font-semibold text-pink-800">Instagram</span>
                    </div>
                    <p class="text-xs text-gray-700">🎤 Incredible lineup announced! Get your tickets before...</p>
                  </div>
                  
                  <div class="bg-gradient-to-r from-sky-50 to-blue-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="openSocialModal('twitter', 'music-festival')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="twitter" class="w-3 h-3 text-sky-600"></i>
                      <span class="text-xs font-semibold text-sky-800">Twitter/X</span>
                    </div>
                    <p class="text-xs text-gray-700">🎤 Epic lineup drop! This festival is going to be legendary...</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Example 4: Event Flyer -->
          <div class="group">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
              <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4 text-white"></i>
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-gray-900">Conference</h3>
                  </div>
                </div>
                
                <!-- Clickable Poster -->
                <div class="mb-4 cursor-pointer" onclick="openModal('Test_posters/FLYER-EED-Visitors-FINAL-2024.jpg', 'Conference Flyer')">
                  <div class="aspect-[3/4] bg-gray-100 rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                    <img src="Test_posters/FLYER-EED-Visitors-FINAL-2024.jpg" alt="Conference Flyer" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                  </div>
                  <p class="text-xs text-gray-500 text-center mt-2">Click to view poster</p>
                </div>
                
                <!-- Generated Content Preview -->
                <div class="space-y-3">
                  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="openSocialModal('facebook', 'conference')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="facebook" class="w-3 h-3 text-blue-600"></i>
                      <span class="text-xs font-semibold text-blue-800">Facebook</span>
                    </div>
                    <p class="text-xs text-gray-700">Join industry leaders at this year's conference. Network...</p>
                  </div>
                  
                  <div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="openSocialModal('instagram', 'conference')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="instagram" class="w-3 h-3 text-pink-600"></i>
                      <span class="text-xs font-semibold text-pink-800">Instagram</span>
                    </div>
                    <p class="text-xs text-gray-700">📊 Innovation meets inspiration at this year's conference...</p>
                  </div>
                  
                  <div class="bg-gradient-to-r from-sky-50 to-blue-50 rounded-lg p-3 cursor-pointer hover:shadow-md transition-shadow duration-200" onclick="openSocialModal('twitter', 'conference')">
                    <div class="flex items-center gap-2 mb-1">
                      <i data-lucide="twitter" class="w-3 h-3 text-sky-600"></i>
                      <span class="text-xs font-semibold text-sky-800">Twitter/X</span>
                    </div>
                    <p class="text-xs text-gray-700">📅 Don't miss out! Early bird tickets available for...</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
        </div>
        
        <!-- Stats Section -->
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
              <i data-lucide="zap" class="w-8 h-8 text-white"></i>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-2">3 Minutes</div>
            <div class="text-gray-600">Average processing time</div>
          </div>
          <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full flex items-center justify-center mx-auto mb-4">
              <i data-lucide="share-2" class="w-8 h-8 text-white"></i>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-2">5+ Platforms</div>
            <div class="text-gray-600">Social media formats</div>
          </div>
          <div class="text-center">
            <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-4">
              <i data-lucide="trending-up" class="w-8 h-8 text-white"></i>
            </div>
            <div class="text-3xl font-bold text-gray-900 mb-2">300%</div>
            <div class="text-gray-600">Average engagement boost</div>
          </div>
        </div>
        
      </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="min-h-screen flex items-center py-20 lg:py-32 bg-gray-50">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- How PostrMagic Works Title & Subtitle -->
        <div class="mb-12 lg:mb-16 text-center">
          <h2 class="text-3xl sm:text-4xl font-bold font-satoshi mb-4 text-gray-900">How PostrMagic Works</h2>
          <p class="max-w-2xl mx-auto text-lg text-gray-700">Transform your poster into engaging social content in 3 simple steps.</p>
        </div>
        <!-- Step Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
            <!-- Step 1: Upload Poster Card -->
            <div class="animate-fade-in-left animate-delay-700 group hover-lift hover:border-green-500/30 transition-all duration-500 hover:shadow-2xl hover:shadow-green-500/20 text-center border border-gray-100 rounded-3xl p-10 bg-white shadow-lg hover:shadow-xl flex flex-col min-h-[28rem]">
              <div class="flex items-center justify-center w-16 h-16 mx-auto rounded-xl bg-gradient-to-br from-green-500 to-green-600 mb-4 icon-animate">
                <i data-lucide="file-up" class="w-8 h-8 text-white"></i>
              </div>
              <h3 class="text-2xl font-bold text-gray-900 mb-4">1. Upload Your Poster</h3>
              <p class="text-gray-600 leading-relaxed text-base px-4">Simply drag &amp; drop your event poster (PNG, JPG, or PDF) or select it from your device.</p>
              <div class="mt-auto pt-6">
                <div class="inline-flex items-center gap-2 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-full py-3 px-6 shadow-sm">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                    <span class="text-sm text-green-700 font-semibold">Works with any image quality</span>
                </div>
              </div>
            </div>
            <!-- Step 2: AI Magic Card -->
            <div class="animate-slide-up animate-delay-800 border-brand-500/30 hover:border-brand-500/60 hover-lift hover:shadow-2xl hover:shadow-brand-500/30 transition-all duration-500 text-center bg-gradient-to-t from-indigo-50/80 to-purple-50/60 hover:from-indigo-100/80 hover:to-purple-100/70 border rounded-3xl p-10 backdrop-blur-sm group flex flex-col min-h-[28rem]">
              <div class="flex items-center justify-center w-16 h-16 mx-auto rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 mb-4 icon-animate" style="animation-delay: 0.2s;">
                <i data-lucide="wand-2" class="w-8 h-8 text-white"></i>
              </div>
              <h3 class="text-2xl font-bold text-gray-900 mb-4">2. AI Magic Happens</h3>
              <p class="text-gray-600 leading-relaxed text-base px-4">Our intelligent system analyzes your design, extracts key info, and crafts compelling social media content.</p>
              <div class="mt-auto pt-6">
                <div class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-full py-3 px-6 shadow-sm">
                    <i data-lucide="check-circle" class="w-5 h-5 text-indigo-600"></i>
                    <span class="text-sm text-indigo-700 font-semibold">Event analysis &amp; copywriting</span>
                </div>
              </div>
            </div>
            <!-- Step 3: Get Your Content Card -->
            <div class="animate-fade-in-right animate-delay-700 group hover-lift hover:border-pink-500/30 transition-all duration-500 hover:shadow-2xl hover:shadow-pink-500/20 text-center border border-gray-100 rounded-3xl p-10 bg-white shadow-lg hover:shadow-xl flex flex-col min-h-[28rem]">
              <div class="flex items-center justify-center w-16 h-16 mx-auto rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 mb-4 icon-animate" style="animation-delay: 0.4s;">
                <i data-lucide="download-cloud" class="w-8 h-8 text-white"></i>
              </div>
              <h3 class="text-2xl font-bold text-gray-900 mb-4">3. Get Your Content</h3>
              <p class="text-gray-600 leading-relaxed text-base px-4">Instantly download perfectly sized posts and captions for Facebook, Instagram, Twitter, LinkedIn, and more.</p>
              <div class="mt-auto pt-6">
                <div class="inline-flex items-center gap-2 bg-gradient-to-r from-pink-50 to-rose-50 border border-pink-200 rounded-full py-3 px-6 shadow-sm">
                    <i data-lucide="check-circle" class="w-5 h-5 text-pink-600"></i>
                    <span class="text-sm text-pink-700 font-semibold">Social-ready assets</span>
                </div>
              </div>
            </div>
          </div>
      </div>
    </section>

    <!-- Why Event Organizers Love Us -->
    <section id="benefits" class="min-h-screen flex flex-col justify-center py-20 lg:py-32 bg-gradient-to-br from-gray-50 to-blue-50/30 px-4 sm:px-6 lg:px-8">
      <h2 class="text-center text-3xl sm:text-4xl font-bold font-satoshi mb-12 text-gray-900">Why Event Organizers Love Us</h2>
      <p class="text-center max-w-2xl mx-auto text-gray-600 mb-10">Go from poster to promotion in minutes. Here's how PostrMagic empowers you:</p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 max-w-6xl mx-auto">
        <!-- Benefit card -->
        <div class="relative border border-gray-100 rounded-3xl p-8 bg-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 hover:scale-105 group overflow-hidden">
          <div class="flex items-center gap-4 mb-6">
            <div class="p-3 rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 shadow-lg group-hover:shadow-xl group-hover:shadow-yellow-500/25 transition-all duration-300">
              <i data-lucide="zap" class="w-6 h-6 text-white"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 group-hover:text-yellow-600 transition-colors duration-300">Save Hours, Not Minutes</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">Get your social campaigns live in record time, freeing you to focus on your event.</p>
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-yellow-100/50 to-transparent rounded-full -translate-y-8 translate-x-8 group-hover:scale-150 transition-transform duration-700"></div>
        </div>
        <!-- Benefit card -->
        <div class="relative border border-gray-100 rounded-3xl p-8 bg-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 hover:scale-105 group overflow-hidden">
          <div class="flex items-center gap-4 mb-6">
            <div class="p-3 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg group-hover:shadow-xl group-hover:shadow-indigo-500/25 transition-all duration-300">
              <i data-lucide="layout-template" class="w-6 h-6 text-white"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors duration-300">Perfectly Formatted Posts</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">No more resizing headaches. Get assets optimized for all major platforms instantly.</p>
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-indigo-100/50 to-transparent rounded-full -translate-y-8 translate-x-8 group-hover:scale-150 transition-transform duration-700"></div>
        </div>
        <div class="relative border border-gray-100 rounded-3xl p-8 bg-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 hover:scale-105 group overflow-hidden">
          <div class="flex items-center gap-4 mb-6">
            <div class="p-3 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg group-hover:shadow-xl group-hover:shadow-green-500/25 transition-all duration-300">
              <i data-lucide="search-check" class="w-6 h-6 text-white"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 group-hover:text-green-600 transition-colors duration-300">Boost Your Reach</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">AI-powered keyword and hashtag suggestions to maximize visibility and engagement.</p>
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-green-100/50 to-transparent rounded-full -translate-y-8 translate-x-8 group-hover:scale-150 transition-transform duration-700"></div>
        </div>
        <div class="relative border border-gray-100 rounded-3xl p-8 bg-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 hover:scale-105 group overflow-hidden">
          <div class="flex items-center gap-4 mb-6">
            <div class="p-3 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 shadow-lg group-hover:shadow-xl group-hover:shadow-pink-500/25 transition-all duration-300">
              <i data-lucide="smartphone" class="w-6 h-6 text-white"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 group-hover:text-pink-600 transition-colors duration-300">Engage Everywhere</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">Content that looks stunning and performs beautifully on any device, desktop or mobile.</p>
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-pink-100/50 to-transparent rounded-full -translate-y-8 translate-x-8 group-hover:scale-150 transition-transform duration-700"></div>
        </div>
        <div class="relative border border-gray-100 rounded-3xl p-8 bg-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 hover:scale-105 group overflow-hidden">
          <div class="flex items-center gap-4 mb-6">
            <div class="p-3 rounded-2xl bg-gradient-to-br from-amber-500 to-yellow-600 shadow-lg group-hover:shadow-xl group-hover:shadow-amber-500/25 transition-all duration-300">
              <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 group-hover:text-amber-600 transition-colors duration-300">Maximize Your ROI</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">Affordable plans designed to fit any event budget, delivering maximum impact for your spend.</p>
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-amber-100/50 to-transparent rounded-full -translate-y-8 translate-x-8 group-hover:scale-150 transition-transform duration-700"></div>
        </div>
        <div class="relative border border-gray-100 rounded-3xl p-8 bg-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 hover:scale-105 group overflow-hidden">
          <div class="flex items-center gap-4 mb-6">
            <div class="p-3 rounded-2xl bg-gradient-to-br from-purple-500 to-violet-600 shadow-lg group-hover:shadow-xl group-hover:shadow-purple-500/25 transition-all duration-300">
              <i data-lucide="target" class="w-6 h-6 text-white"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 group-hover:text-purple-600 transition-colors duration-300">Drive Real Results</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">Create compelling content that captivates your audience and measurably boosts attendance.</p>
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-purple-100/50 to-transparent rounded-full -translate-y-8 translate-x-8 group-hover:scale-150 transition-transform duration-700"></div>
        </div>
      </div>
    </section>
    
    <!-- Modal for Poster Images -->
    <div id="posterModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden p-4">
      <div class="relative flex items-center justify-center">
        <button onclick="closeModal()" class="absolute top-4 right-4 bg-white text-gray-900 rounded-full p-3 hover:bg-gray-100 transition-colors duration-200 z-20 shadow-lg">
          <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-[95vw] max-h-[95vh] w-auto h-auto object-contain rounded-lg shadow-2xl">
      </div>
    </div>

    <!-- 
    ==========================================
    SOCIAL MEDIA POST MODAL - PRESERVE DESIGN
    ==========================================
    This modal has been carefully designed with:
    - Colorful gradient top navigation bar
    - Glassmorphism navigation controls
    - Top-positioned carousel navigation
    - Interactive dots with scaling effects
    - Professional colorful design that pops
    
    DO NOT CHANGE without explicit approval:
    - The gradient colors (purple-500, pink-500, orange-400)
    - The glassmorphism styling (bg-white/20, backdrop-blur-sm)
    - The top navigation positioning
    - The dot styling and animations
    ==========================================
    -->
    <div id="socialModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden p-4">
      <div class="relative bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-hidden shadow-2xl">
        <!-- 
        TOP NAVIGATION BAR - PRESERVE DESIGN
        Features: Colorful gradient, glassmorphism controls, top positioning
        -->
        <div class="relative bg-gradient-to-r from-purple-500 via-pink-500 to-orange-400 p-4 flex items-center justify-between">
          <!-- Navigation Controls - Left Side -->
          <div class="flex items-center gap-3">
            <button id="prevPost" onclick="navigatePost(-1)" class="bg-white/20 backdrop-blur-sm text-white rounded-full p-2 hover:bg-white/30 transition-all duration-200 shadow-lg border border-white/20">
              <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </button>
            <button id="nextPost" onclick="navigatePost(1)" class="bg-white/20 backdrop-blur-sm text-white rounded-full p-2 hover:bg-white/30 transition-all duration-200 shadow-lg border border-white/20">
              <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </button>
          </div>
          
          <!-- Position Indicators - Center -->
          <div class="flex items-center gap-4 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 border border-white/20">
            <div id="postDots" class="flex gap-2">
              <!-- Interactive dots with scaling animations inserted here by JavaScript -->
            </div>
            <span id="postCounter" class="text-sm text-white font-medium">1 of 3</span>
          </div>
          
          <!-- Close Button - Right Side -->
          <button onclick="closeSocialModal()" class="bg-white/20 backdrop-blur-sm text-white rounded-full p-2 hover:bg-white/30 transition-all duration-200 shadow-lg border border-white/20">
            <i data-lucide="x" class="w-5 h-5"></i>
          </button>
        </div>
        
        <!-- Content Area - Scrollable social media content -->
        <div id="socialContent" class="p-6 overflow-y-auto max-h-[80vh]">
          <!-- Social media content will be inserted here by JavaScript -->
        </div>
      </div>
    </div>

    <!-- Pricing Section -->
    <section id="pricing" class="min-h-screen flex flex-col justify-center py-20 lg:py-32 bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/50 px-4 sm:px-6 lg:px-8">
      <h2 class="text-center text-3xl sm:text-4xl font-bold font-satoshi mb-12 text-gray-900">Choose Your Magic Plan</h2>
      <p class="text-center max-w-2xl mx-auto text-gray-600 mb-10">Flexible plans for every event size and need. Get started for free!</p>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto">
        <!-- Free Plan -->
        <div class="relative border border-slate-200 rounded-3xl p-8 bg-white shadow-lg hover:shadow-xl transition-all duration-500 hover:-translate-y-1 flex flex-col overflow-hidden group">
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-slate-100/50 to-transparent rounded-full -translate-y-8 translate-x-8"></div>
          <div class="relative z-10">
            <h3 class="text-2xl font-bold text-slate-900 mb-2">Free Start</h3>
            <p class="text-slate-600 mb-4">Test the Waters</p>
            <div class="text-5xl font-bold text-slate-900 my-6">$0 <span class="text-lg font-normal text-slate-600">/ month</span></div>
            <ul class="space-y-3 text-slate-700 mb-8 flex-1">
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-green-600"></i></div>1 poster processing / month</li>
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-green-600"></i></div>Standard AI analysis</li>
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-green-600"></i></div>Watermarked outputs</li>
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-green-600"></i></div>Community support</li>
            </ul>
            <button class="mt-auto w-full py-4 rounded-2xl bg-slate-700 hover:bg-slate-800 text-white font-semibold transition-all duration-300 hover:scale-105 transform shadow-lg hover:shadow-xl">Start for Free</button>
          </div>
        </div>
        <!-- Viral Plan (Most Popular) -->
        <div class="relative border-2 border-indigo-300 rounded-3xl p-8 bg-gradient-to-br from-indigo-50 to-purple-50 shadow-2xl hover:shadow-3xl transition-all duration-500 hover:-translate-y-2 flex flex-col group transform scale-105">
          <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-bl from-indigo-200/40 to-transparent rounded-full -translate-y-10 translate-x-10"></div>
          <div class="absolute -top-3 -right-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg transform rotate-12 z-20">Most Popular</div>
          <div class="relative z-10">
            <h3 class="text-2xl font-bold text-indigo-900 mb-2">Viral Campaign</h3>
            <p class="text-indigo-700 mb-4">Amplify Your Reach</p>
            <div class="text-5xl font-bold text-indigo-900 my-6">$29 <span class="text-lg font-normal text-indigo-700">/ month</span></div>
            <ul class="space-y-3 text-indigo-800 mb-8 flex-1">
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-indigo-200 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-indigo-700"></i></div>Up to 15 posters / month</li>
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-indigo-200 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-indigo-700"></i></div>No watermarks</li>
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-indigo-200 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-indigo-700"></i></div>Premium AI analysis & templates</li>
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-indigo-200 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-indigo-700"></i></div>HD exports</li>
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-indigo-200 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-indigo-700"></i></div>Priority email support</li>
            </ul>
            <button class="mt-auto w-full py-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold transition-all duration-300 hover:scale-105 transform shadow-xl hover:shadow-2xl">Go Viral</button>
          </div>
        </div>
        <!-- Event Plan -->
        <div class="relative border border-emerald-200 rounded-3xl p-8 bg-gradient-to-br from-emerald-50 to-teal-50 shadow-lg hover:shadow-xl transition-all duration-500 hover:-translate-y-1 flex flex-col overflow-hidden group">
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-emerald-100/50 to-transparent rounded-full -translate-y-8 translate-x-8"></div>
          <div class="relative z-10">
            <h3 class="text-2xl font-bold text-emerald-900 mb-2">Event Campaign</h3>
            <p class="text-emerald-700 mb-4">Dominate Your Event</p>
            <div class="text-5xl font-bold text-emerald-900 my-6">$49 <span class="text-lg font-normal text-emerald-700">/ month</span></div>
            <ul class="space-y-3 text-emerald-800 mb-8 flex-1">
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-emerald-200 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-emerald-700"></i></div>Unlimited posters</li>
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-emerald-200 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-emerald-700"></i></div>Advanced AI features & customization</li>
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-emerald-200 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-emerald-700"></i></div>Priority rendering queue</li>
              <li class="flex items-center gap-3"><div class="w-5 h-5 rounded-full bg-emerald-200 flex items-center justify-center"><i data-lucide="check" class="w-3 h-3 text-emerald-700"></i></div>Dedicated success manager</li>
            </ul>
            <button class="mt-auto w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold transition-all duration-300 hover:scale-105 transform shadow-lg hover:shadow-xl">Get Event Power</button>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <!-- FAQ & CTA Wrapper -->
    <div id="faq" class="pt-20">
        <!-- FAQ Section -->
        <section class="py-16 lg:py-24 bg-slate-800 dark:bg-white">
          <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-3xl sm:text-4xl font-bold font-satoshi mb-12 text-white dark:text-gray-900">Frequently Asked Questions</h2>
            <div class="grid md:grid-cols-2 gap-6">
              <!-- FAQ Item 1 -->
              <details class="group bg-white border border-gray-200 rounded-lg p-6 cursor-pointer hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-gray-800 list-none">
                  What file types can I upload?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="text-gray-600 mt-4 text-sm">PostrMagic currently supports PNG, JPG/JPEG, and PDF files. We recommend using high-resolution images for the best results.</p>
              </details>
              <!-- FAQ Item 2 -->
              <details class="group bg-white border border-gray-200 rounded-lg p-6 cursor-pointer hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-gray-800 list-none">
                  How does the AI work?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="text-gray-600 mt-4 text-sm">Our AI analyzes the visual elements and text on your poster to understand key information like event title, date, time, location, and theme. It then uses this to generate contextually relevant social media posts, including captions, hashtags, and calls to action, formatted for different platforms.</p>
              </details>
              <!-- FAQ Item 3 -->
              <details class="group bg-white border border-gray-200 rounded-lg p-6 cursor-pointer hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-gray-800 list-none">
                  Can I customize the generated content?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="text-gray-600 mt-4 text-sm">Yes! While our AI provides excellent starting points, you'll have the ability to review and edit all generated text and select from different visual styles before finalizing your posts. We aim to give you both speed and control.</p>
              </details>
              <!-- FAQ Item 4 -->
              <details class="group bg-white border border-gray-200 rounded-lg p-6 cursor-pointer hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-gray-800 list-none">
                  What social media platforms are supported?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="text-gray-600 mt-4 text-sm">We generate content optimized for major platforms including Facebook, Instagram (Feed, Stories, Reels), Twitter/X, LinkedIn, and Pinterest. We're always working to add support for more!</p>
              </details>

              <!-- FAQ Item 5 -->
              <details class="group bg-white border border-gray-200 rounded-lg p-6 cursor-pointer hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-gray-800 list-none">
                  Is there a free trial available?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="text-gray-600 mt-4 text-sm">Yes, we offer a free trial that allows you to generate content for one poster. This is a great way to see the magic for yourself before committing to a paid plan.</p>
              </details>

              <!-- FAQ Item 6 -->
              <details class="group bg-white border border-gray-200 rounded-lg p-6 cursor-pointer hover:bg-gray-100 transition-colors duration-300">
                <summary class="flex justify-between items-center font-semibold text-gray-800 list-none">
                  What if I'm not happy with the results?
                  <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <p class="text-gray-600 mt-4 text-sm">We offer a 7-day money-back guarantee on all our plans. If you're not satisfied with PostrMagic, just contact our support team within 7 days of your purchase for a full refund.</p>
              </details>
            </div>
          </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600 text-center text-white px-4 sm:px-6 lg:px-8">
          <h2 class="text-3xl sm:text-4xl font-bold font-satoshi mb-4">Ready to Unleash Your Event's Potential?</h2>
          <p class="max-w-2xl mx-auto mb-8 text-white/90">Stop wrestling with social media. Start creating stunning campaigns in minutes with PostrMagic.</p>
          <button class="px-8 py-4 bg-white text-gray-900 font-semibold rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all hover:scale-105 transform">Claim Your Free Trial Now</button>
        </section>
    </div>

    <!-- Close main container -->
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200/50 px-4 sm:px-6 lg:px-8 py-12 text-gray-600">
      <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <a href="#" class="text-2xl font-bold font-satoshi text-gray-900">PostrMagic</a>
          <p class="mt-4 text-sm">Transform any poster into a social media campaign in seconds.</p>
        </div>
        <div>
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
      <div class="mt-10 text-center text-xs text-gray-600"> 2025 PostrMagic. Built with by your design team.</div>
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
      
      // Modal functions for poster images
      function openModal(imageSrc, imageAlt) {
        const modal = document.getElementById('posterModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageSrc;
        modalImage.alt = imageAlt;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Re-initialize lucide icons for the modal
        setTimeout(() => {
          lucide.createIcons();
        }, 100);
      }
      
      function closeModal() {
        const modal = document.getElementById('posterModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
      }
      
      // Close modal when clicking outside the image
      document.getElementById('posterModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeModal();
        }
      });
      
      // Prevent modal close when clicking on the image container
      document.getElementById('posterModal').querySelector('div').addEventListener('click', function(e) {
        e.stopPropagation();
      });
      
      // Close modal with escape key and add navigation
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeModal();
          closeSocialModal();
        }
        
        // Navigation for social modal when it's open
        if (!document.getElementById('socialModal').classList.contains('hidden')) {
          if (e.key === 'ArrowLeft') {
            e.preventDefault();
            navigatePost(-1);
          } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            navigatePost(1);
          }
        }
      });

      // Social Media Modal Functions
      const socialMediaContent = {
        'community-festival': {
          facebook: [
            {
              title: 'Vancouver Volcanoes Community Fest 2025',
              type: 'announcement',
              content: `🎉 Get ready for an amazing Community Fest! 

Join the Vancouver Volcanoes, Hoops For Hope, Pop Local, Fourth Plain Community Commons, and SWW Boys & Girls Clubs for an incredible day of community fun!

📅 Date: May 24, 2025
🕒 Time: 3PM - 7PM
📍 Location: Hudson's Bay HS
🎟️ FREE ENTRY!
🏀 Volcanoes Game @ 7PM

Event Features:
🎨 Young Makers Market
🎵 Live Music
🍕 Food Trucks
🛍️ Local Vendors
🏀 1/2 Court Hoop Contests

Special Event: Vancouver Volcanoes game starts at 7PM! Come early for the festival activities and stay for an exciting basketball game.

Presented by vancouvervolcanoes.com

#CommunityFest #VancouverVolcanoes #HoopsForHope #FreeEvent #Basketball #CommunityFun #May24`,
              engagement: '412 likes • 67 comments • 23 shares'
            },
            {
              title: 'Meet the Community Partners',
              type: 'behind-the-scenes',
              content: `🤝 Meet the amazing organizations making Community Fest possible!

🏀 Vancouver Volcanoes - Our hometown basketball heroes bringing the excitement
💫 Hoops For Hope - Creating opportunities for youth through basketball
🎨 Pop Local - Supporting local artists and makers in our community
🏘️ Fourth Plain Community Commons - Building stronger neighborhoods
👦👧 SWW Boys & Girls Clubs - Empowering the next generation

Each of these incredible groups shares our vision: bringing our community together for a day of celebration, connection, and fun.

The Young Makers Market will showcase incredible local talent, while live music sets the perfect soundtrack for an unforgettable day. Plus, local food trucks will keep everyone fueled up for the main event!

Don't miss this chance to support local organizations while having an amazing time with your neighbors. See you May 24th! 

#CommunityPartners #LocalSupport #CommunityFest #VancouverVolcanoes #TogetherWeThrive`,
              engagement: '298 likes • 45 comments • 18 shares'
            },
            {
              title: 'Final Week to Join Us!',
              type: 'urgency',
              content: `⏰ ONE WEEK LEFT until Community Fest! 

Are you ready for the ultimate community celebration? We can't wait to see you at Hudson's Bay HS on May 24th!

🔥 What you can't miss:
• Young makers showcasing incredible local talent
• Live music that'll get you moving
• Food trucks with flavors for everyone
• Exciting hoop contests for all skill levels
• The Vancouver Volcanoes taking the court at 7PM!

💝 And remember - this incredible day is completely FREE!

Tag 3 friends who need to join you for this amazing community celebration. Let's make May 24th a day our neighborhood will never forget!

Who's excited?! Drop a 🏀 in the comments if you're coming!

#CommunityFest #OneWeekLeft #VancouverVolcanoes #FreeEvent #CommunityPride #May24 #SeeYouThere`,
              engagement: '567 likes • 89 comments • 34 shares'
            }
          ],
          instagram: [
            {
              title: 'Vancouver Volcanoes Community Fest 2025',
              type: 'announcement',
              content: `🏀 Community Fest + Volcanoes Game! 🎉

May 24 at Hudson's Bay HS
3PM-7PM + Game @ 7PM

✨ Young Makers Market
🎵 Live Music  
🍕 Food Trucks
🏀 Hoop Contests
🎮 FREE ENTRY!

Who's ready for some community love + basketball? 🔥

#CommunityFest #VancouverVolcanoes #HoopsForHope #Basketball #CommunityLove #FreeEvent #Vancouver #May24`,
              engagement: '1,347 likes • 142 comments'
            },
            {
              title: 'Behind the Scenes Magic',
              type: 'behind-the-scenes',
              content: `✨ The magic behind Community Fest ✨

Meet the dream team making May 24th incredible:

🏀 @vancouvervolcanoes bringing the heat
💫 @hoopsforhope changing lives through basketball  
🎨 @poplocal showcasing amazing local artists
🏘️ @fourthplaincommons building community
👫 @swwboysgirlsclub empowering youth

Together we're creating something special 💙

Can't wait to celebrate with our community family!

#BehindTheScenes #CommunityFest #LocalHeroes #Teamwork #Vancouver #May24`,
              engagement: '892 likes • 67 comments'
            },
            {
              title: 'Final Week Alert!',
              type: 'urgency',
              content: `🚨 7 DAYS TO GO! 🚨

Community Fest is almost here and we're SO excited! 

May 24 • Hudson's Bay HS • FREE

Tag someone who needs to come with you! 👇

Story highlights have all the deets 👆

Who's ready to celebrate our amazing community? 🙌

#CommunityFest #SevenDaysLeft #VancouverVolcanoes #FreeEvent #CommunityLove #May24 #AlmostHere`,
              engagement: '1,456 likes • 89 comments'
            }
          ],
          twitter: [
            {
              title: 'Vancouver Volcanoes Community Fest 2025',
              type: 'announcement',
              content: `🏀 COMMUNITY FEST + VOLCANOES GAME! 

May 24 @ Hudson's Bay HS
3PM-7PM + Game @ 7PM

🎵 Live music + food trucks
🏀 Hoop contests  
🎮 FREE ENTRY!

Come for the fest, stay for the game! 

#CommunityFest #VancouverVolcanoes #Basketball`,
              engagement: '189 retweets • 267 likes • 34 replies'
            },
            {
              title: 'Community Partners Spotlight',
              type: 'behind-the-scenes',
              content: `🤝 Shoutout to our Community Fest partners!

🏀 @VancouverVolcs
💫 @HoopsForHope  
🎨 @PopLocal
🏘️ @FourthPlainCC
👫 @SWWBoysGirls

Together = stronger community 💪

May 24 • Hudson's Bay HS • FREE

#CommunityPartners #Teamwork #VancouverVolcanoes`,
              engagement: '134 retweets • 298 likes • 23 replies'
            },
            {
              title: 'One Week Warning!',
              type: 'urgency',
              content: `⏰ 7 DAYS until Community Fest!

May 24 @ Hudson's Bay HS
🎵 Music 🍕 Food 🏀 Basketball
100% FREE!

RT if you're coming! 🔥

#CommunityFest #OneWeekLeft #VancouverVolcanoes #FreeEvent`,
              engagement: '245 retweets • 567 likes • 45 replies'
            }
          ]
        },
        'pool-tournament': {
          facebook: [
            {
              title: '5th Annual Tucker Greenough Memorial Pool Tournament',
              type: 'announcement',
              content: `🎱 Register today for the 5th Annual Tucker Greenough Memorial Pool Tournament! 

Join us at Eagles Club for this special memorial tournament honoring Tucker Greenough. All proceeds from the silent auction will benefit Tucker's kids' college funds.

📅 Dates: 
• Friday April 18th - 7PM-9PM
• Saturday April 19th 
• Sunday April 20th, 2025

🏆 Tournament Formats:
• Friday: 9-ball single elimination ($25 entry)
• Sat/Sun: Calcutta before play starts
• Sat/Sun: 10AM - 8-ball double elimination
  - Lower division: $50 entry
  - Upper division: $75 entry

📍 Location: Eagles Club
306 N Durbin St, Casper WY 82601

📞 To register, call:
• Jeremy (307) 277-9771
• Dale (307) 267-4158

🎯 Silent auction proceeds benefit Tucker's kids' college funds

#TuckerGreenoughMemorial #PoolTournament #CasperWY #EaglesClub #Memorial #BilliardsForACause`,
              engagement: '156 likes • 34 comments • 18 shares'
            },
            {
              title: 'Honoring Tucker Greenough',
              type: 'behind-the-scenes',
              content: `💙 Remembering Tucker Greenough and celebrating his legacy...

Five years ago, our billiards community lost a special person. Tucker was more than just a great player - he was a friend, a mentor, and someone who brought people together around the tables he loved.

This tournament isn't just about competition. It's about continuing Tucker's spirit of bringing people together and supporting what mattered most to him - his children's future.

🎱 What makes this tournament special:
• Every break honors Tucker's memory
• The silent auction creates lasting impact
• Players from across Wyoming come together
• 100% of auction proceeds support Tucker's kids' college education

Whether you knew Tucker personally or you're just learning about him now, you're part of something meaningful. Every shot taken, every game played, every bid placed helps secure a brighter future for his children.

Thank you for keeping Tucker's memory alive through your participation.

#TuckerGreenoughMemorial #RememberingTucker #BilliardsFamily #CasperWY #LegacyOfLove`,
              engagement: '203 likes • 67 comments • 29 shares'
            },
            {
              title: 'Registration Closing Soon!',
              type: 'urgency',
              content: `⏰ FINAL CALL for Tucker Greenough Memorial Tournament registration!

April 18-20 is coming up fast, and spots are filling up quickly. Don't miss your chance to be part of this special memorial tournament.

🏆 Still open spots in:
• Friday 9-ball single elimination
• Saturday/Sunday lower division
• Saturday/Sunday upper division

💰 Remember - this isn't just about the competition:
• Your entry fees support great tournament play
• Silent auction items help Tucker's kids' college funds
• You're joining a billiards family that cares

📞 Register RIGHT NOW:
• Jeremy: (307) 277-9771
• Dale: (307) 267-4158

Eagles Club is ready to host an incredible weekend of billiards. The question is: will you be there?

Time's running out - make the call today!

#TuckerGreenoughMemorial #LastChance #RegisterNow #PoolTournament #CasperWY #DontMissOut`,
              engagement: '245 likes • 42 comments • 31 shares'
            }
          ],
          instagram: [
            {
              title: '5th Annual Tucker Greenough Memorial Pool Tournament',
              type: 'announcement',
              content: `🎱 5th Annual Tucker Greenough Memorial Tournament 

April 18-20, 2025 @ Eagles Club
Casper, WY

🏆 Multiple tournaments:
• 9-ball Friday
• 8-ball double elim weekend
• Silent auction for Tucker's kids

Playing for a great cause 💙

Register: Jeremy (307) 277-9771

#TuckerGreenoughMemorial #PoolTournament #CasperWY #BilliardsForACause #Memorial #CommunitySupport`,
              engagement: '289 likes • 45 comments'
            },
            {
              title: 'Legacy of Love',
              type: 'behind-the-scenes',
              content: `💙 5 years later, Tucker's spirit lives on 💙

This tournament is so much more than billiards - it's family, memory, and hope for the future.

Every game played honors Tucker 🎱
Every auction bid supports his kids' dreams 🎓
Every player continues his legacy of bringing people together 🤝

Swipe to see some memories from past tournaments →

#TuckerGreenoughMemorial #Legacy #BilliardsFamily #Memorial #CasperWY #LoveWins`,
              engagement: '456 likes • 78 comments'
            },
            {
              title: 'Registration Deadline Approaching!',
              type: 'urgency',
              content: `🚨 SPOTS FILLING UP FAST! 🚨

Tucker Greenough Memorial Tournament
April 18-20 @ Eagles Club

Don't wait - register today! 

📞 Jeremy: (307) 277-9771
📞 Dale: (307) 267-4158

Stories have all the tournament details 👆

#TuckerGreenoughMemorial #RegisterToday #AlmostFull #CasperWY #DontMissOut`,
              engagement: '234 likes • 23 comments'
            }
          ],
          twitter: [
            {
              title: '5th Annual Tucker Greenough Memorial Pool Tournament',
              type: 'announcement',
              content: `🎱 5th Annual Tucker Greenough Memorial Pool Tournament

Apr 18-20 @ Eagles Club, Casper WY

🏆 9-ball Friday, 8-ball weekend
💙 Proceeds help Tucker's kids' college funds

Register: Jeremy (307) 277-9771

#TuckerGreenoughMemorial #PoolTournament #CasperWY`,
              engagement: '67 retweets • 134 likes • 23 replies'
            },
            {
              title: 'Honoring Tucker',
              type: 'behind-the-scenes',
              content: `💙 Honoring Tucker Greenough's memory through billiards

5 years later, our community still comes together to support his kids' education.

Apr 18-20 @ Eagles Club, Casper

Every shot matters. Every bid helps.

#TuckerGreenoughMemorial #BilliardsFamily #CasperWY #Legacy`,
              engagement: '89 retweets • 167 likes • 34 replies'
            },
            {
              title: 'Registration Deadline Soon!',
              type: 'urgency',
              content: `⏰ REGISTRATION CLOSING SOON!

Tucker Greenough Memorial Tournament
Apr 18-20, Eagles Club

📞 Call Jeremy (307) 277-9771 NOW

Spots filling up fast! 

#TuckerGreenoughMemorial #RegisterNow #CasperWY #LastCall`,
              engagement: '45 retweets • 123 likes • 18 replies'
            }
          ]
        },
        'music-festival': {
          facebook: [
            {
              title: 'CCMF Music Festival 2025 - Myrtle Beach',
              type: 'announcement',
              content: `🎸 CCMF Music Festival 2025 lineup is HERE! 🔥

Get ready for an incredible beach music festival experience in Myrtle Beach, South Carolina!

📅 Dates: June 5-8, 2025
📍 Location: Myrtle Beach, South Carolina
🎫 Tickets available now!

🌟 HEADLINERS:
• JELLY ROLL
• KID ROCK  
• LAINEY WILSON
• RASCAL FLATTS
• THE BEACH BOYS

Plus amazing performances from:
Brantley Gilbert, Chase Rice, Chris Young, Tyler Hubbard, Dylan Gossett, Mitchell Tenpenny, David Lee Murphy, Jackson Dean, Priscilla Block, and many more incredible artists!

🏖️ Beach setting with world-class country and rock music
🎵 Multiple stages and performances
🍻 Food and beverages available
🏨 Resort packages available

This is going to be the music event of the summer! Don't miss out on this incredible lineup at beautiful Myrtle Beach.

#CCMF2025 #MyrtleBeach #JellyRoll #KidRock #LaineyWilson #RascalFlatts #BeachBoys #CountryMusic #BeachFestival`,
              engagement: '1,234 likes • 278 comments • 156 shares'
            },
            {
              title: 'Artist Spotlight: The Stories Behind the Music',
              type: 'behind-the-scenes',
              content: `🎤 Meet the incredible artists bringing CCMF 2025 to life!

🌟 JELLY ROLL - From struggle to triumph, his authentic storytelling resonates with millions. This will be an emotional, powerful performance you won't forget.

🌟 KID ROCK - A true rock rebel who's been bringing high-energy shows for decades. Expect surprises, guest appearances, and pure rock & roll magic.

🌟 LAINEY WILSON - Country's rising superstar with a voice that touches hearts and lyrics that tell real stories. Her Myrtle Beach debut will be special.

🌟 RASCAL FLATTS - These legends defined country music for a generation. Their beach performance promises all the hits plus some rare deep cuts.

🌟 THE BEACH BOYS - The perfect headliners for a beach festival! Original surf rock legends bringing "Good Vibrations" to Carolina shores.

Plus discover new favorites from rising stars like Dylan Gossett and Jackson Dean!

🏖️ Four days of music under the Carolina sun
🎵 Stories, songs, and memories waiting to be made
🌊 The perfect soundtrack to your summer

Which artist are you most excited to see?

#CCMF2025 #ArtistSpotlight #BeachMusic #CountryMusic #MyrtleBeach #LiveMusic`,
              engagement: '867 likes • 156 comments • 89 shares'
            },
            {
              title: 'Last Call for Early Bird Tickets!',
              type: 'urgency',
              content: `🚨 EARLY BIRD PRICING ENDS FRIDAY! 🚨

CCMF 2025 at Myrtle Beach is almost here, and early bird tickets are flying off the digital shelves!

⏰ You have 3 DAYS LEFT to save big:
• 4-Day Festival Pass: $199 (reg. $299)
• VIP Beach Experience: $399 (reg. $549) 
• Camping Package: $149 (reg. $199)

🌟 What you get:
✅ Jelly Roll, Kid Rock, Lainey Wilson, Rascal Flatts, The Beach Boys
✅ 30+ additional artists
✅ Beach access and activities
✅ Food truck village
✅ Late-night shows and surprises

🏖️ Resort packages selling out fast!
Many hotels are already 80%+ booked for festival weekend.

Don't wait until Friday night and regret it all summer. This lineup has never been to Myrtle Beach before, and at these prices, it may never happen again.

Get your tickets NOW before the price jump hits!

Link in comments 👇

#CCMF2025 #EarlyBird #LastChance #MyrtleBeach #DontMissOut #BeachFestival #SaveBig`,
              engagement: '1,456 likes • 234 comments • 178 shares'
            }
          ],
          instagram: [
            {
              title: 'CCMF Music Festival 2025 - Myrtle Beach',
              type: 'announcement',
              content: `🎸 CCMF 2025 LINEUP DROP! 🔥

June 5-8 | Myrtle Beach, SC 🏖️

🌟 JELLY ROLL
🌟 KID ROCK
🌟 LAINEY WILSON  
🌟 RASCAL FLATTS
🌟 THE BEACH BOYS

+ Brantley Gilbert, Chase Rice, Chris Young & SO many more!

Beach + Country + Rock = PERFECTION ✨

Who's ready for Myrtle Beach?! 🙌

#CCMF2025 #MyrtleBeach #JellyRoll #KidRock #LaineyWilson #CountryMusic #BeachFestival #LineupReveal`,
              engagement: '3,456 likes • 567 comments'
            },
            {
              title: 'Behind the Beach Magic',
              type: 'behind-the-scenes',
              content: `🏖️ Creating CCMF magic at Myrtle Beach! ✨

Swipe to see how we're building something incredible:

📸 Stage construction with ocean views
📸 VIP areas with beach access  
📸 Food truck village setup
📸 Artist rehearsal spaces
📸 Camping areas under palm trees

This isn't just a festival - it's a 4-day beach vacation with the soundtrack of your life! 🎵

Stories have exclusive behind-the-scenes content 👆

#CCMF2025 #BehindTheScenes #MyrtleBeach #BeachFestival #FestivalLife #CountryMusic #BeachVibes`,
              engagement: '2,134 likes • 289 comments'
            },
            {
              title: 'Early Bird Ending Soon!',
              type: 'urgency',
              content: `🚨 3 DAYS LEFT for Early Bird! 🚨

CCMF 2025 early bird pricing ends FRIDAY! 

💰 Save BIG before prices jump:
🎟️ 4-Day Pass: $199 → $299
🌟 VIP Experience: $399 → $549
🏕️ Camping: $149 → $199

Beach + Country + Summer = PERFECT

Link in bio to secure your spot! 👆

#CCMF2025 #EarlyBird #3DaysLeft #MyrtleBeach #SaveMoney #BeachFestival #LastChance`,
              engagement: '1,789 likes • 156 comments'
            }
          ],
          twitter: [
            {
              title: 'CCMF Music Festival 2025 - Myrtle Beach',
              type: 'announcement',
              content: `🎸 CCMF 2025 LINEUP! 

June 5-8 | Myrtle Beach, SC

🌟 JELLY ROLL
🌟 KID ROCK  
🌟 LAINEY WILSON
🌟 RASCAL FLATTS
🌟 THE BEACH BOYS

+ Brantley Gilbert, Chase Rice & more!

Beach festival vibes! 🏖️

#CCMF2025 #MyrtleBeach #CountryMusic`,
              engagement: '456 retweets • 789 likes • 123 replies'
            },
            {
              title: 'Festival Stories',
              type: 'behind-the-scenes',
              content: `🏖️ Why CCMF chose Myrtle Beach:

🌊 Perfect venue for Beach Boys
🎸 Intimate setting for big acts
🏨 Resort packages for festival-goers
🍻 Beach bars + country music = magic

June 5-8 is going to be special.

#CCMF2025 #MyrtleBeach #BeachFestival #CountryMusic #BehindTheScenes`,
              engagement: '234 retweets • 567 likes • 89 replies'
            },
            {
              title: 'Early Bird Deadline Friday!',
              type: 'urgency',
              content: `⏰ EARLY BIRD ENDS FRIDAY!

CCMF 2025 @ Myrtle Beach
Save $100+ before prices jump

🎟️ 4-Day Pass: $199 (3 days left!)
🌟 VIP: $399 (limited spots!)

Don't wait! 🔥

#CCMF2025 #EarlyBird #MyrtleBeach #LastChance`,
              engagement: '345 retweets • 678 likes • 67 replies'
            }
          ]
        },
        'conference': {
          facebook: [
            {
              title: 'Earth Day Festival 2024 - Englewood, FL',
              type: 'announcement',
              content: `🌍 Join us for Earth Day Festival 2024! 🌱

Hosted by Barrier Island Parks Society and local Englewoodians, this FREE family-friendly festival celebrates our planet!

📅 Date: Sunday, April 21, 2024
🕐 Time: 11am to 4pm
📍 Location: Buchans Park (next to Buchans Airport on Old Englewood Rd, Englewood, FL)
🎟️ FREE ADMISSION!

🌟 Festival Features:
🎨 Eco-Friendly Crafts
📚 Educational Programs  
🎵 Live Music
🥗 Health Conscious Fun Foods
🎁 Raffles & Prizes
🥁 Live Drums & Performances
💡 Informative Demos
🎨 Arts & Crafts Vendors
🌱 Plant Sales

Learn about environmental conservation while having fun with the whole family! This is a perfect opportunity to connect with our local environmental community and learn how to make a positive impact.

More info: www.PlanetEnglewood.com or info@PlanetEnglewood.com

#EarthDay2024 #Englewood #EnvironmentalEducation #FamilyFun #FreeEvent #Sustainability #PlanetEnglewood`,
              engagement: '298 likes • 67 comments • 45 shares'
            },
            {
              title: 'Meet Our Environmental Heroes',
              type: 'behind-the-scenes',
              content: `🌱 Meet the incredible people making Earth Day Festival possible!

👥 Barrier Island Parks Society - Dedicated to preserving our coastal ecosystems and educating our community about environmental stewardship.

🏘️ Local Englewoodians - Passionate volunteers who believe that small actions can create big environmental changes.

🎯 What drives us:
• Protecting our beautiful Florida coastline
• Teaching kids to love and respect nature
• Supporting local environmental initiatives
• Creating a sustainable future for Englewood

🌿 This year's special focus:
• Native plant education and giveaways
• Sustainable living workshops
• Beach cleanup demonstration techniques
• Renewable energy exhibits

Every activity at the festival is designed to inspire action. From the eco-friendly crafts made from recycled materials to the educational programs about local wildlife, we're showing that environmental protection can be fun, engaging, and accessible to everyone.

Join us April 21st and be part of the solution! Together, we can make Englewood a model for environmental sustainability.

#EarthDay2024 #EnvironmentalHeroes #Sustainability #Englewood #CommunityAction #PlanetEnglewood`,
              engagement: '412 likes • 89 comments • 67 shares'
            },
            {
              title: 'This Sunday: Make a Difference!',
              type: 'urgency',
              content: `🌍 THIS SUNDAY - April 21st! Earth Day Festival at Buchans Park! 

The planet needs you, and Sunday is your chance to make a real difference in our Englewood community!

⏰ Don't miss out on:
• 11am: Opening ceremony and native plant giveaway
• 12pm: Kids eco-craft workshop (limited supplies!)
• 1pm: Sustainable living presentation 
• 2pm: Local wildlife education program
• 3pm: Community cleanup planning session

🌱 FREE plant giveaways while supplies last!
First 100 families receive native Florida plants perfect for supporting local wildlife.

🎨 Limited craft supplies!
Our recycled material art stations are popular and supplies are limited. Arrive early to guarantee your kids can participate.

🏆 Raffle prizes every hour!
Win eco-friendly products, local business gift cards, and sustainability starter kits.

Weather looks perfect! Bring your family, bring your friends, and bring your enthusiasm for protecting our beautiful planet.

See you Sunday at Buchans Park! 

#EarthDay2024 #ThisSunday #LastChance #FreeEvent #MakeADifference #Englewood #PlanetEnglewood`,
              engagement: '523 likes • 78 comments • 89 shares'
            }
          ],
          instagram: [
            {
              title: 'Earth Day Festival 2024 - Englewood, FL',
              type: 'announcement',
              content: `🌍 EARTH DAY FESTIVAL 2024! 🌱

April 21 | 11am-4pm | Buchans Park
Englewood, FL

🌟 FREE family event! 

✨ Eco-friendly crafts
🎵 Live music  
🌱 Plant sales
🎨 Arts & crafts
💡 Educational demos

Let's celebrate our planet together! 🌎

#EarthDay2024 #Englewood #PlanetEnglewood #EcoFriendly #FamilyFun #Sustainability #FreeEvent #EnvironmentalEducation`,
              engagement: '567 likes • 89 comments'
            },
            {
              title: 'Green Team in Action',
              type: 'behind-the-scenes',
              content: `🌱 Behind the scenes: Creating Earth Day magic! ✨

Swipe to see our amazing volunteers prepping for Sunday:

📸 Setting up native plant displays
📸 Organizing recycled craft materials
📸 Preparing educational exhibits
📸 Testing sound system for live music
📸 Arranging eco-friendly food stations

These incredible people believe in protecting our planet and making environmental education fun for families! 💚

Sunday can't come soon enough! 🌍

#EarthDay2024 #BehindTheScenes #Volunteers #Englewood #PlanetEnglewood #TeamWork #EnvironmentalEducation`,
              engagement: '445 likes • 67 comments'
            },
            {
              title: 'Sunday Funday for the Planet!',
              type: 'urgency',
              content: `🌍 SUNDAY = EARTH DAY FESTIVAL! 🌱

April 21 | Buchans Park | 11am-4pm

🎁 FREE plant giveaways!
🎨 Limited craft supplies!
🏆 Hourly raffles!

Bring the whole family! 👨‍👩‍👧‍👦

Weather is perfect ☀️
Everything is ready ✅
We just need YOU! 

#EarthDay2024 #Sunday #FreeEvent #Englewood #LastCall #PlanetEnglewood #FamilyFun`,
              engagement: '789 likes • 134 comments'
            }
          ],
          twitter: [
            {
              title: 'Earth Day Festival 2024 - Englewood, FL',
              type: 'announcement',
              content: `🌍 EARTH DAY FESTIVAL 2024!

April 21, 11am-4pm
Buchans Park, Englewood FL

🌱 FREE family event
🎨 Eco crafts & education
🎵 Live music
💡 Environmental demos

Celebrate our planet! 🌎

#EarthDay2024 #Englewood #PlanetEnglewood`,
              engagement: '78 retweets • 156 likes • 34 replies'
            },
            {
              title: 'Environmental Education for All',
              type: 'behind-the-scenes',
              content: `🌱 Why Earth Day Festival matters:

🏖️ Protecting Englewood's coastline
📚 Teaching sustainable living
👨‍👩‍👧‍👦 Family-friendly environmental education
🤝 Building community action

April 21 | Buchans Park | FREE

#EarthDay2024 #EnvironmentalEducation #Englewood #Sustainability`,
              engagement: '89 retweets • 167 likes • 45 replies'
            },
            {
              title: 'This Sunday!',
              type: 'urgency',
              content: `🌍 THIS SUNDAY! Earth Day Festival!

April 21 | 11am-4pm | Buchans Park

🌱 FREE plants (while supplies last!)
🎨 Kids crafts  
🎵 Live music
🏆 Raffles

Perfect weather forecast! ☀️

#EarthDay2024 #Sunday #FreeEvent #Englewood`,
              engagement: '67 retweets • 145 likes • 23 replies'
            }
          ]
        }
      };

      // ==========================================
      // SOCIAL MEDIA MODAL - CAROUSEL STATE
      // ==========================================
      // PRESERVE: Global state variables for carousel functionality
      // These variables manage the modal's navigation state
      // ==========================================
      let currentPostIndex = 0;
      let currentPlatform = '';
      let currentEvent = '';
      let currentPosts = [];

      // ==========================================
      // PRESERVE: OPEN SOCIAL MODAL FUNCTION
      // ==========================================
      // This function initializes the colorful modal with:
      // - Three-post carousel functionality
      // - Platform-specific content rendering
      // - Colorful top navigation bar
      // DO NOT CHANGE the modal opening logic or styling
      // ==========================================
      function openSocialModal(platform, event) {
        const modal = document.getElementById('socialModal');
        const posts = socialMediaContent[event][platform];
        
        if (!posts || posts.length === 0) {
          console.error('No post data found for:', event, platform);
          return;
        }
        
        // Set global state for carousel
        currentPlatform = platform;
        currentEvent = event;
        currentPosts = posts;
        currentPostIndex = 0;
        
        // Show the modal with colorful design
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Initialize carousel components
        renderCurrentPost();
        updateDots();
        updateNavigationButtons();
        
        // Ensure Lucide icons render properly
        setTimeout(() => {
          lucide.createIcons();
        }, 100);
      }
      
      function renderCurrentPost() {
        const content = document.getElementById('socialContent');
        const postData = currentPosts[currentPostIndex];
        
        let platformIcon, platformColor, platformName;
        
        switch(currentPlatform) {
          case 'facebook':
            platformIcon = 'facebook';
            platformColor = 'text-blue-600';
            platformName = 'Facebook';
            break;
          case 'instagram':
            platformIcon = 'instagram';
            platformColor = 'text-pink-600';
            platformName = 'Instagram';
            break;
          case 'twitter':
            platformIcon = 'twitter';
            platformColor = 'text-sky-600';
            platformName = 'Twitter / X';
            break;
        }
        
        // Get post type styling
        let typeLabel = '';
        let typeColor = '';
        switch(postData.type) {
          case 'announcement':
            typeLabel = 'Main Announcement';
            typeColor = 'bg-blue-100 text-blue-800';
            break;
          case 'behind-the-scenes':
            typeLabel = 'Behind the Scenes';
            typeColor = 'bg-purple-100 text-purple-800';
            break;
          case 'urgency':
            typeLabel = 'Call to Action';
            typeColor = 'bg-orange-100 text-orange-800';
            break;
        }
        
        content.innerHTML = `
          <div class="mb-4 pb-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-3">
                <i data-lucide="${platformIcon}" class="w-6 h-6 ${platformColor}"></i>
                <h3 class="text-lg font-bold text-gray-900">${platformName} Post</h3>
              </div>
              <span class="text-xs px-2 py-1 rounded-full ${typeColor} font-medium">${typeLabel}</span>
            </div>
            <p class="text-sm text-gray-600">${postData.title}</p>
          </div>
          
          <div class="mb-6">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
              <p class="text-gray-800 whitespace-pre-line leading-relaxed">${postData.content}</p>
            </div>
          </div>
          
          <div class="text-sm text-gray-500 pt-4 border-t border-gray-200">
            <p class="flex items-center gap-2">
              <i data-lucide="heart" class="w-4 h-4"></i>
              ${postData.engagement}
            </p>
          </div>
        `;
        
        setTimeout(() => {
          lucide.createIcons();
        }, 100);
      }
      
      // ==========================================
      // PRESERVE: NAVIGATE POST FUNCTION
      // ==========================================
      // Handles left/right navigation in the colorful modal:
      // - Direction: -1 for previous, +1 for next
      // - Updates all carousel components
      // - Maintains proper state synchronization
      // DO NOT CHANGE the navigation logic
      // ==========================================
      function navigatePost(direction) {
        const newIndex = currentPostIndex + direction;
        
        if (newIndex >= 0 && newIndex < currentPosts.length) {
          currentPostIndex = newIndex;
          renderCurrentPost();
          updateDots();
          updateNavigationButtons();
          
          // Update the white counter in colorful header
          document.getElementById('postCounter').textContent = `${currentPostIndex + 1} of ${currentPosts.length}`;
        }
      }
      
      // ==========================================
      // PRESERVE DOT STYLING FUNCTION
      // ==========================================
      // This function creates interactive dots with:
      // - Larger size (w-3 h-3) for better visibility
      // - White semi-transparent styling for colorful header
      // - Scale animations (110% active, 105% hover)
      // - Smooth transitions (duration-300)
      // DO NOT CHANGE the styling classes without approval
      // ==========================================
      function updateDots() {
        const dotsContainer = document.getElementById('postDots');
        dotsContainer.innerHTML = '';
        
        for (let i = 0; i < currentPosts.length; i++) {
          const dot = document.createElement('div');
          // PRESERVE: White semi-transparent styling with scale animations
          dot.className = `w-3 h-3 rounded-full cursor-pointer transition-all duration-300 border-2 ${
            i === currentPostIndex 
              ? 'bg-white border-white shadow-lg scale-110' 
              : 'bg-white/40 border-white/60 hover:bg-white/60 hover:scale-105'
          }`;
          dot.onclick = () => {
            currentPostIndex = i;
            renderCurrentPost();
            updateDots();
            updateNavigationButtons();
            document.getElementById('postCounter').textContent = `${currentPostIndex + 1} of ${currentPosts.length}`;
          };
          dotsContainer.appendChild(dot);
        }
      }
      
      // ==========================================
      // PRESERVE: NAVIGATION BUTTON STATE FUNCTION
      // ==========================================
      // Manages the colorful navigation button states:
      // - Opacity changes for disabled states (0.3)
      // - Pointer events control for user interaction
      // - Counter updates for "X of 3" display
      // DO NOT CHANGE the opacity/interaction logic
      // ==========================================
      function updateNavigationButtons() {
        const prevBtn = document.getElementById('prevPost');
        const nextBtn = document.getElementById('nextPost');
        
        // PRESERVE: Button state management for colorful navigation
        prevBtn.style.opacity = currentPostIndex === 0 ? '0.3' : '1';
        nextBtn.style.opacity = currentPostIndex === currentPosts.length - 1 ? '0.3' : '1';
        
        prevBtn.style.pointerEvents = currentPostIndex === 0 ? 'none' : 'auto';
        nextBtn.style.pointerEvents = currentPostIndex === currentPosts.length - 1 ? 'none' : 'auto';
        
        // Update the white counter text in the colorful header
        document.getElementById('postCounter').textContent = `${currentPostIndex + 1} of ${currentPosts.length}`;
      }
      
      // ==========================================
      // PRESERVE: CLOSE MODAL FUNCTION
      // ==========================================
      // Properly closes the colorful modal and resets state
      // DO NOT CHANGE the closing sequence or state reset
      // ==========================================
      function closeSocialModal() {
        const modal = document.getElementById('socialModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        resetCarouselState();
      }
      
      // ==========================================
      // PRESERVE: MODAL EVENT LISTENERS
      // ==========================================
      // These handle proper modal behavior:
      // - Click outside to close
      // - Prevent closing when clicking content
      // - State reset functionality
      // DO NOT CHANGE these event handlers
      // ==========================================
      
      // Close colorful modal when clicking outside
      document.getElementById('socialModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeSocialModal();
        }
      });
      
      // Prevent modal close when clicking on the colorful content area
      document.addEventListener('DOMContentLoaded', function() {
        const socialModalContent = document.querySelector('#socialModal > div');
        if (socialModalContent) {
          socialModalContent.addEventListener('click', function(e) {
            e.stopPropagation();
          });
        }
      });
      
      // ==========================================
      // PRESERVE: RESET CAROUSEL STATE FUNCTION
      // ==========================================
      // Cleans up all global state when modal closes
      // Essential for proper carousel functionality
      // DO NOT CHANGE this reset logic
      // ==========================================
      function resetCarouselState() {
        currentPostIndex = 0;
        currentPlatform = '';
        currentEvent = '';
        currentPosts = [];
      }
      
      // Poster modal functions
      function openModal(imageSrc, altText) {
        const modal = document.getElementById('posterModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageSrc;
        modalImage.alt = altText;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
      
      function closeModal() {
        const modal = document.getElementById('posterModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
      }
      
      // Close modal when clicking outside the image
      document.getElementById('posterModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeModal();
        }
      });
    </script>
  </body>
</html>
