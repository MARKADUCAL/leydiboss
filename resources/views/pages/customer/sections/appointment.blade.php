{{-- pages/customer/sections/appointment.blade.php --}}
@extends('layouts.customer')

@section('title', 'Appointments - Leydi Boss')

@push('styles')
    @vite(['resources/css/auth/customer/appointment.css'])
@endpush

@section('content')

    <div class="lb-card">
        {{-- Header Section --}}
        <div class="apt-hero-section">
            <div class="apt-hero-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <path d="M9 11l3 3L22 4"></path>
                </svg>
            </div>
            <div class="apt-hero-content">
                <h1 class="apt-hero-title">Book Your Appointment</h1>
                <p class="apt-hero-subtitle">Schedule your car wash service with ease</p>
            </div>
        </div>

        <h1 class="lb-section-title">Book an Appointment</h1>
        <p class="lb-section-subtitle">Select your vehicle and service package to view pricing.</p>

        {{-- Vehicle & Service Selection --}}
        <div class="apt-form-section">
            <div class="apt-form-group">
                <label class="apt-label">Select from Saved Vehicles <span class="req">*</span></label>
                <select id="vehicleSelect" class="apt-select">
                    <option value="">-- Choose a vehicle --</option>
                    @forelse ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" data-type-id="{{ $vehicle->vehicle_type_id }}"
                            data-type-code="{{ $vehicle->vehicleType->code ?? '' }}"
                            data-type-desc="{{ $vehicle->vehicleType->description ?? '' }}"
                            data-plate="{{ $vehicle->plate_number }}">
                            {{ $vehicle->nickname }} ({{ $vehicle->vehicleType->code ?? 'N/A' }})
                        </option>
                    @empty
                        <option value="" disabled>No vehicles found. Add one in your profile.</option>
                    @endforelse
                </select>
            </div>

            <div class="apt-form-group">
                <label class="apt-label">Select Services <span class="req">*</span></label>
                <select id="serviceSelect" class="apt-select">
                    <option value="">-- Choose a service --</option>
                    @forelse ($servicePackages as $package)
                        <option value="{{ $package->id }}">
                            {{ $package->code }} - {{ $package->name }}
                        </option>
                    @empty
                        <option value="" disabled>No services available</option>
                    @endforelse
                </select>
            </div>
        </div>

        {{-- Vehicle Info Display --}}
        <div id="vehicleInfoSection" class="apt-info-section" style="display: none;">
            <div class="apt-info-card">
                <div class="apt-info-label">
                    <span id="vehicleNickname">—</span>
                    <span id="vehiclePlate" class="apt-plate">—</span>
                </div>
                <div class="apt-info-type">
                    <span id="vehicleTypeCode">—</span> - <span id="vehicleTypeDesc">—</span>
                </div>
            </div>
        </div>

        {{-- Price Display --}}
        <div id="priceSection" class="apt-price-section" style="display: none;">
            <div class="apt-price-box">
                <div class="apt-price-label">Calculated Price</div>
                <div class="apt-price-amount">₱<span id="calculatedPrice">0.00</span></div>
            </div>
        </div>

        {{-- Schedule Section --}}
        <div id="scheduleSection" class="apt-schedule-section" style="display: none;">
            <div class="apt-schedule-header">
                <svg class="apt-schedule-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <h2 class="apt-schedule-title">Schedule</h2>
            </div>

            {{-- Calendar --}}
            <div class="apt-calendar-wrap">
                <div class="apt-calendar-nav">
                    <button type="button" class="apt-calendar-btn" id="prevMonth"
                        onclick="previousMonth()">&lsaquo;</button>
                    <div class="apt-calendar-month" id="monthYear"></div>
                    <button type="button" class="apt-calendar-btn" id="nextMonth" onclick="nextMonth()">&rsaquo;</button>
                    <button type="button" class="apt-calendar-today" id="todayBtn" onclick="goToday()">Today</button>
                </div>

                <div class="apt-calendar">
                    <div class="apt-calendar-weekdays">
                        <div class="apt-weekday">Sun</div>
                        <div class="apt-weekday">Mon</div>
                        <div class="apt-weekday">Tue</div>
                        <div class="apt-weekday">Wed</div>
                        <div class="apt-weekday">Thu</div>
                        <div class="apt-weekday">Fri</div>
                        <div class="apt-weekday">Sat</div>
                    </div>
                    <div class="apt-calendar-days" id="calendarDays"></div>
                </div>
            </div>

            {{-- Available Times --}}
            <div id="availableTimesSection" class="apt-times-section" style="display: none;">
                <div class="apt-times-label" id="availableTimesLabel"></div>
                <div class="apt-times-grid" id="availableTimesGrid"></div>
            </div>
        </div>

        {{-- Payment Section --}}
        <div id="paymentSection" class="apt-payment-section" style="display: none;">
            <div class="apt-payment-header">
                <svg class="apt-payment-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
                <h2 class="apt-payment-title">Payment</h2>
            </div>

            <div class="apt-payment-form">
                <div class="apt-form-group">
                    <label class="apt-label">Payment Type <span class="req">*</span></label>
                    <select id="paymentTypeSelect" class="apt-select">
                        <option value="">-- Choose payment type --</option>
                        <option value="cash">💳 Cash</option>
                        <option value="online">🌐 Online Payment</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Additional Information Section --}}
        <div id="additionalInfoSection" class="apt-additional-section" style="display: none;">
            <div class="apt-additional-header">
                <svg class="apt-additional-icon" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="2" y="2" width="8" height="8" fill="currentColor"></rect>
                </svg>
                <h2 class="apt-additional-title">Additional Information</h2>
            </div>

            <div class="apt-additional-form">
                <div class="apt-form-group">
                    <label class="apt-label">Notes</label>
                    <textarea id="notesTextarea" class="apt-textarea"
                        placeholder="Add any special requests or notes for your appointment" rows="8"></textarea>
                </div>
            </div>
        </div>

        {{-- Book Button --}}
        <div class="apt-action-section" style="display: none;" id="actionSection">
            <button type="button" class="apt-btn-book" id="bookBtn" onclick="handleBooking()">
                📅 Book Appointment
            </button>
        </div>
    </div>

    @push('scripts')
        <script>
            // Prepare pricing map from backend
            const pricingMap = @json(
                $pricingEntries
                    ? $pricingEntries->mapToGroups(fn($e) => ["{$e->vehicle_type_id}_{$e->service_package_id}" => $e->price])->all()
                    : []
            );

            // Current date and time from backend
            const currentDateStr = @json($currentDate->format('Y-m-d'));
            const currentTimeStr = @json($currentTime);
            const currentDate = new Date(currentDateStr);

            // Parse current time (format: HH:mm)
            const [currentHr, currentMin] = currentTimeStr.split(':').map(Number);
            const currentDateTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(),
                currentHr, currentMin);

            let displayDate = new Date(currentDate);
            let selectedDate = null;
            let selectedTime = null;

            const vehicleSelect = document.getElementById('vehicleSelect');
            const serviceSelect = document.getElementById('serviceSelect');
            const vehicleInfoSection = document.getElementById('vehicleInfoSection');
            const priceSection = document.getElementById('priceSection');
            const scheduleSection = document.getElementById('scheduleSection');
            const paymentSection = document.getElementById('paymentSection');
            const additionalInfoSection = document.getElementById('additionalInfoSection');
            const actionSection = document.getElementById('actionSection');

            // Time slots in 30-minute intervals from 8:00 AM to 8:00 PM
            const timeSlots = [
                '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
                '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
                '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00'
            ];

            // Update when vehicle is selected
            vehicleSelect.addEventListener('change', updateSelection);

            // Update when service is selected
            serviceSelect.addEventListener('change', updateSelection);

            // Update when payment type is selected
            document.getElementById('paymentTypeSelect').addEventListener('change', updateActionButton);

            function updateSelection() {
                const vehicleId = vehicleSelect.value;
                const serviceId = serviceSelect.value;

                if (!vehicleId || !serviceId) {
                    vehicleInfoSection.style.display = 'none';
                    priceSection.style.display = 'none';
                    scheduleSection.style.display = 'none';
                    paymentSection.style.display = 'none';
                    additionalInfoSection.style.display = 'none';
                    actionSection.style.display = 'none';
                    return;
                }

                // Get selected vehicle option
                const vehicleOption = vehicleSelect.options[vehicleSelect.selectedIndex];
                const typeId = vehicleOption.getAttribute('data-type-id');
                const typeCode = vehicleOption.getAttribute('data-type-code');
                const typeDesc = vehicleOption.getAttribute('data-type-desc');
                const plate = vehicleOption.getAttribute('data-plate');
                const nickname = vehicleOption.textContent.split('(')[0].trim();

                // Update vehicle info display
                document.getElementById('vehicleNickname').textContent = nickname;
                document.getElementById('vehiclePlate').textContent = plate;
                document.getElementById('vehicleTypeCode').textContent = typeCode;
                document.getElementById('vehicleTypeDesc').textContent = typeDesc;
                vehicleInfoSection.style.display = 'block';

                // Calculate and display price
                const priceKey = `${typeId}_${serviceId}`;
                const priceArray = pricingMap[priceKey];

                if (priceArray && priceArray.length > 0) {
                    const priceAmount = priceArray[0];
                    document.getElementById('calculatedPrice').textContent = parseFloat(priceAmount).toFixed(2);
                    priceSection.style.display = 'block';
                    scheduleSection.style.display = 'block';

                    // Initialize calendar
                    renderCalendar();
                    updateActionButton();
                } else {
                    priceSection.style.display = 'none';
                    scheduleSection.style.display = 'none';
                    paymentSection.style.display = 'none';
                    additionalInfoSection.style.display = 'none';
                    actionSection.style.display = 'none';
                    console.warn('Price not found for combination:', priceKey);
                }
            }

            function renderCalendar() {
                const year = displayDate.getFullYear();
                const month = displayDate.getMonth();

                // Update month/year display
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                document.getElementById('monthYear').textContent = `${monthNames[month]} ${year}`;

                // Get first day of month and number of days
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const prevDaysInMonth = new Date(year, month, 0).getDate();

                const calendarDays = document.getElementById('calendarDays');
                calendarDays.innerHTML = '';

                // Previous month's days
                for (let i = firstDay - 1; i >= 0; i--) {
                    const day = document.createElement('div');
                    day.className = 'apt-calendar-day apt-calendar-day--other';
                    day.textContent = prevDaysInMonth - i;
                    calendarDays.appendChild(day);
                }

                // Current month's days
                for (let day = 1; day <= daysInMonth; day++) {
                    const dayEl = document.createElement('div');
                    const dayDate = new Date(year, month, day);
                    const formattedDate = dayDate.toISOString().split('T')[0];

                    dayEl.className = 'apt-calendar-day';
                    dayEl.textContent = day;

                    // Highlight today
                    if (formattedDate === currentDateStr) {
                        dayEl.classList.add('apt-calendar-day--today');
                    }

                    // Disable past dates
                    if (dayDate < currentDate) {
                        dayEl.classList.add('apt-calendar-day--disabled');
                    } else {
                        dayEl.addEventListener('click', () => selectDate(formattedDate, dayEl));
                    }

                    // Highlight selected date
                    if (selectedDate === formattedDate) {
                        dayEl.classList.add('apt-calendar-day--selected');
                    }

                    calendarDays.appendChild(dayEl);
                }

                // Next month's days
                const totalCells = calendarDays.children.length;
                const remainingCells = 42 - totalCells; // 6 rows * 7 days
                for (let day = 1; day <= remainingCells; day++) {
                    const dayEl = document.createElement('div');
                    dayEl.className = 'apt-calendar-day apt-calendar-day--other';
                    dayEl.textContent = day;
                    calendarDays.appendChild(dayEl);
                }
            }

            function selectDate(dateStr, dayEl) {
                selectedDate = dateStr;
                selectedTime = null;

                // Update selected state
                document.querySelectorAll('.apt-calendar-day--selected').forEach(el => {
                    el.classList.remove('apt-calendar-day--selected');
                });
                dayEl.classList.add('apt-calendar-day--selected');

                // Show available times
                showAvailableTimes(dateStr);
            }

            function showAvailableTimes(dateStr) {
                const selectedDateObj = new Date(dateStr);
                const isToday = dateStr === currentDateStr;

                // Filter available times
                let availableTimes = timeSlots;
                if (isToday) {
                    availableTimes = timeSlots.filter(time => {
                        const [hr, min] = time.split(':').map(Number);
                        const slotDateTime = new Date(selectedDateObj.getFullYear(), selectedDateObj.getMonth(),
                            selectedDateObj.getDate(), hr, min);
                        // Only show times that are at least 30 minutes from now
                        return slotDateTime > new Date(currentDateTime.getTime() + 30 * 60000);
                    });
                }

                // Render time slots
                const timesGrid = document.getElementById('availableTimesGrid');
                timesGrid.innerHTML = '';

                availableTimes.forEach(time => {
                    const timeBtn = document.createElement('button');
                    timeBtn.type = 'button';
                    timeBtn.className = 'apt-time-button';
                    timeBtn.textContent = formatTime(time);

                    if (selectedTime === time) {
                        timeBtn.classList.add('apt-time-button--selected');
                    }

                    timeBtn.addEventListener('click', () => selectTime(time, timeBtn));
                    timesGrid.appendChild(timeBtn);
                });

                // Update label
                const selectedDateDisplay = new Date(dateStr).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                document.getElementById('availableTimesLabel').textContent = `Available time on ${selectedDateDisplay}`;
                document.getElementById('availableTimesSection').style.display = 'block';

                updateActionButton();
            }

            function selectTime(time, timeBtn) {
                selectedTime = time;

                // Update selected state
                document.querySelectorAll('.apt-time-button--selected').forEach(el => {
                    el.classList.remove('apt-time-button--selected');
                });
                timeBtn.classList.add('apt-time-button--selected');

                // Show payment and additional info sections
                paymentSection.style.display = 'block';
                additionalInfoSection.style.display = 'block';

                updateActionButton();
            }

            function formatTime(time24) {
                const [hr, min] = time24.split(':').map(Number);
                const period = hr >= 12 ? 'PM' : 'AM';
                const hr12 = hr > 12 ? hr - 12 : (hr === 0 ? 12 : hr);
                return `${String(hr12).padStart(2, ' ')}:${String(min).padStart(2, '0')} ${period}`;
            }

            function previousMonth() {
                displayDate.setMonth(displayDate.getMonth() - 1);
                renderCalendar();
            }

            function nextMonth() {
                displayDate.setMonth(displayDate.getMonth() + 1);
                renderCalendar();
            }

            function goToday() {
                displayDate = new Date(currentDate);
                renderCalendar();
            }

            function updateActionButton() {
                const vehicleId = vehicleSelect.value;
                const serviceId = serviceSelect.value;
                const paymentType = document.getElementById('paymentTypeSelect').value;

                if (vehicleId && serviceId && selectedDate && selectedTime && paymentType) {
                    actionSection.style.display = 'block';
                } else {
                    actionSection.style.display = 'none';
                }
            }

            function handleBooking() {
                const vehicleId = vehicleSelect.value;
                const serviceId = serviceSelect.value;
                const paymentType = document.getElementById('paymentTypeSelect').value;
                const notes = document.getElementById('notesTextarea').value.trim();

                if (!vehicleId || !serviceId || !selectedDate || !selectedTime || !paymentType) {
                    alert('Please complete all required selections: vehicle, service, date, time, and payment type');
                    return;
                }

                // Store selection and redirect to booking details
                alert(
                    `Booking Confirmed!\nVehicle: ${vehicleId}\nService: ${serviceId}\nDate: ${selectedDate}\nTime: ${selectedTime}\nPayment: ${paymentType}\nNotes: ${notes || '(none)'}`
                );
                // Can submit form or redirect here
            }

            // Initialize calendar on page load
            window.addEventListener('load', () => {
                // Calendar will be shown when vehicle and service are selected
            });
        </script>
    @endpush

@endsection
