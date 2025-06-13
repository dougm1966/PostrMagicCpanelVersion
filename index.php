<?php include_once __DIR__ . '/includes/header.php'; ?>

<section class="relative overflow-hidden py-16 md:py-24 bg-gradient-to-b from-dark to-dark/90 w-full">
    <!-- Background Elements (Using Tailwind CSS) -->
    <div class="absolute inset-0 bg-gradient-to-br from-primary/20 to-transparent opacity-60"></div>
    <div class="absolute top-1/4 -left-20 w-64 h-64 rounded-full bg-primary/20 blur-3xl"></div>
    <div class="absolute bottom-1/4 -right-20 w-80 h-80 rounded-full bg-secondary/20 blur-3xl"></div>
    
    <div class="relative container mx-auto px-6 sm:px-12 max-w-6xl">
        <!-- Trust Badge -->
        <div class="inline-flex items-center mb-8 py-2 px-4 bg-white/10 backdrop-blur-md rounded-full">
            <i class="fa fa-trending-up text-accent mr-2"></i>
            <span class="text-xs md:hidden">Used by 10k+ organizers</span>
            <span class="hidden md:inline text-sm text-gray-200">Trusted by 10,000+ event organizers at</span>
            <span class="ml-1 text-xs md:text-sm font-semibold text-white">Eventbrite, Meetup, Facebook</span>
        </div>
        <!-- Main Headline -->
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
            Transform Your Event Posters Into<br>
            <span class="bg-gradient-to-r from-primary to-secondary inline-block text-transparent bg-clip-text">Social Media Magic</span>
        </h1>

        <!-- Subheadline -->
        <p class="text-lg md:text-xl text-gray-300 mb-10 max-w-3xl">
            PostrMagic unifies poster analysis with AI-powered social content generation. 
            <span class="text-white font-semibold">Reduce posting time by 90%</span> and boost event attendance with professional content.
        </p>

        <!-- Stats -->
        <div class="flex flex-wrap gap-4 mb-10">
            <div class="inline-flex items-center py-2 px-4 bg-white/5 backdrop-blur-md rounded-lg border border-white/10">
                <i class="fa fa-shield-check text-accent mr-2"></i>
                <span class="text-sm text-gray-200">AI-Powered</span>
            </div>
            <div class="inline-flex items-center py-2 px-4 bg-white/5 backdrop-blur-md rounded-lg border border-white/10">
                <i class="fa fa-clock text-accent mr-2"></i>
                <span class="text-sm text-gray-200">60-second setup</span>
            </div>
            <div class="inline-flex items-center py-2 px-4 bg-white/5 backdrop-blur-md rounded-lg border border-white/10">
                <i class="fa fa-globe text-accent mr-2"></i>
                <span class="text-sm text-gray-200">All platforms</span>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="mb-8 max-w-4xl">
            <form class="flex flex-col md:flex-row gap-4 mb-4">
                <div class="relative flex-1 flex items-center bg-white/5 backdrop-blur-md rounded-xl border border-white/10 px-4 py-3 group hover:border-primary/40 transition duration-300">
                    <i class="fa fa-upload text-accent mr-3"></i>
                    <input type="file" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <span class="text-gray-300 text-sm">Drop your poster here or click to browse</span>
                </div>
                <button type="submit" class="bg-gradient-to-r from-primary to-primary/80 hover:from-primary/90 hover:to-primary text-white px-6 py-3 rounded-xl flex items-center justify-center gap-2 transition-all duration-300 shadow-lg shadow-primary/30">
                    <span>Analyze Poster</span>
                    <i class="fa fa-arrow-right"></i>
                </button>
            </form>
            <div class="flex flex-wrap gap-6">
                <span class="inline-flex items-center text-sm text-gray-300">
                    <i class="fa fa-check text-accent mr-2"></i>
                    No signup required
                </span>
                <span class="inline-flex items-center text-sm text-gray-300">
                    <i class="fa fa-clock text-accent mr-2"></i>
                    Results in 60 seconds
                </span>
                <span class="inline-flex items-center text-sm text-gray-300">
                    <i class="fa fa-star text-accent mr-2"></i>
                    Always free to start
                </span>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section (Redesigned) -->
