
(function() {


    class ImageManager extends React.Component {

        constructor(props) {
            super(props);
            this.state = {
                images: [],
                hasMore: false,
                page: 0
            };
        }

        show(callback) {
            $(React.findDOMNode(this)).show();
            this.callback = callback;
        }

        hide() {
            $(React.findDOMNode(this)).hide();
        }

        selectImage(image) {
            if(this.callback) {
                this.callback(image);
            }
            this.hide();
        }

        componentDidMount() {
            var _this = this;
            // Set initial images
            $.getJSON('/images/all', data => {
                this.setState({
                    images: data.images,
                    hasMore: data.hasMore
                });
            });
            // Create dropzone
            this.dropZone = new Dropzone(React.findDOMNode(this.refs.dropZone), {
                url: '/upload/image',
                init: function() {
                    var dz = this;
                    this.on("sending", function(file, xhr, data) {
                        data.append("_token", document.querySelector('meta[name=token]').getAttribute('content'));
                    });
                    this.on("success", function(file, data) {
                        _this.state.images.unshift(data);
                        _this.setState({
                            images: _this.state.images
                        });
                        //$(file.previewElement).fadeOut(400, function() {
                        //    dz.removeFile(file);
                        //})
                    });
                }
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
            var loadMore = this.loadMore.bind(this);
            var selectImage = this.selectImage.bind(this);
            return (
                <div className="overlay">
                    <div id="image-manager">
                        <div className="image-manager-content">
                            <div className="dropzone-container" ref="dropZone">
                                <div className="dz-message">Drop files or click here to upload</div>
                            </div>
                            <ImageList data={this.state.images} loadMore={loadMore} selectImage={selectImage} hasMore={this.state.hasMore}/>
                        </div>
                        <div className="image-manager-sidebar">
                            <h2>Images</h2>
                        </div>
                    </div>
                </div>
            );
        }

    }
    window.ImageManager = new ImageManager();

    class ImageList extends React.Component {

        render() {
            var selectImage = this.props.selectImage;
            var images = this.props.data.map(function(image) {
                return (
                    <Image key={image.id} image={image} selectImage={selectImage} />
                );
            });
            return (
                <div className="image-manager-list clearfix">
                    {images}
                    { this.props.hasMore ? <div className="load-more" onClick={this.props.loadMore}>Load More</div> : null }
                </div>
            );
        }

    }

    class Image extends React.Component {

        setImage() {
            this.props.selectImage(this.props.image);
        }

        render() {
            var setImage = this.setImage.bind(this);
            return (
                <div>
                    <img onDoubleClick={setImage} src={this.props.image.thumbnail}/>
                </div>
            );
        }

    }

    if(document.getElementById('image-manager-container')) {
        window.ImageManager = React.render(
            <ImageManager />,
            document.getElementById('image-manager-container')
        );
    }

})();


