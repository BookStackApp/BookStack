
class Sidebar {

    constructor(elem) {
        this.elem = elem;
        this.toggleElem = elem.querySelector('.sidebar-toggle');
        this.toggleElem.addEventListener('click', this.toggle.bind(this));
    }

    toggle(show = true) {
        this.elem.classList.toggle('open');
    }

}

export default Sidebar;