<section class="relative overflow-hidden py-24 md:py-32 bg-gradient-to-br from-[#0A1233] via-[#1E2B59] to-[#0A1233] w-full">
    <!-- Shader canvas background -->
    <canvas id="shader-canvas-how" class="absolute inset-0 w-full h-full opacity-30 -z-10"></canvas>
    
    <!-- Animated light/glow effects -->
    <div class="absolute top-40 left-20 w-[400px] h-[400px] rounded-full bg-gradient-to-r from-primary/20 to-secondary/20 blur-[100px] animate-pulse -z-10 opacity-40"></div>
    <div class="absolute -bottom-20 right-0 w-[500px] h-[500px] rounded-full bg-gradient-to-br from-secondary/20 to-primary/20 blur-[120px] animate-pulse animation-delay-2000 -z-10 opacity-30"></div>
    
    <!-- Subtle geometric pattern overlay -->
    <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAzNGgyLTJoLTJ6TTM1IDM1aDJ2MmgtMnoiLz48cGF0aCBmaWxsPSIjZmZmIiBkPSJNNjAgMEgzMHYzMGgzMHptMCAzMEgzMHYzMGgzMHpNMzAgMEgwdjMwaDMwem0wIDMwSDB2MzBoMzB6Ii8+PC9nPjwvc3ZnPg==');"></div>
    
    <div class="container relative mx-auto px-6 sm:px-12 max-w-6xl">
        <!-- Enhanced section header -->
        <div class="text-center mb-16 md:mb-24 max-w-3xl mx-auto">
            <span class="inline-block py-1 px-4 rounded-full bg-gradient-to-r from-primary/20 to-secondary/20 text-white text-sm font-medium mb-6">SIMPLE WORKFLOW</span>
            <h2 class="text-[clamp(2.5rem,5vw,4rem)] font-bold mb-6">
                <span class="text-white">From poster to posts in</span> 
                <span class="block mt-1 text-transparent bg-clip-text bg-gradient-to-r from-primary via-secondary to-primary animate-gradient-x">under 60 seconds</span>
            </h2>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">Generate social media magic in three simple steps.</p>
        </div>
        
        <!-- Connected step process with path indicator -->
        <div class="hidden lg:block absolute top-[45%] left-1/2 transform -translate-x-1/2 w-[70%] h-0.5 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
        
        <!-- Step cards with enhanced visual design -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-10 relative z-10">
            <!-- Step 1 -->
            <div class="group bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-8 pt-12 transition-all duration-500 transform hover:-translate-y-2 hover:shadow-xl hover:shadow-primary/10 hover:border-primary/30 relative overflow-hidden">
                <!-- Top accent bar that animates on hover -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-primary to-accent transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                
                <!-- Large step number with position -->
                <div class="absolute -right-5 -top-10 text-[120px] font-black opacity-5 select-none">1</div>
                
                <!-- Step icon with glow effect -->
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary/20 to-transparent p-5 flex items-center justify-center mb-8 mx-auto group-hover:scale-110 transition-transform duration-300">
                    <i class="fa fa-cloud-arrow-up text-accent text-3xl"></i>
                </div>
                
                <h3 class="text-2xl font-bold text-white text-center mb-4">Upload</h3>
                <p class="text-gray-300 text-center mb-6">Drag and drop your event poster or photo.</p>
                
                <!-- Feature badge -->
                <div class="flex items-center justify-center bg-white/5 rounded-full py-2 px-4 max-w-max mx-auto">
                    <i class="fa fa-check-circle text-accent mr-2"></i>
                    <span class="text-sm text-gray-200">Works with any image quality</span>
                </div>
            </div>
            
            <!-- Step 2 (Featured) -->
            <div class="group bg-primary/10 backdrop-blur-xl rounded-2xl border border-primary/30 p-8 pt-12 transition-all duration-500 transform hover:-translate-y-2 hover:shadow-xl hover:shadow-secondary/20 relative overflow-hidden z-10 scale-105">
                <!-- Top accent bar that animates on hover -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-secondary to-accent transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                
                <!-- AI Magic badge -->
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-primary to-secondary text-white text-xs font-bold py-1 px-4 rounded-full shadow-lg">AI Magic</div>
                
                <!-- Large step number with position -->
                <div class="absolute -right-5 -top-10 text-[120px] font-black opacity-5 select-none">2</div>
                
                <!-- Step icon with glow effect -->
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-secondary/20 to-transparent p-5 flex items-center justify-center mb-8 mx-auto group-hover:scale-110 transition-transform duration-300">
                    <i class="fa fa-wand-magic-sparkles text-accent text-3xl"></i>
                </div>
                
                <h3 class="text-2xl font-bold text-white text-center mb-4">AI Creates</h3>
                <p class="text-gray-300 text-center mb-6">Our AI reads your poster and crafts engaging posts.</p>
                
                <!-- Feature badge -->
                <div class="flex items-center justify-center bg-white/10 rounded-full py-2 px-4 max-w-max mx-auto">
                    <i class="fa fa-check-circle text-accent mr-2"></i>
                    <span class="text-sm text-gray-200">Event analysis & copywriting</span>
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="group bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-8 pt-12 transition-all duration-500 transform hover:-translate-y-2 hover:shadow-xl hover:shadow-primary/10 hover:border-primary/30 relative overflow-hidden">
                <!-- Top accent bar that animates on hover -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-primary to-accent transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                
                <!-- Large step number with position -->
                <div class="absolute -right-5 -top-10 text-[120px] font-black opacity-5 select-none">3</div>
                
                <!-- Step icon with glow effect -->
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary/20 to-transparent p-5 flex items-center justify-center mb-8 mx-auto group-hover:scale-110 transition-transform duration-300">
                    <i class="fa fa-paper-plane text-accent text-3xl"></i>
                </div>
                
                <h3 class="text-2xl font-bold text-white text-center mb-4">Publish</h3>
                <p class="text-gray-300 text-center mb-6">Download or schedule ready-to-post captions instantly.</p>
                
                <!-- Feature badge -->
                <div class="flex items-center justify-center bg-white/5 rounded-full py-2 px-4 max-w-max mx-auto">
                    <i class="fa fa-check-circle text-accent mr-2"></i>
                    <span class="text-sm text-gray-200">Social-ready assets</span>
                </div>
            </div>
        </div>
        
        <!-- Enhanced CTA Button -->
        <div class="mt-16 md:mt-20 text-center">
            <a href="#" class="inline-flex items-center py-4 px-8 bg-gradient-to-r from-primary to-secondary hover:from-primary/90 hover:to-secondary/90 rounded-xl text-white font-medium text-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg shadow-primary/20 group">
                <span>Try It Free</span>
                <i class="fa fa-arrow-right ml-2 group-hover:ml-3 transition-all"></i>
            </a>
            <p class="text-gray-400 text-sm mt-3">No credit card required</p>
        </div>
    </div>
