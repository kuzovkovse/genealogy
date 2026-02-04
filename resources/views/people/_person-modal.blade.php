<div class="modal modal-blur fade"
     id="personModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-user"></i>
                    Карточка человека
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">

                {{-- TOP BLOCK --}}
                <div class="d-flex align-items-center gap-4 mb-4">

                    {{-- PHOTO --}}
                    <div class="position-relative">
                        <img id="personModalPhoto"
                             src=""
                             class="avatar avatar-xl rounded-circle">

                        <span id="personModalCandle"
                              class="position-absolute top-0 end-0 fs-3"
                              style="display:none">
                            🕯️
                        </span>
                    </div>

                    {{-- MAIN INFO --}}
                    <div>
                        <h3 id="personModalName" class="mb-1"></h3>

                        <div id="personModalMeta"
                             class="text-muted mb-2"></div>

                        <div id="personModalDates"
                             class="text-secondary"></div>
                    </div>
                </div>

                {{-- BIO --}}
                <div class="mb-4">
                    <h4 class="mb-2">📝 Биография</h4>
                    <div id="personModalBio"
                         class="text-muted">
                        —
                    </div>
                </div>

                {{-- MARRIAGES --}}
                <div class="mb-4">
                    <h4 class="mb-2">💍 Браки</h4>

                    <div id="personModalCouples"
                         class="list-group list-group-flush">
                        {{-- dynamically --}}
                    </div>
                </div>

                {{-- CHILDREN --}}
                <div>
                    <h4 class="mb-2">👶 Дети</h4>

                    <div id="personModalChildren"
                         class="list-group list-group-flush">
                        {{-- dynamically --}}
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <a id="personModalEdit"
                   href="#"
                   class="btn btn-outline-primary">
                    <i class="ti ti-pencil"></i>
                    Редактировать
                </a>

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Закрыть
                </button>
            </div>

        </div>
    </div>
</div>
