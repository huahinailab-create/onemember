import Cropper from 'cropperjs';

/**
 * OMEGA-001A frontend — reusable premium image upload.
 *
 * Despite the filename (kept for continuity with the Product Image sprint
 * that first needed it), this is generic: it enhances every
 * [data-media-upload] root in the page, so any future module (merchant
 * logo, staff avatar, supplier logo, gallery item, ...) that renders
 * <x-ui.media-upload> gets drag/drop + crop + rotate for free.
 *
 * Progressive enhancement contract: until this runs, each root's plain
 * <input type="file"> (and, in edit mode, its "remove" checkbox) are the
 * only controls and are fully functional on their own. This script's only
 * job is to hide that native fallback and drive the richer UI in its
 * place — if anything below throws, the native fallback is simply never
 * hidden and the form keeps working.
 */

function humanFileSize(bytes) {
    if (bytes < 1024) return `${bytes} B`;
    const units = ['KB', 'MB', 'GB'];
    let value = bytes / 1024;
    let unitIndex = 0;
    while (value >= 1024 && unitIndex < units.length - 1) {
        value /= 1024;
        unitIndex += 1;
    }
    return `${value.toFixed(1)} ${units[unitIndex]}`;
}

function initMediaUpload(root) {
    const options = JSON.parse(root.dataset.mediaUploadOptions || '{}');
    const maxBytes = (options.maxMb || 2) * 1024 * 1024;
    const accept = options.accept || ['image/jpeg', 'image/png', 'image/webp'];
    const presets = options.presets || {};
    const defaultAspect = options.aspect ?? 1;

    const fallback = root.querySelector('.media-upload-native-fallback');
    const enhanced = root.querySelector('.media-upload-enhanced');
    const nativeInput = root.querySelector('.media-upload-native-input');
    const removeCheckbox = root.querySelector('.media-upload-remove-checkbox');
    const dropzone = root.querySelector('.media-upload-dropzone');
    const workspace = root.querySelector('.media-upload-workspace');
    const cropStage = root.querySelector('.media-upload-crop-stage');
    const cropImage = root.querySelector('.media-upload-crop-image');
    const filenameEl = root.querySelector('.media-upload-filename');
    const dimensionsEl = root.querySelector('.media-upload-dimensions');
    const filesizeEl = root.querySelector('.media-upload-filesize');
    const errorEl = root.querySelector('.media-upload-error');
    const replaceBtn = root.querySelector('.media-upload-replace');
    const removeBtn = root.querySelector('.media-upload-remove');
    const rotateLeftBtn = root.querySelector('.media-upload-rotate-left');
    const rotateRightBtn = root.querySelector('.media-upload-rotate-right');
    const presetButtons = root.querySelectorAll('.media-upload-aspect-btn');

    if (!fallback || !enhanced || !nativeInput) return;

    let cropper = null;
    let objectUrl = null;

    function showError(message) {
        if (!errorEl) return;
        errorEl.textContent = message;
        errorEl.hidden = !message;
    }

    function destroyCropperInstance() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    function revokeCurrentObjectUrl() {
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }
    }

    function showDropzoneState() {
        destroyCropperInstance();
        revokeCurrentObjectUrl();
        dropzone.classList.remove('d-none');
        workspace.classList.add('d-none');
        showError('');
    }

    function showWorkspaceState(imageSrc, label, sizeBytes) {
        dropzone.classList.add('d-none');
        workspace.classList.remove('d-none');

        filenameEl.textContent = label || '';
        filesizeEl.textContent = sizeBytes ? humanFileSize(sizeBytes) : '';
        dimensionsEl.textContent = '';

        destroyCropperInstance();
        cropImage.src = imageSrc;
        cropImage.onload = function () {
            dimensionsEl.textContent = `${cropImage.naturalWidth} × ${cropImage.naturalHeight}px`;
            cropper = new Cropper(cropImage, {
                aspectRatio: defaultAspect,
                viewMode: 1,
                autoCropArea: 1,
                background: false,
            });
        };
    }

    function acceptFile(file) {
        if (!accept.includes(file.type)) {
            showError(window.mediaUploadStrings?.errorBadType || 'Please choose a JPG, PNG, or WebP image.');
            return;
        }
        if (file.size > maxBytes) {
            showError((window.mediaUploadStrings?.errorTooLarge || 'That image is too large.'));
            return;
        }

        showError('');
        if (removeCheckbox) removeCheckbox.checked = false;

        revokeCurrentObjectUrl();
        objectUrl = URL.createObjectURL(file);
        showWorkspaceState(objectUrl, file.name, file.size);
    }

    // ── File selection: native input change (dropzone click delegates here) ──
    nativeInput.addEventListener('change', function () {
        const file = nativeInput.files && nativeInput.files[0];
        if (file) acceptFile(file);
    });

    dropzone.addEventListener('click', () => nativeInput.click());
    dropzone.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            nativeInput.click();
        }
    });

    ['dragover', 'dragenter'].forEach((evt) => {
        dropzone.addEventListener(evt, function (e) {
            e.preventDefault();
            dropzone.classList.add('is-dragover');
        });
    });
    ['dragleave', 'dragend'].forEach((evt) => {
        dropzone.addEventListener(evt, () => dropzone.classList.remove('is-dragover'));
    });
    dropzone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropzone.classList.remove('is-dragover');
        const file = e.dataTransfer.files && e.dataTransfer.files[0];
        if (!file) return;

        // Reflect the dropped file onto the real <input> so it submits with the form.
        const transfer = new DataTransfer();
        transfer.items.add(file);
        nativeInput.files = transfer.files;
        acceptFile(file);
    });

    // ── Rotate ──
    rotateLeftBtn?.addEventListener('click', () => cropper?.rotate(-90));
    rotateRightBtn?.addEventListener('click', () => cropper?.rotate(90));

    // ── Aspect presets ──
    presetButtons.forEach((btn) => {
        btn.addEventListener('click', function () {
            presetButtons.forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');
            cropper?.setAspectRatio(parseFloat(btn.dataset.ratio));
        });
    });

    // ── Replace / remove ──
    replaceBtn?.addEventListener('click', () => nativeInput.click());

    removeBtn?.addEventListener('click', function () {
        nativeInput.value = '';
        if (removeCheckbox) removeCheckbox.checked = true;
        showDropzoneState();
    });

    // ── Crop → real upload file ──
    // On submit, if a crop session is active, bake the crop into a fresh
    // File and swap it into the native input before the form actually
    // posts. Nothing server-side changes: it just receives cropped bytes
    // instead of the original.
    const form = root.closest('form');
    let cropApplied = false;
    if (form) {
        form.addEventListener('submit', function (e) {
            if (cropApplied || !cropper) return;

            e.preventDefault();
            cropper.getCroppedCanvas().toBlob(function (blob) {
                if (blob) {
                    const originalName = nativeInput.files?.[0]?.name || 'image.jpg';
                    const cropped = new File([blob], originalName, { type: blob.type });
                    const transfer = new DataTransfer();
                    transfer.items.add(cropped);
                    nativeInput.files = transfer.files;
                }
                cropApplied = true;
                form.requestSubmit();
            }, 'image/jpeg', 0.92);
        });
    }

    // ── Initial state: existing stored image, or empty dropzone ──
    const currentUrl = root.dataset.mediaUploadCurrent;
    const currentLabel = root.dataset.mediaUploadCurrentLabel;
    if (currentUrl) {
        dropzone.classList.add('d-none');
        workspace.classList.remove('d-none');
        filenameEl.textContent = currentLabel || '';
        filesizeEl.textContent = '';
        dimensionsEl.textContent = '';
        cropImage.src = currentUrl;
        cropImage.onload = function () {
            dimensionsEl.textContent = `${cropImage.naturalWidth} × ${cropImage.naturalHeight}px`;
        };
        // No Cropper on the already-stored image — cropping only applies to
        // a newly selected file (re-processing a stored image is future work).
    } else {
        showDropzoneState();
    }

    // ── Reveal: enhancement succeeded, hand off from the native fallback ──
    fallback.hidden = true;
    enhanced.hidden = false;
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-media-upload]').forEach(function (root) {
        try {
            initMediaUpload(root);
        } catch (err) {
            // Enhancement failed — leave the native fallback visible/functional.
            console.error('media-upload: enhancement failed, falling back to native input', err);
        }
    });
});
