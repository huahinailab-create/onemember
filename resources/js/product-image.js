/**
 * OMEGA-001A — premium product image experience (Commerce).
 *
 * Progressive enhancement over the plain <input type="file"> (which keeps
 * working without JS). Adds: drag & drop, keyboard/click browse, live
 * preview with metadata, Cropper.js v2 crop (1:1 / 4:5 / 16:9) + rotate,
 * client-side WebP export (≤1200px longest edge). The server re-optimizes
 * every upload regardless (ProductImageService), so client output is a UX
 * nicety, not a trust boundary.
 */

const MAX_BYTES = 2 * 1024 * 1024;
const MIN_EDGE = 800;
const MAX_EDGE = 1200;
const ACCEPTED = ['image/jpeg', 'image/png', 'image/webp'];

function formatBytes(bytes) {
    if (bytes >= 1024 * 1024) return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    return Math.max(1, Math.round(bytes / 1024)) + ' KB';
}

class ProductImageManager {
    constructor(root) {
        this.root = root;
        this.input = root.querySelector('[data-omega-input]');
        this.dropzone = root.querySelector('[data-omega-dropzone]');
        this.previewWrap = root.querySelector('[data-omega-preview]');
        this.previewImg = root.querySelector('[data-omega-preview-img]');
        this.meta = root.querySelector('[data-omega-meta]');
        this.warning = root.querySelector('[data-omega-warning]');
        this.error = root.querySelector('[data-omega-error]');
        this.editor = root.querySelector('[data-omega-editor]');
        this.editorStage = root.querySelector('[data-omega-stage]');
        this.toolbar = root.querySelector('[data-omega-toolbar]');
        this.removeFlag = root.querySelector('[data-omega-remove-flag]');
        this.strings = JSON.parse(root.dataset.strings || '{}');

        this.cropper = null;
        this.objectUrl = null;

        this.bind();
    }

