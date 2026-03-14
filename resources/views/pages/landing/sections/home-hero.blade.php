{{-- Hero & Features Section --}}
<section class="hero" id="home"
    style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ $hero['backgroundImage'] }}') center center / cover no-repeat;">
    <div class="hero-card">
        <h1>{{ $hero['title'] }}</h1>
        <p>{{ $hero['description'] }}</p>
        <a href="#" class="btn btn-primary" onclick="showConfirm(event)">{{ $hero['buttonText'] }}</a>
    </div>
</section>

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
