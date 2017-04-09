
let termString = document.querySelector('[name=searchTerm]').value;
let terms = termString.split(' ');

let data = {
    terms: terms,
        termString : termString,
        search: {
        type: {
            page: true,
            chapter: true,
            book: true
        }
    }
};

let computed = {

};

let methods = {

    appendTerm(term) {
        if (this.termString.slice(-1) !== " ") this.termString += ' ';
        this.termString += term;
    },

    typeParse(searchString) {
        let typeFilter = /{\s?type:\s?(.*?)\s?}/;
        let match = searchString.match(typeFilter);
        let type = this.search.type;
        if (!match) {
            type.page = type.book = type.chapter = true;
            return;
        }
        let splitTypes = match[1].replace(/ /g, '').split('|');
        type.page = (splitTypes.indexOf('page') !== -1);
        type.chapter = (splitTypes.indexOf('chapter') !== -1);
        type.book = (splitTypes.indexOf('book') !== -1);
    },

    typeChange() {
        let typeFilter = /{\s?type:\s?(.*?)\s?}/;
        let type = this.search.type;
        if (type.page === type.chapter && type.page === type.book) {
            this.termString = this.termString.replace(typeFilter, '');
            return;
        }
        let selectedTypes = Object.keys(type).filter(type => {return this.search.type[type];}).join('|');
        let typeTerm = '{type:'+selectedTypes+'}';
        if (this.termString.match(typeFilter)) {
            this.termString = this.termString.replace(typeFilter, typeTerm);
            return;
        }
        this.appendTerm(typeTerm);
    }

};

function created() {
    this.typeParse(this.termString);
}

module.exports = {
    data, computed, methods, created
};