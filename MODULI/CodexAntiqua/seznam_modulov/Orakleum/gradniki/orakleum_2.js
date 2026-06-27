document.addEventListener('DOMContentLoaded', function() {
    // Inicializacija
    initApp();
    
    // Funkcije za inicializacijo
    function initApp() {
        // Nastavitev CSRF tokena
        setCsrfToken();
        
        // Prikaz intro animacije
        setTimeout(() => {
            document.getElementById('intro-animation').classList.add('hidden');
            document.getElementById('main-content').classList.remove('hidden');
            initAnimations();
        }, 4000);
        
        // Inicializacija glasbe
        initMusic();
        
        // Inicializacija navigacije
        initNavigation();
        
        // Inicializacija obrazcev
        initForms();
        
        // Inicializacija teme
        initTheme();
        
        // Nalaganje komentarjev
        loadTestimonials();
        
        // Inicializacija strani z branji
        if (document.querySelector('.reading-category')) {
            initReadingsPage();
        }
        
        // Inicializacija zahvalne strani za naročila
        if (document.getElementById('order-summary')) {
            initThankYouPage();
        }
    }
    
    function setCsrfToken() {
        const token = generateCsrfToken();
        const tokenInput = document.getElementById('csrf_token');
        if (tokenInput) {
            tokenInput.value = token;
        }
        sessionStorage.setItem('csrf_token', token);
    }
    
    function generateCsrfToken() {
        return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    }
    
    function initAnimations() {
        // Inicializacija animacij za elemente
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                }
            });
        }, { threshold: 0.1 });
        
        // Opazuj vse elemente, ki naj bi se animirali
        document.querySelectorAll('.category-card, .donation-method, .testimonial-card').forEach(el => {
            observer.observe(el);
        });
        
        // Animacija kategorij
        const categoryCards = document.querySelectorAll('.category-card');
        categoryCards.forEach(card => {
            card.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                window.location.href = `branja.html?category=${category}`;
            });
        });
    }
    
    function initMusic() {
        const musicToggle = document.getElementById('music-toggle');
        const ambientMusic = document.getElementById('ambient-music');
        
        if (!musicToggle || !ambientMusic) return;
        
        // Preveri, ali je uporabnik že interagiral s stranjo za avdio predvajanje
        document.body.addEventListener('click', function() {
            if (ambientMusic.paused) {
                ambientMusic.play().catch(e => console.log('Avdio ni mogoče predvajati:', e));
            }
        }, { once: true });
        
        musicToggle.addEventListener('click', function() {
            if (ambientMusic.paused) {
                ambientMusic.play();
                musicToggle.innerHTML = '<i class="fas fa-volume-up"></i>';
            } else {
                ambientMusic.pause();
                musicToggle.innerHTML = '<i class="fas fa-volume-mute"></i>';
            }
        });
    }
    
    function initNavigation() {
        const hamburger = document.querySelector('.hamburger');
        const navMenu = document.querySelector('.nav-menu');
        
        if (!hamburger || !navMenu) return;
        
        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Skrij navigacijo ob kliku na povezavo
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
        
        // Gladka pomikanje do ankerskih povezav
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
    
    function initForms() {
        // Kontaktni obrazec
        const contactForm = document.getElementById('contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (validateForm(this)) {
                    submitForm(this, 'kontakt.php');
                }
            });
        }
        
        // Prijavni obrazec
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (validateLoginForm(this)) {
                    processLogin(this);
                }
            });
        }
        
        // Obrazec za naročilo
        const orderForm = document.getElementById('order-form');
        if (orderForm) {
            orderForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (validateOrderForm(this)) {
                    submitForm(this, 'poslji_narocilo.php');
                }
            });
        }
    }
    
    function validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input, textarea');
        
        // Preveri honeypot polje
        const honeypot = form.querySelector('.honeypot input');
        if (honeypot && honeypot.value !== '') {
            isValid = false;
            // Verjetno je bot, ne prikaži napake
            return false;
        }
        
        inputs.forEach(input => {
            if (input.hasAttribute('required') && !input.value.trim()) {
                isValid = false;
                highlightError(input, 'To polje je obvezno');
            } else if (input.type === 'email' && !isValidEmail(input.value)) {
                isValid = false;
                highlightError(input, 'Vnesite veljaven e-poštni naslov');
            }
        });
        
        return isValid;
    }
    
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function highlightError(input, message) {
        input.style.borderColor = 'red';
        
        // Prikaz sporočila o napaki
        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('error-message')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.style.color = 'red';
            errorDiv.style.fontSize = '0.8rem';
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }
        
        errorDiv.textContent = message;
        
        // Odstrani napako ob spremembi vnosa
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            if (errorDiv) errorDiv.textContent = '';
        }, { once: true });
    }
    
    function submitForm(form, actionUrl) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.textContent = 'Pošiljanje...';
        submitBtn.disabled = true;
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } else if (data.errors) {
                alert(data.errors.join('\n'));
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch(error => {
            console.error('Napaka:', error);
            alert('Prišlo je do napake. Prosimo, poskusite znova.');
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }
    
    function initTheme() {
        const themeToggle = document.querySelector('.theme-toggle');
        if (!themeToggle) return;
        
        const savedTheme = localStorage.getItem('theme') || 'dark';
        
        // Nastavi shranjeno temo
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);
        
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });
    }
    
    function updateThemeIcon(theme) {
        const icon = document.querySelector('.theme-toggle i');
        if (icon) {
            icon.className = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        }
    }
    
    function loadTestimonials() {
        const container = document.getElementById('testimonial-container');
        if (!container) return;
        
        // Naloži komentarje iz JSON datoteke
        fetch('komentarji.php')
            .then(response => response.json())
            .then(data => {
                displayTestimonials(data);
            })
            .catch(error => {
                console.error('Napaka pri nalaganju komentarjev:', error);
                // Prikaži testne komentarje, če nalaganje ne uspe
                displayTestimonials([
                    { name: "Anja", comment: "Branje je bilo neverjetno natančno. Hvala za vpogled v mojo situacijo!", timestamp: "2023-10-15 14:30:22" },
                    { name: "Bojan", comment: "Tarot branje mi je pomagalo razumeti trenutno stanje in mi dalo pogum za sprejem odločitev.", timestamp: "2023-10-16 09:15:47" },
                    { name: "Mojca", comment: "Energijska diagnostika je razkrila vzroke za mojo utrujenost. Sedaj delam na izboljšanju pretoka energije.", timestamp: "2023-10-17 16:45:33" }
                ]);
            });
    }
    
    function displayTestimonials(testimonials) {
        const container = document.getElementById('testimonial-container');
        if (!container) return;
        
        // Prikaži le prvih 6 komentarjev
        const limitedTestimonials = testimonials.slice(0, 6);
        
        container.innerHTML = limitedTestimonials.map(testimonial => `
            <div class="testimonial-card">
                <div class="testimonial-text">"${testimonial.comment}"</div>
                <div class="testimonial-author">- ${testimonial.name}</div>
                <div class="testimonial-date">${formatDate(testimonial.timestamp)}</div>
            </div>
        `).join('');
        
        // Nastavi obnašanje gumba "Naloži več"
        const loadMoreBtn = document.getElementById('load-more');
        if (loadMoreBtn && testimonials.length > 6) {
            loadMoreBtn.addEventListener('click', function() {
                // Naloži vse preostale komentarje
                container.innerHTML = testimonials.map(testimonial => `
                    <div class="testimonial-card">
                        <div class="testimonial-text">"${testimonial.comment}"</div>
                        <div class="testimonial-author">- ${testimonial.name}</div>
                        <div class="testimonial-date">${formatDate(testimonial.timestamp)}</div>
                    </div>
                `).join('');
                
                // Skrij gumb
                this.style.display = 'none';
            });
        } else if (loadMoreBtn) {
            loadMoreBtn.style.display = 'none';
        }
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('sl-SI', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    function initReadingsPage() {
        const toggleButtons = document.querySelectorAll('.toggle-description');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const content = this.previousElementSibling;
                const longDescription = content.querySelector('.long-description');
                
                if (longDescription) {
                    longDescription.classList.toggle('show');
                    this.classList.toggle('active');
                    
                    // Spremeni besedilo gumba
                    if (longDescription.classList.contains('show')) {
                        this.innerHTML = 'Manj o branju <i class="fas fa-chevron-up"></i>';
                    } else {
                        this.innerHTML = 'Več o branju <i class="fas fa-chevron-down"></i>';
                    }
                }
            });
        });
        
        // Odpri kategorijo iz URL, če obstaja
        openCategoryFromURL();
    }
    
    function openCategoryFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');
        
        if (category) {
            const element = document.getElementById(category);
            if (element) {
                const toggleButton = element.querySelector('.toggle-description');
                if (toggleButton) {
                    toggleButton.click(); // Simuliraj klik za odpiranje
                    // Scrollaj do elementa
                    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        }
    }
    
    function initThankYouPage() {
        const urlParams = new URLSearchParams(window.location.search);
        const branje = urlParams.get('branje');
        const ime = urlParams.get('ime');
        const email = urlParams.get('email');
        
        const orderSummary = document.getElementById('order-summary');
        
        if (branje && ime && email && orderSummary) {
            const branjeMap = {
                'hitro': 'Hitro vprašanje (9 €)',
                'ljubezen': 'Ljubezensko branje (18 €)',
                'kariera': 'Kariera & Finance (18 €)',
                'senca': 'Senca & notranje blokade (18 €)',
                'boginje': 'Zmaji, boginje, vile (18 €)',
                'karma': 'Dušna pot & karma (27 €)',
                'pretekla': 'Pretekla življenja (27 €)',
                'energetsko': 'Energetska diagnostika (27 €)',
                'pdf': 'Mesečni PDF vodnik (33 €)',
                'dusni_par': 'Dušni odnosi (33 €)',
                'astro': 'Astro-numerološki vpogled (42 €)',
                'svetovanje': 'Zoom svetovanje (42 €)',
                'celostno': 'Celostno intuitivno branje (54 €)'
            };
            
            orderSummary.innerHTML = `
                <p><strong>Ime:</strong> ${ime}</p>
                <p><strong>Email:</strong> ${email}</p>
                <p><strong>Branje:</strong> ${branjeMap[branje] || branje}</p>
                <p><strong>Datum:</strong> ${new Date().toLocaleDateString('sl-SI')}</p>
            `;
        } else if (orderSummary) {
            orderSummary.innerHTML = '<p>Podrobnosti naročila niso na voljo.</p>';
        }
    }
    
    // Funkcije za prijavo in registracijo
    function validateLoginForm(form) {
        // Enostavna validacija
        const email = form.querySelector('#login-email');
        const password = form.querySelector('#login-password');
        
        if (!email.value || !isValidEmail(email.value)) {
            highlightError(email, 'Vnesite veljaven e-poštni naslov');
            return false;
        }
        
        if (!password.value || password.value.length < 6) {
            highlightError(password, 'Geslo mora vsebovati vsaj 6 znakov');
            return false;
        }
        
        return true;
    }
    
    function processLogin(form) {
        // Simulacija pošiljanja podatkov
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.textContent = 'Prijavljanje...';
        submitBtn.disabled = true;
        
        // Simulacija zakasnitve strežnika
        setTimeout(() => {
            // V resnični aplikaciji bi tukaj poslali podatke na strežnik
            alert('Prijava uspešna! (to je samo demonstracija)');
            document.getElementById('login-modal').classList.add('hidden');
            
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }, 1500);
    }
    
    function validateOrderForm(form) {
        // Preveri, ali je potrjen pravni checkbox
        const legalConfirm = form.querySelector('#legalConfirm');
        if (legalConfirm && !legalConfirm.checked) {
            alert('Potrditi morate, da ste seznanjeni s pogoji storitve.');
            return false;
        }
        
        return validateForm(form);
    }
    
    // Odpri modal za prijavo
    const loginLink = document.getElementById('login-link');
    if (loginLink) {
        loginLink.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('login-modal').classList.remove('hidden');
        });
    }
    
    // Zapri modal
    const closeModal = document.querySelector('.close-modal');
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            this.closest('.modal').classList.add('hidden');
        });
    }
    
    // Klik zunaj modalnega okna ga zapre
    window.addEventListener('click', function(e) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
});