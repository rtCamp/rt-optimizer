# rt Scripts Optimizer

A WordPress plugin that improves Core Web Vitals score by loading scripts via worker thread. Keeps the main thread idle for users to interact with page as quickly as possible.

## Features

1. **Uses Worker Thread** - This plugin uses worker thread for all the scripts loaded via `wp_enqueue_scripts` function, to minimize the main thread scripts execution, and loads them only on the user interactions like tap, click, scroll or keypress events.
2. **Disables Emojis** - Removes core WP Emojis javascript to reduce extra script execution.
3. **Async/Delayed CSS** - Asynchronously loads non-critical CSS or delays their loading until the first user interaction to improve rendering performance.
4. **Removes Unused Assets** - Provides options to dequeue styles for non-logged-in users or completely comment out specified CSS files from the HTML.
5. **Lazy-loads iFrames** - Delays the loading of all iFrames (including embeds) until they are about to enter the viewport.

## How it works?

1. With the help of `rt_scripts_handler` function hooked with `scripts_loader_tag` filter, the function outputs all script tags with `type="text/rtscript"` which are loaded via `wp_enqueue_scripts`.
2. You can filter or skip some javascripts incase any error occurs by adding script handle like this and then scripts will load normally on main thread by path or handles via backend plugin option.

## Does this interest you?

<a href="https://rtcamp.com/"><img src="https://rtcamp.com/wp-content/uploads/sites/2/2019/04/github-banner@2x.png" alt="Join us at rtCamp, we specialize in providing high performance enterprise WordPress solutions"></a>
