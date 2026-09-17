import {Component} from './component';

export class BackToTop extends Component {

    private container!: HTMLElement;
    private button!: HTMLElement;
    private progress!: HTMLElement;
    private progressPath!: SVGPathElement;
    private targetElem!: HTMLElement;

    private showing: boolean = false;
    private breakPoint: number = 1200;
    private isAnimating: boolean = false;

    setup(): void {
        this.container = this.$el;
        this.button = this.$refs.button;
        this.progress = this.$refs.progress;
        this.progressPath = this.$refs.progressPath as unknown as SVGPathElement;

        this.targetElem = document.getElementById('header') as HTMLElement;

        if (document.body.classList.contains('flexbox')) {
            this.container.style.display = 'none';
            return;
        }

        this.button.addEventListener('click', this.scrollToTop.bind(this));
        window.addEventListener('scroll', this.onPageScroll.bind(this));

        this.setupProgressBar();
    }

    private setupProgressBar(): void {
        this.button.addEventListener('transitionstart', event => {
            if (event.target !== this.button) return;
            this.isAnimating = true;
            this.renderProgressPath();
        });
        const stopAnimating = (event: TransitionEvent) => {
            if (event.target !== this.button) return;
            this.isAnimating = false;
        };
        this.button.addEventListener('transitionend', stopAnimating);
        this.button.addEventListener('transitioncancel', stopAnimating);
        this.renderProgressPath();
    }

    private onPageScroll(): void {
        const scrollTopPos = document.documentElement.scrollTop || document.body.scrollTop || 0;
        if (!this.showing && scrollTopPos > this.breakPoint) {
            this.container.style.display = 'block';
            this.showing = true;
            setTimeout(() => {
                this.container.style.opacity = '0.4';
            }, 1);
        } else if (this.showing && scrollTopPos < this.breakPoint) {
            this.container.style.opacity = '0';
            this.showing = false;
            setTimeout(() => {
                this.container.style.display = 'none';
            }, 500);
        }

        if (this.showing) {
            const maxScrollTop = document.documentElement.scrollHeight - window.innerHeight;
            const scrollTopPercent = (scrollTopPos / maxScrollTop) * 100;
            this.updateProgress(scrollTopPercent);
        }
    }

    private scrollToTop(): void {
        const targetTop = this.targetElem.getBoundingClientRect().top;
        const scrollElem = document.documentElement.scrollTop ? document.documentElement : document.body;
        const duration = 300;
        const start = Date.now();
        const scrollStart = this.targetElem.getBoundingClientRect().top;

        const setPos = () => {
            const percentComplete = (1 - ((Date.now() - start) / duration));
            const target = Math.abs(percentComplete * scrollStart);
            if (percentComplete > 0) {
                scrollElem.scrollTop = target;
                requestAnimationFrame(setPos);
            } else {
                scrollElem.scrollTop = targetTop;
            }
        };

        requestAnimationFrame(setPos);
    }

    private renderProgressPath(): void {
        const bounds = this.button.getBoundingClientRect();
        const progressInset = window.getComputedStyle(this.progress).insetInlineStart;
        const offset = Math.abs(Number(progressInset.replace('px', '') || 0));
        const path = this.roundedRectPath(bounds.width, bounds.height, bounds.height / 2, offset);
        this.progressPath.setAttribute('d', path);
        if (this.isAnimating) {
            window.requestAnimationFrame(this.renderProgressPath.bind(this));
        }
    }

    private updateProgress(percentComplete: number): void {
        if (percentComplete < 5) {
            percentComplete = 5;
        }
        this.progressPath.setAttribute('stroke-dasharray', `${Math.ceil(percentComplete)} 100`);
    }

    private roundedRectPath(w: number, h: number, r: number, offset: number = 1.5): string {
        // Expand dimensions outward by the offset
        const W = w + 2 * offset;
        const H = h + 2 * offset;

        // The corner radius also grows by the offset
        const R = Math.min(r + offset, W / 2, H / 2);

        return [
            `M ${W / 2} 0`,                       // start at top center
            `H ${W - R}`,                          // line to top-right (before arc)
            `A ${R} ${R} 0 0 1 ${W} ${R}`,        // arc: top-right corner
            `V ${H - R}`,                          // line down right edge
            `A ${R} ${R} 0 0 1 ${W - R} ${H}`,    // arc: bottom-right corner
            `H ${R}`,                              // line across bottom edge
            `A ${R} ${R} 0 0 1 ${0} ${H - R}`,    // arc: bottom-left corner
            `V ${R}`,                              // line up left edge
            `A ${R} ${R} 0 0 1 ${R} ${0}`,        // arc: top-left corner
            `Z`                                    // close path back to start
        ].join(' ');
    }
}
