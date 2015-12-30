"use strict";

module.exports = function(ngApp) {

    ngApp.controller('ImageManagerController', ['$scope', '$attrs', '$http', '$timeout','imageManagerService',
    function($scope, $attrs, $http, $timeout, imageManagerService) {
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

        $scope.getUploadUrl = function() {
            return '/images/' + $scope.imageType + '/upload';
        };

        $scope.uploadSuccess = function(file, data) {
            $scope.$apply(() => {
                $scope.images.unshift(data);
            });
        };

        function callbackAndHide(returnData) {
            if (callback) callback(returnData);
            $scope.showing = false;
        }

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

        $scope.selectButtonClick = function() {
            callbackAndHide($scope.selectedImage);
        };

        function show(doneCallback) {
            callback = doneCallback;
            $scope.showing = true;
            // Get initial images if they have not yet been loaded in.
            if (!dataLoaded) {
                fetchData();
                dataLoaded = true;
            }
        }

        imageManagerService.show = show;
        imageManagerService.showExternal = function(doneCallback) {
            $scope.$apply(() => {
                show(doneCallback);
            });
        };
        window.ImageManager = imageManagerService;

        $scope.hide = function() {
            $scope.showing = false;
        };

        function fetchData() {
            var url = '/images/' + $scope.imageType + '/all/' + page;
            $http.get(url).then((response) => {
                $scope.images = $scope.images.concat(response.data.images);
                $scope.hasMore = response.data.hasMore;
                page++;
            });
        }

        $scope.saveImageDetails = function(event) {
            event.preventDefault();
            var url = '/images/update/' + $scope.selectedImage.id;
            $http.put(url, this.selectedImage).then((response) => {
                $scope.imageUpdateSuccess = true;
                $timeout(() => {
                    $scope.imageUpdateSuccess = false;
                }, 3000);
            }, (response) => {
                var errors = response.data;
                var message = '';
                Object.keys(errors).forEach((key) => {
                    message += errors[key].join('\n');
                });
                $scope.imageUpdateFailure = message;
                $timeout(() => {
                    $scope.imageUpdateFailure = false;
                }, 5000);
            });
        };

        $scope.deleteImage = function(event) {
            event.preventDefault();
            var force = $scope.dependantPages !== false;
            var url = '/images/' + $scope.selectedImage.id;
            if (force) url += '?force=true';
            $http.delete(url).then((response) => {
                $scope.images.splice($scope.images.indexOf($scope.selectedImage), 1);
                $scope.selectedImage = false;
                $scope.imageDeleteSuccess = true;
                $timeout(() => {
                    $scope.imageDeleteSuccess = false;
                }, 3000);
            }, (response) => {
                // Pages failure
                if (response.status === 400) {
                    $scope.dependantPages = response.data;
                }
            });
        };

    }]);


    ngApp.controller('BookShowController', ['$scope', '$http', '$attrs', function($scope, $http, $attrs) {
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
                $scope.searchResults = response.data;
            });
        };

        $scope.checkSearchForm = function () {
            if ($scope.searchTerm.length < 1) {
                $scope.searching = false;
            }
        };

        $scope.clearSearch = function() {
            $scope.searching = false;
            $scope.searchTerm = '';
        };

    }]);


};