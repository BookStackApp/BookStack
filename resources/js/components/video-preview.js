import {Component} from './component';

export class VideoPreview extends Component {

    setup() {
        this.container = null;
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Handle clicks via event delegation
        document.addEventListener('click', (e) => {

            if (e.target.classList.contains('video-preview-btn') || e.target.closest('.video-preview-btn')) {
                e.preventDefault();
                const button = e.target.classList.contains('video-preview-btn') ? e.target : e.target.closest('.video-preview-btn');
                const url = button.getAttribute('data-video-url');
                const name = button.getAttribute('data-video-name');
                this.showPreview(url, name);
            }
            

            // Handle close button clicks
            if (e.target.classList.contains('video-preview-close') || e.target.closest('.video-preview-close')) {
                e.preventDefault();
                this.closePreview();
            }

            // Handle backdrop clicks
            if (e.target.classList.contains('video-preview-backdrop')) {
                e.preventDefault();
                this.closePreview();
            }
        });
    }

    showPreview(videoUrl, videoName) {
        // Remove any existing preview
        this.closePreview();

        // Create modal container
        this.container = document.createElement('div');
        this.container.className = 'video-preview-modal';
        this.container.innerHTML = `
            <div class="video-preview-backdrop"></div>
            <div class="video-preview-content">
                <div class="video-preview-header">
                    <h3>${this.escapeHtml(videoName)}</h3>
                    <button type="button" class="video-preview-close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="video-preview-player">
                    <video controls autoplay preload="metadata">
                        <source src="${this.escapeHtml(videoUrl)}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        `;

        document.body.appendChild(this.container);

        // Add ESC key listener
        this.escListener = (e) => {
            if (e.key === 'Escape') {
                this.closePreview();
            }
        };
        document.addEventListener('keydown', this.escListener);

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    closePreview() {
        if (this.container) {
            this.container.remove();
            this.container = null;
        }

        if (this.escListener) {
            document.removeEventListener('keydown', this.escListener);
            this.escListener = null;
        }

        // Restore body scroll
        document.body.style.overflow = '';
    }


    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}