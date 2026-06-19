<br>
<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
    <div class="sidebar-brand-icon"
        style="background-color: #0000; padding: 8px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
        
        <style>
            .badge-pending-yellow {
                background: linear-gradient(135deg, #f6c23e 0%, #f4b619 100%) !important;
                color: white !important;
                border: 2px solid #fff !important;
                box-shadow: 0 0 15px rgba(246, 194, 62, 0.6) !important;
                font-size: 0.75rem !important;
                font-weight: 900 !important;
                width: 22px !important;
                height: 22px !important;
                min-width: 22px !important;
                border-radius: 50% !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                animation: pulse-yellow 2s infinite, notify-bounce 3s infinite !important;
                flex-shrink: 0 !important;
                line-height: 1 !important;
                position: relative;
                top: -1px;
            }
        </style>

        @if (in_array($nama, $namaSMI))
            {{-- Logo SMI --}}
            <img src="{{ asset('backend/logosmi1.jpg') }}" alt="SMI Logo"
                style="height: 70px; width: auto; object-fit: contain; display: block;">
        @else
            {{-- Logo MBC --}}
            <img src="{{ asset('backend/img/MBC.svg') }}" alt="MBC Logo"
                style="height: 65px; width: auto; object-fit: contain; display: block;">
        @endif
    </div>
</a>

<!-- Divider -->
<hr class="sidebar-divider my-0" />
