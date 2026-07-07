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
    let objectUrl = null;   // the blob URL currently assigned to cropImage.src, if any
    let loadToken = 0;      // guards against a stale load/error callback touching newer state

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
        // Cropper hides the source <img> while it's active; make sure it's
        // visible again for whatever state comes next (plain preview or a
        // fresh Cropper instance) instead of staying blank.
        cropImage.style.display = '';
        cropImage.classList.remove('cropper-hidden');
    }

    function revokeUrl(url) {
        if (url) URL.revokeObjectURL(url);
    }

    function showDropzoneState() {
        destroyCropperInstance();
        revokeUrl(objectUrl);
        objectUrl = null;
        loadToken += 1;
        dropzone.classList.remove('d-none');
        workspace.classList.add('d-none');
        showError('');
    }

    // Cropper needs the stage element to already have real, laid-out
    // dimensions when it measures the container — constructing it against a
    // still-collapsed (e.g. just-unhidden) parent produces a blank/zero-size
    // crop view. Wait a couple of animation frames for layout to settle
    // before mounting, and never let a failed mount leave the image hidden.
    function mountCropper(retriesLeft = 5) {
        if (cropStage.offsetWidth === 0 || cropStage.offsetHeight === 0) {
            if (retriesLeft > 0) requestAnimationFrame(() => mountCropper(retriesLeft - 1));
            return;
        }

        try {
            cropper = new Cropper(cropImage, {
                aspectRatio: defaultAspect,
                viewMode: 1,
                autoCropArea: 1,
                background: false,
            });
        } catch (err) {
            console.error('media-upload: Cropper failed to initialize — showing plain preview instead', err);
            cropper = null;
            cropImage.style.display = '';
            cropImage.classList.remove('cropper-hidden');
        }
    }

    // `crop`: whether to mount Cropper once the image is ready (skipped for
    // an already-stored image — cropping only applies to a newly selected
    // file). `previousUrl`: a blob URL to release, but only once the new
    // image has actually finished loading — revoking it any earlier can
    // race with the browser still reading the old <img> src mid-swap and
    // leave the preview blank.
    function showWorkspaceState(imageSrc, label, sizeBytes, { crop = false, previousUrl = null } = {}) {
        dropzone.classList.add('d-none');
        workspace.classList.remove('d-none');

        filenameEl.textContent = label || '';
        filesizeEl.textContent = sizeBytes ? humanFileSize(sizeBytes) : '';
        dimensionsEl.textContent = '';
        showError('');

        destroyCropperInstance();

        const token = ++loadToken;
        const onReady = function () {
            if (token !== loadToken) return; // superseded by a later selection
            dimensionsEl.textContent = `${cropImage.naturalWidth} × ${cropImage.naturalHeight}px`;
            revokeUrl(previousUrl);
            if (crop) mountCropper();
        };

        cropImage.onload = onReady;
        cropImage.onerror = function () {
            if (token !== loadToken) return;
            console.error('media-upload: image failed to load — reverting to dropzone');
            showError(window.mediaUploadStrings?.errorBadType || 'Please choose a JPG, PNG, or WebP image.');
            showDropzoneState();
        };
        cropImage.src = imageSrc;

        // Some browsers resolve a same-frame/cached image without firing a
        // fresh 'load' event — handle that synchronously instead of relying
        // solely on the async event.
        if (cropImage.complete && cropImage.naturalWidth > 0) onReady();
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

        if (removeCheckbox) removeCheckbox.checked = false;

        const previousUrl = objectUrl;
        objectUrl = URL.createObjectURL(file);
        showWorkspaceState(objectUrl, file.name, file.size, { crop: true, previousUrl });
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
    // No Cropper on the already-stored image — cropping only applies to a
    // newly selected file (re-processing a stored image is future work).
    const currentUrl = root.dataset.mediaUploadCurrent;
    const currentLabel = root.dataset.mediaUploadCurrentLabel;
    if (currentUrl) {
        showWorkspaceState(currentUrl, currentLabel, null, { crop: false });
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
