{{-- resources/views/landing/footer.blade.php --}}
<footer class="footer">
    <div class="footer-container">

        {{-- Quick Links --}}
        <div class="footer-col">
            <h3 class="footer-heading">Quick Links</h3>
            <ul class="footer-links">
                <a href="#home" class="nav-link active">Home</a>
                <a href="#services" class="nav-link">Services</a>
                <a href="#gallery" class="nav-link">Gallery</a>
                <a href="#contact" class="nav-link">Contact</a>
            </ul>
        </div>
        {{-- Contact Us --}}
        <div class="footer-col">
            <h3 class="footer-heading">Contact Us</h3>
            <ul class="footer-contact">
                <li class="contact-item">
                    <span class="contact-icon">&#128205;</span>
                    <span><strong>Address:</strong> {{ $footerData['contact']['address'] }}</span>
                </li>
                <li class="contact-item">
                    <span class="contact-icon">&#128222;</span>
                    <span><strong>Phone:</strong> {{ $footerData['contact']['phone'] }}</span>
                </li>
                <li class="contact-item">
                    <span class="contact-icon">&#9993;</span>
                    <span><strong>Email:</strong> {{ $footerData['contact']['email'] }}</span>
                </li>
            </ul>
        </div>

        {{-- Follow Us --}}
        <div class="footer-col">
            <h3 class="footer-heading">Follow Us</h3>
            <ul class="footer-social">
                @foreach ($footerData['socialLinks'] as $social)
                    <li>
                        <a href="{{ $social['url'] }}" class="footer-link" target="_blank" rel="noopener">
                            {{ $social['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('customer.login') }}"
                class="btn-book-now"onclick="showConfirm(event)">{{ $hero['buttonText'] }}></a>
        </div>

    </div>

    <div class="footer-bottom">
        <hr class="footer-divider">
        <p class="footer-copyright">{!! $footerData['copyrightText'] !!}</p>
        <a href="{{ route('admin.login') }}" class="footer-link" style="font-size: 0.75rem; opacity: 0.5;">Admin</a>
    </div>
</footer>

{{-- Custom Confirm Modal --}}
<div id="confirmModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-icon">🚗</div>
        <h2 class="modal-title">Ready to Book?</h2>
        <p class="modal-message">You're about to be redirected to the booking page. Would you like to continue?</p>
        <div class="modal-buttons">
            <button class="modal-btn modal-cancel" onclick="closeConfirm()">Cancel</button>
            <a href="{{ route('customer.login') }}" class="modal-btn modal-confirm">Yes, Book Now!</a>
        </div>
    </div>
</div>

<script>
    function showConfirm(e) {
        e.preventDefault();
        document.getElementById('confirmModal').classList.add('active');
    }

    function closeConfirm() {
        document.getElementById('confirmModal').classList.remove('active');
    }

    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) closeConfirm();
    });
</script>
