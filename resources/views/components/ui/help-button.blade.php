{{-- Design System / PLATFORM-002 P11: contextual "?" help button.
     Usage: <x-ui.help-button topic="members.index" />
     Links to the Knowledge Center article registered for the topic
     (KnowledgeArticle.context_key); falls back to the Help Center index.
     `label` doubles as the tooltip hook (title + aria-label). --}}
@props(['topic', 'label' => null])

<a href="{{ route('help.context', $topic) }}"
   {{ $attributes->merge(['class' => 'btn btn-sm btn-outline-secondary rounded-circle help-btn']) }}
   title="{{ $label ?? __('help.help_button') }}"
   aria-label="{{ $label ?? __('help.help_button') }}"
   data-help-topic="{{ $topic }}">
    <i class="bi bi-question-lg" aria-hidden="true"></i>
</a>
