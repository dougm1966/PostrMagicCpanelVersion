// Main JS for PostrMagic

document.addEventListener('DOMContentLoaded', () => {
    /* Theme Management */
    const themeToggle = document.querySelector('.theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;
    
    // Initialize theme from localStorage or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);
    
    function setTheme(theme) {
        if (theme === 'dark') {
            body.setAttribute('data-theme', 'dark');
            if (themeIcon) themeIcon.className = 'fa fa-sun';
        } else {
            body.removeAttribute('data-theme');
            if (themeIcon) themeIcon.className = 'fa fa-moon';
        }
        localStorage.setItem('theme', theme);
    }
    
    function toggleTheme() {
        const currentTheme = body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    }
    
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
    
    // Expose theme functions globally for future code snippets
    window.PostrMagicTheme = {
        setTheme,
        toggleTheme,
        getCurrentTheme: () => body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light'
    };

    /* Mobile Nav Toggle */
    const navToggle = document.getElementById('nav-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (navToggle && mobileMenu) {
        navToggle.addEventListener('click', () => {
            // Toggle the mobile menu visibility
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('block');
            } else {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('block');
            }
        });
        
        // Close mobile menu when clicking outside of it
        document.addEventListener('click', (e) => {
            if (!navToggle.contains(e.target) && !mobileMenu.contains(e.target) && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('block');
            }
        });
    }

    /* Drag & Drop Preview for Poster Upload */
    const dropZone = document.getElementById('poster-dropzone');
    const fileInput = document.getElementById('poster-input');

    function handleFiles(files) {
        if (!files || !files[0]) return;
        const preview = document.getElementById('poster-preview');
        const file = files[0];
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = () => {
            preview.innerHTML = `<img src="${reader.result}" alt="Poster preview" />`;
        };
        reader.readAsDataURL(file);
    }

    if (dropZone) {
        ['dragenter', 'dragover'].forEach(evt => dropZone.addEventListener(evt, e => {
            e.preventDefault();
            dropZone.classList.add('over');
        }));

        ['dragleave', 'drop'].forEach(evt => dropZone.addEventListener(evt, e => {
            e.preventDefault();
            dropZone.classList.remove('over');
        }));

        dropZone.addEventListener('drop', e => {
            const files = e.dataTransfer.files;
            fileInput.files = files; // update original input
            handleFiles(files);
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', e => handleFiles(e.target.files));
    }

    /* Shader Background Animation - Performance Optimized */
    // Handle all shader canvases with a single manager
    const shaderCanvases = document.querySelectorAll('#shader-canvas, #shader-canvas-how');
    
    // Track if user is actively scrolling for performance optimization
    let isScrolling = false;
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        isScrolling = true;
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            isScrolling = false;
        }, 100); // Short delay to resume animations after scrolling stops
    });
    
    // Process each canvas with optimized rendering
    shaderCanvases.forEach(canvas => {
        if (!canvas) return;
        
        // Check if canvas is in viewport
        const isInViewport = (element) => {
            const rect = element.getBoundingClientRect();
            return (
                rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.bottom >= 0 &&
                rect.left <= (window.innerWidth || document.documentElement.clientWidth) &&
                rect.right >= 0
            );
        };
        
        const gl = canvas.getContext('webgl', { alpha: true, antialias: false, powerPreference: 'low-power' });
        if (gl) {
            function resize() {
                // Only resize when necessary
                const displayWidth = canvas.clientWidth;
                const displayHeight = canvas.clientHeight;
                
                if (canvas.width !== displayWidth || canvas.height !== displayHeight) {
                    canvas.width = displayWidth;
                    canvas.height = displayHeight;
                    gl.viewport(0, 0, gl.drawingBufferWidth, gl.drawingBufferHeight);
                    return true; // Dimensions changed
                }
                return false; // Dimensions unchanged
            }
            
            // Initial resize
            resize();
            
            // Throttled resize handler
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    resize();
                }, 100);
            });

            const vertexShaderSource = `
                attribute vec2 aPosition;
                void main() {
                    gl_Position = vec4(aPosition, 0.0, 1.0);
                }
            `;

            const fragmentShaderSource = `
                precision highp float;
                uniform float iTime;
                uniform vec2 iResolution;
                mat2 rotate2d(float angle){
                    float c = cos(angle), s = sin(angle);
                    return mat2(c, -s, s, c);
                }
                float variation(vec2 v1, vec2 v2, float strength, float speed) {
                    return sin(
                        dot(normalize(v1), normalize(v2)) * strength + iTime * speed
                    ) / 100.0;
                }
                vec3 paintCircle (vec2 uv, vec2 center, float rad, float width) {
                    vec2 diff = center-uv;
                    float len = length(diff);
                    len += variation(diff, vec2(0.0, 1.0), 5.0, 2.0);
                    len -= variation(diff, vec2(1.0, 0.0), 5.0, 2.0);
                    float circle = smoothstep(rad-width, rad, len) - smoothstep(rad, rad+width, len);
                    return vec3(circle);
                }
                void main() {
                    vec2 uv = gl_FragCoord.xy / iResolution.xy;
                    uv.x *= 1.5;
                    uv.x -= 0.25;
                    vec3 color = vec3(0.0);
                    float radius = 0.35;
                    vec2 center = vec2(0.5);
                    color += paintCircle(uv, center, radius, 0.035);
                    color += paintCircle(uv, center, radius - 0.018, 0.01);
                    color += paintCircle(uv, center, radius + 0.018, 0.005);
                    vec2 v = rotate2d(iTime) * uv;
                    color *= vec3(v.x, v.y, 0.7-v.y*v.x);
                    color += paintCircle(uv, center, radius, 0.003);
                    gl_FragColor = vec4(color, 1.0);
                }
            `;

            function compileShader(type, source) {
                const shader = gl.createShader(type);
                gl.shaderSource(shader, source);
                gl.compileShader(shader);
                if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
                    console.error('Shader compilation error:', gl.getShaderInfoLog(shader));
                    return null;
                }
                return shader;
            }

            const vertexShader = compileShader(gl.VERTEX_SHADER, vertexShaderSource);
            const fragmentShader = compileShader(gl.FRAGMENT_SHADER, fragmentShaderSource);
            
            if (vertexShader && fragmentShader) {
                const program = gl.createProgram();
                gl.attachShader(program, vertexShader);
                gl.attachShader(program, fragmentShader);
                gl.linkProgram(program);
                
                if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
                    console.error('Program linking error:', gl.getProgramInfoLog(program));
                } else {
                    gl.useProgram(program);
                    
                    const buffer = gl.createBuffer();
                    gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
                    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([
                        -1, -1,
                         1, -1,
                        -1,  1,
                        -1,  1,
                         1, -1,
                         1,  1,
                    ]), gl.STATIC_DRAW);
                    
                    const aPosition = gl.getAttribLocation(program, 'aPosition');
                    gl.enableVertexAttribArray(aPosition);
                    gl.vertexAttribPointer(aPosition, 2, gl.FLOAT, false, 0, 0);
                    
                    const iTimeLoc = gl.getUniformLocation(program, 'iTime');
                    const iResLoc = gl.getUniformLocation(program, 'iResolution');
                    
                    function render(time) {
                        // Skip rendering during active scroll for better performance
                        if (isScrolling && !isInViewport(canvas)) {
                            requestAnimationFrame(render);
                            return;
                        }
                        
                        // Lower framerate when off-screen
                        if (!isInViewport(canvas)) {
                            setTimeout(() => requestAnimationFrame(render), 100); // Reduced framerate when not visible
                            return;
                        }
                        
                        gl.useProgram(program);
                        if (iTimeLoc !== -1) {
                            gl.uniform1f(iTimeLoc, time * 0.001);
                        }
                        if (iResLoc !== -1) {
                            gl.uniform2f(iResLoc, canvas.width, canvas.height);
                        }
                        gl.drawArrays(gl.TRIANGLES, 0, 6);
                        requestAnimationFrame(render);
                    }
                    requestAnimationFrame(render);
                }
            }
        }
    });
});
