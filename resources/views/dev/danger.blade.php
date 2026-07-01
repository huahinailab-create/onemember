<x-app-layout>
    <x-slot name="pageTitle">Developer Tools — Danger Zone</x-slot>
    <div class="d-flex">
        @include('dev._nav')
        <div class="flex-grow-1 p-4">
            <h4 class="mb-4"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Danger Zone</h4>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-shield-exclamation fs-4"></i>
                <div>Every action on this page is <strong>permanent and irreversible</strong>. A confirmation dialog requiring you to type <code>DELETE</code> is shown before anything runs.</div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-danger h-100">
                        <div class="card-header bg-danger text-white fw-semibold">
                            <i class="bi bi-people me-1"></i>Truncate All Members
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Current count: <strong>{{ $stats['members'] }}</strong></p>
                            <p class="small">Permanently deletes all members, transactions, and redemptions for all merchants.</p>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#dangerModal"
                                data-action="{{ route('dev.danger.truncate-members') }}"
                                data-label="Delete All Members ({{ $stats['members'] }} records)">
                                <i class="bi bi-trash me-1"></i>Delete All Members
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-danger h-100">
                        <div class="card-header bg-danger text-white fw-semibold">
                            <i class="bi bi-person-x me-1"></i>Truncate All Users
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Current count: <strong>{{ $stats['users'] }}</strong></p>
                            <p class="small">Permanently deletes all users <strong>except your own account</strong>.</p>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#dangerModal"
                                data-action="{{ route('dev.danger.truncate-users') }}"
                                data-label="Delete All Users (except me)">
                                <i class="bi bi-trash me-1"></i>Delete All Users
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-danger h-100">
                        <div class="card-header bg-danger text-white fw-semibold">
                            <i class="bi bi-nuclear me-1"></i>Nuke Database
                        </div>
                        <div class="card-body">
                            <p class="small">Wipes all members, merchants, and users (except your account). Transactions and redemptions are also deleted.</p>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#dangerModal"
                                data-action="{{ route('dev.danger.nuke') }}"
                                data-label="Nuke Database (preserve my account)">
                                <i class="bi bi-nuclear me-1"></i>Nuke Database
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

{{-- Shared danger confirmation modal --}}
<div class="modal fade" id="dangerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-1"></i>Confirm Destructive Action</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="dangerModalLabel" class="fw-semibold mb-3"></p>
                <p>This action is <strong>permanent and cannot be undone</strong>. Type <code>DELETE</code> to confirm:</p>
                <form id="dangerForm" method="POST">
                    @csrf @method('DELETE')
                    <input type="text" name="confirm" class="form-control" id="dangerConfirmInput" placeholder="Type DELETE" autocomplete="off" required>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="dangerForm" class="btn btn-danger" id="dangerSubmitBtn" disabled>Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('dangerModal');
    modal.addEventListener('show.bs.modal', function (e) {
        var btn    = e.relatedTarget;
        var action = btn.getAttribute('data-action');
        var label  = btn.getAttribute('data-label');
        document.getElementById('dangerModalLabel').textContent = label;
        document.getElementById('dangerForm').action = action;
        document.getElementById('dangerConfirmInput').value = '';
        document.getElementById('dangerSubmitBtn').disabled = true;
    });
    document.getElementById('dangerConfirmInput').addEventListener('input', function () {
        document.getElementById('dangerSubmitBtn').disabled = this.value !== 'DELETE';
    });
});
</script>
