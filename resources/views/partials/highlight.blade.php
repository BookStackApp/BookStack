
<script src="{{ baseUrl('/libs/highlightjs/highlight.min.js') }}"></script>
<script>
    $(function() {
        $(document).ready(function() {
            $('pre code').each(function(i, block) {
                hljs.highlightBlock(block);
            });
        });
    });
</script>
