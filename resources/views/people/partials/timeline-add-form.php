<div id="add-event-form" class="card mb-3" style="display:none;">
    <div class="card-body">

        <h5 class="mb-3">➕ Добавить событие</h5>

        <form method="POST" action="{{ route('people.events.store', $person) }}">
            @csrf

            <div class="row g-2 mb-2">
                <div class="col-md-3">
                    <input type="date"
                           name="event_date"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-2">
                    <input type="text"
                           name="icon"
                           class="form-control"
                           placeholder="🎉">
                </div>

                <div class="