</section>

<!-- Benefits (Tailwind CSS) -->
<section class="py-20 bg-white w-full overflow-hidden">
    <div class="container mx-auto px-6 sm:px-12 max-w-6xl">
        <div class="text-center mb-16">
            <h2 class="text-[clamp(2rem,5vw,3.5rem)] font-bold mb-4 text-gray-800">Why Event Organizers Love Us</h2>
            <p class="text-[clamp(1rem,3vw,1.25rem)] text-gray-600">Everything you need to promote your events</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mx-auto">
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="text-4xl mb-4">⚡</div>
                <h4 class="text-xl font-semibold mb-3 text-gray-800">Lightning Fast</h4>
                <p class="text-gray-600">Get professional content in under 60 seconds. No more hours spent on design.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="text-4xl mb-4">🎯</div>
                <h4 class="text-xl font-semibold mb-3 text-gray-800">Platform Ready</h4>
                <p class="text-gray-600">Content optimized for Instagram, Facebook, Twitter, and more.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="text-4xl mb-4">🔍</div>
                <h4 class="text-xl font-semibold mb-3 text-gray-800">Smart Research</h4>
                <p class="text-gray-600">AI researches venues and audiences to create better, more engaging content.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="text-4xl mb-4">📚</div>
                <h4 class="text-xl font-semibold mb-3 text-gray-800">Media Library</h4>
                <p class="text-gray-600">Build your photo library over time. Every event adds to your content bank.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="text-4xl mb-4">💰</div>
                <h4 class="text-xl font-semibold mb-3 text-gray-800">Affordable</h4>
                <p class="text-gray-600">Start free, upgrade when you need more. No monthly subscriptions required.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="text-4xl mb-4">📊</div>
                <h4 class="text-xl font-semibold mb-3 text-gray-800">Track Performance</h4>
                <p class="text-gray-600">See which content performs best and improve your marketing over time.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing (Tailwind CSS) -->
