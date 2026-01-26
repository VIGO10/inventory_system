<!-- resources/views/layouts/footer.blade.php -->
<footer style="
    position: relative;
    background: linear-gradient(to right, #1e1b4b, #312e81, #4338ca);
    border-top: 1px solid rgba(255,255,255,0.08);
    color: white;
    margin-top: auto;
">

    <!-- Subtle overlay (reduced opacity & size) -->
    <div style="
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 80%, rgba(139,92,246,0.05) 0%, transparent 80%);
        pointer-events: none;
    "></div>

    <div style="
        max-width: 700px;
        margin: 0 auto;
        padding: 1.4rem 1rem 1.2rem;           /* ← smaller padding */
        position: relative;
        z-index: 5;
    ">

        <div style="
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.6rem;                           /* ← smaller gap */
        ">

            <!-- Compact brand line - smaller size -->
            <div style="
                display: flex;
                align-items: center;
                gap: 0.8rem;
                opacity: 0.9;
            ">
                <div style="
                    width: 36px;                       /* ← smaller icon container */
                    height: 36px;
                    background: linear-gradient(135deg, #6366f1, #a855f7);
                    border-radius: 0.75rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 6px 15px -3px rgba(99,102,241,0.35);
                    border: 1px solid rgba(255,255,255,0.15);
                ">
                    <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>

                <p style="
                    font-size: 1.1rem;                 /* ← smaller text */
                    font-weight: 700;
                    letter-spacing: -0.015em;
                    margin: 0;
                    background: linear-gradient(to right, #e0e7ff, #c7d2fe);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                ">
                    Inventory System
                </p>
            </div>

            <!-- Very compact copyright -->
            <div style="
                font-size: 0.75rem;                    /* ← even smaller */
                color: rgba(199,210,254,0.65);
            ">
                © {{ date('Y') }} All rights reserved
            </div>

        </div>
    </div>
</footer>