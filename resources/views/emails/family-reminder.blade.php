<p style="font-size:16px">
    Сегодня {{ $reminder->humanDate() }} —
    {{ $reminder->title }}
</p>

<p>
    {{ $reminder->body }}
</p>

@if($reminder->link())
    <p>
        <a href="{{ $reminder->link() }}">
            Посмотреть в семейном архиве →
        </a>
    </p>
@endif

<hr>

<p style="font-size:13px;color:#777">
    Ваша семья помнит.
</p>
