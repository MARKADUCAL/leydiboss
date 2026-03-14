{{-- Contact Section --}}
<section class="contact" id="contact">
    <div class="container">
        <h2>Contact Us</h2>
        <p>We'd love to hear from you. Get in touch with us today!</p>
        <div class="contact-wrapper">
            {{-- Contact Form --}}
            <div class="contact-form">
                <h3>Send us a Message</h3>
                <form action="{{ route('landing.contact.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>

            {{-- Contact Info --}}
            <div class="contact-info">
                <h3>Contact Information</h3>
                @foreach ($contactInfo as $info)
                    <div class="info-item">
                        <i class="fas {{ $info['icon'] }}"></i>
                        <div>
                            <h4>{{ $info['title'] }}</h4>
                            <p>{!! $info['content'] !!}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
