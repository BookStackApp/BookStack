@inject('mermaidProvider', 'BookStack\Plugins\MermaidProvider')

@if(setting('enable-mermaid') != 'disabled')
<!-- Mermaid JS external dependency initialization -->
<script src="{{ $mermaidProvider->getMermaidJsCdnUri() }}" nonce="{{ $cspNonce }}"></script>
<script nonce="{{ $cspNonce }}">
    mermaid.initialize({ startOnLoad: true });
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('pre code.language-mermaid').forEach((block, i) => {
            const parent = block.parentElement;
            const graphCode = block.textContent;

            const mermaidDiv = document.createElement('div');
            mermaidDiv.classList.add('mermaid');
            mermaidDiv.textContent = graphCode;

            parent.replaceWith(mermaidDiv);
        });

        // Re-run Mermaid in case new diagrams were added
        mermaid.init(undefined, document.querySelectorAll('.mermaid'));
    });
</script>
@endif