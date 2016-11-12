"use strict";

import moment from 'moment';
import 'moment/locale/en-gb';
moment.locale('en-gb');

export default function (ngApp, events) {

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
            $scope.uploadedTo = $attrs.uploadedTo;
            $scope.view = 'all';

            $scope.searching = false;
            $scope.searchTerm = '';

            var page = 0;
            var previousClickTime = 0;
            var previousClickImage = 0;
            var dataLoaded = false;
            var callback = false;

            var preSearchImages = [];
            var preSearchHasMore = false;

            /**
             * Used by dropzone to get the endpoint to upload to.
             * @returns {string}
             */
            $scope.getUploadUrl = function () {
                return window.baseUrl('/images/' + $scope.imageType + '/upload');
            };

            /**
             * Cancel the current search operation.
             */
            function cancelSearch() {
                $scope.searching = false;
                $scope.searchTerm = '';
                $scope.images = preSearchImages;
                $scope.hasMore = preSearchHasMore;
            }
            $scope.cancelSearch = cancelSearch;


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
                $scope.hide();
            }

            /**
             * Image select action. Checks if a double-click was fired.
             * @param image
             */
            $scope.imageSelect = function (image) {
                var dblClickTime = 300;
                var currentTime = Date.now();
                var timeDiff = currentTime - previousClickTime;

                if (timeDiff < dblClickTime && image.id === previousClickImage) {
                    // If double click
                    callbackAndHide(image);
                } else {
                    // If single
                    $scope.selectedImage = image;
                    $scope.dependantPages = false;
                }
                previousClickTime = currentTime;
                previousClickImage = image.id;
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
                $('#image-manager').find('.overlay').css('display', 'flex').hide().fadeIn(240);
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
                $('#image-manager').find('.overlay').fadeOut(240);
            };

            var baseUrl = window.baseUrl('/images/' + $scope.imageType + '/all/');

            /**
             * Fetch the list image data from the server.
             */
            function fetchData() {
                var url = baseUrl + page + '?';
                var components = {};
                if ($scope.uploadedTo) components['page_id'] = $scope.uploadedTo;
                if ($scope.searching) components['term'] = $scope.searchTerm;


                var urlQueryString = Object.keys(components).map((key) => {
                    return key + '=' + encodeURIComponent(components[key]);
                }).join('&');
                url += urlQueryString;

                $http.get(url).then((response) => {
                    $scope.images = $scope.images.concat(response.data.images);
                    $scope.hasMore = response.data.hasMore;
                    page++;
                });
            }
            $scope.fetchData = fetchData;

            /**
             * Start a search operation
             */
            $scope.searchImages = function() {

                if ($scope.searchTerm === '') {
                    cancelSearch();
                    return;
                }

                if (!$scope.searching) {
                    preSearchImages = $scope.images;
                    preSearchHasMore = $scope.hasMore;
                }

                $scope.searching = true;
                $scope.images = [];
                $scope.hasMore = false;
                page = 0;
                baseUrl = window.baseUrl('/images/' + $scope.imageType + '/search/');
                fetchData();
            };

            /**
             * Set the current image listing view.
             * @param viewName
             */
            $scope.setView = function(viewName) {
                cancelSearch();
                $scope.images = [];
                $scope.hasMore = false;
                page = 0;
                $scope.view = viewName;
                baseUrl = window.baseUrl('/images/' + $scope.imageType  + '/' + viewName + '/');
                fetchData();
            };

            /**
             * Save the details of an image.
             * @param event
             */
            $scope.saveImageDetails = function (event) {
                event.preventDefault();
                var url = window.baseUrl('/images/update/' + $scope.selectedImage.id);
                $http.put(url, this.selectedImage).then(response => {
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
                var url = window.baseUrl('/images/' + $scope.selectedImage.id);
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
            var searchUrl = window.baseUrl('/search/book/' + $attrs.bookId);
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


    ngApp.controller('PageEditController', ['$scope', '$http', '$attrs', '$interval', '$timeout', '$sce',
        function ($scope, $http, $attrs, $interval, $timeout, $sce) {

        $scope.editorOptions = require('./pages/page-form');
        $scope.editContent = '';
        $scope.draftText = '';
        var pageId = Number($attrs.pageId);
        var isEdit = pageId !== 0;
        var autosaveFrequency = 30; // AutoSave interval in seconds.
        var isMarkdown = $attrs.editorType === 'markdown';
        $scope.draftsEnabled = $attrs.draftsEnabled === 'true';
        $scope.isUpdateDraft = Number($attrs.pageUpdateDraft) === 1;
        $scope.isNewPageDraft = Number($attrs.pageNewDraft) === 1;

        // Set initial header draft text
        if ($scope.isUpdateDraft || $scope.isNewPageDraft) {
            $scope.draftText = 'Editing Draft'
        } else {
            $scope.draftText = 'Editing Page'
        }

        var autoSave = false;

        var currentContent = {
            title: false,
            html: false
        };

        if (isEdit && $scope.draftsEnabled) {
            setTimeout(() => {
                startAutoSave();
            }, 1000);
        }

        // Actions specifically for the markdown editor
        if (isMarkdown) {
            $scope.displayContent = '';
            // Editor change event
            $scope.editorChange = function (content) {
                $scope.displayContent = $sce.trustAsHtml(content);
            }
        }

        if (!isMarkdown) {
            $scope.editorChange = function() {};
        }

        let lastSave = 0;

        /**
         * Start the AutoSave loop, Checks for content change
         * before performing the costly AJAX request.
         */
        function startAutoSave() {
            currentContent.title = $('#name').val();
            currentContent.html = $scope.editContent;

            autoSave = $interval(() => {
                // Return if manually saved recently to prevent bombarding the server
                if (Date.now() - lastSave < (1000*autosaveFrequency)/2) return;
                var newTitle = $('#name').val();
                var newHtml = $scope.editContent;

                if (newTitle !== currentContent.title || newHtml !== currentContent.html) {
                    currentContent.html = newHtml;
                    currentContent.title = newTitle;
                    saveDraft();
                }

            }, 1000 * autosaveFrequency);
        }

        let draftErroring = false;
        /**
         * Save a draft update into the system via an AJAX request.
         */
        function saveDraft() {
            if (!$scope.draftsEnabled) return;
            var data = {
                name: $('#name').val(),
                html: isMarkdown ? $sce.getTrustedHtml($scope.displayContent) : $scope.editContent
            };

            if (isMarkdown) data.markdown = $scope.editContent;

            let url = window.baseUrl('/ajax/page/' + pageId + '/save-draft');
            $http.put(url, data).then(responseData => {
                draftErroring = false;
                var updateTime = moment.utc(moment.unix(responseData.data.timestamp)).toDate();
                $scope.draftText = responseData.data.message + moment(updateTime).format('HH:mm');
                if (!$scope.isNewPageDraft) $scope.isUpdateDraft = true;
                showDraftSaveNotification();
                lastSave = Date.now();
            }, errorRes => {
                if (draftErroring) return;
                events.emit('error', 'Failed to save draft. Ensure you have internet connection before saving this page.')
                draftErroring = true;
            });
        }

        function showDraftSaveNotification() {
            $scope.draftUpdated = true;
            $timeout(() => {
                $scope.draftUpdated = false;
            }, 2000)
        }

        $scope.forceDraftSave = function() {
            saveDraft();
        };

        // Listen to shortcuts coming via events
        $scope.$on('editor-keydown', (event, data) => {
            // Save shortcut (ctrl+s)
            if (data.keyCode == 83 && (navigator.platform.match("Mac") ? data.metaKey : data.ctrlKey)) {
                data.preventDefault();
                saveDraft();
            }
        });

        /**
         * Discard the current draft and grab the current page
         * content from the system via an AJAX request.
         */
        $scope.discardDraft = function () {
            let url = window.baseUrl('/ajax/page/' + pageId);
            $http.get(url).then((responseData) => {
                if (autoSave) $interval.cancel(autoSave);
                $scope.draftText = 'Editing Page';
                $scope.isUpdateDraft = false;
                $scope.$broadcast('html-update', responseData.data.html);
                $scope.$broadcast('markdown-update', responseData.data.markdown || responseData.data.html);
                $('#name').val(responseData.data.name);
                $timeout(() => {
                    startAutoSave();
                }, 1000);
                events.emit('success', 'Draft discarded, The editor has been updated with the current page content');
            });
        };

    }]);

    ngApp.controller('PageTagController', ['$scope', '$http', '$attrs',
        function ($scope, $http, $attrs) {

            const pageId = Number($attrs.pageId);
            $scope.tags = [];

            $scope.sortOptions = {
                handle: '.handle',
                items: '> tr',
                containment: "parent",
                axis: "y"
            };

            /**
             * Push an empty tag to the end of the scope tags.
             */
            function addEmptyTag() {
                $scope.tags.push({
                    name: '',
                    value: ''
                });
            }
            $scope.addEmptyTag = addEmptyTag;

            /**
             * Get all tags for the current book and add into scope.
             */
            function getTags() {
                let url = window.baseUrl(`/ajax/tags/get/page/${pageId}`);
                $http.get(url).then((responseData) => {
                    $scope.tags = responseData.data;
                    addEmptyTag();
                });
            }
            getTags();

            /**
             * Set the order property on all tags.
             */
            function setTagOrder() {
                for (let i = 0; i < $scope.tags.length; i++) {
                    $scope.tags[i].order = i;
                }
            }

            /**
             * When an tag changes check if another empty editable
             * field needs to be added onto the end.
             * @param tag
             */
            $scope.tagChange = function(tag) {
                let cPos = $scope.tags.indexOf(tag);
                if (cPos !== $scope.tags.length-1) return;

                if (tag.name !== '' || tag.value !== '') {
                    addEmptyTag();
                }
            };

            /**
             * When an tag field loses focus check the tag to see if its
             * empty and therefore could be removed from the list.
             * @param tag
             */
            $scope.tagBlur = function(tag) {
                let isLast = $scope.tags.length - 1 === $scope.tags.indexOf(tag);
                if (tag.name === '' && tag.value === '' && !isLast) {
                    let cPos = $scope.tags.indexOf(tag);
                    $scope.tags.splice(cPos, 1);
                }
            };

            /**
             * Save the tags to the current page.
             */
            $scope.saveTags = function() {
                setTagOrder();
                let postData = {tags: $scope.tags};
                let url = window.baseUrl('/ajax/tags/update/page/' + pageId);
                $http.post(url, postData).then((responseData) => {
                    $scope.tags = responseData.data.tags;
                    addEmptyTag();
                    events.emit('success', responseData.data.message);
                })
            };

            /**
             * Remove a tag from the current list.
             * @param tag
             */
            $scope.removeTag = function(tag) {
                let cIndex = $scope.tags.indexOf(tag);
                $scope.tags.splice(cIndex, 1);
            };

        }]);


    ngApp.controller('PageAttachmentController', ['$scope', '$http', '$attrs',
        function ($scope, $http, $attrs) {

            const pageId = $scope.uploadedTo = $attrs.pageId;
            let currentOrder = '';
            $scope.files = [];
            $scope.editFile = false;
            $scope.file = getCleanFile();
            $scope.errors = {
                link: {},
                edit: {}
            };

            function getCleanFile() {
                return {
                    page_id: pageId
                };
            }

            // Angular-UI-Sort options
            $scope.sortOptions = {
                handle: '.handle',
                items: '> tr',
                containment: "parent",
                axis: "y",
                stop: sortUpdate,
            };

            /**
             * Event listener for sort changes.
             * Updates the file ordering on the server.
             * @param event
             * @param ui
             */
            function sortUpdate(event, ui) {
                let newOrder = $scope.files.map(file => {return file.id}).join(':');
                if (newOrder === currentOrder) return;

                currentOrder = newOrder;
                $http.put(window.baseUrl(`/attachments/sort/page/${pageId}`), {files: $scope.files}).then(resp => {
                    events.emit('success', resp.data.message);
                }, checkError('sort'));
            }

            /**
             * Used by dropzone to get the endpoint to upload to.
             * @returns {string}
             */
            $scope.getUploadUrl = function (file) {
                let suffix = (typeof file !== 'undefined') ? `/${file.id}` : '';
                return window.baseUrl(`/attachments/upload${suffix}`);
            };

            /**
             * Get files for the current page from the server.
             */
            function getFiles() {
                let url = window.baseUrl(`/attachments/get/page/${pageId}`)
                $http.get(url).then(resp => {
                    $scope.files = resp.data;
                    currentOrder = resp.data.map(file => {return file.id}).join(':');
                }, checkError('get'));
            }
            getFiles();

            /**
             * Runs on file upload, Adds an file to local file list
             * and shows a success message to the user.
             * @param file
             * @param data
             */
            $scope.uploadSuccess = function (file, data) {
                $scope.$apply(() => {
                    $scope.files.push(data);
                });
                events.emit('success', 'File uploaded');
            };

            /**
             * Upload and overwrite an existing file.
             * @param file
             * @param data
             */
            $scope.uploadSuccessUpdate = function (file, data) {
                $scope.$apply(() => {
                    let search = filesIndexOf(data);
                    if (search !== -1) $scope.files[search] = data;

                    if ($scope.editFile) {
                        $scope.editFile = angular.copy(data);
                        data.link = '';
                    }
                });
                events.emit('success', 'File updated');
            };

            /**
             * Delete a file from the server and, on success, the local listing.
             * @param file
             */
            $scope.deleteFile = function(file) {
                if (!file.deleting) {
                    file.deleting = true;
                    return;
                }
                  $http.delete(window.baseUrl(`/attachments/${file.id}`)).then(resp => {
                      events.emit('success', resp.data.message);
                      $scope.files.splice($scope.files.indexOf(file), 1);
                  }, checkError('delete'));
            };

            /**
             * Attach a link to a page.
             * @param file
             */
            $scope.attachLinkSubmit = function(file) {
                file.uploaded_to = pageId;
                $http.post(window.baseUrl('/attachments/link'), file).then(resp => {
                    $scope.files.push(resp.data);
                    events.emit('success', 'Link attached');
                    $scope.file = getCleanFile();
                }, checkError('link'));
            };

            /**
             * Start the edit mode for a file.
             * @param file
             */
            $scope.startEdit = function(file) {
                $scope.editFile = angular.copy(file);
                $scope.editFile.link = (file.external) ? file.path : '';
            };

            /**
             * Cancel edit mode
             */
            $scope.cancelEdit = function() {
                $scope.editFile = false;
            };

            /**
             * Update the name and link of a file.
             * @param file
             */
            $scope.updateFile = function(file) {
                $http.put(window.baseUrl(`/attachments/${file.id}`), file).then(resp => {
                    let search = filesIndexOf(resp.data);
                    if (search !== -1) $scope.files[search] = resp.data;

                    if ($scope.editFile && !file.external) {
                        $scope.editFile.link = '';
                    }
                    $scope.editFile = false;
                    events.emit('success', 'Attachment details updated');
                }, checkError('edit'));
            };

            /**
             * Get the url of a file.
             */
            $scope.getFileUrl = function(file) {
                return window.baseUrl('/attachments/' + file.id);
            };

            /**
             * Search the local files via another file object.
             * Used to search via object copies.
             * @param file
             * @returns int
             */
            function filesIndexOf(file) {
                for (let i = 0; i < $scope.files.length; i++) {
                    if ($scope.files[i].id == file.id) return i;
                }
                return -1;
            }

            /**
             * Check for an error response in a ajax request.
             * @param errorGroupName
             */
            function checkError(errorGroupName) {
                $scope.errors[errorGroupName] = {};
                return function(response) {
                    if (typeof response.data !== 'undefined' && typeof response.data.error !== 'undefined') {
                        events.emit('error', response.data.error);
                    }
                    if (typeof response.data !== 'undefined' && typeof response.data.validation !== 'undefined') {
                        $scope.errors[errorGroupName] = response.data.validation;
                        console.log($scope.errors[errorGroupName])
                    }
                }
            }

        }]);

};
