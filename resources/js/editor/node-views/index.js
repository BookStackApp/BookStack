import ImageView from "./ImageView";

const views = {
    image: (node, view, getPos) => new ImageView(node, view, getPos),
};

export default views;