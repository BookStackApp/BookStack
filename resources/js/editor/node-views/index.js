import ImageView from "./ImageView";
import IframeView from "./IframeView";
import TableView from "./TableView";

const views = {
    image: (node, view, getPos) => new ImageView(node, view, getPos),
    iframe: (node, view, getPos) => new IframeView(node, view, getPos),
    table: (node, view, getPos) => new TableView(node, view, getPos),
};

export default views;