{{--
    Feedback Modal — included in layouts/app.blade.php.
    Submits to POST /feedback. Auto-fills page URL and browser via JS.
--}}
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">
                    <i class="bi bi-chat-dots me-2 text-primary"></i>{{ __('feedback.title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('buttons.cancel') }}"></button>
            </div>

            <form method="POST" action="{{ route('feedback.store') }}" id="feedbackForm">
                @csrf

                {{-- Hidden auto-collected fields --}}
                <input type="hidden" name="current_url" id="feedback_url">
                <input type="hidden" name="browser"     id="feedback_browser">

                <div class="modal-body">

                    {{-- Category --}}
                    <div class="mb-3">
                        <label for="feedback_category" class="form-label fw-semibold">
                            {{ __('feedback.category') }} <span class="text-danger">*</span>
                        </label>
                        <select name="category" id="feedback_category" class="form-select" required>
                            <option value="" disabled selected>{{ __('buttons.select') }}</option>
                            <option value="bug">{{ __('feedback.category_bug') }}</option>
                            <option value="feature">{{ __('feedback.category_feature') }}</option>
                            <option value="question">{{ __('feedback.category_question') }}</option>
                            <option value="general">{{ __('feedback.category_general') }}</option>
                        </select>
                    </div>

                    {{-- Subject --}}
                    <div class="mb-3">
                        <label for="feedback_subject" class="form-label fw-semibold">
                            {{ __('feedback.subject') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="subject"
                               id="feedback_subject"
                               class="form-control"
                               maxlength="200"
                               placeholder="{{ __('feedback.subject_placeholder') }}"
                               required>
                    </div>

                    {{-- Message --}}
                    <div class="mb-3">
                        <label for="feedback_message" class="form-label fw-semibold">
                            {{ __('feedback.message') }} <span class="text-danger">*</span>
                        </label>
                        <textarea name="message"
                                  id="feedback_message"
                                  class="form-control"
                                  rows="5"
                                  maxlength="5000"
                                  placeholder="{{ __('feedback.message_placeholder') }}"
                                  required></textarea>
                        <div class="form-text">{{ __('feedback.message_hint') }}</div>
                    </div>

                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i>{{ __('feedback.auto_info') }}
                    </p>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('buttons.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>{{ __('feedback.submit') }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        var modal = document.getElementById('feedbackModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function () {
            var urlField     = document.getElementById('feedback_url');
            var browserField = document.getElementById('feedback_browser');

            if (urlField)     urlField.value     = window.location.href;
            if (browserField) browserField.value = navigator.userAgent;
        });

        // Reset form when modal is closed so next open starts fresh
        modal.addEventListener('hidden.bs.modal', function () {
            var form = document.getElementById('feedbackForm');
            if (form) form.reset();
        });
    }());
</script>
