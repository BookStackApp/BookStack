import * as Dates from "../services/dates";

let data = {
    terms: '',
    termString : '',
    search: {
        type: {
            page: true,
            chapter: true,
            book: true,
            bookshelf: true,
        },
        exactTerms: [],
        tagTerms: [],
        option: {},
        dates: {
            updated_after: false,
            updated_before: false,
            created_after: false,
            created_before: false,
        }
    }
};

let computed = {

};

let methods = {

    appendTerm(term) {
        this.termString += ' ' + term;
        this.termString = this.termString.replace(/\s{2,}/g, ' ');
        this.termString = this.termString.replace(/^\s+/, '');
        this.termString = this.termString.replace(/\s+$/, '');
    },

    exactParse(searchString) {
        this.search.exactTerms = [];
        let exactFilter = /"(.+?)"/g;
        let matches;
        while ((matches = exactFilter.exec(searchString)) !== null) {
            this.search.exactTerms.push(matches[1]);
        }
    },

    exactChange() {
        let exactFilter = /"(.+?)"/g;
        this.termString = this.termString.replace(exactFilter, '');
        let matchesTerm = this.search.exactTerms.filter(term =>  term.trim() !== '').map(term => `"${term}"`).join(' ');
        this.appendTerm(matchesTerm);
    },

    addExact() {
        this.search.exactTerms.push('');
        setTimeout(() => {
            let exactInputs = document.querySelectorAll('.exact-input');
            exactInputs[exactInputs.length - 1].focus();
        }, 100);
    },

    removeExact(index) {
        this.search.exactTerms.splice(index, 1);
        this.exactChange();
    },

    tagParse(searchString) {
        this.search.tagTerms = [];
        let tagFilter = /\[(.+?)\]/g;
        let matches;
        while ((matches = tagFilter.exec(searchString)) !== null) {
            this.search.tagTerms.push(matches[1]);
        }
    },

    tagChange() {
        let tagFilter = /\[(.+?)\]/g;
        this.termString = this.termString.replace(tagFilter, '');
        let matchesTerm = this.search.tagTerms.filter(term => {
            return term.trim() !== '';
        }).map(term => {
            return `[${term}]`
        }).join(' ');
        this.appendTerm(matchesTerm);
    },

    addTag() {
        this.search.tagTerms.push('');
        setTimeout(() => {
            let tagInputs = document.querySelectorAll('.tag-input');
            tagInputs[tagInputs.length - 1].focus();
        }, 100);
    },

    removeTag(index) {
        this.search.tagTerms.splice(index, 1);
        this.tagChange();
    },

    typeParse(searchString) {
        let typeFilter = /{\s?type:\s?(.*?)\s?}/;
        let match = searchString.match(typeFilter);
        let type = this.search.type;
        if (!match) {
            type.page = type.book = type.chapter = type.bookshelf = true;
            return;
        }
        let splitTypes = match[1].replace(/ /g, '').split('|');
        type.page = (splitTypes.indexOf('page') !== -1);
        type.chapter = (splitTypes.indexOf('chapter') !== -1);
        type.book = (splitTypes.indexOf('book') !== -1);
        type.bookshelf = (splitTypes.indexOf('bookshelf') !== -1);
    },

    typeChange() {
        let typeFilter = /{\s?type:\s?(.*?)\s?}/;
        let type = this.search.type;
        if (type.page === type.chapter === type.book === type.bookshelf) {
            this.termString = this.termString.replace(typeFilter, '');
            return;
        }
        let selectedTypes = Object.keys(type).filter(type => this.search.type[type]).join('|');
        let typeTerm = '{type:'+selectedTypes+'}';
        if (this.termString.match(typeFilter)) {
            this.termString = this.termString.replace(typeFilter, typeTerm);
            return;
        }
        this.appendTerm(typeTerm);
    },

    optionParse(searchString) {
        let optionFilter = /{([a-z_\-:]+?)}/gi;
        let matches;
        while ((matches = optionFilter.exec(searchString)) !== null) {
            this.search.option[matches[1].toLowerCase()] = true;
        }
    },

    optionChange(optionName) {
        let isChecked = this.search.option[optionName];
        if (isChecked) {
            this.appendTerm(`{${optionName}}`);
        } else {
            this.termString = this.termString.replace(`{${optionName}}`, '');
        }
    },

    updateSearch(e) {
        e.preventDefault();
        window.location = window.baseUrl('/search?term=' + encodeURIComponent(this.termString));
    },

    enableDate(optionName) {
        this.search.dates[optionName.toLowerCase()] = Dates.getCurrentDay();
        this.dateChange(optionName);
    },

    dateParse(searchString) {
        let dateFilter = /{([a-z_\-]+?):([a-z_\-0-9]+?)}/gi;
        let dateTags = Object.keys(this.search.dates);
        let matches;
        while ((matches = dateFilter.exec(searchString)) !== null) {
            if (dateTags.indexOf(matches[1]) === -1) continue;
            this.search.dates[matches[1].toLowerCase()] = matches[2];
        }
    },

    dateChange(optionName) {
        let dateFilter = new RegExp('{\\s?'+optionName+'\\s?:([a-z_\\-0-9]+?)}', 'gi');
        this.termString = this.termString.replace(dateFilter, '');
        if (!this.search.dates[optionName]) return;
        this.appendTerm(`{${optionName}:${this.search.dates[optionName]}}`);
    },

    dateRemove(optionName) {
        this.search.dates[optionName] = false;
        this.dateChange(optionName);
    }

};

function created() {
    this.termString = document.querySelector('[name=searchTerm]').value;
    this.typeParse(this.termString);
    this.exactParse(this.termString);
    this.tagParse(this.termString);
    this.optionParse(this.termString);
    this.dateParse(this.termString);
}

export default {
    data, computed, methods, created
};
