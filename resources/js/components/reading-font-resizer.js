import {Component} from "./component";

export class ReadingFontResizer extends Component {

    setup() {
        this.container = this.$el;
        this.name = this.$opts.name;
        this.shouldIncreaseFont = (this.$opts.font === "increase");
        this.shouldDecreaseFont = (this.$opts.font === "decrease");
        this.setupListeners();
    }

    setupListeners() {
        this.container.addEventListener('click', async(event) => {
            if (event.target === this.container) {
                const url = this.shouldIncreaseFont ? `/preferences/change-reading-font-size/increaseFont` : `/preferences/change-reading-font-size/decreaseFont`
                const apiResponse = await window.$http.patch(url);
                console.log(apiResponse?.data);
                if(apiResponse?.data?.fontSize) {
                    const cssRoot = document.querySelector(':root');
                    cssRoot.style.setProperty('--reading-font-size', `${apiResponse?.data?.fontSize}em`);
                }
            }
        });
    }
}