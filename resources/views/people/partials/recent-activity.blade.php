@if($recentActivity->isNotEmpty())
    <div class="mt-4">

        <a href="#"
           class="text-muted small"
           onclick="event.preventDefault(); toggleRecentActivity();">
            Последние изменения ▸
        </a>

        <div id="recent-activity"
             style="display:none;"
             class="mt-2">

            @foreach($recentActivity as $item)
                <div class="small text-muted mb-1">
                    {{ $item['icon'] }}
                    {{ $item['text'] }}
                    · {{ $item['time'] }}
                </div>
            @endforeach

        </div>
    </div>

    <script>
        function toggleRecentActivity() {
            const el = document.getElementById('recent-activity');
            if (!el) return;
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }
    </script>
@endif
