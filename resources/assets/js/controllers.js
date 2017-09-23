"use strict";

const moment = require('moment');
require('moment/locale/en-gb');

moment.locale('en-gb');

module.exports = function (ngApp, events) {


    ngApp.controller('PageEditController', ['$scope', '$http', '$attrs', '$interval', '$timeout', '$sce',
        function ($scope, $http, $attrs, $interval, $timeout, $sce) {

        $scope.editorHTML = '';
        $scope.editorMarkdown = '';

        $scope.draftText = '';
        let pageId = Number($attrs.pageId);
        let isEdit = pageId !== 0;
        let autosaveFrequency = 30; // AutoSave interval in seconds.
        let isMarkdown = $attrs.editorType === 'markdown';
        $scope.draftsEnabled = $attrs.draftsEnabled === 'true';
        $scope.isUpdateDraft = Number($attrs.pageUpdateDraft) === 1;
        $scope.isNewPageDraft = Number($attrs.pageNewDraft) === 1;

        // Set initial header draft text
        if ($scope.isUpdateDraft || $scope.isNewPageDraft) {
            $scope.draftText = trans('entities.pages_editing_draft');
        } else {
            $scope.draftText = trans('entities.pages_editing_page');
        }

        let autoSave = false;

        let currentContent = {
            title: false,
            html: false
        };

        if (isEdit && $scope.draftsEnabled) {
            setTimeout(() => {
                startAutoSave();
            }, 1000);
        }

        let lastSave = 0;

        /**
         * Start the AutoSave loop, Checks for content change
         * before performing the costly AJAX request.
         */
        function startAutoSave() {
            currentContent.title = $('#name').val();
            currentContent.html = $scope.editorHTML;

            autoSave = $interval(() => {
                // Return if manually saved recently to prevent bombarding the server
                if (Date.now() - lastSave < (1000*autosaveFrequency)/2) return;
                let newTitle = $('#name').val();
                let newHtml = $scope.editorHTML;

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

            let data = {
                name: $('#name').val(),
                html: $scope.editorHTML
            };
            if (isMarkdown) data.markdown = $scope.editorMarkdown;

            let url = window.baseUrl('/ajax/page/' + pageId + '/save-draft');
            $http.put(url, data).then(responseData => {
                draftErroring = false;
                let updateTime = moment.utc(moment.unix(responseData.data.timestamp)).toDate();
                $scope.draftText = responseData.data.message + moment(updateTime).format('HH:mm');
                if (!$scope.isNewPageDraft) $scope.isUpdateDraft = true;
                showDraftSaveNotification();
                lastSave = Date.now();
            }, errorRes => {
                if (draftErroring) return;
                events.emit('error', trans('errors.page_draft_autosave_fail'));
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

        // Listen to save draft events from editor
        window.$events.listen('editor-save-draft', saveDraft);

        // Listen to content changes from the editor
        window.$events.listen('editor-html-change', html => {
            $scope.editorHTML = html;
        });
        window.$events.listen('editor-markdown-change', markdown => {
            $scope.editorMarkdown = markdown;
        });

        /**
         * Discard the current draft and grab the current page
         * content from the system via an AJAX request.
         */
        $scope.discardDraft = function () {
            let url = window.baseUrl('/ajax/page/' + pageId);
            $http.get(url).then(responseData => {
                if (autoSave) $interval.cancel(autoSave);

                $scope.draftText = trans('entities.pages_editing_page');
                $scope.isUpdateDraft = false;
                window.$events.emit('editor-html-update', responseData.data.html);
                window.$events.emit('editor-markdown-update', responseData.data.markdown || responseData.data.html);

                $('#name').val(responseData.data.name);
                $timeout(() => {
                    startAutoSave();
                }, 1000);
                events.emit('success', trans('entities.pages_draft_discarded'));
            });
        };

    }]);
};
