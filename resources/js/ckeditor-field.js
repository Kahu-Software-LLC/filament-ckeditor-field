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
export default function ckeditorField({ state, config, isDisabled }) {
    return {
        state,

        editor: null,

        isTornDown: false,

        isSyncingFromEditor: false,

        bundleHandler: null,

        keydownHandler: null,

        navigatedHandler: null,

        init() {
            this.whenBundleReady(() => this.createEditor())

            // A page restored from Livewire's SPA navigation snapshot can
            // arrive with editor chrome whose JavaScript instance died with
            // the previous page. createEditor() strips it before rebuilding.
            this.navigatedHandler = () => {
                if (this.editor) return

                this.whenBundleReady(() => this.createEditor())
            }
            document.addEventListener('livewire:navigated', this.navigatedHandler)

            this.$watch('state', (value) => {
                if (! this.editor) return
                if (this.isSyncingFromEditor) return

                // A null or undefined state (e.g. a form reset after submit)
                // clears the editor rather than being skipped.
                const content = value ?? ''

                if (this.editor.getData() !== content) {
                    this.editor.setData(content)
                }
            })
        },

        whenBundleReady(callback) {
            if (window.ClassicEditor) {
                callback()

                return
            }

            this.bundleHandler = () => {
                this.bundleHandler = null
                callback()
            }

            window.addEventListener('ckeditor-field:bundle-loaded', this.bundleHandler, { once: true })
        },

        createEditor() {
            if (this.editor || this.isTornDown) return

            // Any editor chrome present without a live instance was restored
            // from a navigation snapshot and belongs to a dead editor.
            this.$el.querySelectorAll('.ck-editor').forEach((element) => element.remove())

            // Plugin constructors cannot travel through the serialised
            // config, so it carries plugin names that are resolved against
            // the window scope here. Names the bundle does not expose are
            // skipped.
            const editorConfig = {
                ...config,
                plugins: (config.plugins ?? []).map((name) => window[name]).filter(Boolean),
            }

            window.ClassicEditor.create(this.$refs.textarea, editorConfig)
                .then((editor) => {
                    // The component may be torn down while creation is in
                    // flight (fast navigation); destroy the orphan instead
                    // of leaking it.
                    if (this.isTornDown) {
                        editor.destroy().catch(() => {})

                        return
                    }

                    this.editor = editor

                    editor.ui.view.element
                        ?.querySelector('.ck-editor__main')
                        ?.classList.add('prose', 'max-w-none', 'dark:prose-invert')

                    if (isDisabled) {
                        editor.enableReadOnlyMode('filament-ckeditor-field')

                        return
                    }

                    const sync = () => {
                        this.isSyncingFromEditor = true
                        this.state = editor.getData()
                        this.isSyncingFromEditor = false
                    }

                    editor.model.document.on('change:data', sync)

                    // Flush pending content before Filament handles the
                    // Ctrl+S / Cmd+S save shortcut.
                    this.keydownHandler = (event) => {
                        const isSave = (event.ctrlKey || event.metaKey) && (event.key === 's' || event.key === 'S')

                        if (! isSave) return

                        sync()
                    }

                    window.addEventListener('keydown', this.keydownHandler, true)
                })
                .catch((error) => {
                    console.error('Error creating CKEditor:', error)
                })
        },

        destroy() {
            this.isTornDown = true

            if (this.bundleHandler) {
                window.removeEventListener('ckeditor-field:bundle-loaded', this.bundleHandler)
                this.bundleHandler = null
            }

            if (this.keydownHandler) {
                window.removeEventListener('keydown', this.keydownHandler, true)
                this.keydownHandler = null
            }

            if (this.navigatedHandler) {
                document.removeEventListener('livewire:navigated', this.navigatedHandler)
                this.navigatedHandler = null
            }

            const editor = this.editor

            // Cleared synchronously so a create racing this teardown cannot
            // observe a stale instance while the asynchronous destroy is
            // still in flight.
            this.editor = null

            if (editor) {
                editor.destroy().catch((error) => console.error('Error destroying CKEditor:', error))
            }
        },
    }
}