<section class="relative py-24 bg-dark overflow-hidden text-white w-full">
    <!-- Canvas background -->
    <canvas id="shader-canvas" class="absolute inset-0 w-full h-full -z-10"></canvas>
    
    <div class="container mx-auto px-6 sm:px-12 max-w-6xl">
        <div class="text-center max-w-4xl mx-auto mb-12 md:mb-16">
            <h2 class="text-[clamp(2rem,5vw,3.5rem)] font-bold mb-4 bg-gradient-to-r from-primary to-secondary inline-block text-transparent bg-clip-text">Simple, transparent pricing</h2>
            <p class="text-[clamp(1rem,3vw,1.25rem)] text-gray-300">Choose a plan that fits your needs. Upgrade or downgrade anytime. No hidden fees, ever.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mx-auto">
            <!-- Free Start Card -->
            <div class="bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 overflow-hidden flex flex-col transition-transform hover:-translate-y-2 duration-300">
                <div class="p-6 sm:p-8">
                    <h3 class="text-[clamp(1.25rem,4vw,1.75rem)] font-bold mb-2">Free Start</h3>
                    <p class="text-gray-400 mb-6">Perfect for trying PostrMagic with your first event.</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-white">$0</span>
                        <span class="text-gray-400">/event</span>
                    </div>
                    <div class="h-px bg-white/10 my-6"></div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>3 social media posts</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>AI event analysis</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>Email delivery</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>Basic templates</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-auto p-6 sm:p-8 pt-0">
                    <button class="w-full py-3 px-4 border border-white/20 text-white rounded-xl hover:bg-white/10 transition-colors duration-300 font-medium">Start Free</button>
                </div>
            </div>
            
            <!-- Pro Card (Featured) -->
            <div class="relative bg-primary/10 backdrop-blur-md rounded-2xl border border-primary/40 overflow-hidden flex flex-col transition-transform hover:-translate-y-2 duration-300 shadow-lg shadow-primary/20 md:z-10">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 bg-accent text-dark text-xs font-semibold px-4 py-1 rounded-b-lg">
                    Most Popular
                </div>
                <div class="p-6 sm:p-8 pt-10">
                    <h3 class="text-[clamp(1.25rem,4vw,1.75rem)] font-bold mb-2">1-Week Campaign</h3>
                    <p class="text-gray-300 mb-6">For event organizers running marketing campaigns.</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-white">$29</span>
                        <span class="text-gray-300">/week</span>
                    </div>
                    <div class="h-px bg-primary/30 my-6"></div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>7 daily posts</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>Premium content</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>Email & SMS delivery</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>Priority support</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-auto p-6 sm:p-8 pt-0">
                    <button class="w-full py-3 px-4 bg-primary hover:bg-primary/90 text-white rounded-xl transition-colors duration-300 font-medium">Choose Plan</button>
                </div>
            </div>
            
            <!-- Enterprise Card -->
            <div class="bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 overflow-hidden flex flex-col transition-transform hover:-translate-y-2 duration-300">
                <div class="p-6 sm:p-8">
                    <h3 class="text-[clamp(1.25rem,4vw,1.75rem)] font-bold mb-2">2-Week Campaign</h3>
                    <p class="text-gray-400 mb-6">Extended marketing for major events and festivals.</p>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-white">$49</span>
                        <span class="text-gray-400">/2 weeks</span>
                    </div>
                    <div class="h-px bg-white/10 my-6"></div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>14 daily posts</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>Premium content</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>Multiple formats</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa fa-check text-accent"></i>
                            <span>Dedicated support</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-auto p-6 sm:p-8 pt-0">
                    <button class="w-full py-3 px-4 border border-white/20 text-white rounded-xl hover:bg-white/10 transition-colors duration-300 font-medium">Choose Plan</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA (Tailwind CSS) -->
<section class="py-24 bg-gradient-to-r from-primary/90 to-primary text-white w-full">
    <div class="container mx-auto px-6 sm:px-12 max-w-6xl text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Transform Your Event Marketing?</h2>
        <p class="text-lg md:text-xl opacity-90 mb-8">Join thousands of event organizers who are already using PostrMagic to create better content faster.</p>
        <button class="bg-white text-primary hover:bg-gray-100 px-8 py-4 rounded-xl font-semibold text-lg transition-colors duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
            Upload Your First Poster Free
        </button>
    </div>
</section>



<?php include_once __DIR__ . '/includes/footer.php'; ?>