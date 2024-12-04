import {ElementNode, type SerializedElementNode} from "./LexicalElementNode";
import {CommonBlockAlignment, CommonBlockInterface} from "./common";
import {Spread} from "lexical";


export type SerializedCommonBlockNode = Spread<{
    id: string;
    alignment: CommonBlockAlignment;
    inset: number;
}, SerializedElementNode>

export class CommonBlockNode extends ElementNode implements CommonBlockInterface {
    __id: string = '';
    __alignment: CommonBlockAlignment = '';
    __inset: number = 0;

    setId(id: string) {
        const self = this.getWritable();
        self.__id = id;
    }

    getId(): string {
        const self = this.getLatest();
        return self.__id;
    }

    setAlignment(alignment: CommonBlockAlignment) {
        const self = this.getWritable();
        self.__alignment = alignment;
    }

    getAlignment(): CommonBlockAlignment {
        const self = this.getLatest();
        return self.__alignment;
    }

    setInset(size: number) {
        const self = this.getWritable();
        self.__inset = size;
    }

    getInset(): number {
        const self = this.getLatest();
        return self.__inset;
    }

    exportJSON(): SerializedCommonBlockNode {
        return {
            ...super.exportJSON(),
            id: this.__id,
            alignment: this.__alignment,
            inset: this.__inset,
        };
    }
}

export function copyCommonBlockProperties(from: CommonBlockNode, to: CommonBlockNode): void {
    // to.__id = from.__id;
    to.__alignment = from.__alignment;
    to.__inset = from.__inset;
}