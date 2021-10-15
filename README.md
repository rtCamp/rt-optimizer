# rt Scripts Optimizer

Improves Core Web Vitals score by loading scripts via worker thread. Keeps the main thread idle for users to interact with page as quickly as possible.

## Features

1. **Uses Worker Thread** - This plugin uses worker thread for all the scripts loaded via `wp_enqueue_scripts` function, to minimize the main thread scripts execution, and loads them only on the user interactions like tap, click, scroll or keypress events.
2. **Disables Emojis** - Removes core WP Emojis javascript to reduce extra script execution.
