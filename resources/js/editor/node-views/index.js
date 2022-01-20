import ImageView from "./ImageView";
import IframeView from "./IframeView";

const views = {
    image: (node, view, getPos) => new ImageView(node, view, getPos),
    iframe: (node, view, getPos) => new IframeView(node, view, getPos),
};

export default views;