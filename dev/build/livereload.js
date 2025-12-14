if (!window.__dev_reload_listening) {
    listen();
    window.__dev_reload_listening = true;
}


function listen() {
    console.log('Listening for livereload events...');
    new EventSource("http://127.0.0.1:8000/esbuild").addEventListener('change', e => {
        const { added, removed, updated } = JSON.parse(e.data);

        if (!added.length && !removed.length && updated.length > 0) {
            const updatedPath = updated.filter(path => path.endsWith('.css'))[0]
            if (!updatedPath) return;

            const links = [...document.querySelectorAll("link[rel='stylesheet']")];
            for (const link of links) {
                const url = new URL(link.href);
                const name = updatedPath.replace('-dummy', '');

                if (url.pathname.endsWith(name)) {
                    const next = link.cloneNode();
                    next.href = name + '?version=' + Math.random().toString(36).slice(2);
                    next.onload = function() {
                        link.remove();
                    };
                    link.after(next);
                    return
                }
            }
        }

        location.reload()
    });
}