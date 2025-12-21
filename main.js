document.addEventListener('DOMContentLoaded', function() {
    
    // 1. MOBILE NAVIGATION
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            const isOpen = mobileMenu.classList.toggle('open');
            const icon = mobileMenuButton.querySelector('i');
            if (icon) {
                icon.className = isOpen ? 'fas fa-times' : 'fas fa-bars-staggered';
            }
            document.body.style.overflow = isOpen ? 'hidden' : '';
        });
    }

    // 2. NAVBAR SCROLL EFFEKT
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) navbar?.classList.add('nav-scrolled');
        else navbar?.classList.remove('nav-scrolled');
    }, { passive: true });

    // 3. KONTAKTFORMULAR (AJAX)
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const submitButton = contactForm.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            submitButton.disabled = true;
            submitButton.textContent = 'Wird gesendet...';

            try {
                const formData = new FormData(contactForm);
                const response = await fetch('send_mail.php', { method: 'POST', body: formData });
                const data = await response.json();
                
                if (data.success) {
                    contactForm.innerHTML = `
                        <div class="text-center py-16 animate-reveal">
                            <div class="w-20 h-20 bg-orange-600 text-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl shadow-orange-600/20">
                                <i class="fas fa-check text-3xl"></i>
                            </div>
                            <h3 class="text-4xl font-serif font-bold mb-4 tracking-tighter uppercase">Erfolg!</h3>
                            <p class="text-stone-500 font-serif italic text-xl">Vielen Dank für Ihre Anfrage. Ich melde mich in Kürze bei Ihnen.</p>
                            <button onclick="window.location.reload()" class="mt-12 text-[10px] font-bold uppercase tracking-[0.3em] text-stone-400 hover:text-orange-600 transition-colors">Neue Nachricht senden</button>
                        </div>`;
                } else { throw new Error(data.message); }
            } catch (error) {
                alert('Es gab ein Problem: ' + error.message);
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
    }

    // 4. HELPER (Year & Active Links)
    document.querySelectorAll('#current-year').forEach(el => el.textContent = new Date().getFullYear());
    
    const currentPath = window.location.pathname.split('/').pop() || 'index.html';
    document.querySelectorAll('nav a').forEach(link => {
        if (link.getAttribute('href') === currentPath) link.classList.add('nav-link-active');
    });
});