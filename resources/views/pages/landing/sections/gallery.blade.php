{{-- Gallery Section --}}
<section class="gallery" id="gallery">
    <div class="container">
        <h2>Our Gallery</h2>
        <div class="gallery-grid">
            @foreach ($galleries as $gallery)
                <div class="gallery-item">
                    <img src="{{ asset($gallery['image']) }}" alt="{{ $gallery['alt'] }}" class="gallery-image">
                    <h3>{{ $gallery['title'] }}</h3>
                </div>
            @endforeach
        </div>
    </div>
</section>
