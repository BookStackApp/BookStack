var fileInput = document.getElementById('fileInput');
var optionDiv = document.getElementById('docs-option-control');
fileInput.addEventListener('change', function() {
    var file = fileInput.files[0];
    mammoth.convertToHtml({arrayBuffer: file})
        .then(function(result) {
            var html = result.value;
            var hiddenField = document.getElementById("html_input");
            hiddenField.value = html;
            optionDiv.style.display = 'block';
        })
        .done();
});
