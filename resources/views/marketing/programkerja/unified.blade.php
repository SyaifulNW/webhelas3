@extends('layouts.masteradmin')

@section('content')
<div class="container-fluid px-4 py-2">
    <!-- Gorgeous Premium Navigation Tabs -->
    <ul class="nav nav-pills mb-4 p-2 shadow-sm rounded-lg border bg-white" id="unifiedProgramTabs" role="tablist"
        style="border-radius: 12px; gap: 8px;">
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link active w-100 py-3 font-weight-bold text-center border-0 transition-all d-flex align-items-center justify-content-center" 
                    id="tab-programkerja-btn" data-toggle="pill" data-target="#panel-programkerja" type="button" role="tab" aria-controls="panel-programkerja" aria-selected="true"
                    style="border-radius: 8px; font-size: 0.95rem; letter-spacing: 0.5px; transition: all 0.3s ease;">
                <i class="fas fa-tasks mr-2" style="font-size: 1.1rem;"></i> Panel Program Kerja
            </button>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link w-100 py-3 font-weight-bold text-center border-0 transition-all d-flex align-items-center justify-content-center" 
                    id="tab-gantt-btn" data-toggle="pill" data-target="#panel-gantt" type="button" role="tab" aria-controls="panel-gantt" aria-selected="false"
                    style="border-radius: 8px; font-size: 0.95rem; letter-spacing: 0.5px; transition: all 0.3s ease;">
                <i class="fas fa-project-diagram mr-2" style="font-size: 1.1rem;"></i> Panel Gantt Chart
            </button>
        </li>
    </ul>

    <!-- Tab Panels with Beautiful Loader and Seamless Responsive Iframes -->
    <div class="tab-content shadow-lg rounded-lg border bg-white" id="unifiedProgramTabContent"
         style="border-radius: 16px; overflow: hidden; min-height: 80vh; position: relative;">
        
        <!-- PANEL PROGRAM KERJA -->
        <div class="tab-pane fade show active" id="panel-programkerja" role="tabpanel" aria-labelledby="tab-programkerja-btn" 
             style="min-height: 80vh; position: relative;">
            <div class="iframe-loader position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white" 
                 style="z-index: 10; transition: opacity 0.3s ease; pointer-events: none;">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
                <h6 class="text-secondary font-weight-bold">Memuat Panel Program Kerja...</h6>
            </div>
            <iframe id="iframe-programkerja" onload="iframeLoaded(this)" src="{{ route('programkerja.index', ['embed' => 1]) }}" 
                    style="width: 100%; border: none; min-height: 82vh; opacity: 0; transition: opacity 0.3s ease;"></iframe>
        </div>

        <!-- PANEL GANTT CHART -->
        <div class="tab-pane fade" id="panel-gantt" role="tabpanel" aria-labelledby="tab-gantt-btn" 
             style="min-height: 80vh; position: relative;">
            <div class="iframe-loader position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white" 
                 style="z-index: 10; transition: opacity 0.3s ease; pointer-events: none;">
                <div class="spinner-border text-info mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="sr-only">Loading...</span>
                </div>
                <h6 class="text-secondary font-weight-bold">Memuat Panel Gantt Chart...</h6>
            </div>
            <iframe id="iframe-gantt" onload="iframeLoaded(this)" src="{{ route('gantt.index', ['embed' => 1]) }}" 
                    style="width: 100%; border: none; min-height: 82vh; opacity: 0; transition: opacity 0.3s ease;"></iframe>
        </div>
    </div>
</div>

<style>
    /* Premium Styling and Animations for Pills */
    #unifiedProgramTabs .nav-link {
        color: #4e73df;
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
    }
    #unifiedProgramTabs .nav-link:hover {
        background-color: #eaecf4;
        color: #224abe;
    }
    #unifiedProgramTabs .nav-link.active {
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
        const iframeProgram = document.getElementById("iframe-programkerja");
        const iframeGantt = document.getElementById("iframe-gantt");

        // Lazy load Gantt iframe only when tab is clicked for maximum performance
        let ganttLoaded = false;
        document.getElementById("tab-gantt-btn").addEventListener("click", function() {
            if (!ganttLoaded) {
                iframeGantt.src = iframeGantt.src;
                ganttLoaded = true;
            }
        });

        // Continuous sync to keep iframe heights perfectly matched (handles dynamic AJAX filtering)
        setInterval(function() {
            resizeIframe(iframeProgram);
            if (ganttLoaded) {
                resizeIframe(iframeGantt);
            }
        }, 400);
    });
</script>
@endsection
