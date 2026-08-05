// Alpine component for the CKEditor field, loaded on demand through
// Filament's `x-load` mechanism. Living here instead of in an inline
// <script> tag matters: Livewire-morphed DOM (repeater items, locale
// switches) never executes inline scripts, but Alpine initialises x-data on
// morphed elements normally, so every editor sets itself up regardless of
// how its markup reached the page.
//
// The editor bundle (window.ClassicEditor and the plugin constructors)
// loads separately via x-load-js; when this component initialises first, it
// waits for the bundle's `ckeditor-field:bundle-loaded` event instead of
// polling.
export default function ckeditorField({ state, config, isDisabled, isAutofocused = false }) {
    // Everything the editor owns lives in this closure, NOT on the Alpine
    // component: Alpine wraps the component object in a reactive Proxy, and
    // CKEditor instances carry non-configurable properties (`_events`) whose
    // Proxy invariants throw the moment they are read through it.
    let editor = null
    let isTornDown = false
    let isSyncingFromEditor = false
    let bundleHandler = null
    let keydownHandler = null
    let navigatedHandler = null

    return {
        state,

        init() {
            this.whenBundleReady(() => this.createEditor())

            // A page restored from Livewire's SPA navigation snapshot can
            // arrive with editor chrome whose JavaScript instance died with
            // the previous page. createEditor() strips it before rebuilding.
            navigatedHandler = () => {
                if (editor) return

                this.whenBundleReady(() => this.createEditor())
            }
            document.addEventListener('livewire:navigated', navigatedHandler)

            this.$watch('state', (value) => {
                if (! editor) return
                if (isSyncingFromEditor) return

                // A null or undefined state (e.g. a form reset after submit)
                // clears the editor rather than being skipped.
                const content = value ?? ''

                if (editor.getData() !== content) {
                    editor.setData(content)
                }
            })
        },

        whenBundleReady(callback) {
            if (window.ClassicEditor) {
                callback()

                return
            }

            bundleHandler = () => {
                bundleHandler = null
                callback()
            }

            window.addEventListener('ckeditor-field:bundle-loaded', bundleHandler, { once: true })
        },

        createEditor() {
            if (editor || isTornDown) return

            // Any editor chrome present without a live instance was restored
            // from a navigation snapshot and belongs to a dead editor.
            this.$el.querySelectorAll('.ck-editor').forEach((element) => element.remove())

            // Plugin constructors cannot travel through the serialised
            // config, so it carries plugin names that are resolved against
            // the window scope here. Names the bundle does not expose are
            // skipped.
            //
            // The editor content comes from the entangled state rather than
            // the textarea: on SPA navigation the bundle is already loaded
            // and creation runs before Alpine has processed the textarea's
            // directives, so its value cannot be relied on.
            const editorConfig = {
                ...config,
                plugins: (config.plugins ?? []).map((name) => window[name]).filter(Boolean),
                initialData: this.state ?? '',
            }

            window.ClassicEditor.create(this.$refs.textarea, editorConfig)
                .then((instance) => {
                    // The component may be torn down while creation is in
                    // flight (fast navigation); destroy the orphan instead
                    // of leaking it.
                    if (isTornDown) {
                        instance.destroy().catch(() => {})

                        return
                    }

                    editor = instance

                    editor.ui.view.element
                        ?.querySelector('.ck-editor__main')
                        ?.classList.add('prose', 'max-w-none', 'dark:prose-invert')

                    if (isDisabled) {
                        editor.enableReadOnlyMode('filament-ckeditor-field')

                        return
                    }

                    if (isAutofocused) {
                        editor.editing.view.focus()
                    }

                    const sync = () => {
                        isSyncingFromEditor = true
                        this.state = editor.getData()
                        isSyncingFromEditor = false
                    }

                    editor.model.document.on('change:data', sync)

                    // Flush pending content before Filament handles the
                    // Ctrl+S / Cmd+S save shortcut.
                    keydownHandler = (event) => {
                        const isSave = (event.ctrlKey || event.metaKey) && (event.key === 's' || event.key === 'S')

                        if (! isSave) return

                        sync()
                    }

                    window.addEventListener('keydown', keydownHandler, true)
                })
                .catch((error) => {
                    console.error('Error creating CKEditor:', error)
                })
        },

        destroy() {
            isTornDown = true

            if (bundleHandler) {
                window.removeEventListener('ckeditor-field:bundle-loaded', bundleHandler)
                bundleHandler = null
            }

            if (keydownHandler) {
                window.removeEventListener('keydown', keydownHandler, true)
                keydownHandler = null
            }

            if (navigatedHandler) {
                document.removeEventListener('livewire:navigated', navigatedHandler)
                navigatedHandler = null
            }

            const instance = editor

            // Cleared synchronously so a create racing this teardown cannot
            // observe a stale instance while the asynchronous destroy is
            // still in flight.
            editor = null

            if (instance) {
                instance.destroy().catch((error) => console.error('Error destroying CKEditor:', error))
            }
        },
    }
}
