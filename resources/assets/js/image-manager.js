
class ImageList extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            images: [],
            hasMore: false,
            page: 0
        };
    }

    componentDidMount() {
        $.getJSON('/images/all', data => {
            this.setState({
                images: data.images,
                hasMore: data.hasMore
            });
        });
    }

    loadMore() {
        this.state.page++;
        $.getJSON('/images/all/' + this.state.page, data => {
            this.setState({
                images: this.state.images.concat(data.images),
                hasMore: data.hasMore
            });
        });
    }

    render() {
        var images = this.state.images.map(function(image) {
            return (
                <div key={image.id}>
                    <img src={image.thumbnail}/>
                </div>
            );
        });
        return (
            <div className="image-list">
                {images}
                <div className="load-more" onClick={this.loadMore}>Load More</div>
            </div>
        );
    }

}

class ImageManager extends React.Component {
    render() {
        return (
            <div id="image-manager">
                <ImageList/>
            </div>
        );
    }
}

React.render(
    <ImageManager />,
    document.getElementById('container')
);