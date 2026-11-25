@if(session('success') || session('error') || session('warning') || session('info'))
    <div class="small-container" style="margin-top: 20px;">
        @if(session('success'))
            <div style="padding: 12px 15px; background: #eef9f1; border: 1px solid #2e8b57; border-radius: 6px; color: #2e8b57; margin-bottom: 10px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="padding: 12px 15px; background: #fff3f1; border: 1px solid #ff523b; border-radius: 6px; color: #ff523b; margin-bottom: 10px;">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div style="padding: 12px 15px; background: #fff9e6; border: 1px solid #e0a800; border-radius: 6px; color: #856404; margin-bottom: 10px;">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('info'))
            <div style="padding: 12px 15px; background: #eef6fc; border: 1px solid #3182ce; border-radius: 6px; color: #2b6cb0; margin-bottom: 10px;">
                {{ session('info') }}
            </div>
        @endif
    </div>
@endif