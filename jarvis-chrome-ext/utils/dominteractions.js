// utils/domInteractions.js

(function (window) {
    "use strict";

    window.domInteractions = {
        waitForElement: function (selector, timeout = 5000) {
            return new Promise((resolve, reject) => {
                const startTime = Date.now();

                function checkElement() {
                    const element = document.querySelector(selector);
                    if (element) {
                        resolve(element);
                    } else if (Date.now() - startTime >= timeout) {
                        reject(
                            new Error(`Elemento no encontrado: ${selector}`)
                        );
                    } else {
                        requestAnimationFrame(checkElement);
                    }
                }

                checkElement();
            });
        },

        simulateClick: function (element) {
            const event = new MouseEvent("click", {
                view: window,
                bubbles: true,
                cancelable: true,
            });
            element.dispatchEvent(event);
        },

        fillInput: function (inputElement, value) {
            inputElement.value = value;
            inputElement.dispatchEvent(new Event("input", { bubbles: true }));
            inputElement.dispatchEvent(new Event("change", { bubbles: true }));
        },

        wait: function (ms) {
            return new Promise((resolve) => setTimeout(resolve, ms));
        },

        scrollToElement: function (element) {
            element.scrollIntoView({ behavior: "smooth", block: "center" });
        },

        observeDOMChanges: function (selector, callback) {
            const targetNode = document.querySelector(selector);
            if (!targetNode) return null;

            const observer = new MutationObserver(callback);
            observer.observe(targetNode, { childList: true, subtree: true });
            return observer;
        },
    };
})(window);
