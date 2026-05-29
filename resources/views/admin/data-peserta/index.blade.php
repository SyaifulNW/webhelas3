@extends('layouts.masteradmin')

@section('content')
<div class="container-fluid px-4 py-2">
    <!-- Gorgeous Premium Navigation Tabs -->
    <ul class="nav nav-pills mb-4 p-2 shadow-sm rounded-lg border bg-white" id="unifiedParticipantTabs" role="tablist"
        style="border-radius: 12px; gap: 8px;">
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link active w-100 py-3 font-weight-bold text-center border-0 transition-all d-flex align-items-center justify-content-center" 
                    id="tab-mbc-btn" data-toggle="pill" data-target="#panel-mbc" type="button" role="tab" aria-controls="panel-mbc" aria-selected="true"
                    style="border-radius: 8px; font-size: 0.95rem; letter-spacing: 0.5px; transition: all 0.3s ease;">
                <i class="fas fa-users mr-2" style="font-size: 1.1rem;"></i> Panel Data Peserta MBC
            </button>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link w-100 py-3 font-weight-bold text-center border-0 transition-all d-flex align-items-center justify-content-center" 
                    id="tab-m1t-btn" data-toggle="pill" data-target="#panel-m1t" type="button" role="tab" aria-controls="panel-m1t" aria-selected="false"
                    style="border-radius: 8px; font-size: 0.95rem; letter-spacing: 0.5px; transition: all 0.3s ease;">
                <i class="fas fa-graduation-cap mr-2" style="font-size: 1.1rem;"></i> Panel Data Peserta M1T
            </button>
        </li>
    </ul>

    <!-- Tab Panels with Beautiful Loader and Seamless Responsive Iframes -->
    <div class="tab-content shadow-lg rounded-lg border bg-white" id="unifiedParticipantTabContent"
         style="border-radius: 16px; overflow: hidden; min-height: 80vh; position: relative;">
        
        <!-- PANEL DATA PESERTA MBC -->
        <div class="tab-pane fade show active" id="panel-mbc" role="tabpanel" aria-labelledby="tab-mbc-btn" 
             style="min-height: 80vh; position: relative;">
            <div class="iframe-loader position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white" 
                 style="z-index: 10; transition: opacity 0.3s ease; pointer-events: none;">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
                <h6 class="text-secondary font-weight-bold">Memuat Panel Data Peserta MBC...</h6>
            </div>
            <iframe id="iframe-mbc" onload="iframeLoaded(this)" src="{{ route('admin.salesplan.index', ['type' => 'mbc', 'embed' => 1]) }}" 
                    style="width: 100%; border: none; min-height: 82vh; opacity: 0; transition: opacity 0.3s ease;"></iframe>
        </div>

        <!-- PANEL DATA PESERTA M1T -->
        <div class="tab-pane fade" id="panel-m1t" role="tabpanel" aria-labelledby="tab-m1t-btn" 
             style="min-height: 80vh; position: relative;">
            <div class="iframe-loader position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white" 
                 style="z-index: 10; transition: opacity 0.3s ease; pointer-events: none;">
                <div class="spinner-border text-info mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
                <h6 class="text-secondary font-weight-bold">Memuat Panel Data Peserta M1T...</h6>
            </div>
            @if(auth()->check() && strtolower(auth()->user()->role) === 'administrator')
                <iframe id="iframe-m1t" onload="iframeLoaded(this)" src="{{ route('peserta-smi.index', ['embed' => 1]) }}" 
                        style="width: 100%; border: none; min-height: 82vh; opacity: 0; transition: opacity 0.3s ease;"></iframe>
            @else
                <iframe id="iframe-m1t" onload="iframeLoaded(this)" src="{{ route('admin.salesplan.index', ['type' => 'smi', 'kelas' => 'Start-Up Muslim Indonesia', 'embed' => 1]) }}" 
                        style="width: 100%; border: none; min-height: 82vh; opacity: 0; transition: opacity 0.3s ease;"></iframe>
            @endif
        </div>
    </div>
</div>

<style>
    /* Premium Styling and Animations for Pills */
    #unifiedParticipantTabs .nav-link {
        color: #4e73df;
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
    }
    #unifiedParticipantTabs .nav-link:hover {
        background-color: #eaecf4;
        color: #224abe;
    }
    #unifiedParticipantTabs .nav-link.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.25);
    }
    .transition-all {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<script>
    // Bulletproof onload handler registered globally to eliminate any potential timing race conditions
    function iframeLoaded(iframe) {
        try {
            // Find the loader associated with this iframe
            const loader = iframe.previousElementSibling;
            if (loader && loader.classList.contains('iframe-loader')) {
                loader.style.opacity = "0";
                setTimeout(() => {
                    loader.remove(); // Physically remove from DOM so it can NEVER intercept click events!
                }, 300);
            }
            iframe.style.opacity = "1";
            
            // Initial height calculation
            resizeIframe(iframe);
        } catch (e) {
            console.error("Iframe loaded handler failed: ", e);
        }
    }

    // Function to dynamically calculate and adjust height
    function resizeIframe(iframe) {
        try {
            if (iframe && iframe.contentWindow && iframe.contentWindow.document) {
                const doc = iframe.contentWindow.document;
                const body = doc.body;
                const html = doc.documentElement;
                if (body && html) {
                    const height = Math.max(
                        body.scrollHeight,
                        body.offsetHeight,
                        html.clientHeight,
                        html.scrollHeight,
                        html.offsetHeight
                    );
                    iframe.style.height = (height + 60) + "px";
                }
            }
        } catch (e) {
            // Safe fallback
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const iframeMbc = document.getElementById("iframe-mbc");
        const iframeM1t = document.getElementById("iframe-m1t");

        // Lazy load M1T iframe only when tab is clicked for maximum performance
        let m1tLoaded = false;
        document.getElementById("tab-m1t-btn").addEventListener("click", function() {
            if (!m1tLoaded) {
                iframeM1t.src = iframeM1t.src;
                m1tLoaded = true;
            }
        });

        // Continuous sync to keep iframe heights perfectly matched (handles dynamic AJAX filtering)
        setInterval(function() {
            resizeIframe(iframeMbc);
            if (m1tLoaded) {
                resizeIframe(iframeM1t);
            }
        }, 400);
    });
</script>
@endsection
