"use strict";

module.exports = function (ngApp, events) {

    ngApp.controller('ImageManagerController', ['$scope', '$attrs', '$http', '$timeout', 'imageManagerService',
        function ($scope, $attrs, $http, $timeout, imageManagerService) {
            $scope.images = [];
            $scope.imageType = $attrs.imageType;
            $scope.selectedImage = false;
            $scope.dependantPages = false;
            $scope.showing = false;
            $scope.hasMore = false;
            $scope.imageUpdateSuccess = false;
            $scope.imageDeleteSuccess = false;
            var page = 0;
            var previousClickTime = 0;
            var dataLoaded = false;
            var callback = false;

            /**
             * Simple returns the appropriate upload url depending on the image type set.
             * @returns {string}
             */
            $scope.getUploadUrl = function () {
                return '/images/' + $scope.imageType + '/upload';
            };

            /**
             * Runs on image upload, Adds an image to local list of images
             * and shows a success message to the user.
             * @param file
             * @param data
             */
            $scope.uploadSuccess = function (file, data) {
                $scope.$apply(() => {
                    $scope.images.unshift(data);
                });
                events.emit('success', 'Image uploaded');
            };

            /**
             * Runs the callback and hides the image manager.
             * @param returnData
             */
            function callbackAndHide(returnData) {
                if (callback) callback(returnData);
                $scope.showing = false;
            }

            /**
             * Image select action. Checks if a double-click was fired.
             * @param image
             */
            $scope.imageSelect = function (image) {
                var dblClickTime = 300;
                var currentTime = Date.now();
                var timeDiff = currentTime - previousClickTime;

                if (timeDiff < dblClickTime) {
                    // If double click
                    callbackAndHide(image);
                } else {
                    // If single
                    $scope.selectedImage = image;
                    $scope.dependantPages = false;
                }
                previousClickTime = currentTime;
            };

            /**
             * Action that runs when the 'Select image' button is clicked.
             * Runs the callback and hides the image manager.
             */
            $scope.selectButtonClick = function () {
                callbackAndHide($scope.selectedImage);
            };

            /**
             * Show the image manager.
             * Takes a callback to execute later on.
             * @param doneCallback
             */
            function show(doneCallback) {
                callback = doneCallback;
                $scope.showing = true;
                // Get initial images if they have not yet been loaded in.
                if (!dataLoaded) {
                    fetchData();
                    dataLoaded = true;
                }
            }

            // Connects up the image manger so it can be used externally
            // such as from TinyMCE.
            imageManagerService.show = show;
            imageManagerService.showExternal = function (doneCallback) {
                $scope.$apply(() => {
                    show(doneCallback);
                });
            };
            window.ImageManager = imageManagerService;

            /**
             * Hide the image manager
             */
            $scope.hide = function () {
                $scope.showing = false;
            };

            /**
             * Fetch the list image data from the server.
             */
            function fetchData() {
                var url = '/images/' + $scope.imageType + '/all/' + page;
                $http.get(url).then((response) => {
                    $scope.images = $scope.images.concat(response.data.images);
                    $scope.hasMore = response.data.hasMore;
                    page++;
                });
            }

            $scope.fetchData = fetchData;

            /**
             * Save the details of an image.
             * @param event
             */
            $scope.saveImageDetails = function (event) {
                event.preventDefault();
                var url = '/images/update/' + $scope.selectedImage.id;
                $http.put(url, this.selectedImage).then((response) => {
                    events.emit('success', 'Image details updated');
                }, (response) => {
                    if (response.status === 422) {
                        var errors = response.data;
                        var message = '';
                        Object.keys(errors).forEach((key) => {
                            message += errors[key].join('\n');
                        });
                        events.emit('error', message);
                    } else if (response.status === 403) {
                        events.emit('error', response.data.error);
                    }
                });
            };

            /**
             * Delete an image from system and notify of success.
             * Checks if it should force delete when an image
             * has dependant pages.
             * @param event
             */
            $scope.deleteImage = function (event) {
                event.preventDefault();
                var force = $scope.dependantPages !== false;
                var url = '/images/' + $scope.selectedImage.id;
                if (force) url += '?force=true';
                $http.delete(url).then((response) => {
                    $scope.images.splice($scope.images.indexOf($scope.selectedImage), 1);
                    $scope.selectedImage = false;
                    events.emit('success', 'Image successfully deleted');
                }, (response) => {
                    // Pages failure
                    if (response.status === 400) {
                        $scope.dependantPages = response.data;
                    } else if (response.status === 403) {
                        events.emit('error', response.data.error);
                    }
                });
            };

            /**
             * Simple date creator used to properly format dates.
             * @param stringDate
             * @returns {Date}
             */
            $scope.getDate = function (stringDate) {
                return new Date(stringDate);
            };

        }]);


    ngApp.controller('BookShowController', ['$scope', '$http', '$attrs', '$sce', function ($scope, $http, $attrs, $sce) {
        $scope.searching = false;
        $scope.searchTerm = '';
        $scope.searchResults = '';

        $scope.searchBook = function (e) {
            e.preventDefault();
            var term = $scope.searchTerm;
            if (term.length == 0) return;
            $scope.searching = true;
            $scope.searchResults = '';
            var searchUrl = '/search/book/' + $attrs.bookId;
            searchUrl += '?term=' + encodeURIComponent(term);
            $http.get(searchUrl).then((response) => {
                $scope.searchResults = $sce.trustAsHtml(response.data);
            });
        };

        $scope.checkSearchForm = function () {
            if ($scope.searchTerm.length < 1) {
                $scope.searching = false;
            }
        };

        $scope.clearSearch = function () {
            $scope.searching = false;
            $scope.searchTerm = '';
        };

    }]);


};