    bind() {
        // JS is live: the card drives the (now hidden) native input.
        this.input.classList.add('d-none');

        // Click / keyboard browse
        this.dropzone.addEventListener('click', () => this.input.click());
        this.dropzone.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.input.click();
            }
        });

        // Drag & drop
        ['dragenter', 'dragover'].forEach((ev) =>
            this.dropzone.addEventListener(ev, (e) => {
                e.preventDefault();
                this.dropzone.classList.add('is-dragover');
            }),
        );
        ['dragleave', 'drop'].forEach((ev) =>
            this.dropzone.addEventListener(ev, (e) => {
                e.preventDefault();
                this.dropzone.classList.remove('is-dragover');
            }),
        );
        this.dropzone.addEventListener('drop', (e) => {
            const file = e.dataTransfer?.files?.[0];
            if (file) this.handleFile(file);
        });

        this.input.addEventListener('change', () => {
            const file = this.input.files?.[0];
            if (file) this.handleFile(file);
        });

        // Toolbar actions (event delegation)
        this.toolbar?.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-omega-action]');
            if (!btn) return;
            e.preventDefault();
            this.handleAction(btn.dataset.omegaAction, btn);
        });
    }

    setError(message) {
        this.error.textContent = message || '';
        this.error.classList.toggle('d-none', !message);
    }

    setWarning(message) {
        this.warning.textContent = message || '';
        this.warning.classList.toggle('d-none', !message);
    }

    handleFile(file) {
        this.setError('');
        this.setWarning('');

        if (!ACCEPTED.includes(file.type)) {
            this.input.value = '';
            this.setError(this.strings.error_type);
            return;
        }
        if (file.size > MAX_BYTES) {
            this.input.value = '';
            this.setError(this.strings.error_size);
            return;
        }

        this.assignFile(file);
        if (this.removeFlag) this.removeFlag.value = '';

        if (this.objectUrl) URL.revokeObjectURL(this.objectUrl);
        this.objectUrl = URL.createObjectURL(file);

        const probe = new Image();
        probe.onload = () => {
            const { naturalWidth: w, naturalHeight: h } = probe;
            this.showPreview(file, w, h);
            if (w < MIN_EDGE || h < MIN_EDGE) {
                this.setWarning(this.strings.warning_low_res);
            }
        };
        probe.src = this.objectUrl;
    }

    showPreview(file, width, height) {
        this.previewImg.src = this.objectUrl;
        this.meta.textContent = `${file.name} — ${width} × ${height} px — ${formatBytes(file.size)}`;
        this.previewWrap.classList.remove('d-none');
        this.dropzone.classList.add('d-none');
        this.destroyCropper();
    }

    /** Put a File into the real input so the normal form submit carries it. */
    assignFile(file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        this.input.files = dt.files;
    }

    async handleAction(action, btn) {
        switch (action) {
            case 'replace':
                this.input.click();
                break;

            case 'remove':
                this.reset(true);
                break;

            case 'crop':
                await this.openEditor();
                break;

            case 'rotate-left':
                await this.rotate('-90deg');
                break;

            case 'rotate-right':
                await this.rotate('90deg');
                break;

            case 'aspect': {
                await this.openEditor();
                const selection = this.cropper.getCropperSelection();
                selection.aspectRatio = parseFloat(btn.dataset.omegaRatio);
                selection.$center();
                this.toolbar.querySelectorAll('[data-omega-action="aspect"]').forEach((b) => {
                    b.classList.toggle('active', b === btn);
                    b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
                });
                break;
            }

            case 'apply':
                await this.applyCrop();
                break;

            case 'cancel-edit':
                this.closeEditor();
                break;
        }
    }

    async openEditor() {
        if (this.cropper) return;
        const { default: Cropper } = await import('cropperjs');

        const img = document.createElement('img');
        img.src = this.objectUrl;
        img.alt = '';
        this.editorStage.innerHTML = '';
        this.editorStage.appendChild(img);

        this.cropper = new Cropper(img, { container: this.editorStage });
        const selection = this.cropper.getCropperSelection();
        selection.aspectRatio = 1; // 1:1 default
        this.editor.classList.remove('d-none');
        this.root.querySelector('[data-omega-edit-actions]')?.classList.remove('d-none');
    }

    async rotate(angle) {
        await this.openEditor();
        this.cropper.getCropperImage().$rotate(angle);
    }

    async applyCrop() {
        if (!this.cropper) return;
        const selection = this.cropper.getCropperSelection();
        const canvas = await selection.$toCanvas({ maxWidth: MAX_EDGE, maxHeight: MAX_EDGE });

        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/webp', 0.9));
        if (!blob) return;

        const file = new File([blob], 'product-image.webp', { type: 'image/webp' });
        this.assignFile(file);

        if (this.objectUrl) URL.revokeObjectURL(this.objectUrl);
        this.objectUrl = URL.createObjectURL(file);
        this.closeEditor();
        this.showPreview(file, canvas.width, canvas.height);
        this.setWarning('');
    }

    closeEditor() {
        this.destroyCropper();
        this.editor.classList.add('d-none');
        this.root.querySelector('[data-omega-edit-actions]')?.classList.add('d-none');
    }

    destroyCropper() {
        if (this.cropper) {
            this.editorStage.innerHTML = '';
            this.cropper = null;
        }
    }

    reset(markRemoved) {
        this.input.value = '';
        if (this.objectUrl) URL.revokeObjectURL(this.objectUrl);
        this.objectUrl = null;
        this.closeEditor();
        this.previewWrap.classList.add('d-none');
        this.dropzone.classList.remove('d-none');
        this.setError('');
        this.setWarning('');
        if (markRemoved && this.removeFlag) this.removeFlag.value = '1';
        // Hide the current (server-side) image card too, if present
        this.root.querySelector('[data-omega-current]')?.classList.add('d-none');
    }
}

document.querySelectorAll('[data-omega-image]').forEach((root) => new ProductImageManager(root));
